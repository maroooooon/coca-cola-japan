<?php

namespace CokeEurope\Catalog\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class FiltersViewModel implements ArgumentInterface
{
	/* A constant array that is used to map the filter name to a label. */
	const FILTER_ATTRIBUTE_LABELS = [
		'brand_swatch' => 'Beverage',
		'package_bev_type' => 'Package',
		'prefilled_message' => 'Moments',
		'pattern' => 'Pattern'
	];
	
	/**
	 * It returns the label of the filter, or the filter name if the label is not set
	 * @param $filter //The filter object
	 * @return string The filter title.
	 */
	public function getFilterTitle($filter): string
	{
		if (isset(self::FILTER_ATTRIBUTE_LABELS[$filter->getRequestVar()])) {
			return self::FILTER_ATTRIBUTE_LABELS[$filter->getRequestVar()];
		}
		
		return $filter->getName();
	}
}
