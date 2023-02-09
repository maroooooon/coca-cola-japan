<?php /** @noinspection PhpUnhandledExceptionInspection */

/**
 * @category Bounteous
 * @copyright Copyright (c) 2021 Bounteous LLC
 */

declare(strict_types=1);

namespace FortyFour\Email\Setup\Patch\Data;

use FortyFour\Config\Model\Config;
use FortyFour\Email\Model\ContentUpgrader;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use \Magento\Email\Model\Template as MagentoEmailTemplate;
use Magento\Store\Model\ScopeInterface;

/**
 * Class AddCokeFranceEmails
 */
class AddCokeFranceEmails implements DataPatchInterface
{
    /**
     * @var ContentUpgrader
     */
    private $contentUpgrader;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var int|null
     */
    private $websiteId = null;

    /**
     * AddCokeFranceEmails constructor.
     * @param ContentUpgrader $contentUpgrader
     * @param Filesystem $filesystem
     * @param WriterInterface $configWriter
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ContentUpgrader $contentUpgrader,
        Filesystem $filesystem,
        WriterInterface $configWriter,
        ResourceConnection $resourceConnection
    )
    {
        $this->contentUpgrader = $contentUpgrader;
        $this->filesystem = $filesystem;
        $this->configWriter = $configWriter;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return AddCokeFranceEmails
     * @throws FileSystemException|LocalizedException
     */
    public function apply(): AddCokeFranceEmails
    {
        // Top and bottom blocks
        $this->addDesignEmailHeaderTemplate();
        $this->addDesignEmailFooterTemplate();

        // Customer
        $this->addCustomerCreateAccountEmailTemplate();
        $this->addCustomerAccountInformationChangeEmailAndPasswordTemplate();
        $this->addCustomerPasswordForgotEmailTemplate();

        // Sales - Checkout
        $this->addCheckoutPaymentFailedTemplate(); // no applied config

        // Sales - Order
        $this->addSalesEmailOrderTemplate();
        $this->addSalesEmailOrderGuestTemplate();

        // Sales - Invoice
        $this->addSalesEmailInvoiceTemplate();

        // Sales - Credit Memo
        $this->addSalesEmailCreditMemoTemplate();
        $this->addSalesEmailCreditMemoGuestTemplate();

        // Sales - Shipment
        $this->addSalesEmailShipmentTemplate();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function addSalesEmailShipmentTemplate(): void
    {
        $data = [
            'template_code' => 'France - Sales - Shipment',
            'template_text' => $this->contentUpgrader->readFile('email', 'coke_fr_sales_email_shipment.html'),
            'template_type' => MagentoEmailTemplate::TYPE_HTML,
            'template_subject' => 'Confirmation d\'expédition',
            'orig_template_code' => 'sales_email_shipment_template',
            'orig_template_variables' => '{"var formattedBillingAddress|raw":"Billing Address","var this.getUrl($store, \'customer/account/\')":"Customer Account URL","var order.getCustomerName()":"Customer Name","var order.increment_id":"Order Id","var payment_html|raw":"Payment Details","var comment":"Shipment Comment","var shipment.increment_id":"Shipment Id","layout handle=\"sales_email_order_shipment_items\" shipment=$shipment order=$order":"Shipment Items Grid","block class=\'Magento\\\\Framework\\\\View\\\\Element\\\\Template\' area=\'frontend\' template=\'Magento_Sales::email\/shipment\/track.phtml\' shipment=$shipment order=$order":"Shipment Track Details","var formattedShippingAddress|raw":"Shipping Address","var order.shipping_description":"Shipping Description","var order.getShippingDescription()":"Shipping Description"}'
        ];

        $templateId = $this->addTemplate('email_template', $data);
        if ($templateId) {
            $this->applyConfiguration('sales_email/shipment/guest_template', $templateId);
            $this->applyConfiguration('sales_email/shipment/template', $templateId);
        }
    }

    /**
     * @return void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function addSalesEmailCreditMemoGuestTemplate(): void
    {
        $data = [
            'template_code' => 'France - Sales - Credit Memo for Guest',
            'template_text' => $this->contentUpgrader->readFile('email', 'coke_fr_sales_email_creditmemo_guest.html'),
            'template_type' => MagentoEmailTemplate::TYPE_HTML,
            'template_subject' => '{{trans "Credit memo for your %store_name order" store_name=$store.getFrontendName()}}',
            'orig_template_code' => 'sales_email_creditmemo_guest_template',
            'orig_template_variables' => '{"var formattedBillingAddress|raw":"Billing Address","var comment":"Credit Memo Comment","var creditmemo.increment_id":"Credit Memo Id","layout handle=\"sales_email_order_creditmemo_items\" creditmemo=$creditmemo order=$order":"Credit Memo Items Grid","var billing.getName()":"Guest Customer Name (Billing)","var order.increment_id":"Order Id","var payment_html|raw":"Payment Details","var formattedShippingAddress|raw":"Shipping Address","var order.getShippingDescription()":"Shipping Description","var order.shipping_description":"Shipping Description"}'
        ];

        $templateId = $this->addTemplate('email_template', $data);
        if ($templateId) {
            $this->applyConfiguration('sales_email/creditmemo/guest_template', $templateId);
        }
    }

    /**
     * @return void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function addSalesEmailCreditMemoTemplate(): void
    {
        $data = [
            'template_code' => 'France - Sales - Credit Memo',
            'template_text' => $this->contentUpgrader->readFile('email', 'coke_fr_sales_email_creditmemo.html'),
            'template_type' => MagentoEmailTemplate::TYPE_HTML,
            'template_subject' => '{{trans "Credit memo for your %store_name order" store_name=$store.getFrontendName()}}',
            'orig_template_code' => 'sales_email_creditmemo_template',
            'orig_template_variables' => '{"var formattedBillingAddress|raw":"Billing Address","var comment":"Credit Memo Comment","var creditmemo.increment_id":"Credit Memo Id","layout handle=\"sales_email_order_creditmemo_items\" creditmemo=$creditmemo order=$order":"Credit Memo Items Grid","var this.getUrl($store, \'customer/account/\')":"Customer Account URL","var order.getCustomerName()":"Customer Name","var order.increment_id":"Order Id","var payment_html|raw":"Payment Details","var formattedShippingAddress|raw":"Shipping Address","var order.getShippingDescription()":"Shipping Description","var order.shipping_description":"Shipping Description"}'
        ];

        $templateId = $this->addTemplate('email_template', $data);
        if ($templateId) {
            $this->applyConfiguration('sales_email/creditmemo/template', $templateId);
        }
    }

    /**
     * @return void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function addCustomerCreateAccountEmailTemplate(): void
    {
        $data = [
            'template_code' => 'France - Customer - Create Account',
            'template_text' => $this->contentUpgrader->readFile('email', 'coke_fr_customer_create_account_email.html'),
            'template_type' => MagentoEmailTemplate::TYPE_HTML,
            'template_subject' => '{{trans "Welcome to %store_name" store_name=$store.getFrontendName()}}',
            'orig_template_code' => 'customer_create_account_email_template',
            'orig_template_variables' => '{"var this.getUrl($store, \'customer/account/\')":"Customer Account URL","var customer.email":"Customer Email","var customer.name":"Customer Name"}'
        ];

        $templateId = $this->addTemplate('email_template', $data);
        if ($templateId) {
            $this->applyConfiguration('customer/create_account/email_template', $templateId);
        }
    }

    /**
     * @return void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function addCustomerPasswordForgotEmailTemplate(): void
    {
        $data = [
            'template_code' => 'France - Customer - Forgot Password',
            'template_text' => $this->contentUpgrader->readFile('email', 'coke_fr_customer_password_forgot_email.html'),
            'template_type' => MagentoEmailTemplate::TYPE_HTML,
            'template_subject' => 'Votre mot de passe COCA-COLA STORE a été changé',
            'orig_template_code' => 'customer_password_forgot_email_template',
            'orig_template_variables' => '{"var customer.name":"Customer Name","var this.getUrl($store, \'customer/account/createPassword/\', [_query:[id:$customer.id, token:$customer.rp_token]])":"Reset Password URL"}'
        ];

        $templateId = $this->addTemplate('email_template', $data);
        if ($templateId) {
            $this->applyConfiguration('customer/password/forgot_email_template', $templateId);
        }
    }

    /**
     * @return void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function addCustomerAccountInformationChangeEmailAndPasswordTemplate(): void
    {
        $data = [
            'template_code' => 'France - Customer - Reset Password',
            'template_text' => $this->contentUpgrader->readFile('email', 'coke_fr_customer_account_information_change_email_and_password.html'),
            'template_type' => MagentoEmailTemplate::TYPE_HTML,
            'template_subject' => 'Votre mot de passe COCA-COLA STORE a été changé',
            'orig_template_code' => 'customer_account_information_change_email_and_password_template',
            'orig_template_variables' => null
        ];

        $templateId = $this->addTemplate('email_template', $data);
        if ($templateId) {
            $this->applyConfiguration('customer/password/reset_password_template', $templateId);
        }
    }

    /**
     * @return void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function addSalesEmailInvoiceTemplate(): void
    {
        $data = [
            'template_code' => 'France - Sales - Invoice',
            'template_text' => $this->contentUpgrader->readFile('email', 'coke_fr_sales_email_invoice.html'),
            'template_type' => MagentoEmailTemplate::TYPE_HTML,
            'template_subject' => 'Facture de votre commande sur COCA-COLA STORE',
            'orig_template_code' => 'sales_email_invoice_template',
            'orig_template_variables' => '{"var formattedBillingAddress|raw":"Billing Address","var this.getUrl($store, \'customer/account/\')\":\"Customer Account URL","var order.getCustomerName()":"Customer Name","var comment":"Invoice Comment","var invoice.increment_id":"Invoice Id","layout area=\"frontend\" handle=\"sales_email_order_invoice_items\" invoice=$invoice order=$order":"Invoice Items Grid","var order.increment_id":"Order Id","var payment_html|raw":"Payment Details","var formattedShippingAddress|raw":"Shipping Address","var order.shipping_description":"Shipping Description","var order.getShippingDescription()":"Shipping Description"}'
        ];

        $templateId = $this->addTemplate('email_template', $data);
        if ($templateId) {
            $this->applyConfiguration('sales_email/invoice/guest_template', $templateId);
            $this->applyConfiguration('sales_email/invoice/template', $templateId);
        }
    }

    /**
     * @return void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function addSalesEmailOrderGuestTemplate(): void
    {
        $data = [
            'template_code' => 'France - Sales - New Order for Guest',
            'template_text' => $this->contentUpgrader->readFile('email', 'coke_fr_sales_email_order_guest.html'),
            'template_type' => MagentoEmailTemplate::TYPE_HTML,
            'template_subject' => 'Votre commande sur COCA-COLA STORE est validée',
            'orig_template_code' => null,
            'orig_template_variables' => null
        ];

        $templateId = $this->addTemplate('email_template', $data);
        if ($templateId) {
            $this->applyConfiguration('sales_email/order/guest_template', $templateId);
        }
    }

    /**
     * @return void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function addSalesEmailOrderTemplate(): void
    {
        $data = [
            'template_code' => 'France - Sales - New Order',
            'template_text' => $this->contentUpgrader->readFile('email', 'coke_fr_sales_email_order.html'),
            'template_type' => MagentoEmailTemplate::TYPE_HTML,
            'template_subject' => 'Votre commande sur COCA-COLA STORE est validée',
            'orig_template_code' => 'sales_email_order_template',
            'orig_template_variables' => '{"var formattedBillingAddress|raw":"Billing Address","var order.getEmailCustomerNote()":"Email Order Note","var order.increment_id":"Order Id","layout handle=\"sales_email_order_items\" order=$order area=\"frontend\"":"Order Items Grid","var payment_html|raw":"Payment Details","var formattedShippingAddress|raw":"Shipping Address","var order.getShippingDescription()":"Shipping Description","var shipping_msg":"Shipping message"}'
        ];

        $templateId = $this->addTemplate('email_template', $data);
        if ($templateId) {
            $this->applyConfiguration('sales_email/order/template', $templateId);
        }
    }

    /**
     * @return void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function addCheckoutPaymentFailedTemplate(): void
    {
        $data = [
            'template_code' => 'France - Checkout - Payment Failed',
            'template_text' => $this->contentUpgrader->readFile('email', 'coke_fr_checkout_payment_failed.html'),
            'template_type' => MagentoEmailTemplate::TYPE_HTML,
            'template_subject' => '{{trans "Payment Transaction Failed Reminder"}}',
            'template_styles' => '.totals-tax-summary {display:none !important} .totals-tax-summary {display:none !important; visibility:hidden !important; color:#f5f5f5 !important; height: 0px !important}',
            'orig_template_code' => 'checkout_payment_failed_template',
            'orig_template_variables' => '{"var billingAddress.format(\'html\')|raw":"Billing Address","var checkoutType":"Checkout Type","var customerEmail":"Customer Email","var customer":"Customer Name","var dateAndTime":"Date and Time of Transaction","var paymentMethod":"Payment Method","var shippingAddress.format(\'html\')|raw":"Shipping Address","var shippingMethod":"Shipping Method","var items|raw":"Shopping Cart Items","var total":"Total","var reason":"Transaction Failed Reason"}'
        ];

        $this->addTemplate('email_template', $data);
    }
    /**
     * @return void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function addDesignEmailHeaderTemplate(): void
    {
        $data = [
            'template_code' => 'France - Design - Header',
            'template_text' => $this->contentUpgrader->readFile('email', 'coke_fr_design_email_header.html'),
            'template_type' => MagentoEmailTemplate::TYPE_HTML,
            'template_subject' => '{{trans "Header"}}',
            'orig_template_code' => 'design_email_header_template',
            'orig_template_variables' => '{"var logo_height":"Email Logo Image Height","var logo_width":"Email Logo Image Width","var template_styles|raw":"Template CSS"}'
        ];

        $templateId = $this->addTemplate('email_template', $data);
        if ($templateId) {
            $this->applyConfiguration('design/email/header_template', $templateId);
        }
    }

    /**
     * @return void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function addDesignEmailFooterTemplate(): void
    {
        $templateCode = 'France - Design - Footer';
        $fileContent = 'coke_fr_design_email_footer.html';
        $origTemplateCode = 'design_email_footer_template';

        $data = [
            'template_code' => $templateCode,
            'template_text' => $this->contentUpgrader->readFile('email', $fileContent),
            'template_type' => MagentoEmailTemplate::TYPE_HTML,
            'template_subject' => '{{trans "Footer"}}',
            'orig_template_code' => $origTemplateCode,
            'orig_template_variables' => '{"var store.getFrontendName()":"Store Name"}'
        ];


        $templateId = $this->addTemplate('email_template', $data);
        if ($templateId) {
            $this->applyConfiguration('design/email/footer_template', $templateId);
        }
    }

    /**
     * @param string $templateName
     * @param array $data
     * @return int
     */
    private function addTemplate(string $templateName, array $data): int
    {
        $connection = $this->resourceConnection->getConnection();

        $connection->insert(
            $connection->getTableName($templateName),
            $data
        );

        return (int)$connection->lastInsertId();
    }

    /**
     * @param string $path
     * @param mixed $value
     * @throws LocalizedException
     */
    private function applyConfiguration(string $path, $value): void
    {
        if ($this->websiteId === null) {
            $this->websiteId = $this->getWebsiteIdByCode(Config::FRANCE_STORE_CODE);
        }

        if (!$this->websiteId) {
            throw new LocalizedException(
                __('Invalid website ID. Value can not be saved to the `config_data_core` table.')
            );
        }

        $this->configWriter->save(
            $path,
            $value,
            ScopeInterface::SCOPE_WEBSITES,
            $this->websiteId
        );
    }

    /**
     * @param string $code
     * @return string
     */
    private function getWebsiteIdByCode(string $code): string
    {
        $connection = $this->resourceConnection->getConnection();

        $select = $connection->select()->from(
            $connection->getTableName('store_website'),
            'website_id'
        )->where("code = ?", $code);

        return $connection->fetchOne($select);
    }
}
