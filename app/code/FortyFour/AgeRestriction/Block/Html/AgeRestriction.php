<?php

namespace FortyFour\AgeRestriction\Block\Html;

use FortyFour\AgeRestriction\Helper\AgeRestrictionContext;
use FortyFour\AgeRestriction\Helper\Config as AgeRestrictionConfig;
use FortyFour\AgeRestriction\Helper\Cookie;
use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Header\Logo;

class AgeRestriction extends Template
{
    /**
     * @var Logo
     */
    private $logo;
    /**
     * @var AgeRestrictionContext
     */
    private $ageRestrictionContext;
    /**
     * @var AgeRestrictionConfig
     */
    private $ageRestrictionConfig;

    /**
     * Popup constructor.
     * @param Template\Context $context
     * @param Logo $logo
     * @param AgeRestrictionContext $ageRestrictionContext
     * @param AgeRestrictionConfig $ageRestrictionConfig
     */
    public function __construct(
        Template\Context $context,
        Logo $logo,
        AgeRestrictionContext $ageRestrictionContext,
        AgeRestrictionConfig $ageRestrictionConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->logo = $logo;
        $this->ageRestrictionContext = $ageRestrictionContext;
        $this->ageRestrictionConfig = $ageRestrictionConfig;
    }

    /**
     * @return string
     */
    public function getLogoSrc()
    {
        return $this->logo->getLogoSrc();
    }

    /**
     * @return string
     */
    public function getLogoAlt()
    {
        return $this->logo->getLogoAlt();
    }

    /**
     * @return mixed|null
     */
    public function getAgeRestrictionCookie()
    {
        return $this->ageRestrictionContext->getAgeRestrictionCookie();
    }

    /**
     * @return int|null
     */
    public function isMinimumAgeNotMet(): ?int
    {
        if (!$this->getAgeRestrictionCookie()) {
            return null;
        }

        return ($this->getAgeRestrictionCookie() === Cookie::AGE_RESTRICTION_INVALID) ? 1 : null;
    }

    /**
     * @return mixed
     */
    public function getRedirectUrlText()
    {
        return $this->ageRestrictionConfig->getRedirectUrlText();
    }

    /**
     * @return mixed
     */
    public function getRedirectUrl()
    {
        return $this->ageRestrictionConfig->getRedirectUrl();
    }

    /**
     * @return false|string
     */
    public function getSuccessRedirect()
    {
        $currentUrl = $this->_urlBuilder->getCurrentUrl();
        $baseUrl = $this->_urlBuilder->getUrl();
        $url = $baseUrl . 'agerestriction?referrer=';

        $encodedSuccessRedirect =  str_replace(
            $url,
            '',
            $currentUrl
        );

        return base64_decode($encodedSuccessRedirect);
    }
}
