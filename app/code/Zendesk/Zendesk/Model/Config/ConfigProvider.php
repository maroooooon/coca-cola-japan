<?php

namespace Zendesk\Zendesk\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
class ConfigProvider
{
    const XML_PATH_AGENT_DOMAIN = 'zendesk/general/domain';
    const XML_PATH_AGENT_EMAIL = 'zendesk/general/email';
    const XML_PATH_AGENT_PASSWORD = 'zendesk/general/password';

    const XML_PATH_EVENT_ORDER_SHIPPED = 'sunshine/events/order_shipped';
    const XML_PATH_EVENT_CART_ADD_ITEMS = 'sunshine/events/cart_add_items';
    const XML_PATH_EVENT_CART_REMOVE_ITEMS = 'sunshine/events/cart_remove_items';
    const XML_PATH_EVENT_REFUND_STATUS = 'sunshine/events/refund_status';
    const XML_PATH_EVENT_CHECKOUT_BEGIN = 'sunshine/events/checkout_begin';
    const XML_PATH_EVENT_CUSTOMER_CREATE_UPDATE = 'sunshine/events/customer_create_update';
    const XML_PATH_EVENT_CUSTOMER_DELETE = 'sunshine/events/customer_delete';
    const XML_PATH_EVENT_ORDER_CREATE_UPDATE = 'sunshine/events/order_placed_updated';
    const XML_PATH_EVENT_ORDER_CANCEL = 'sunshine/events/order_cancel';
    const XML_PATH_EVENT_ORDER_PAID = 'sunshine/events/order_paid';


    const XML_PATH_CORS_ORIGIN_PATTERN = 'sunshine/general/cors_origin_pattern';
    const XML_PATH_DEBUG_ENABLED = 'sunshine/debug/enable_debug_logging';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * ConfigProvider constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $path
     * @param string $scopeType
     * @param null $scopeCode
     * @return mixed
     */
    public function getValue($path, $scopeType = ScopeInterface::SCOPE_WEBSITE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * @param $path
     * @param string $scopeType
     * @return bool
     */
    public function isSetFlag($path, $scopeType = ScopeInterface::SCOPE_WEBSITE)
    {
        return $this->scopeConfig->isSetFlag($path, $scopeType);
    }

    /**
     * Get regex pattern for valid CORS origins for Zendesk app.
     * Currently fixed value in config.xml, but could conceivably be updated in the future.
     *
     * @return string
     */
    public function getZendeskAppCorsOrigin()
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_CORS_ORIGIN_PATTERN);
    }
}
