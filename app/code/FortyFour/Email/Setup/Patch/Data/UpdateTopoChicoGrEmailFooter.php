<?php

namespace FortyFour\Email\Setup\Patch\Data;

use FortyFour\Email\Model\ContentUpgrader;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;

class UpdateTopoChicoGrEmailFooter implements DataPatchInterface
{
    /**
     * @var ContentUpgrader
     */
    private $contentUpgrader;
    /**
     * @var WriterInterface
     */
    private $configWriter;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * AddTopoChicoGrEmailLogo constructor.
     * @param ContentUpgrader $contentUpgrader
     * @param WriterInterface $configWriter
     * @param ResourceConnection $resourceConnection
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ContentUpgrader $contentUpgrader,
        WriterInterface $configWriter,
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager
    ) {
        $this->contentUpgrader = $contentUpgrader;
        $this->configWriter = $configWriter;
        $this->resourceConnection = $resourceConnection;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return $this|UpdateTopoChicoGrEmailFooter
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function apply()
    {
        $topoChicoGrGreekFooterTemplateId = $this->insertTopoChicoGrGreekFooterTemplate();
        $topoChicoGrGreekStore = $this->storeManager->getStore('topo_chico_gr_gr');

        $this->configWriter->save(
            'design/email/footer_template',
            $topoChicoGrGreekFooterTemplateId,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
            $topoChicoGrGreekStore->getId()
        );

        return $this;
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function insertTopoChicoGrGreekFooterTemplate()
    {
        $data = [
            'template_code' => 'Topo Chico GR Footer - Greek',
            'template_text' => $this->contentUpgrader->readFile('email', 'topo_chico_gr_footer.html'),
            'template_type' => 2,
            'template_subject' => '{{trans "Footer"}}',
            'orig_template_code' => 'design_email_footer_template',
            'orig_template_variables' => '{"var store.frontend_name":"Store Name","var url_about_us":"About Us URL","var url_customer_service":"Customer Service URL","var store_phone":"Store Phone","var store_hours":"Store Hours","var store.formatted_address|raw":"Store Address"}'
        ];
        $connection = $this->resourceConnection->getConnection();
        $connection->insert(
            $connection->getTableName('email_template'),
            $data
        );

        return $connection->lastInsertId();
    }
}
