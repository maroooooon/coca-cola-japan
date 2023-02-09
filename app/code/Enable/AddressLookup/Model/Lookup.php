<?php
namespace Enable\AddressLookup\Model;

use Enable\AddressLookup\Api\LookupInterface;
use Magento\Framework\HTTP\Client\Curl;
use Enable\AddressLookUp\Helper\Config;
use Magento\Framework\UrlInterface;

class Lookup implements LookupInterface
{
    const ENABLE_API_ENDPOINT = "https://ws.postcoder.com/pcw/";
    const ENABLE_API_REQUEST = "%s/address/%s/%s?format=json&lines=2&include=posttown,postcode";

    private Curl $curl;
    private Config $helper;

    protected UrlInterface $url;

    public function __construct(Curl $curl, Config $helper, UrlInterface $url)
    {
        $this->curl = $curl;
        $this->helper = $helper;
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function lookup($data): string
    {
        if (!$this->helper->isEnabled() || $data == "[]"){
            return __("No results to show");
        }

        $search = current(json_decode($data));
        $lookupResult = $this->getCallResponse($search);

        if ($errorMessage = $this->helper->hasErrorMessage($this->curl->getStatus(), $lookupResult, 'Lookup')){
            return json_encode($errorMessage);
        }

        /* Checking if the street, city and postcode of the data matches the first response from the API */
        $isValid = $this->validate($search, $lookupResult);
        $result = [
            'isValid' => $isValid,
            'suggestions' => $isValid ? null : $this->helper->formatResponse($lookupResult)
        ];

        return json_encode($result);
    }

    /**
     * It takes an array of search terms, formats them into a URL, and then calls the API
     *
     * @param string $search The search term you want to use.
     * @return array The response from the API call.
     */
    public function getCallResponse(string $search): array
    {
        $parameters = sprintf(self::ENABLE_API_REQUEST,
            $this->helper->getApiKey(), $this->helper->getCountryCode(), urlencode($search)
        );

        return $this->callApi($parameters);
    }

    /**
     * It takes a string, sends it to the API, and returns the first three results
     * @param string $parameters The text to be translated.
     *
     * @return array An array of the first 3 elements of the decoded JSON response.
     */
    public function callApi(string $parameters): array
    {
        $this->curl->addHeader('ContentType', 'application/json');
        $this->curl->setOption(CURLOPT_REFERER, $this->url->getBaseUrl());
        $this->curl->get(self::ENABLE_API_ENDPOINT . $parameters);

        return array_slice(json_decode($this->curl->getBody()), 0, 3);
    }

    /**
     * It checks if the street, city and postcode of the data matches the first response from the API
     *
     * @param string $data The data that you want to validate.
     * @param array $callResponse This is the response from the API call.
     *
     * @return bool a boolean value.
     */
    public function validate(string $data, array $callResponse): bool
    {
		$isAddressValid = true; // Assume Address is correct
		$formAddress = json_decode($data);
		$firstResponse = $callResponse[0];

        // Depending on the existence of organisation / building name / sub building name, our validation will differ
        $organisation = property_exists($firstResponse, 'organisation') ? $firstResponse->organisation : '';
        $buildingName = property_exists($firstResponse, 'buildingname') ? $firstResponse->buildingname : '';
        $subBuildingName = property_exists($firstResponse, 'subbuildingname') ? $firstResponse->subbuildingname : '';

        $hasAdditionalInfo = (!empty($organisation) || !empty($buildingName) || !empty($subBuildingName));

        // Check to make sure we have two lines if we have building / organisation / flat information
        if ($hasAdditionalInfo && (!isset($formAddress->{'street[0]'}) || !isset($formAddress->{'street[1]'}))) {
            return false;
        }

        $streetLine = $hasAdditionalInfo ? $formAddress->{'street[1]'} : $formAddress->{'street[0]'};

        // Check street[0] for the existence of these 3.
        if ($hasAdditionalInfo) {
            if (!empty($organisation) && strpos(strtolower($formAddress->{'street[0]'}), strtolower($organisation)) === false) {
                $isAddressValid = false;
            }
            if (!empty($buildingName) && strpos(strtolower($formAddress->{'street[0]'}), strtolower($buildingName)) === false) {
                $isAddressValid = false;
            }
            if (!empty($subBuildingName) && strpos(strtolower($formAddress->{'street[0]'}), strtolower($subBuildingName)) === false) {
                $isAddressValid = false;
            }
        }

		// Check Street
        if (strpos(strtolower($streetLine), strtolower($firstResponse->street)) === false) {
            $isAddressValid = false;
        }

        // Check City
        if (!isset($formAddress->city) || strtolower($formAddress->city) != strtolower($firstResponse->posttown)){
			$isAddressValid = false;
        }

        if (!isset($formAddress->postcode) || strtolower($formAddress->postcode) != strtolower($firstResponse->postcode)){
			$isAddressValid = false;
        }

        return $isAddressValid;
    }
}
