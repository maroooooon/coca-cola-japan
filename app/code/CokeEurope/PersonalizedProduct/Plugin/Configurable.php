<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\PersonalizedProduct\Plugin;

use CokeEurope\PersonalizedProduct\Helper\Config as ConfigHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;

class Configurable
{

    private Json $json;
    private ConfigHelper $configHelper;
    private ProductRepositoryInterface $productRepository;

    /**
     * @param Json $json
     * @param ConfigHelper $configHelper
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Json                       $json,
        ConfigHelper               $configHelper,
        ProductRepositoryInterface $productRepository
    )
    {
        $this->json = $json;
        $this->configHelper = $configHelper;
        $this->productRepository = $productRepository;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function afterGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
                                                                          $result
    )
    {
        // Skip if not enabled
        if (!$this->configHelper->isEnabled()) {
            return $result;
        }

        // Add dynamic description & ingredients for each simple product
        $jsonResult = $this->json->unserialize($result);
        $jsonResult['dynamicAttributes']['description']['default'] = $subject->getProduct()->getData('description');
        $jsonResult['dynamicAttributes']['ingredients']['default'] = $subject->getProduct()->getData('ingredients');
        foreach ($subject->getAllowProducts() as $simple) {
            $id = $simple->getId();
            $product = $this->productRepository->getById($id);
            $description = $product->getDescription();
            $ingredients = $product->getCustomAttribute('ingredients');
            $x = $product->getCustomAttribute('pp_label_x');
            $y = $product->getCustomAttribute('pp_label_y');
            $width = $product->getCustomAttribute('pp_label_width');
			$color = $product->getCustomAttribute('pp_label_color');
            $font_size = $product->getCustomAttribute('pp_label_font_size');
            $font_family = $product->getCustomAttribute('pp_label_font_family');
			$character_limit = $product->getCustomAttribute('pp_label_character_limit');
			$regex = $product->getCustomAttribute('pp_label_regex');

			// Add Dynamic attributes
            if ($description) $jsonResult['dynamicAttributes']['description'][$id] = $description;
            if ($ingredients) $jsonResult['dynamicAttributes']['ingredients'][$id] = $ingredients->getValue();

            // Add Personalized Product label attributes
            if ($x) $jsonResult['labelAttributes']['x'][$id] = $x->getValue();
            if ($y) $jsonResult['labelAttributes']['y'][$id] = $y->getValue();
            if ($width) $jsonResult['labelAttributes']['width'][$id] = $width->getValue();
			if ($color) $jsonResult['labelAttributes']['color'][$id] = $color->getValue();
			if ($font_size) $jsonResult['labelAttributes']['fontSize'][$id] = $font_size->getValue();
            if ($font_family) $jsonResult['labelAttributes']['fontFamily'][$id] = $product->getAttributeText('pp_label_font_family');
			if ($character_limit) $jsonResult['labelAttributes']['characterLimit'][$id] = $character_limit->getValue();
			if ($regex) $jsonResult['labelAttributes']['regex'][$id] = $regex->getValue();
		}
        return $this->json->serialize($jsonResult);
    }
}
