<?php

namespace Coke\ContactAgeRestrict\Helper;

use Coke\ContactAgeRestrict\Helper\Config as ContactAgeRestrictHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data
{

    const COOKIE_NAME = 'agerestrict';

    /**
     * @var Config
     */
    private $contactAgeRestrictConfig;
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
     * @var Config
     */
    private $config;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Data constructor.
     * @param Config $contactAgeRestirctConfig
     */
    public function __construct(
        ContactAgeRestrictHelper $contactAgeRestirctConfig,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        Config $config,
        StoreManagerInterface $storeManager
    ) {
        $this->contactAgeRestrictConfig = $contactAgeRestirctConfig;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * Get error message for failed minimum age validation
     *
     * @return string
     */
    public function getMinimumAgeErrorMessage()
    {
        $tocMessage = '';
        if ($tocLink = $this->config->getTocLink($this->storeManager->getStore()->getId())) {
            $tocMessage = " " . __("Please see our %1 for any questions.", $tocLink);
        }
        if ($this->config->getMinimumAge()) {
            return __("Sorry, we cannot accept registration under %1. We have not saved any of your personal information.", $this->config->getMinimumAge()) . $tocMessage;
        } else {
            return __("Sorry, we cannot accept your registration. We have not saved any of your personal information.") . $tocMessage;
        }
    }

    /**
     * @param $dob
     *
     * @return int
     */
    public function calculateAge($dob)
    {
        return (int)($dob->diff(new \DateTime('today'))->y);
    }

    /**
     * @param $dob
     * @param bool $sendCookie
     * @return bool
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function validateAge($dob, bool $sendCookie = true)
    {

        if ($sendCookie) {
            if ($this->cookieManager->getCookie(self::COOKIE_NAME)) {
                throw new \Exception(
                    $this->getMinimumAgeErrorMessage()
                );
            }
        }

        $minAge = $this->contactAgeRestrictConfig->getMinimumAge();

        if (!$minAge) {
            return true;
        }

        if ($this->calculateAge($dob) < $minAge) {
            if ($sendCookie) {
                $metadata = $this->cookieMetadataFactory
                    ->createPublicCookieMetadata()
                    ->setDuration(1800)
                    ->setPath($this->sessionManager->getCookiePath())
                    ->setDomain($this->sessionManager->getCookieDomain());

                $this->cookieManager->setPublicCookie(
                    self::COOKIE_NAME,
                    1,
                    $metadata
                );
            }
            throw new \Exception(
                $this->getMinimumAgeErrorMessage()
            );
        }

        return true;
    }
}
