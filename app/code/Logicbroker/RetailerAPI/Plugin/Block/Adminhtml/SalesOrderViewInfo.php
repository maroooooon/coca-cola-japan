<?php
namespace Logicbroker\RetailerAPI\Plugin\Block\Adminhtml;

class SalesOrderViewInfo
{
    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View\Info $subject
     * @param string $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(
        \Magento\Sales\Block\Adminhtml\Order\View\Info $subject,
        $result
    ) {
        $customInvoiceNumberBlock = $subject->getLayout()->getBlock('order_custom_invoice_number');
        if ($customInvoiceNumberBlock !== false && $subject->getNameInLayout() == 'order_info') {
            $customInvoiceNumberBlock->setCustomInvoiceNumber($subject->getOrder()->getData('custom_invoice_number'));
            $result = $result . $customInvoiceNumberBlock->toHtml();
        }

        return $result;
    }
}
