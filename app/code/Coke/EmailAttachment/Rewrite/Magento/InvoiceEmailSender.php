<?php /** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpDocSignatureInspection */

/**
 * @category FortyFour
 * @copyright Copyright (c) 2020 FortyFour LLC
 */

declare(strict_types=1);

namespace Coke\EmailAttachment\Rewrite\Magento;

use Coke\EmailAttachment\Model\PdfBuilder\Invoice as PdfBuilderInvoice;
use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Api\Data\InvoiceCommentCreationInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\InvoiceIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Invoice\Sender\EmailSender as MagentoInvoiceEmailSender;
use Magento\Sales\Model\Order\Email\SenderBuilderFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice as InvoiceResource;
use Psr\Log\LoggerInterface;

/**
 * Class InvoiceSender
 */
class InvoiceEmailSender extends MagentoInvoiceEmailSender
{
    /**
     * @var Template
     */
    protected $templateContainer;

    /**
     * @var PdfBuilderInvoice
     */
    protected $pdfBuilder;

    /**
     * InvoiceEmailSender constructor.
     * @param Template $templateContainer
     * @param InvoiceIdentity $identityContainer
     * @param SenderBuilderFactory $senderBuilderFactory
     * @param LoggerInterface $logger
     * @param Renderer $addressRenderer
     * @param PaymentHelper $paymentHelper
     * @param InvoiceResource $invoiceResource
     * @param ScopeConfigInterface $globalConfig
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        Template $templateContainer,
        InvoiceIdentity $identityContainer,
        SenderBuilderFactory $senderBuilderFactory,
        LoggerInterface $logger,
        Renderer $addressRenderer,
        PaymentHelper $paymentHelper,
        InvoiceResource $invoiceResource,
        ScopeConfigInterface $globalConfig,
        ManagerInterface $eventManager,
        PdfBuilderInvoice $pdfBuilder
    ) {
        $this->templateContainer = $templateContainer;
        $this->pdfBuilder = $pdfBuilder;
        parent::__construct(
            $templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer,
            $paymentHelper,
            $invoiceResource,
            $globalConfig,
            $eventManager
        );
    }

    /**
     * @param OrderInterface $order
     * @param InvoiceInterface $invoice
     * @param InvoiceCommentCreationInterface|null $comment
     * @param false $forceSyncMode
     * @return bool
     * @throws Exception
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function send(
        OrderInterface $order,
        InvoiceInterface $invoice,
        InvoiceCommentCreationInterface $comment = null,
        $forceSyncMode = false
    ): bool {
        $pdfContent = $this->pdfBuilder->build($invoice);
        if ($pdfContent) {
            $this->templateContainer->setPdfAttachment($pdfContent);
        }

        return parent::send($order, $invoice, $comment, $forceSyncMode);
    }
}
