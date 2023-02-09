<?php /** @noinspection PhpMissingReturnTypeInspection */

/**
 * @category FortyFour
 * @copyright Copyright (c) 2020 FortyFour LLC
 */

declare(strict_types=1);

namespace Coke\EmailAttachment\Model;

use DateTime;
use DateTimeInterface;
use Exception;
use IntlDateFormatter;
use Magento\Email\Model\BackendTemplate;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\TemplateInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Payment\Helper\Data;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\InvoiceIdentity;
use Magento\Sales\Model\Order\Invoice;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Zend_Pdf;

/**
 * Class PdfGenerator
 */
class PdfGenerator
{
    const TEMPLATE_ID = 'invoice_pdf_template';

    /**
     * @var FactoryInterface
     */
    private $templateFactory;

    /**
     * @var Data
     */
    private $paymentHelper;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var Renderer
     */
    private $addressRenderer;

    /**
     * @var InvoiceIdentity
     */
    private $identityContainer;

    /**
     * @var BackendTemplate
     */
    private $templateModel;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * PdfGenerator constructor.
     * @param FactoryInterface $templateFactory
     * @param Data $paymentHelper
     * @param TimezoneInterface $timezone
     * @param Renderer $addressRenderer
     * @param InvoiceIdentity $identityContainer
     * @param BackendTemplate $templateModel
     * @param DirectoryList $directoryList
     */
    public function __construct(
        FactoryInterface $templateFactory,
        Data $paymentHelper,
        TimezoneInterface $timezone,
        Renderer $addressRenderer,
        InvoiceIdentity $identityContainer,
        BackendTemplate $templateModel,
        DirectoryList $directoryList
    ) {
        $this->templateFactory = $templateFactory;
        $this->paymentHelper = $paymentHelper;
        $this->timezone = $timezone;
        $this->addressRenderer = $addressRenderer;
        $this->identityContainer = $identityContainer;
        $this->templateModel = $templateModel;
        $this->directoryList = $directoryList;
    }

    /**
     * @param Invoice $invoice
     * @return Zend_Pdf
     * @throws MpdfException
     * @throws Exception
     */
    public function generate(Invoice $invoice)
    {
        $contents['body'] = $this->getTemplate($invoice)->processTemplate();
        $contents['header'] = '';
        $contents['footer'] = '';
        $applySettings = $this->mPDFSettings($contents);

        return Zend_Pdf::parse($applySettings);
    }

    /**
     * @param $invoice
     * @return TemplateInterface
     * @throws Exception
     */
    protected function getTemplate(Invoice $invoice)
    {
        return $this->templateFactory->get(self::TEMPLATE_ID, BackendTemplate::class)
            ->setVars($this->getVariables($invoice))
            ->setOptions(['area' => 'frontend', 'store' => $invoice->getStoreId()]);
    }

    /**
     * @param Invoice $invoice
     * @return array
     * @throws Exception
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function getVariables(Invoice $invoice)
    {
        $order = $invoice->getOrder();
        $paymentTitle = $order->getPayment()->getMethodInstance()->getTitle();
        $comments = '';
        foreach ($invoice->getComments() as $comment) {
            $comments .= $comment->getComment() . "\n";
        }

        return [
            'order' => $order,
            'order_id' => $order->getEntityId(),
            'invoice' => $invoice,
            'comment' => $comments,
            'billing' => $order->getBillingAddress(),
            'payment_html' => $this->getPaymentHtml($order),
            'paymentInfo' => $paymentTitle,
            'paymentAmountInvoiced' => str_replace('€', '', $order->formatPrice($order->getTotalInvoiced())) . '€',
            'discountAmount' => $order->formatPrice($order->getDiscountAmount()),
            'store' => $order->getStore(),
            'formattedOrderCreatedAt' => $this->formatDate($order->getData('created_at')),
            'formattedInvoiceCreatedAt' => $this->formatDate($invoice->getData('created_at')),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order)
        ];
    }

    /**
     * @param Order $order
     * @return string
     * @throws Exception
     */
    public function getPaymentHtml(Order $order)
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $this->identityContainer->getStore()->getStoreId()
        );
    }

    /**
     * @param null $datetime
     * @param int $format
     * @param bool $showTime
     * @param null $timezone
     * @param string $pattern
     * @return mixed
     * @throws Exception
     * @noinspection PhpComposerExtensionStubsInspection
     */
    public function formatDate(
        $datetime = null,
        $format = IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null,
        $pattern = 'd/M/yyyy'
    ) {
        $date = $datetime instanceof DateTimeInterface ? $datetime :
            new DateTime($datetime ? $datetime : 'now');

        return $this->timezone->formatDateTime(
            $date,
            $format,
            $showTime ? $format : IntlDateFormatter::NONE,
            null,
            $timezone,
            $pattern
        );
    }

    /**
     * @param Order $order
     * @return null
     */
    public function getFormattedShippingAddress(Order $order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * @param Order $order
     * @return null|string
     */
    public function getFormattedBillingAddress(Order $order)
    {
        /** @var Address $billing */
        $billing = $order->getBillingAddress();
        return $this->addressRenderer->format($billing, 'html');
    }

    /**
     * @param array $parts
     * @return string
     * @throws MpdfException
     */
    public function mPDFSettings(array $parts)
    {
        $oldErrorReporting = error_reporting();
        error_reporting(0);

        $pdf = new \Mpdf\Mpdf($this->config());

        $pdf->SetHTMLHeader($parts['header']);
        $pdf->SetHTMLFooter($parts['footer']);

        //@codingStandardsIgnoreLine
        $pdf->WriteHTML('<body>' . html_entity_decode($parts['body']) . '</body>');
        $pdfToOutput = $pdf->Output('', 'S');

        error_reporting($oldErrorReporting);

        return $pdfToOutput;
    }

    /**
     * @return array
     * @throws FileSystemException
     */
    private function config()
    {
        $finalOri = 'P';
        $marginTop = 15;
        $marginBottom = 15;
        $form = 'A4-' . $finalOri;

        return [
            'mode' => '',
            'format' => $form,
            'default_font_size' => '',
            'default_font' => '',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => $marginTop,
            'margin_bottom' => $marginBottom,
            'margin_header' => 0,
            'margin_footer' => 0,
            'tempDir' => $this->directoryList->getPath('tmp')
        ];
    }
}
