<?php
namespace Enable\AddressLookup\Model;

use Enable\AddressLookup\Api\AutocompleteInterface;
use Enable\AddressLookup\Helper\Config;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\UrlInterface;

class Autocomplete implements AutocompleteInterface
{
    const ENABLE_API_ENDPOINT = "https://ws.postcoder.com/pcw/autocomplete/";
    const ENABLE_API_REQUEST_FIND = "find?query=%s&country=%s&apikey=%s";
    const ENABLE_API_REQUEST_RETRIEVE = "retrieve/?id=%s&query=%s&country=%s&apikey=%s";

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
    public function getSuggestions($data): string
    {
        if (!$this->helper->isEnabled() | !$data | $data == "[]") {
            return json_encode(__('No results to show'));
        }

        $findResult = $this->findResult($data);
        if ($errorMessage = $this->helper->hasErrorMessage($this->curl->getStatus(), $findResult, 'Autocomplete Find')){
            return json_encode($errorMessage);
        }

        /* This is sorting the results by the count column. */
        $id = $this->getFirstResultId($findResult);

        $retrieveResult = $this->retrieveResult($id, $data);
        if ($errorMessage = $this->helper->hasErrorMessage($this->curl->getStatus(), $retrieveResult, 'Autocomplete Retrieve')){
            return json_encode($errorMessage);
        }

        return json_encode($this->helper->formatResponse($retrieveResult));
    }


    /**
     * It takes a string as a parameter, adds a header to the curl object, makes a get request to the endpoint, and returns
     * the body of the response as an array
     *
     * @param string $parameters The parameters to be passed to the API.
     * @return array An array of the response body.
     */
    public function getCallResult(string $parameters): array
    {
        $this->curl->addHeader('ContentType', 'application/json');
        //The referer does not need to include query params
        $this->curl->setOption(CURLOPT_REFERER, $this->url->getBaseUrl());
        $this->curl->get(self::ENABLE_API_ENDPOINT . $parameters);

        return json_decode($this->curl->getBody());
    }

    /**
     * It takes a string as an argument, and returns an array of results
     *
     * @param string $data The data to be searched.
     * @return array An array of results.
     */
    public function findResult(string $data): array
    {
        /* Calling the Autocomplete Find API and getting the results. */
        $parameters = sprintf(
            self::ENABLE_API_REQUEST_FIND,
            urlencode($data),
            $this->helper->getCountryCode(),
            $this->helper->getApiKey()
        );

        return $this->getCallResult($parameters);
    }

    /**
     * It takes an ID and a data string, and returns an array of results
     *
     * @param string $id The ID of the request you want to retrieve.
     * @param string $data The data you want to retrieve.
     * @return array An array of data.
     */
    public function retrieveResult(string $id, string $data): array
    {
        $parameters = sprintf(
            self::ENABLE_API_REQUEST_RETRIEVE,
            urlencode($id),
            urlencode($data),
            $this->helper->getCountryCode(),
            $this->helper->getApiKey()
        );

        return $this->getCallResult($parameters);
    }

    /**
     * It takes an array of objects, sorts them by the count property, and returns the id of the first object in the array
     *
     * @param array $findResult The result of the find() method.
     * @return string The first result id
     */
    public function getFirstResultId(array $findResult): string
    {
        array_multisort(
            array_column($findResult, 'count'),
            SORT_ASC,
            $findResult
        );

        return $findResult[0]->id;
    }
}
