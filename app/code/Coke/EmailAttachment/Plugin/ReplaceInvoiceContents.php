<?php

/**
 * @category FortyFour
 * @copyright Copyright (c) 2020 FortyFour LLC
 */

declare(strict_types=1);

namespace Coke\EmailAttachment\Plugin;

use Closure;
use Coke\EmailAttachment\Model\PdfGenerator;
use Magento\Sales\Model\Order\Pdf\Invoice;
use Mpdf\MpdfException;
use Zend_Pdf;

/**
 * Class ReplaceInvoiceContents
 */
class ReplaceInvoiceContents
{
    /**
     * @var PdfGenerator
     */
    private $pdfGenerator;

    /**
     * ReplaceInvoiceContents constructor.
     * @param PdfGenerator $pdfGenerator
     */
    public function __construct(
        PdfGenerator $pdfGenerator
    ) {
        $this->pdfGenerator = $pdfGenerator;
    }

    /**
     * @param Invoice $subject
     * @param Closure $proceed
     * @param array $invoices
     * @return array|string|Zend_Pdf
     * @throws MpdfException
     * @noinspection PhpUnusedParameterInspection
     */
    public function aroundGetPdf(
        Invoice $subject,
        Closure $proceed,
        array $invoices
    ) {
        $invoice = reset($invoices);

        return $this->pdfGenerator->generate($invoice);
    }
}
