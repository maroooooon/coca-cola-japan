<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use CokeEurope\StoreModifications\Helper\Data;
use Magento\Eav\Api\Data\AttributeFrontendLabelInterfaceFactory;

class UpdateProductAttributesV1 implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;
    /**
     * @var AttributeFrontendLabelInterface
     */
    private $attributeFrontendLabel;

    /**
     * InstallAttributes constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param AttributeFrontendLabelInterface $attributeFrontendLabel
     * @param Data $helper
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        AttributeFrontendLabelInterfaceFactory $attributeFrontendLabel,
        Data $helper
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->helper = $helper;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->attributeFrontendLabel = $attributeFrontendLabel;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        $storeArr = $storeBrandLabels = $storePackageTypeLabels = [];
        $s1 = $this->helper->getEuropeIrelandEnglishStore()->getId();
        $s2 = $this->helper->getEuropeNetherlandsDutchStore()->getId();
        $s3 = $this->helper->getEuropeGermanyGermanStore()->getId();
        $s4 = $this->helper->getEuropeFranceFrenchStore()->getId();
        $s5 = $this->helper->getEuropeBelgiumFrenchStore()->getId();
        $s6 = $this->helper->getEuropeBelgiumDutchStore()->getId();
        $s7 = $this->helper->getUkIrelandEnglishStore()->getId();
        $s8 = $this->helper->getUkGreatBritainEnglishStore()->getId();

        //English
        $storeArrEng = [$s1, $s7, $s8];
        foreach ($storeArrEng as $storeId) {
            $bLabel = $this->attributeFrontendLabel->create()
                ->setStoreId($storeId)
                ->setLabel('Select beverage');
            array_push($storeBrandLabels, $bLabel);

            $pLabel = $this->attributeFrontendLabel->create()
                ->setStoreId($storeId)
                ->setLabel('Select package');
            array_push($storePackageTypeLabels, $pLabel);
        }

        //German
        $storeArrGer = [$s3];
        foreach ($storeArrGer as $storeId) {
            $bLabel = $this->attributeFrontendLabel->create()
                ->setStoreId($storeId)
                ->setLabel('Getränk auswählen');
            array_push($storeBrandLabels, $bLabel);

            $pLabel = $this->attributeFrontendLabel->create()
                ->setStoreId($storeId)
                ->setLabel('Verpackung auswählen');
            array_push($storePackageTypeLabels, $pLabel);
        }

        //French
        $storeArrFr = [$s4, $s5];
        foreach ($storeArrFr as $storeId) {
            $bLabel = $this->attributeFrontendLabel->create()
                ->setStoreId($storeId)
                ->setLabel('Sélectionnez la boisson');
            array_push($storeBrandLabels, $bLabel);

            $pLabel = $this->attributeFrontendLabel->create()
                ->setStoreId($storeId)
                ->setLabel('Sélectionnez le forfait');
            array_push($storePackageTypeLabels, $pLabel);
        }

        //Dutch
        $storeArrDut = [$s2, $s6];
        foreach ($storeArrDut as $storeId) {
            $bLabel = $this->attributeFrontendLabel->create()
                ->setStoreId($storeId)
                ->setLabel('Drank selecteren');
            array_push($storeBrandLabels, $bLabel);

            $pLabel = $this->attributeFrontendLabel->create()
                ->setStoreId($storeId)
                ->setLabel('Pakket selecteren');
            array_push($storePackageTypeLabels, $pLabel);
        }

        //Get attribute, existing labels and merge with new.
        $attribute1 = $this->productAttributeRepository->get('brand_swatch');
        $frontendLabels1 = $attribute1->getFrontendLabels();
        $arrMerged1 = array_merge($frontendLabels1, $storeBrandLabels);
        $attribute1->setFrontendLabels($arrMerged1);
        $this->productAttributeRepository->save($attribute1);

        //Get attribute, existing labels and merge with new.
        $attribute2 = $this->productAttributeRepository->get('package_bev_type');
        $frontendLabels2 = $attribute2->getFrontendLabels();
        $arrMerged2 = array_merge($frontendLabels2, $storePackageTypeLabels);
        $attribute2->setFrontendLabels($arrMerged2);
        $this->productAttributeRepository->save($attribute2);
    }


    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [
            NewProductAttributes::class
        ];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}
