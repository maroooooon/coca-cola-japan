<?php

namespace FortyFour\Config\Setup\Patch\Data;

use FortyFour\Config\Helper\ConfigWriter as ConfigWriterHelper;
use FortyFour\Config\Model\Config;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class SetTopoChicoGrCopyright implements DataPatchInterface
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
        $this->setTopoChicoGrEngStoreCopyright();
        $this->setTopoChicoGrGrStoreCopyright();
        return $this;
    }

    /**
     * @return void
     */
    private function setTopoChicoGrEngStoreCopyright(): void
    {
        $storeId = $this->configWriterHelper->getStoreIdByCode(Config::TOPO_CHICO_EN_STORE_CODE);
        $this->configWriter->save(
            Config::COPYRIGHT_MESSAGE_PATH,
            'Copyright ©2020 The Coca-Cola Company. All rights reserved. TOPO CHICO is a trademark of The Coca-Cola Company.',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
            $storeId
        );
    }

    /**
     * @return void
     */
    private function setTopoChicoGrGrStoreCopyright(): void
    {
        $storeId = $this->configWriterHelper->getStoreIdByCode(Config::TOPO_CHICO_GR_STORE_CODE);
        $this->configWriter->save(
            Config::COPYRIGHT_MESSAGE_PATH,
            'Copyright © The Coca-Cola Company. Με επιφύλαξη παντός δικαιώματος. Το TOPO CHICO είναι εμπορικό σήμα της The Coca-Cola Company.',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
            $storeId
        );
    }
}
