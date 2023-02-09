<?php

namespace Bounteous\CountryTranslation\Plugin\Sales\Model\Order\Email\Container\Template;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\TranslateInterface;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\Template;

class TranslateCountryName
{
    /** @var TranslateInterface  */
    protected $translate;

    /** @var ScopeConfigInterface  */
    protected $scopeConfig;

    /** @var ResolverInterface  */
    protected $localeResolver;

    /** @var Renderer  */
    protected $addressRenderer;

    public function __construct(
        TranslateInterface $translate,
        ScopeConfigInterface $scopeConfig,
        ResolverInterface $localeResolver,
        Renderer $addressRenderer
    ) {
        $this->translate = $translate;
        $this->scopeConfig = $scopeConfig;
        $this->localeResolver = $localeResolver;
        $this->addressRenderer = $addressRenderer;
    }

    /**
     * Re-run formatted address with the correct locale + shipping address
     *
     * @param Template $subject
     * @param array $vars
     * @return array[]
     */
    public function beforeSetTemplateVars(Template $subject, array $vars)
    {
        if (!isset($vars['order']) || !isset($vars['formattedShippingAddress']) || !isset($vars['formattedBillingAddress'])) {
            return [$vars];
        }

        $order = $vars['order'];

        $storeId       = $order->getStoreId();
        $newLocaleCode = $this->scopeConfig->getValue(
            $this->localeResolver->getDefaultLocalePath(),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $this->localeResolver->setLocale($newLocaleCode);
        $this->translate->setLocale($newLocaleCode);

        $vars['formattedShippingAddress'] = $this->addressRenderer->format($order->getShippingAddress(), 'html');
        $vars['formattedBillingAddress']  = $this->addressRenderer->format($order->getBillingAddress(), 'html');

        return [$vars];
    }
}
