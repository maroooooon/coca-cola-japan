<?php

namespace FortyFour\Sales\Plugin;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\DataProvider\ProductCollection;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

class OrderCreateSearchGridProductCollectionPlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * OrderCreateSearchGridProductCollectionPlugin constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param ProductCollection $subject
     * @param $result
     * @param Store $store
     * @return Collection
     */
    public function afterGetCollectionForStore(
        ProductCollection $subject,
        $result,
        Store $store
    ): Collection {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $result */

        try {
            $result->addAttributeToSelect('brand')
                ->setOrder('brand', 'ASC');
        } catch (LocalizedException $e) {
            $this->logger->info(
                __('[OrderCreateSearchGridProductCollectionPlugin::afterGetCollectionForStore()] $1', $e->getMessage())
            );
        }

        return $result;
    }
}
