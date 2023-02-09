<?php

namespace Coke\Sarp2\ViewModel;

use Aheadworks\Sarp2\Api\Data\ProfileInterface;
use Coke\Sarp2\Model\Profile\View\Action\Permission as CokeSarp2ActionPermission;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class NextPaymentDate implements ArgumentInterface
{
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var TimezoneInterface
     */
    private $localeDate;
    /**
     * @var CokeSarp2ActionPermission
     */
    private $cokeSarp2ActionPermission;
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @param Registry $registry
     * @param TimezoneInterface $localeDate
     * @param CokeSarp2ActionPermission $cokeSarp2ActionPermission
     * @param UrlInterface $url
     */
    public function __construct(
        Registry $registry,
        TimezoneInterface $localeDate,
        CokeSarp2ActionPermission $cokeSarp2ActionPermission,
        UrlInterface $url
    ) {
        $this->registry = $registry;
        $this->localeDate = $localeDate;
        $this->cokeSarp2ActionPermission = $cokeSarp2ActionPermission;
        $this->url = $url;
    }

    /**
     * @param ProfileInterface $profile
     * @param string $nextPaymentDate
     * @return string
     * @throws \Exception
     */
    public function getFuturePaymentDate(ProfileInterface $profile, string $nextPaymentDate): string
    {
        $dateIncrement = $this->getDateIncrement($profile);
        $futurePaymentDate = new \DateTime($nextPaymentDate, new \DateTimeZone($this->localeDate->getConfigTimezone()));
        $futurePaymentDate->modify($dateIncrement);
        return $futurePaymentDate->format('Y/m/d');
    }

    /**
     * @return CokeSarp2ActionPermission
     */
    public function getCokeSarp2ActionPermission(): CokeSarp2ActionPermission
    {
        return $this->cokeSarp2ActionPermission;
    }

    /**
     * @param int $profileId
     * @return string
     */
    public function getSkipPaymentDateUrl(int $profileId): string
    {
        return $this->url->getUrl(
            'aw_sarp2/profile_edit/skipNextPaymentDate',
            ['profile_id' => $profileId]
        );
    }

    /**
     * @param int $profileId
     * @return string
     */
    public function getSaveSkipPaymentDateUrl(int $profileId): string
    {
        return $this->url->getUrl(
            'aw_sarp2/profile_edit/saveSkipNextPaymentDate',
            ['profile_id' => $profileId]
        );
    }

    /**
     * @return ProfileInterface
     */
    public function getProfile(): ProfileInterface
    {
        return $this->registry->registry('profile');
    }

    /**
     * @param ProfileInterface $profile
     * @return string
     */
    private function getDateIncrement(ProfileInterface $profile): string
    {
        return sprintf(
            '%s %s',
            $profile->getPlanDefinition()->getBillingFrequency(),
            $profile->getPlanDefinition()->getBillingPeriod()
        );
    }
}
