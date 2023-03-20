<?php

namespace Coke\WhitelistEmail\Model\Email\Sender;

use Magento\Sales\Model\Order\Email\Sender as MagentoSender;
use Magento\Sales\Model\Order;

class OrderUnderReviewSender extends MagentoSender
{
    /**
     * @param Order $order
     * @return bool
     */
    public function send(Order $order): bool
    {
        $this->templateContainer->setTemplateVars([
            'order' => $order,
            'order_id' => $order->getId(),
            'billing' => $order->getBillingAddress(),
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
            'created_at_formatted' => $order->getCreatedAtFormatted(2),
            'order_data' => [
                'customer_name' => $order->getCustomerName(),
                'is_not_virtual' => $order->getIsNotVirtual(),
                'email_customer_note' => $order->getEmailCustomerNote(),
                'frontend_status_label' => $order->getFrontendStatusLabel()
            ]
        ]);

        if ($this->checkAndSend($order)) {
            return true;
        }

        return false;
    }
}
