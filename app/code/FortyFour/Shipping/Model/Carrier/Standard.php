<?php

namespace FortyFour\Shipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;

class Standard extends Carrier
{
    const DATE_INTERVAL_ONE_DAY = 'P1D';

    /**
     * @var string
     */
    protected $_code = 'standard';

    /**
     * @param RateRequest $request
     * @return bool|\Magento\Framework\DataObject|\Magento\Shipping\Model\Rate\Result|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        if (!($postalCode = $request->getDestPostcode())) {
            return false;
        }

        $result = $this->createShippingRateResult();
        $method = $this->createShippingRateMethod();

        if (!in_array($request->getDestPostcode(), $this->getAllowedPostalCodes())) {
            $error = $this->createShippingRateError();
            return $result->append($error);
        }

        $deliveryDate = $this->getAvailableDeliveryDate();
        $method->setCarrierTitle(
            $this->getDeliveryDateString(
                __($deliveryDate->format('F')),
                $deliveryDate->format('d')
            )
        );
        return $result->append($method);
    }

    /**
     * @return \DateTime
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAvailableDeliveryDate()
    {
        $unavailableDays = $this->expressStandardConfig->getUnavailableDays();
        $unavailableDates = $this->expressStandardConfig->getUnavailableDates();
        $currentDate = $this->getCurrentDate();
        $deliveryDate = null;
        $dayPadding = 1;

        if ($currentDate->getTimestamp() > $this->getCutOffTime()->getTimestamp()) {
            $currentDate = $this->incrementDateByOneDay($currentDate);
        }

        while ($dayPadding > 0) {
            if (!$this->isDateAnUnavailableDate($currentDate, $unavailableDays, $unavailableDates)) {
                $dayPadding--;
            }
            $currentDate = $this->incrementDateByOneDay($currentDate);
        }

        return $currentDate;
    }

    /**
     * @param \DateTime $currentDate
     * @return \DateTime
     */
    private function incrementDateByOneDay(\DateTime $currentDate): \DateTime
    {
        $oneDayInterval = new \DateInterval(self::DATE_INTERVAL_ONE_DAY);
        $currentDate = $currentDate->add($oneDayInterval);

        return $currentDate;
    }

    /**
     * @param \DateTime $currentDate
     * @param array $unavailableDays
     * @param array $unavailableDates
     * @return bool
     */
    private function isDateAnUnavailableDate(
        \DateTime $currentDate,
        array $unavailableDays,
        array $unavailableDates): bool
    {
        return in_array($currentDate->format('w'), $unavailableDays)
            || in_array($currentDate->format('d-m'), $unavailableDates);
    }
}
