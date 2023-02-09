<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use CokeEurope\StoreModifications\Helper\Data;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use Magento\Directory\Model\ResourceModel\Currency;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Condition\Combine;
use Magento\SalesRule\Model\Rule\Condition\Address;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Store\Api\Data\WebsiteInterface;

class AddFreeShippingRuleEUUK implements DataPatchInterface
{
	private CollectionFactory $customerGroupColl;
	private RuleFactory $ruleFactory;
	private ModuleDataSetupInterface $moduleDataSetup;
	private State $appState;
	private Data $cokeEuHelper;
	private Currency $currency;
	
	/**
	 * @param CollectionFactory $customerGroupColl
	 * @param RuleFactory $ruleFactory
	 * @param ModuleDataSetupInterface $moduleDataSetup
	 * @param State $appState
	 * @param Data $cokeEuHelper
	 * @param Currency $currency
	 */
	public function __construct(
		CollectionFactory        $customerGroupColl,
		RuleFactory              $ruleFactory,
		ModuleDataSetupInterface $moduleDataSetup,
		State                    $appState,
		Data                     $cokeEuHelper,
		Currency $currency
	)
	{
		$this->customerGroupColl = $customerGroupColl;
		$this->ruleFactory = $ruleFactory;
		$this->moduleDataSetup = $moduleDataSetup;
		$this->appState = $appState;
		$this->cokeEuHelper = $cokeEuHelper;
		$this->currency = $currency;
	}
	
	/**
	 * It sets the area code to frontend, and then creates a rule
	 */
	public function apply(): void
	{
		try {
			$this->appState->setAreaCode(Area::AREA_FRONTEND);
		} catch (\Exception $e) {
			// Area code is already set.
		}
		
		/** Set up Europe Free Shipping Rule */
		$minimumPurchasePrice = 75.0;
		$this->createRule($this->cokeEuHelper->getEuropeWebsite(), 'CokeEU',$minimumPurchasePrice);
		
		/** Set up UK Free Shipping Rule */
		$conversionRate = $this->currency->getRate('EUR', 'GBP');
		$convertedMinimumPurchasePrice = $minimumPurchasePrice * $conversionRate;
		$this->createRule($this->cokeEuHelper->getUkWebsite(), 'CokeUK', $convertedMinimumPurchasePrice);
	}
	
	/**
	 * It creates a new sales rule for the website passed in, with a minimum purchase price of the amount passed in
	 *
	 * @param WebsiteInterface $website The website you want to create the rule for.
	 * @param float $minimumPurchasePrice The minimum purchase price for the free shipping rule to apply.
	 * @throws \Exception
	 */
	public function createRule(WebsiteInterface $website, $websiteName, float $minimumPurchasePrice): void
	{
		$this->moduleDataSetup->getConnection()->startSetup();
		$customerGroups = $this->customerGroupColl->create()->load()->toOptionArray();
		$salesRule = $this->ruleFactory->create();
		
		$salesRule->setData(
			[
				'name' => sprintf('FreeShipping%s', $websiteName),
				'description' =>sprintf( 'Free Shipping for %s Orders Over Admin Set Amount', $website->getName()),
				'is_active' => 1,
				'customer_group_ids' => array_keys($customerGroups),
				'conditions' => [],
				'coupon_type' => Rule::COUPON_TYPE_NO_COUPON,
				'simple_action' => Rule::BY_PERCENT_ACTION,
				'apply_to_shipping' => 1,
				'simple_free_shipping' => 1,
				'stop_rules_processing' => 0,
				'website_ids' => [$website->getId()]
			]
        );

		$salesRule->getConditions()->loadArray(
			[
				'type' => Combine::class,
				'attribute' => null,
				'operator' => null,
				'value' => '1',
				'is_value_processed' => null,
				'aggregator' => 'all',
				'conditions' => [
					[
						'type' => Address::class,
						'attribute' => 'base_subtotal_with_discount',
						'operator' => '>=',
						'value' => $minimumPurchasePrice,
						'is_value_processed' => false,
					]
				]
			]
		);
		
		$salesRule->save();
		$this->moduleDataSetup->getConnection()->endSetup();
	}
	
	public static function getDependencies(): array
	{
		return [
			\CokeEurope\StoreModifications\Setup\Patch\Data\RemoveJapanWebsiteMigrateOLNBToCokeEU::class
		];
	}
	
	public function getAliases(): array
	{
		return [];
	}
}