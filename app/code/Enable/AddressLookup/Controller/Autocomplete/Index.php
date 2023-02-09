<?php

namespace Enable\AddressLookup\Controller\Autocomplete;

use Enable\AddressLookup\Api\AutocompleteInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Index
 * @package Enable\AddressLookup\Controller\Autocomplete\Index
 */
class Index extends Action
{
    const ADMIN_RESOURCE = 'Enable_AddressLookup::config';

    private JsonFactory $resultJsonFactory;
    protected AutocompleteInterface $autocomplete;

    public function __construct(
        Context $context,
        AutocompleteInterface $autocomplete,
        JsonFactory $resultJsonFactory
    )
    {
        parent::__construct($context);
        $this->autocomplete = $autocomplete;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * It takes the address parameter from the request, passes it to the autocomplete class, and returns the result as a
     * JSON object
     *
     * @return Json A JSON object with the results of the autocomplete search.
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();

        $address = $this->getRequest()->getParam('data');
        $result = $this->autocomplete->getSuggestions($address);

        return $resultJson->setJsonData($result);
    }
}
