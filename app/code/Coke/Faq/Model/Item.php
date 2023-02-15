<?php
namespace Coke\Faq\Model;

class Item 
    extends \Magento\Framework\Model\AbstractModel 
    implements \Coke\Faq\Api\ItemInterface, 
               \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'faq_item_element';
 
    protected function _construct()
    {
        $this->_init('Coke\Faq\Model\ResourceModel\Item');
    }
 
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
    
    /**
     * Checks if size chart is active
     * 
     * @return bool
     */
    public function isActive()
    {
        $active = $this->getIsActive() == 1 ? true : false;
        
        return $active;
    }    
}