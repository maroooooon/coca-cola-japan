<?php

namespace FortyFour\Shipping\Model\Carrier;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class Express extends Carrier
{
    /**
     * @var string
     */
    protected $_code = 'express';

    /**
     * @param RateRequest $request
     * @return Result|bool
     * @throws LocalizedException
     * @throws Exception
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        if (!($postalCode = $request->getDestPostcode())) {
            return false;
        }

        if ($this->isTodayAnUnavailableDeliveryDay()) {
            return false;
        }

        $currentDate = $this->getCurrentDate();
        if ($currentDate->getTimestamp() > $this->getCutOffTime()->getTimestamp()) {
            return false;
        }

        $result = $this->createShippingRateResult();
        $method = $this->createShippingRateMethod();

        if (!in_array($request->getDestPostcode(), $this->getAllowedPostalCodes())) {
            $error = $this->createShippingRateError();
            return $result->append($error);
        }

        $method->setCarrierTitle(
            $this->getDeliveryDateString(
                __($currentDate->format('F')),
                $currentDate->format('d')
            )
        );

        return $result->append($method);
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    private function isTodayAnUnavailableDeliveryDay()
    {
        $unavailableDays = $this->expressStandardConfig->getUnavailableDays();
        $unavailableDates = $this->expressStandardConfig->getUnavailableDates();

        if (!($date = $this->getCurrentDate())) {
            return true;
        }

        if (in_array($date->format('w'), $unavailableDays)) {
            return true;
        }

        if (in_array($date->format('d-m'), $unavailableDates)) {
            return true;
        }

        return false;
    }
}
