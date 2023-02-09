<?php

namespace FortyFour\AgeRestriction\Model;

use FortyFour\AgeRestriction\Api\MinimumAgeServiceInterface;
use FortyFour\AgeRestriction\Helper\Config as AgeRestrictionConfig;
use FortyFour\AgeRestriction\Helper\Cookie as AgeRestrictionCookieHelper;
use FortyFour\AgeRestriction\Model\App\Action\ContextPlugin;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;

class MinimumAgeService implements MinimumAgeServiceInterface
{
    /**
     * @var AgeRestrictionConfig
     */
    private $ageRestrictionConfig;
    /**
     * @var TimezoneInterface
     */
    private $timezone;
    /**
     * @var AgeRestrictionCookieHelper
     */
    private $ageRestrictionCookieHelper;
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * MinimumAgeService constructor.
     * @param AgeRestrictionConfig $ageRestrictionConfig
     * @param TimezoneInterface $timezone
     * @param AgeRestrictionCookieHelper $ageRestrictionCookieHelper
     * @param UrlInterface $url
     */
    public function __construct(
        AgeRestrictionConfig $ageRestrictionConfig,
        TimezoneInterface $timezone,
        AgeRestrictionCookieHelper $ageRestrictionCookieHelper,
        UrlInterface $url
    ) {
        $this->ageRestrictionConfig = $ageRestrictionConfig;
        $this->timezone = $timezone;
        $this->ageRestrictionCookieHelper = $ageRestrictionCookieHelper;
        $this->url = $url;
    }

    /**
     * @param string $date
     * @param string $successfulRedirectUrl
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function validate(string $date, string $successfulRedirectUrl): string
    {
        $minimumAge = $this->ageRestrictionConfig->getMinimumAgeForEntry();
        $cookieLifetime = $this->ageRestrictionConfig->getCookieLifetime();

        $date = str_replace('/', '-', $date);
        $dtForValidation = $this->timezone->date($date, null, true, false);
        $yearsDifference = $this->getDifferenceInYears($dtForValidation);

        if ($yearsDifference > $minimumAge) {
            $this->ageRestrictionCookieHelper->setCookie(
                AgeRestrictionCookieHelper::AGE_RESTRICTION_VALID,
                AgeRestrictionCookieHelper::AGE_RESTRICTION_COOKIE_NAME,
                $cookieLifetime
            );
            $response = $successfulRedirectUrl;
        } else {
            $this->ageRestrictionCookieHelper->setCookie(
                AgeRestrictionCookieHelper::AGE_RESTRICTION_INVALID,
                AgeRestrictionCookieHelper::AGE_RESTRICTION_COOKIE_NAME,
                $cookieLifetime
            );
            $response = $this->url->getUrl('agerestriction/invalid');
        }

        $this->ageRestrictionCookieHelper->deleteMagentoVaryCookie();
        return $response;
    }

    /**
     * @param \DateTime $dateTime
     * @return int
     */
    private function getDifferenceInYears(\DateTime $dateTime)
    {
        $dtNow = $this->timezone->date();
        return $dtNow->diff($dateTime)->y;
    }
}
