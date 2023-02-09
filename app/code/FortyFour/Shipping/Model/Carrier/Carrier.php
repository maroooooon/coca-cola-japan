<?php

namespace FortyFour\Shipping\Model\Carrier;

use Exception;
use FortyFour\Shipping\Helper\ExpressStandard\Config as ExpressStandardConfig;
use Magento\Backend\Model\Locale\Resolver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Shipping\Model\Tracking\Result\StatusFactory;
use Psr\Log\LoggerInterface;

abstract class Carrier extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var MethodFactory
     */
    private $rateMethodFactory;
    /**
     * @var TimezoneInterface
     */
    private $timezone;
    /**
     * @var ExpressStandardConfig
     */
    protected $expressStandardConfig;
    /**
     * @var \Magento\Shipping\Model\Tracking\ResultFactory
     */
    protected $trackFactory;
    /**
     * @var StatusFactory
     */
    protected $trackStatusFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param TimezoneInterface $timezone
     * @param ExpressStandardConfig $expressStandardConfig
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param StatusFactory $trackStatusFactory
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        TimezoneInterface $timezone,
        ExpressStandardConfig $expressStandardConfig,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        StatusFactory $trackStatusFactory,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->timezone = $timezone;
        $this->expressStandardConfig = $expressStandardConfig;
        $this->trackFactory = $trackFactory;
        $this->trackStatusFactory = $trackStatusFactory;
    }

    /**
     * @return Result
     */
    protected function createShippingRateResult(): Result
    {
        /** @var Result $result */
        return $this->rateResultFactory->create();
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    protected function createShippingRateMethod(): \Magento\Quote\Model\Quote\Address\RateResult\Method
    {
        $method = $this->rateMethodFactory->create();
        $method->setCarrier($this->_code);
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('title'));
        $shippingCost = (float)$this->getConfigData('shipping_cost');
        $method->setPrice($shippingCost);
        $method->setCost($shippingCost);

        return $method;
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Error
     */
    protected function createShippingRateError(): \Magento\Quote\Model\Quote\Address\RateResult\Error
    {
        return $this->_rateErrorFactory->create()
            ->setCarrier($this->_code)
            ->setCarrierTitle($this->getConfigData('title'))
            ->setMethodTitle($this->getConfigData('title'))
            ->setErrorMessage($this->getConfigData('specificerrmsg'));
    }

    /**
     * @return bool
     */
    public function isTrackingAvailable()
    {
        return true;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @return \DateTime
     * @throws LocalizedException
     */
    protected function getCurrentDate(): \DateTime
    {
        try {
            $date = new \DateTime(
                'now',
                new \DateTimeZone($this->timezone->getConfigTimezone())
            );
        } catch (Exception $exception) {
            throw new LocalizedException(__($exception->getMessage()));
        }

        return $this->timezone->date($date);
    }

    /**
     * @return int|null
     * @throws LocalizedException
     */
    protected function getCurrentDateTimestamp(): ?int
    {
        if (!($date = $this->getCurrentDate()->format('Y-m-d H:i:s'))) {
            return null;
        }

        return strtotime($date);
    }

    /**
     * @return \DateTime|null
     * @throws Exception
     */
    protected function getCutOffTime(): ?\DateTime
    {
        if (!($cutOffTime = $this->getConfigData('cut_off_time'))) {
            return null;
        }

        $storeTimezone = new \DateTimeZone($this->timezone->getConfigTimezone());
        $cutOffDateTime = new \DateTime($cutOffTime, $storeTimezone);
        $cutOffTime = new \DateTime($cutOffDateTime->format('Y-m-d H:i:s'), $storeTimezone);

        return $cutOffTime;
    }

    /**
     * @return array
     */
    protected function getAllowedPostalCodes(): array
    {
        $allowedPostalCodes = explode(',', $this->getConfigData('allowed_postal_codes'));

        return $allowedPostalCodes ? $allowedPostalCodes : [];
    }

    /**
     * @param $month
     * @param $day
     * @return \Magento\Framework\Phrase
     */
    protected function getDeliveryDateString($month, $day)
    {
        return __('Estimated Delivery: %1 %2', $month, $day);
    }

    /**
     * Get tracking information
     *
     * @param string $tracking
     * @return string|false
     * @api
     */
    public function getTrackingInfo($tracking)
    {
        $result = $this->getTracking($tracking);

        if ($result instanceof \Magento\Shipping\Model\Tracking\Result) {
            $trackings = $result->getAllTrackings();
            if ($trackings) {
                return $trackings[0];
            }
        } elseif (is_string($result) && !empty($result)) {
            return $result;
        }

        return false;
    }

    /**
     * @param int $trackingNumber
     * @param string $url
     * @return \Magento\Shipping\Model\Tracking\Result
     */
    public function getTracking(int $trackingNumber)
    {
        $result = $this->trackFactory->create();
        $status = $this->trackStatusFactory->create();
        $status->setCarrier($this->_code);
        $status->setCarrierTitle($this->getConfigData('title'));
        $status->setTracking($trackingNumber);
        $status->setUrl($this->getConfigData('tracking_url'));
        $result->append($status);

        return $result;
    }
}
