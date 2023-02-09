<?php

namespace FortyFour\Config\Setup\Patch\Data;

use FortyFour\Config\Helper\ConfigWriter as ConfigWriterHelper;
use FortyFour\Config\Model\Config;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class SetTopoChicoGrDefaultPageTitle implements DataPatchInterface
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
        $websiteId = $this->configWriterHelper->getWebsiteIdByCode(Config::TOPO_CHICO_GR_WEBSITE_CODE);
        $this->configWriter->save(
            Config::DESIGN_DEFAULT_TITLE,
            'TopoChico Greece',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
            $websiteId
        );

        return $this;
    }
}
