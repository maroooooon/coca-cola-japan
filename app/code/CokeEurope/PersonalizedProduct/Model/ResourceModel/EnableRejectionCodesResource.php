<?php

namespace CokeEurope\PersonalizedProduct\Model\ResourceModel;

use CokeEurope\PersonalizedProduct\Api\Data\EnableRejectionCodesInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class EnableRejectionCodesResource extends AbstractDb
{
	/**
	 * @var string
	 */
	protected $_eventPrefix = 'enable_rejection_codes_resource_model';
	
	/**
	 * Initialize resource model.
	 */
	protected function _construct()
	{
		$this->_init('enable_rejection_codes', EnableRejectionCodesInterface::ID);
		$this->_useIsObjectNew = true;
	}
}
