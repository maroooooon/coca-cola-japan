<?php

namespace CokeEurope\PersonalizedProduct\Model\ResourceModel\EnableRejectionCodesModel;

use CokeEurope\PersonalizedProduct\Model\EnableRejectionCodesModel;
use CokeEurope\PersonalizedProduct\Model\ResourceModel\EnableRejectionCodesResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class EnableRejectionCodesCollection extends AbstractCollection
{
	/**
	 * @var string
	 */
	protected $_eventPrefix = 'enable_rejection_codes_collection';
	
	/**
	 * Initialize collection model.
	 */
	protected function _construct()
	{
		$this->_init(EnableRejectionCodesModel::class, EnableRejectionCodesResource::class);
	}
}
