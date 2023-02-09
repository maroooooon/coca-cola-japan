<?php

namespace Coke\Whitelist\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface WhitelistTypeSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Coke\Whitelist\Api\Data\WhitelistTypeInterface[]
     */
    public function getItems();

    /**
     * @param \Coke\Whitelist\Api\Data\WhitelistTypeInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
