<?php
namespace Logicbroker\RetailerAPI\Jobs\Cron;

use \Logicbroker\RetailerAPI\Helper\Data;
use Magento\Framework\App\Filesystem\DirectoryList;

class PullInventory
{
    protected $helper;
    protected $apiUrl;
    protected $apiKey;
    protected $db;
    protected $fileSystem;
    protected $indexerFactory;
    protected $cacheManager;

    public function __construct(
        Data $helper,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\ResourceConnection $db,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\Framework\App\Cache\Manager $cacheManager
    ) {
        $this->helper = $helper;
        $this->db = $db;
        $this->fileSystem = $filesystem;
        $this->indexerFactory = $indexerFactory;
        $this->cacheManager = $cacheManager;
    }

    public function execute()
    {
        $this->apiKey = $this->helper->getApiKey();
        if ($this->apiKey === null) {
            $this->helper->logInfo("API key is null, unable to pull inventory.");
            return;
        }
        $this->apiUrl = $this->helper->getApiUrl();
        $partners = $this->getPartners();
        $reindex = false;
        foreach ($partners as $partner) {
            try {
                $date = date("Y-m-d H:i:s", time());
                $file = $this->downloadInventory($partner);
                $items = $this->getLinecount($file) - 1;
                $this->helper->logInfo('Updating inventory for partner '.$partner.' ('.$items.' items in file)');
                if ($items > 0) {
                    $this->importInventory($file, $partner);
                    $reindex = true;
                    $this->saveInventoryHistory($partner, $items, $date);
                }
                unlink($file);
                $this->helper->logInfo('Inventory update complete for partner '.$partner);
            } catch (\Exception $e) {
                $this->helper->logError('Error updating inventory for partner '.$partner.': '.$e->getMessage());
            }
        }
        if ($reindex) {
            $this->reindex();
        }
    }

    protected function reindex()
    {
        if ($this->helper->getConfig(Data::REINDEX_AFTER_INVENTORY_IMPORT, "true") == "true") {
            $indexer = $this->indexerFactory->create()->load('cataloginventory_stock');
            $indexer->reindexAll();
            $this->helper->logInfo('Re-indexed cataloginventory_stock after importing inventory.');
        }
        if ($this->helper->getConfig(Data::CLEAR_CACHE_AFTER_INVENTORY_IMPORT, "true") == "true") {
            $this->cacheManager->flush(array('full_page', 'block_html', 'collections'));
            $this->helper->logInfo('Flushed cache after importing inventory.');
        }
    }

    protected function importInventory($file, $partner)
    {
        $connection = $this->db->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $connection->rawQuery('DROP TEMPORARY TABLE IF EXISTS LbDropshipInventory;');
        $create = 'CREATE TEMPORARY TABLE LbDropshipInventory (MerchantSKU VARCHAR(256) NULL, Quantity INT NULL);';
        $connection->rawQuery($create);
        # Can't use load data local by default in magento, need to set value in app/etc/env.php
        # 'driver_options' => array(PDO::MYSQL_ATTR_LOCAL_INFILE => true)
        $importQuery = "LOAD DATA LOCAL INFILE '".$file."' INTO TABLE LbDropshipInventory ".
                      "FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' ".
                      "LINES TERMINATED BY '\r\n' IGNORE 1 LINES ".
                      "(@SupplierSKU,@MerchantSKU,@UPC,@ManufacturerSKU,@Quantity) ".
                      "SET Quantity = IF(@Quantity='',0,@Quantity), MerchantSKU=@MerchantSKU;";
        try {
            $importResult = $connection->rawQuery($importQuery);
            if ($importResult->rowCount() == 0) {
                throw new \Exception("No rows imported.");
            }
        } catch (\Exception $e) {
            $this->helper->logInfo("Unable to import inventory data with 'LOAD DATA LOCAL', using slow method.");
            $connection->rawQuery('DELETE FROM LbDropshipInventory;');
            $this->fallbackImport($file, $connection);
        }
        $file = null;
        try {
            $file = $this->createHistoryFile($partner);
        } catch (\Exception $e) {
            $this->helper->logError("Failed to create inventory history file: ".$e->getMessage());
        }
        $stockItemTable   = $this->db->getTableName('cataloginventory_stock_item');
        $catalogProductTable   = $this->db->getTableName('catalog_product_entity');

        $updateQuery = "update ".$stockItemTable." inner join ".
            "(select Quantity,entity_id from LbDropshipInventory lb inner join ".$catalogProductTable." cp on ".
            "lb.MerchantSKU=cp.sku) source ".
            "on ".$stockItemTable.".product_id = source.entity_id ".
            "set qty = Quantity, is_in_stock = IF(Quantity = 0, 0, 1);";
        $connection->rawQuery($updateQuery);
        $connection->rawQuery('DROP TEMPORARY TABLE IF EXISTS LbDropshipInventory;');
        try {
            if ($file !== null) {
                $this->createHistoryEvent($file, $partner);
            }
        } catch (\Exception $e) {
            $this->helper->logError("Failed to create inventory history event: ".$e->getMessage());
        }
    }

