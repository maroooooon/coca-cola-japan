<?php

namespace Coke\Whitelist\Model;

use Coke\Whitelist\Model\ResourceModel\WhitelistType\CollectionFactory;
use Magento\Backend\Model\Session;

class WhitelistTypeDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $_loadedData = [];
    /**
     * @var Session
     */
    private $backendSession;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $whitelistTypeCollectionFactory
     * @param Session $backendSession
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $whitelistTypeCollectionFactory,
        Session $backendSession,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $whitelistTypeCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->backendSession = $backendSession;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (empty($this->_loadedData)) {
            $items = $this->collection->getItems();

            /** @var Whitelist $item */
            foreach ($items as $item) {
                $this->_loadedData[$item->getId()] = $item->getData();
            }
        }

        return $this->_loadedData;
    }
}
