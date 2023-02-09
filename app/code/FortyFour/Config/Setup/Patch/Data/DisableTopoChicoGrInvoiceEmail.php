<?php

namespace FortyFour\Config\Setup\Patch\Data;

use FortyFour\Config\Helper\ConfigWriter as ConfigWriterHelper;
use FortyFour\Config\Model\Config;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class DisableTopoChicoGrInvoiceEmail implements DataPatchInterface
{
    /**
     * @var ConfigWriterHelper
     */
    private $configWriterHelper;
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    private $configWriter;

    /**
     * RemoveEgyptWelcomeMessage constructor.
     * @param ConfigWriterHelper $configWriterHelper
     */
    public function __construct(ConfigWriterHelper $configWriterHelper)
    {
        $this->configWriterHelper = $configWriterHelper;
        $this->configWriter = $this->configWriterHelper->getConfigWriter();
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
     * @return $this|SetEgyptTaxSortOrder
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function apply()
    {
        $this->disableInvoiceEmailForTopoChico();
        return $this;
    }

    /**
     * @return void
     */
    private function disableInvoiceEmailForTopoChico(): void
    {
        $websiteId = $this->configWriterHelper->getWebsiteIdByCode(Config::TOPO_CHICO_GR_WEBSITE_CODE);
        $this->configWriter->save(
            Config::SALES_EMAIL_INVOICE_ENABLED,
            0,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
            $websiteId
        );
    }
}
