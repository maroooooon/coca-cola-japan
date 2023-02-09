<?php

namespace Coke\Whitelist\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class WhitelistValues extends Column
{
    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $logger = \Magento\Framework\App\ObjectManager::getInstance()->create(\Psr\Log\LoggerInterface::class);
        $logger->info(__('[Coke\Whitelist] prepareDataSource()'));
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = implode(PHP_EOL, $item[$this->getData('name')]);
            }
        }

        return $dataSource;
    }
}
