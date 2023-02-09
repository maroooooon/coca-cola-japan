<?php

namespace Coke\PostcodeRestrictions\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface PostcodeSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Coke\PostcodeRestrictions\Api\Data\PostcodeInterface[]
     */
    public function getItems();

    /**
     * @param \Coke\PostcodeRestrictions\Api\Data\PostcodeInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
