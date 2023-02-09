<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace Coke\Japan\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;

class EmailOrderSetTemplateVarsBefore implements ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TimezoneInterface $timezone
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->timezone = $timezone;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var DataObject $transportObject */
        $transportObject = $observer->getEvent()->getData('transportObject');

        if (!($store = $transportObject->getStore())
            || $store->getWebsite()->getCode() != \Coke\Japan\Model\Website::MARCHE) {
            return;
        }

        /** @var Order $order */
        if ($order = $transportObject->getOrder()) {
            $transportObject->setData(
                'created_at_formatted',
                $this->getCreatedAtFormatted(2, $order)
            );
        }
    }

    /**
     * @param $format
     * @param Order $order
     * @return string
     */
    private function getCreatedAtFormatted($format, Order $order): string
    {
        try {
            $createdAtDate = $this->timezone->formatDateTime(
                new \DateTime($order->getCreatedAt()),
                $format,
                $format,
                $this->scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE, $order->getStore()),
                $this->timezone->getConfigTimezone(ScopeInterface::SCOPE_STORE, $order->getStore()->getCode())
            );

            return date('Y/n/j, h:i:s A', strtotime($createdAtDate));
        } catch (\Exception $e) {
            return $order->getCreatedAtFormatted(2);
        }
    }
}
