<?php
namespace FortyFour\Shipping\Observer;

use Exception;
use FortyFour\Shipping\Helper\ExpressStandard\Config;
use FortyFour\Shipping\Model\ExpressStandard\DeliveryComment;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;

class AddExpressStandardDeliveryCommentToOrder implements ObserverInterface
{
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Config
     */
    private $expressStandardConfig;

    /**
     * SalesModelServiceQuoteSubmitBefore constructor.
     *
     * @param QuoteRepository $quoteRepository
     * @param LoggerInterface $logger
     * @param Config $expressStandardConfig
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        LoggerInterface $logger,
        Config $expressStandardConfig
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->expressStandardConfig = $expressStandardConfig;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     * @throws Exception
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->expressStandardConfig->isExpressShippingEnabled()
            || !$this->expressStandardConfig->isStandardShippingEnabled()) {
            return $this;
        }

        $order = $observer->getOrder();
        try {
            $quote = $this->quoteRepository->get($order->getQuoteId());
            $order->setData(
                DeliveryComment::EXPRESS_STANDARD_DELIVERY_COMMENT,
                $quote->getData(DeliveryComment::EXPRESS_STANDARD_DELIVERY_COMMENT)
            );
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $this;
    }
}
