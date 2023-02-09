<?php

namespace FortyFour\Tax\Setup\Patch\Data;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Tax\Api\TaxRateRepositoryInterface;
use Magento\Tax\Model\Calculation\Rate\Converter;

class AddAlcoholTaxRateForGreece implements DataPatchInterface
{
    const GREECE_ALCOHOL_TAX_CODE = '24% VAT Greece - Alcohol';

    /**
     * @var Converter
     */
    private $taxRateConverter;
    /**
     * @var TaxRateRepositoryInterface
     */
    private $taxRateRepository;

    /**
     * AddAlcoholTaxRateForGreece constructor.
     * @param Converter $taxRateConverter
     * @param TaxRateRepositoryInterface $taxRateRepository
     */
    public function __construct(
        Converter $taxRateConverter,
        TaxRateRepositoryInterface $taxRateRepository
    ) {
        $this->taxRateConverter = $taxRateConverter;
        $this->taxRateRepository = $taxRateRepository;
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return $this|AddAlcoholTaxRateForGreece
     * @throws InputException
     */
    public function apply()
    {
        $data = [
            'code' => self::GREECE_ALCOHOL_TAX_CODE,
            'tax_postcode' => '*',
            'tax_country_id' => 'GR',
            'rate' => '24.0000'
        ];
        $taxData = $this->taxRateConverter->populateTaxRateData($data);
        $this->taxRateRepository->save($taxData);

        return $this;
    }
}
