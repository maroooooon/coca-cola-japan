<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;

class AddShippingRates implements DataPatchInterface
{
	public const EU_SHIPPING_CONFIGS_XML = [
		[
			'scope' => 'websites',
			'path' => 'carriers/tablerate/condition_name',
			'value' => 'package_weight'
		],
		[
			'scope' => 'websites',
			'path' => 'carriers/tablerate/include_virtual_price',
			'value' => 0
		],
		[
			'scope' => 'websites',
			'path' => 'carriers/tablerate/sallowspecific',
			'value' => 1
		],
		[
			'scope' => 'websites',
			'path' => 'carriers/tablerate/specificcountry',
			'value' => 'BE,FI,FR,DE,IE,LU,NL'
		]
	];
	
	public const UK_SHIPPING_CONFIGS_XML = [
		[
			'scope' => 'websites',
			'path' => 'carriers/tablerate/condition_name',
			'value' => 'package_weight'
		],
		[
			'scope' => 'websites',
			'path' => 'carriers/tablerate/include_virtual_price',
			'value' => 0
		],
		[
			'scope' => 'websites',
			'path' => 'carriers/tablerate/sallowspecific',
			'value' => 1
		],
		[
			'scope' => 'websites',
			'path' => 'carriers/tablerate/specificcountry',
			'value' => 'GB'
		]
	];
	
	/**
	 * @var WriterInterface
	 */
	private $configWriter;
	
	/**
	 * @var WebsiteRepositoryInterface
	 */
	private $websiteRepository;
	
	/**
	 * @param WriterInterface $configWriter
	 * @param WebsiteRepositoryInterface $websiteRepository
	 */
	public function __construct(
		WriterInterface $configWriter,
		WebsiteRepositoryInterface $websiteRepository
	) {
		$this->configWriter = $configWriter;
		$this->websiteRepository = $websiteRepository;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public static function getDependencies()
	{
		return [];
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getAliases()
	{
		return [];
	}
	
	/**
	 * It loops through the array of shipping configs and saves them to the Europe website
	 */
	public function apply()
	{
		$europeWebsite = $this->getEuropeWebsite();
		foreach (self::EU_SHIPPING_CONFIGS_XML as $config) {
			$this->configWriter->save(
				$config['path'],
				$config['value'],
				\Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
				$europeWebsite->getId()
			);
		}
		
		$ukWebsite = $this->getUkWebsite();
		foreach (self::UK_SHIPPING_CONFIGS_XML as $config) {
			$this->configWriter->save(
				$config['path'],
				$config['value'],
				\Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
				$ukWebsite->getId()
			);
		}
	}
	
	/**
	 * Get the website object for the Coke Europe website.
	 *
	 * @return WebsiteInterface The website object for the coke_eu website.
	 */
	private function getEuropeWebsite(): WebsiteInterface
	{
		return $this->websiteRepository->get('coke_eu');
	}
	
	/**
	 * Get the website object for the Coke Europe website.
	 *
	 * @return WebsiteInterface The website object for the coke_eu website.
	 */
	private function getUKWebsite(): WebsiteInterface
	{
		return $this->websiteRepository->get('coke_uk');
	}
}
