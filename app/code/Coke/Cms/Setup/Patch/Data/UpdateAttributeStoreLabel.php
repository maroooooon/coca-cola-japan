<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Api\Data\AttributeFrontendLabelInterfaceFactory;

class UpdateAttributeStoreLabel implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var AttributeFrontendLabelInterfaceFactory
     */
    private $attributeLabel;

    /**
     * @var Data
     */
    private $helper;

    /**
     * UpdateAttributeStoreLabel constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param AttributeRepositoryInterface $attributeRepository
     * @param AttributeFrontendLabelInterfaceFactory $attributeLabel
     * @param Data $helper
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        AttributeRepositoryInterface $attributeRepository,
        AttributeFrontendLabelInterfaceFactory $attributeLabel,
        Data $helper
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->attributeRepository = $attributeRepository;
        $this->attributeLabel = $attributeLabel;
        $this->helper = $helper;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $arStore = $this->helper->getEgyptArabicStore();

        try {
            /** @var AttributeInterface $containerAttribute */
            $containerAttribute = $this->attributeRepository->get(ProductAttributeInterface::ENTITY_TYPE_CODE, 'container');
            $newContainerLabel = $this->attributeLabel->create();
            $newContainerLabel->setStoreId($arStore->getId());
            $newContainerLabel->setLabel('حجم العبوة الفردية');
            $containerAttribute->setFrontendLabels([$newContainerLabel]);
            $this->attributeRepository->save($containerAttribute);

            $brandAttribute = $this->attributeRepository->get(ProductAttributeInterface::ENTITY_TYPE_CODE, 'brand');
            $newContainerLabel = $this->attributeLabel->create();
            $newContainerLabel->setStoreId($arStore->getId());
            $newContainerLabel->setLabel('منتجات');
            $brandAttribute->setFrontendLabels([$newContainerLabel]);
            $this->attributeRepository->save($brandAttribute);
        } catch (\Exception $exception) {
            /** skip */
        }

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
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
}
