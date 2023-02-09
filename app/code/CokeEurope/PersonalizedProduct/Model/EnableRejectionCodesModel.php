<?php

namespace CokeEurope\PersonalizedProduct\Model;

use CokeEurope\PersonalizedProduct\Model\ResourceModel\EnableRejectionCodesResource;
use Magento\Framework\Model\AbstractModel;

class EnableRejectionCodesModel extends AbstractModel
{
	/**
	 * @var string
	 */
	protected $_eventPrefix = 'enable_rejection_codes_model';
	
	/**
	 * Initialize magento model.
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init(EnableRejectionCodesResource::class);
	}
}
