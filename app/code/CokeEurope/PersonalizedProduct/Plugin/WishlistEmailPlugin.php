<?php
/**
 * WishlistEmailPlugin
 *
 * @copyright Copyright © 2022 Bounteous. All rights reserved.
 * @author    tanya.lamontagne@bounteous.com
 */

namespace CokeEurope\PersonalizedProduct\Plugin;

use Magento\Wishlist\Helper\Data;

class WishlistEmailPlugin
{
	/**
	 * @param Data $subject
	 * @param string $result
	 * @param $item
	 * @param $additional
	 * @return string
	 */
	public function afterGetProductUrl(Data $subject, string $result, $item, $additional = []): string
	{
		$parsed = parse_url($result);

		if ($item->getBuyRequest()->getOptions()) {
			foreach ($item->getBuyRequest()->getOptions() as $key => $option) {
				$parsed['query'] .=  sprintf('&options-%s=%s',$key,$option);
			}
			
			return sprintf('%s://%s%s?%s#%s',
				$parsed['scheme'],
				$parsed['host'],
				$parsed['path'],
				$parsed['query'],
				$parsed['fragment']
			);
		}
		
		return $result;
	}
}
