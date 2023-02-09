<?php
namespace Logicbroker\RetailerAPI\Helper;

use \Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const API_URL = 'logicbroker_retailerapi_api/general/apiurl';
    const API_KEY = 'logicbroker_retailerapi_api/general/apikey';
    const PROD_PORTAL = 'https://portal.logicbroker.com';
    const STAGE_PORTAL = 'https://stageportal.logicbroker.com';
    const BUNDLE_ITEM_ENABLE = 'logicbroker_retailerapi_options/options/enablebundleitems';
    const REINDEX_AFTER_INVENTORY_IMPORT = 'logicbroker_retailerapi_options/inventory/reindexafterimport';
    const CLEAR_CACHE_AFTER_INVENTORY_IMPORT = 'logicbroker_retailerapi_options/inventory/clearcacheafterimport';
    const INVOICE_AFTER_FIRST_SHIPMENT = 'logicbroker_retailerapi_options/shipping/invoiceafterfirstshipment';
    const CUSTOM_INVOICE_NUMBER = 'logicbroker_retailerapi_options/shipping/custom_invoice_number';
    const LOG_PREFIX = '[Logicbroker] ';
    protected $apiUrl = null;
    protected $apiKey = null;

    public function getConfig($propName, $defaultValue)
    {
        $val = $this->scopeConfig->getValue($propName, ScopeInterface::SCOPE_STORE);
        if ($val === null || $val === "") {
            return $defaultValue;
        }
        return $val;
    }

    public function getApiUrl()
    {
        if ($this->apiUrl == null) {
            $this->apiUrl = $this->scopeConfig->getValue(self::API_URL, ScopeInterface::SCOPE_STORE);
        }
        return $this->apiUrl;
    }

    public function getApiKey()
    {
        if ($this->apiKey == null) {
            $this->apiKey = $this->scopeConfig->getValue(self::API_KEY, ScopeInterface::SCOPE_STORE);
        }
        return $this->apiKey;
    }

    public function isProduction()
    {
        $url = $this->getApiUrl();
        $match = "https://commerceapi.io";
        return strlen($url) >= strlen($match) && substr($url, 0, strlen($match)) === $match;
    }

    public function getPortalUrl()
    {
        if (!$this->isProduction()) {
            return self::STAGE_PORTAL;
        }
        return self::PROD_PORTAL;
    }

    public function logInfo($msg)
    {
        $this->_logger->info(self::LOG_PREFIX.$msg);
    }

    public function logError($msg)
    {
        $this->_logger->error(self::LOG_PREFIX.$msg);
    }

    public function updateDocumentStatus($apiUrl, $apiKey, $docType, $key, $status)
    {
        try {
            $ch = curl_init();
            $url = $apiUrl."api/v1/".$docType."s/".$key."/status/".$status."?subscription-key=".$apiKey;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:application/json', 'Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
            $result = curl_exec($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($http_status >= 400) {
                $this->logError('Failed to update '.$docType.' '.$key.' to status '.$status.'.');
                $this->logInfo('Status: '.$http_status);
                $this->logInfo('Result: '.$result);
            }
        } catch (\Exception $e) {
            $this->logError('Error updating '.$docType.' '.$key.' to status '.$status.': '.$e->getMessage());
        }
    }

    public function createFailedEvent($apiUrl, $docType, $document, $message)
    {
        try {
            $key = $document->Identifier->LogicbrokerKey;
            $data = array();
            $data["LogicbrokerKey"] = $key;
            $data["Summary"] = "Failed to import into Magento";
            $data["Details"] = $message;
            $data["Level"] = "Alert";
            $data["TypeId"] = 57;
            $data["ReceiverId"] = $document->SenderCompanyId;
            $this->postToApi($apiUrl."api/v1/activityevents/", $data);
        } catch (\Exception $e) {
            $this->logError('Error creating failed document event for '.$docType.' '.$key.': '.$e->getMessage());
        }
    }

    public function getKeyValue($kvpList, $kvpName)
    {
        if ($kvpList == null) {
            return null;
        }
        foreach ($kvpList as $kvp) {
            if ($kvp->Name == $kvpName) {
                if (property_exists($kvp, "Value")) {
                    return $kvp->Value;
                }
                return null;
            }
        }
        return null;
    }

    protected function addSubKey($url)
    {
        $authUrl = $url;
        if (strpos($url, '?') !== false) {
            $authUrl .= "&subscription-key=".$this->getApiKey();
        } else {
            $authUrl .= "?subscription-key=".$this->getApiKey();
        }
        return $authUrl;
    }

    public function getFromApi($url, $path = null)
    {
        $ch = curl_init();
        $ret = array();
        $authUrl = $this->addSubKey($url);
        curl_setopt($ch, CURLOPT_URL, $authUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:application/json'));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        $ret['Result'] = curl_exec($ch);
        $ret['Status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($ret['Result'] == false || $ret['Status'] >= 400) {
            $this->logError("Error getting API response from ".$url);
            $this->logInfo("API responded with status ".$ret['Status']);
            $this->logInfo("API result: ".$ret['Result']);
            throw new \Exception("GET failed.");
        }

        if ($path != null && $ret['Result'] != null) {
            $ret['Result'] = $this->getObjectFromString($ret['Result'], $path);
        }
        return $ret;
    }

    public function postToApi($url, $obj, $path = null)
    {
        $json = json_encode($obj);
        $ch = curl_init();
        $headers = array('Accept:application/json', 'Content-Type:application/json', 'SourceSystem:Magento');
        $authUrl = $this->addSubKey($url);
        $ret = array();
        curl_setopt($ch, CURLOPT_URL, $authUrl);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        $ret['Result'] = curl_exec($ch);
        $ret['Status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($ret['Result'] == false || $ret['Status'] >= 400) {
            $this->logError("Error posting data to API endpoint ".$url);
            $this->logInfo("API responded with status ".$ret['Status']);
            $this->logInfo("API result: ".$ret['Result']);
            throw new \Exception("POST failed.");
        }
        if ($path != null && $ret['Result'] != null) {
            $ret['Result'] = $this->getObjectFromString($ret['Result'], $path);
        }
        return $ret;
    }

    public function getObjectFromString($objStr, $path)
    {
        $obj = json_decode($objStr);
        $ct = count($path);
        for ($i = 0; $i < $ct; $i++) {
            $partial = $path[$i];
            if ($obj !== null && property_exists($obj, $partial) && $obj->{$partial} !== null
                && (($i < $ct - 1 && is_object($obj->{$partial})) || ($i === $ct - 1))) {
                $obj = $obj->{$partial};
            } else {
                throw new \Exception("Invalid property: ".$partial);
            }
        }
        return $obj;
    }
}
