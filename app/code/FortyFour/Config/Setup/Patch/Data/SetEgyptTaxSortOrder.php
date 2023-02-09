<?php

namespace FortyFour\Config\Setup\Patch\Data;

use FortyFour\Config\Helper\ConfigWriter as ConfigWriterHelper;
use FortyFour\Config\Model\Config;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class SetEgyptTaxSortOrder implements DataPatchInterface
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
        $this->setCokeEgyptTaxSortOrder();
        return $this;
    }

    /**
     *
     */
    private function setCokeEgyptTaxSortOrder(): void
    {
        $cokeEgyptWebsiteId = $this->configWriterHelper->getWebsiteIdByCode(Config::EGYPT_WEBSITE_CODE);
        $this->configWriter->save(
            Config::TAX_SORT_ORDER_PATH,
            5,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
            $cokeEgyptWebsiteId
        );
    }
}