    protected function createHistoryFile($partner)
    {
        $connection = $this->db->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $stockItemTable   = $this->db->getTableName('cataloginventory_stock_item');
        $catalogProductTable   = $this->db->getTableName('catalog_product_entity');
        $query = "SELECT entity_id, MerchantSKU, qty, Quantity FROM ".
            $stockItemTable." INNER JOIN ".
            "(SELECT Quantity, MerchantSKU, entity_id from LbDropshipInventory lb INNER JOIN ".
            $catalogProductTable." cp ON lb.MerchantSKU=cp.sku) source ".
            "ON ".$stockItemTable.".product_id = source.entity_id ".
            "WHERE qty <> Quantity";
        $directory = $this->fileSystem->getDirectoryWrite(DirectoryList::TMP);
        $directory->create();
        $rows = $connection->fetchAll($query);
        $tmpFileName = $directory->getAbsolutePath('report_'.$partner.'_'.time().'.csv');
        if (count($rows) === 0) {
            return null;
        }
        $fp = fopen($tmpFileName, 'w');
        if ($fp) {
            fputcsv($fp, array("product_id", "sku", "quantity_old", "quantity_new"));
            foreach ($rows as $row) {
                fputcsv($fp, array_values($row));
            }
            fclose($fp);
        } else {
            $this->helper->logInfo("Failed to create inventory history file: ".$tmpFileName);
            return null;
        }
        return $tmpFileName;
    }

    protected function createHistoryEvent($tmpFileName, $partner)
    {
        $link = $this->uploadReport($partner, $tmpFileName);
        if ($link === null) {
            return;
        }
        $obj = array("ReceiverId" => $partner,
            "Level" => "Info",
            "Summary" => "Magento Inventory Updated",
            "Details" => "Magento processed inventory changes, download the report for full details.",
            "Category" => "Inventory",
            "TypeId" => 39,
            "AdditionalData" => json_encode(array("OriginalFileUrl" => $link))
        );
        $this->helper->postToApi($this->apiUrl."api/v1/activityevents/", $obj);
    }

    protected function getLinecount($file)
    {
        $linecount = 0;
        $handle = fopen($file, "r");
        while (!feof($handle)) {
            $line = fgets($handle, 4096);
            $linecount = $linecount + substr_count($line, "\r\n");
        }
        fclose($handle);
        return $linecount;
    }

    protected function fallbackImport($file, $connection)
    {
        $fp = fopen($file, 'r');
        $csv = fgetcsv($fp, 0, ',', '"');
        $count = count($csv);
        $batch = array();
        $i = 0;
        while (($data = fgetcsv($fp, 1000, ",")) !== false) {
            if (count($data) < 5) {
                continue;
            }
            $batch[] = array("MerchantSKU" => $data[1], "Quantity" => $data[4]);
            if ($i%1000 === 0) {
                $connection->insertMultiple("LbDropshipInventory", $batch);
                $batch = array();
            }
            $i++;
        }
        if ($batch != null) {
            $connection->insertMultiple("LbDropshipInventory", $batch);
        }
        fclose($fp);
    }

    protected function downloadInventory($partner)
    {
        $date = $this->getLastPull($partner);
        $link = $this->getInventoryLink($partner, $date);
        $directory = $this->fileSystem->getDirectoryWrite(DirectoryList::TMP);
        $directory->create();
        $tmpFileName = $directory->getAbsolutePath('inventory_'.$partner.'_'.time().'.csv');
        $this->helper->logInfo('Saving inventory file for partner '.$partner.' to '.$tmpFileName);
        $fp = fopen($tmpFileName, 'w+');
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $tmpFileName;
    }

    protected function uploadReport($partner, $filePath)
    {
        $url = $this->apiUrl."api/v1/attachments?type=csv&description=Magento%20Update%20Report&receiverId="
            .$partner."&subscription-key=".$this->apiKey;
        $ch = curl_init($url);
        $cfile = curl_file_create($filePath, 'text/csv', 'report.csv');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('file_upload' => $cfile));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        unlink($filePath);
        if ($status >= 400) {
            $this->helper->logError("Failed to upload attachment: ".$result);
            return null;
        }
        $records = $this->helper->getObjectFromString($result, array("Body", "Records"));
        return $records[0]->Url;
    }

    protected function getInventoryLink($partner, $date)
    {
        $link = null;
        $ch = curl_init();
        $url = $this->apiUrl."api/v1/inventory/".$partner."?transform=false";
        if ($date !== null) {
            $url .= "&modifiedAfter=".curl_escape($ch, $date);
        }
        try {
            $apiRes = $this->helper->getFromApi($url, array('Body'));
            $link = $apiRes["Result"];
        } catch (\Exception $e) {
            $this->helper->logError('Error getting inventory link: '.$e->getMessage());
        }
        return $link;
    }

    protected function getPartners()
    {
        $partners = array();
        try {
            $apiRes = $this->helper->getFromApi($this->apiUrl."api/v1/partners", array('Body', 'Partners'));
            foreach ($apiRes['Result'] as $partial) {
                $partners[] = $partial->Id;
            }
        } catch (\Exception $e) {
            $this->helper->logError('Error getting partner list: '.$e->getMessage());
        }
        return $partners;
    }

    protected function getLastPull($partnerid)
    {
        $connection = $this->db->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $historyTable   = $this->db->getTableName('logicbroker_inventory_history');
        $query = "SELECT MAX(date) as MAX FROM ".$historyTable." WHERE partnerid = :PARTNERID;";
        $res = $connection->query($query, array("PARTNERID" => $partnerid));
        $date = null;
        if ($res != null) {
            foreach ($res as $row) {
                $date = $row["MAX"];
            }
        }
        return $date;
    }

    protected function saveInventoryHistory($partnerid, $totalItems, $date)
    {
        $connection = $this->db->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $historyTable   = $this->db->getTableName('logicbroker_inventory_history');
        $data = array("partnerid" => $partnerid, "total_items" => $totalItems, "date" => $date);
        $connection->insert($historyTable, $data);
    }
}
