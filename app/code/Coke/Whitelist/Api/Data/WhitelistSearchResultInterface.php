<?php

namespace Coke\Whitelist\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface WhitelistSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Coke\Whitelist\Api\Data\WhitelistInterface[]
     */
    public function getItems();

    /**
     * @param \Coke\Whitelist\Api\Data\WhitelistInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
