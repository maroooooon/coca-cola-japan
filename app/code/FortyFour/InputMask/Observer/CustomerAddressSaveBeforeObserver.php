<?php

namespace FortyFour\InputMask\Observer;

use FortyFour\InputMask\Helper\Config;
use FortyFour\InputMask\Model\Source\PostcodeMaskValidation;
use FortyFour\InputMask\Model\Source\TelephoneMaskValidation;
use Magento\Customer\Model\Address;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;

class CustomerAddressSaveBeforeObserver implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * CustomerAddressSaveAfterObserver constructor.
     * @param LoggerInterface $logger
     * @param Config $config
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config,
        ManagerInterface $messageManager
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return CustomerAddressSaveBeforeObserver
     * @throws CouldNotSaveException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $customerAddress Address */
        $customerAddress = $observer->getCustomerAddress();

        if (($inputMask = $this->getPostcodeInputMaskFromMap($this->config->getPostcodeInputMask()))
            && isset($customerAddress['postcode'])) {

            if (!preg_match_all(__('/%1/', $inputMask), $customerAddress['postcode'])) {
                $this->messageManager->addErrorMessage(__('Please enter a valid postcode (NNN-NNNN).'));
                throw new CouldNotSaveException(__('Please enter a valid postcode (NNN-NNNN).'));
            }
        }

        if (($inputMask = $this->getTelephoneInputMaskFromMap($this->config->getTelephoneInputMask()))
            && isset($customerAddress['telephone'])) {

            if (!preg_match_all(__('/%1/', $inputMask), $customerAddress['telephone'])) {
                $this->messageManager->addErrorMessage(__('Please enter a valid telephone number.'));
                throw new CouldNotSaveException(__('Please enter a valid telephone number.'));
            }
        }

        return $this;
    }

    /**
     * @param $postcodeInputMask
     * @return string|null
     */
    private function getPostcodeInputMaskFromMap($postcodeInputMask): ?string
    {
        return PostcodeMaskValidation::$inputMaskMap[$postcodeInputMask] ?? null;
    }

    /**
     * @param $telephoneInputMask
     * @return string|null
     */
    private function getTelephoneInputMaskFromMap($telephoneInputMask): ?string
    {
        return TelephoneMaskValidation::$inputMaskMap[$telephoneInputMask] ?? null;
    }
}
