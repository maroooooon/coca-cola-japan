<?php

namespace Enable\AddressLookup\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_CONFIG_GENERAL_IS_ENABLED = 'enable/address/general/is_enabled';
    const XML_CONFIG_GENERAL_API_KEY = 'enable/address/general/api_key';
    const XML_CONFIG_GENERAL_IDENTIFIER = 'enable/address/general/identifier';
    const XML_CONFIG_GENERAL_STORE_COUNTRY_ID = 'enable/address/general/country_code';
    const XML_CONFIG_AUTOCOMPLETE_MAXIMUM_RESULTS = 'enable/address/general/maximum_results';

    /**
     * This function returns a boolean value based on the value of the `general/is_enabled` configuration path
     * @return bool A boolean value.
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_CONFIG_GENERAL_IS_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * This function returns the API key from the Magento configuration
     * @return string|null The API key from the configuration.
     */
    public function getApiKey(): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_CONFIG_GENERAL_API_KEY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * It returns the value of the XML path `general/identifier` from the `config.xml` file
     * @return string|null The value of the identifier field in the admin panel.
     */
    public function getIdentifier(): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_CONFIG_GENERAL_IDENTIFIER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * It returns the country code of the store
     * @return string|null The country code of the store.
     */
    public function getCountryCode(): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_CONFIG_GENERAL_STORE_COUNTRY_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * It returns the maximum number of results that should be displayed in the autocomplete dropdown
     * @return int The maximum number of results to be displayed in the autocomplete dropdown.
     */
    public function getMaximumResults(): int
    {
        return $this->scopeConfig->getValue(
            self::XML_CONFIG_AUTOCOMPLETE_MAXIMUM_RESULTS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * It takes the response from the API call, and formats it into an array that Magento can use to populate the address
     * fields
     * @param array $callResponse The response from the API call
     *
     * @return array An array of addresses.
     */
    public function formatResponse(array $callResponse): array
    {
        $formattedResults = [];
        $results = array_slice($callResponse, 0, (int) $this->getMaximumResults());
        foreach($results as $result){
            $street1 = null;
            $street2 = null;
            $number = $result->number ?? null;
            $street = $result->street ?? null;

            $organisation = property_exists($result, 'organisation') ? $result->organisation : '';
            $buildingName = property_exists($result, 'buildingname') ? $result->buildingname : '';
            $subBuildingName = property_exists($result, 'subbuildingname') ? $result->subbuildingname : '';

            // First line is always organisation, building, or sub building
            if (!empty($organisation) || !empty($buildingName) || !empty($subBuildingName)) {
                $street1 = trim(sprintf('%s %s %s', $subBuildingName, $buildingName, $organisation));
                $street2 = trim(sprintf('%s %s', $number, $street));
            } else {
                $street1 = trim(sprintf('%s %s', $number, $street));
            }

            $formattedResults[] = [
                'address' => $result->summaryline,
                'street[0]' => $street1,
                'street[1]' => $street2,
                'postcode' => $result->postcode,
                'region' => $result->county,
                'city' => $result->posttown
            ];
        }

        return $formattedResults;
    }


    /**
     * > This function checks if the API call was successful and if there are any results to show
     *
     * @param int $curlStatus the status code of the curl request
     * @param array $result the result of the API call
     * @param string $callType The type of call you're making. This is used to display a more specific error message.
     *
     * @return ?string The error message if there is one, otherwise null.
     */
    public function hasErrorMessage(int $curlStatus, array $result, string $callType): ?string
    {
        if ($curlStatus >= 400) {
            return __(
                sprintf("API %s Call Error: code %s",
                    $callType,
                    $curlStatus
                )
            );
        }

        if (count($result) == 0) {
            return __('No results to show');
        }

        return null;
    }
}
