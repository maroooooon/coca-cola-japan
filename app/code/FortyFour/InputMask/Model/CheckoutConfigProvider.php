<?php

namespace FortyFour\InputMask\Model;

use FortyFour\InputMask\Helper\Config;
use FortyFour\InputMask\Model\Source\PostcodeMaskValidation;
use FortyFour\InputMask\Model\Source\TelephoneMaskValidation;
use Magento\Checkout\Model\ConfigProviderInterface;
use Psr\Log\LoggerInterface;

class CheckoutConfigProvider implements ConfigProviderInterface
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
     * CheckoutConfigProvider constructor.
     * @param LoggerInterface $logger
     * @param Config $config
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config
    ) {
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @return array|\string[][]
     */
    public function getConfig()
    {
        try {
            return [
                'input_mask_config' => [
                    'telephone' => $this->getTelephoneInputMaskFromMap($this->config->getTelephoneInputMask()),
                    'postcode' => $this->getPostcodeInputMaskFromMap($this->config->getPostcodeInputMask())
                ]
            ];
        } catch (\Exception $e) {
            $this->logger->debug(
                __(
                    '[\FortyFour\InputMask\Model\CheckoutConfigProvider::getConfig] %1',
                    $e->getMessage()
                )
            );

            return [];
        }
    }

    /**
     * @param $postcodeInputMask
     * @return string|null
     */
    private function getPostcodeInputMaskFromMap($postcodeInputMask)
    {
        return isset(PostcodeMaskValidation::$inputMaskMap[$postcodeInputMask])
            ? PostcodeMaskValidation::$inputMaskMap[$postcodeInputMask]
            : null;
    }

    /**
     * @param $telephoneInputMask
     * @return string|null
     */
    private function getTelephoneInputMaskFromMap($telephoneInputMask)
    {
        return isset(TelephoneMaskValidation::$inputMaskMap[$telephoneInputMask])
            ? TelephoneMaskValidation::$inputMaskMap[$telephoneInputMask]
            : null;
    }
}
