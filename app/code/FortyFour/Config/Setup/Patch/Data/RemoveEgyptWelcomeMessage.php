<?php

namespace FortyFour\Config\Setup\Patch\Data;

use FortyFour\Config\Helper\ConfigWriter as ConfigWriterHelper;
use FortyFour\Config\Model\Config;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class RemoveEgyptWelcomeMessage implements DataPatchInterface
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
     * @return $this|RemoveEgyptWelcomeMessage
     */
    public function apply()
    {
        $this->deleteCokeEgyptStoreWelcomeMessages();
        return $this;
    }

    /**
     * @return void
     */
    private function deleteCokeEgyptStoreWelcomeMessages(): void
    {
        $storeIds = [
            $this->configWriterHelper->getStoreIdByCode('default'),
            $this->configWriterHelper->getStoreIdByCode('egypt'),
            $this->configWriterHelper->getStoreIdByCode('egypt_en')
        ];

        foreach ($storeIds as $storeId) {
            $this->configWriter->delete(
                Config::WELCOME_MESSAGE_PATH,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
                $storeId
            );
        }
    }
}
