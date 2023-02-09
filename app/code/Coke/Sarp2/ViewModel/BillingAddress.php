<?php

namespace Coke\Sarp2\ViewModel;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class BillingAddress implements ArgumentInterface
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @param UrlInterface $url
     */
    public function __construct(
        UrlInterface $url
    ) {
        $this->url = $url;
    }

    /**
     * Get shipping address edit url
     *
     * @param int $profileId
     * @return string
     */
    public function getBillingAddressEditUrl(int $profileId): string
    {
        return $this->url->getUrl(
            'aw_sarp2/profile_edit/billingAddress',
            ['profile_id' => $profileId]
        );
    }

    /**
     * @return string
     */
    public function getAddAddressUrl(): string
    {
        return $this->url->getUrl('customer/address/new/');
    }
}
