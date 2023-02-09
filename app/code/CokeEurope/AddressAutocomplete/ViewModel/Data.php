<?php
/**
 * Copyright © bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\AddressAutocomplete\ViewModel;

use CokeEurope\AddressAutocomplete\Helper\Config as ConfigHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;

class Data implements ArgumentInterface
{

	const GOOGLE_GEOCODE_API_URI = 'https://maps.googleapis.com/maps/api/geocode/json?address=';
	const POSTCODE_PATTERNS = [
		'BE' => ['^\\d{4}$'],
		'FI' => ['^\\d{5}$'],
		'FR' => ['^\\d{5}$'],
		'DE' => [
			'^\\d{2}$',
			'^\\d{4}$',
			'^\\d{5}$'
		],
		'IE' => [
			'^[0-9a-zA-Z]{3} [0-9a-zA-Z]{4}$',
			'^[0-9a-zA-Z]{7}$'
		],
		'NL' => [
			'^\\d{4}\\s{0,1}[A-Za-z]{2}$',
			'^[0-9a-zA-Z]{6}$'
		],
		'GB' => [
			'^(([A-Z]{1,2}[0-9][A-Z0-9]?|ASCN|STHL|TDCU|BBND|[BFS]IQQ|PCRN|TKCA) ?[0-9][A-Z]{2}|BFPO ?[0-9]{1,4}|(KY[0-9]|MSR|VG|AI)[ -]?[0-9]{4}|[A-Z]{2} ?[0-9]{2}|GE ?CX|GIR ?0A{2}|SAN ?TA1)$',
			'^[A-Z]{1,2}[0-9][A-Z0-9]? ?[0-9][A-Z]{2}$'
		]
	];

	private ConfigHelper $configHelper;
	private SerializerInterface $serializer;
	private StoreManagerInterface $storeManager;
	private DirectoryHelper $directoryHelper;

	/**
	 * Autocomplete constructor.
	 * @param ConfigHelper $configHelper
	 * @param SerializerInterface $serializer
	 */
    public function __construct(
        ConfigHelper $configHelper,
		DirectoryHelper $directoryHelper,
		SerializerInterface $serializer,
		StoreManagerInterface $storeManager
    ) {
        $this->configHelper = $configHelper;
		$this->serializer = $serializer;
		$this->storeManager = $storeManager;
		$this->directoryHelper = $directoryHelper;
	}

    /**
     * Get the Google API Key from system config
     * @return string
     */
    public function getApiKey(): ?string
    {
        return $this->configHelper->getApiKey();
    }

    /**
     * Get the config for knockout components
     * @return string
     */
    public function getJsonConfig(): string
	{
		$storeId = $this->storeManager->getStore()->getId();
		$country = $this->directoryHelper->getDefaultCountry($storeId);

		return $this->serializer->serialize([
            'enabled' => $this->configHelper->isEnabled(),
			'country' => $country,
			'validate_address' => $this->configHelper->isValidateAddressEnabled(),
			'validate_postcode' => $this->configHelper->isValidatePostcodeEnabled(),
			'postcode_pattern' => self::POSTCODE_PATTERNS[$country],
			'api_key' => $this->configHelper->getApiKey(),
			'api_url' => self::GOOGLE_GEOCODE_API_URI,
        ]);
    }
}
