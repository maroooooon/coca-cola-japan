<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use Magento\Catalog\Api\Data\ProductSearchResultsInterface;
use Magento\Catalog\Model\Product;
use \Magento\Eav\Model\Config;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use CokeEurope\StoreModifications\Helper\Data;

class UpdatePersonalizedProductSugarTaxV3 implements DataPatchInterface
{
	private Data $helper;
	private Config $eavConfig;
	private ProductRepository $productRepository;
	private SearchCriteriaBuilder $_searchCriteria;
	private State $appState;

	public function __construct(
		Config    $eavConfig,
		Data                                   $helper,
		ProductRepository                      $productRepository,
		SearchCriteriaBuilder                  $searchCriteriaBuilder,
		State $appState
	) {
		$this->helper = $helper;
		$this->eavConfig = $eavConfig;
		$this->productRepository = $productRepository;
		$this->_searchCriteria = $searchCriteriaBuilder;
		$this->appState = $appState;
	}

	/**
	 * @return void
	 * @throws AlreadyExistsException
	 * @throws LocalizedException
	 * @throws NoSuchEntityException
	 */
	public function apply()
	{
		try {
			$this->appState->setAreaCode(Area::AREA_ADMINHTML);
		} catch (\Exception $e) {}

        $gbpStoreId = (int) $this->helper->getUkGreatBritainEnglishStore()->getId();
		$packageBevTypes = $this->getPackageTypes();

		$cokeOriginalProducts = $this->getCokeOriginalProducts();
		foreach ($cokeOriginalProducts->getItems() as $cokeOriginal) {
            /** @var Product $product */
            $product = $this->productRepository->get($cokeOriginal->getSku(), true, $gbpStoreId, true);

            if ($cokeOriginal->getCustomAttribute('package_bev_type')->getValue() == $packageBevTypes['Can']) {
                // set the sugar tax to 0.06
                $product->setSugarTax(0.000);
                $product->addAttributeUpdate('sugar_tax', 0.6000, $gbpStoreId);
                $product->save();
            } else {
                //set the sugar tax to 0.48
                $product->setSugarTax(0.0000);
                $product->addAttributeUpdate('sugar_tax', 0.4800, $gbpStoreId);
                $product->save();
            }
        }
	}

	/**
	 * @return array|string[]
	 */
	public static function getDependencies(): array
	{
		return [];
	}

	/**
	 * @return array|string[]
	 */
	public function getAliases(): array
	{
		return [];
	}

	/**
	 * It takes an array of arrays, and returns an array of the same values, but with the keys rearranged
	 * @param array The array to be rearranged.
	 */
	public function rearrangeArrayKeys($array): array
	{
		$newArray = [];
		foreach ($array as $arrayItem) {
			$newArray[$arrayItem['label']] = $arrayItem['value'];
		}

		return $newArray;
	}

	/**
	 * It gets all the options for the attribute `package_bev_type` and returns them as an array
	 * @return array An array of package types.
	 */
	private function getPackageTypes(): array
	{
		$packageBevTypes = $this->eavConfig->getAttribute(Product::ENTITY, 'package_bev_type')->getSource()->getAllOptions();
		array_shift($packageBevTypes);
		$packageBevTypes = $this->rearrangeArrayKeys($packageBevTypes);
		return $packageBevTypes;
	}

	/**
	 * > Get all products with a SKU that starts with "Coke-Original"
	 * @return ProductSearchResultsInterface A list of products that match the search criteria.
	 */
	public function getCokeOriginalProducts(): ProductSearchResultsInterface
	{
		$cokeOriginalProductSearch = $this->_searchCriteria
			->addFilter('sku', 'Coke-Original%', 'like')
			->create();
		return $this->productRepository->getList($cokeOriginalProductSearch);
	}
}
