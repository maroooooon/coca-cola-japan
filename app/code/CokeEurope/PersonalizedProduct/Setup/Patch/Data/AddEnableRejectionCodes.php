<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CokeEurope\PersonalizedProduct\Setup\Patch\Data;

use CokeEurope\PersonalizedProduct\Model\EnableRejectionCodesModelFactory;
use CokeEurope\PersonalizedProduct\Model\ResourceModel\EnableRejectionCodesResource;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class AddEnableRejectionCodes implements DataPatchInterface, PatchRevertableInterface
{
	public const REJECTION_CODES = [
		[
			'code' => 101,
			'short_description' => "Cannot ship with provided details.",
			'long_description' => "The provided shipping details are invalid. Please, verify your email address and phone number format or try re-entering your shipping address into two lines.",
		],
		[
			'code' => 102,
			'short_description' => "Invalid customisation.",
			'long_description' => "Please, enter a shorter personalized message or contact us here"
		],
		[
			'code' => 103,
			'short_description' => "Invalid consumer details.",
			'long_description' => "Currently, we could not ship outside of the country."
		],
		[
			'code' => 104,
			'short_description' => "Failed to pass consumer entered text moderation.",
			'long_description' => "The personalized \"Message\" or \"Name\" contains content that doesn’t comply with our moderation rules e.g. brand name, profanity, invalid character."
		]
	];

	private EnableRejectionCodesModelFactory $enableRejectionCodes;
	private EnableRejectionCodesResource $enableRejectionCodesResource;

	/**
	 * @var ModuleDataSetupInterface
	 */
	private $moduleDataSetup;

	/**
	 * @param ModuleDataSetupInterface $moduleDataSetup
	 * @param EnableRejectionCodesModelFactory $enableRejectionCodes
	 * @param EnableRejectionCodesResource $enableRejectionCodesResource
	 */
	public function __construct(
		ModuleDataSetupInterface         $moduleDataSetup,
		EnableRejectionCodesModelFactory $enableRejectionCodes,
		EnableRejectionCodesResource $enableRejectionCodesResource
	) {
		$this->moduleDataSetup = $moduleDataSetup;
		$this->enableRejectionCodes = $enableRejectionCodes;
		$this->enableRejectionCodesResource = $enableRejectionCodesResource;
	}


	/**
	 * It inserts the rejection codes into the database
	 */
	public function apply(): void
	{
		$this->moduleDataSetup->getConnection()->startSetup();
		$rejectionTable = $this->moduleDataSetup->getTable('enable_rejection_codes');

		foreach (self::REJECTION_CODES as $code) {
			$this->moduleDataSetup->getConnection()->insert($rejectionTable, $code);
		}

		$this->moduleDataSetup->getConnection()->endSetup();
	}

	/**
	 * It truncates the table `enable_rejection_codes`
	 */
	public function revert(): void
	{
		$this->moduleDataSetup->getConnection()->startSetup();
		$rejectionTable = $this->moduleDataSetup->getTable('enable_rejection_codes');
		$this->moduleDataSetup->getConnection()->truncateTable($rejectionTable);
		$this->moduleDataSetup->getConnection()->endSetup();
	}

	/**
	 * @inheritdoc
	 */
	public static function getDependencies()
	{
		return [];
	}

	/**
	 * @inheritdoc
	 */
	public function getAliases()
	{
		return [];
	}
}
