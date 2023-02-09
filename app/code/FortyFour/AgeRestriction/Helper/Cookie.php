<?php

namespace FortyFour\AgeRestriction\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

class Cookie extends AbstractHelper
{
    const AGE_RESTRICTION_COOKIE_NAME = 'age_registriction';
    const AGE_RESTRICTION_VALID = 'valid';
    const AGE_RESTRICTION_INVALID = 'invalid';
    const DEFAULT_COOKIE_LIFETIME = 86400;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;
    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;
    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * Cookie constructor.
     * @param Context $context
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param SessionManagerInterface $sessionManager
     */
    public function __construct(
        Context $context,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager
    ) {
        parent::__construct($context);
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @param null $name
     * @return string|null
     */
    public function getCookie($name)
    {
        return $this->cookieManager->getCookie($name);
    }

    /**
     * @param $value
     * @param $name
     * @param null $age
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function setCookie($value, $name, $age = null): void
    {
        $age = (!$age) ? self::DEFAULT_COOKIE_LIFETIME : $age;

        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain())
            ->setDuration($age);

        $this->cookieManager->setPublicCookie($name, $value, $metadata);
    }

    /**
     * @param $name
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function deleteCookie($name): void
    {
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain());

        $this->cookieManager->deleteCookie($name, $metadata);
    }

    /**
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     * @return void
     */
    public function deleteMagentoVaryCookie(): void
    {
        $cookieMetadata = $this->cookieMetadataFactory->createSensitiveCookieMetadata()->setPath('/');
        $this->cookieManager->deleteCookie(HttpResponse::COOKIE_VARY_STRING, $cookieMetadata);
        $this->deleteCookie(HttpResponse::COOKIE_VARY_STRING);

        $this->_logger->info(__("[Cookie] Deleted the %1 cookie.", HttpResponse::COOKIE_VARY_STRING));
    }
}
