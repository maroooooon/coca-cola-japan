<?php

/**
 * @category FortyFour
 * @copyright Copyright (c) 2020 FortyFour LLC
 */

declare(strict_types=1);

namespace Coke\EmailAttachment\Model\PdfBuilder;

use Coke\EmailAttachment\Model\Config;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Model\Order\Pdf\Invoice as PdfInvoice;
use Zend_Pdf_Exception;

/**
 * Class Invoice
 */
class Invoice
{
    /**
     * @var PdfInvoice
     */
    private $pdfInvoice;

    /**
     * @var Config
     */
    private $config;

    /**
     * Invoice constructor.
     * @param PdfInvoice $pdfInvoice
     * @param Config $config
     */
    public function __construct(
        PdfInvoice $pdfInvoice,
        Config $config
    ) {
        $this->pdfInvoice = $pdfInvoice;
        $this->config = $config;
    }

    /**
     * @param InvoiceInterface $invoice
     * @return string
     */
    public function build(InvoiceInterface $invoice): string
    {
        $pdfContent = '';

        if (!$this->config->invoiceAttachmentIsEnabled($invoice->getStoreId())) {
            return $pdfContent;
        }

        try {
            $pdfContent = $this->pdfInvoice->getPdf([$invoice])->render();
        } catch (Zend_Pdf_Exception $e) {
            // do nothing
        }

        return $pdfContent;
    }
}
