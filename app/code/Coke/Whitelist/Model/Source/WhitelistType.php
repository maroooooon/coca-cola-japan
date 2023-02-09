<?php

namespace Coke\Whitelist\Model\Source;

use Coke\Whitelist\Model\ResourceModel\WhitelistType\CollectionFactory;

class WhitelistType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $whitelistTypeCollectionFactory;

    /**
     * WhitelistType constructor.
     * @param CollectionFactory $whitelistTypeCollectionFactory
     */
    public function __construct(
        CollectionFactory $whitelistTypeCollectionFactory
    ) {
        $this->whitelistTypeCollectionFactory = $whitelistTypeCollectionFactory;
    }

    public function toOptionArray()
    {
        $collection = $this->whitelistTypeCollectionFactory->create()
            ->load();

        $result = [
            [
                'value' => '',
                'label' => __('-- Please Select --')
            ]
        ];
        foreach ($collection->getData() as $row) {
            $result[] = [
                'label' => $row['name'],
                'value' => $row['type_id']
            ];
        }

        return $result;
    }
}