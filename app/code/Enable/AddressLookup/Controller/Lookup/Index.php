<?php

namespace Enable\AddressLookup\Controller\Lookup;

use Enable\AddressLookup\Api\LookupInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Index
 * @package Enable\AddressLookup\Controller\Isvalid\Index
 */
class Index extends Action
{
    const ADMIN_RESOURCE = 'Enable_AddressLookup::config';

    private JsonFactory $resultJsonFactory;
    private LookupInterface $lookup;

    public function __construct(
        Context $context,
        LookupInterface $lookup,
        JsonFactory $resultJsonFactory
    )
    {
        parent::__construct($context);
        $this->lookup = $lookup;
        $this->resultJsonFactory = $resultJsonFactory;
    }


    /**
     * It takes the address from the request, passes it to the lookup service, and returns the result as a JSON object
     *
     * @return Json A JSON object with the result of the lookup.
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();

        $address[] = $this->getRequest()->getParam('data');
        $result = $this->lookup->lookup(json_encode($address));

        return $resultJson->setJsonData($result);
    }
}
