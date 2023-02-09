<?php

namespace FortyFour\InputMask\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_INPUT_MASK_POSTCODE = 'checkout/input_mask/postcode';
    const XML_PATH_INPUT_MASK_TELEPHONE = 'checkout/input_mask/telephone';
    const XML_PATH_MAX_LENGTH_STREET_LINES = 'checkout/max_length/street_lines';

    /**
     * @param null $store
     * @return mixed
     */
    public function getPostcodeInputMask($store = null)
    {
        $mask = $this->scopeConfig->getValue(
            self::XML_PATH_INPUT_MASK_POSTCODE,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $mask ? $mask : null;
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getTelephoneInputMask($store = null)
    {
        $mask = $this->scopeConfig->getValue(
            self::XML_PATH_INPUT_MASK_TELEPHONE,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $mask ? $mask : null;
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getMaxLengthStreetLines($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MAX_LENGTH_STREET_LINES,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
