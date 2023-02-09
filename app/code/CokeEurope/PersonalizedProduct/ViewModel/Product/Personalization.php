<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\PersonalizedProduct\ViewModel\Product;

use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use CokeEurope\PersonalizedProduct\Helper\Config as ConfigHelper;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface as AttributeRepository;

class Personalization implements ArgumentInterface
{
    private LoggerInterface $logger;
    private ConfigHelper $configHelper;
    private AttributeRepository $attributeRepository;

    /**
     * @param LoggerInterface $logger
     * @param ConfigHelper $configHelper
     * @param AttributeRepository $attributeRepository
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigHelper $configHelper,
        AttributeRepository $attributeRepository
    )
    {
        $this->configHelper = $configHelper;
        $this->attributeRepository = $attributeRepository;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function getStepsConfig(): array
    {
        return $this->configHelper->getStepsConfig();
    }

    /**
     * @return array
     */
    public function getModeration(): array
    {
        return [
            'enabled' => $this->configHelper->getModerationEnabled(),
            'script_url' => $this->configHelper->getModerationScript()
        ];
    }

    /**
     * @param $option
     */
    public function getInputAttributes($option)
    {
        $id = $option->getId();
        $selector = 'options[' . $id . ']';

        $validation = [
            "validate-no-utf8mb4-characters" => true
        ];

        // Add additional validation
        if ($option->getIsRequire()) $validation['required'] = true;
        if ($option->getMaxCharacters()) $validation['maxlength'] = $option->getMaxCharacters();

        $attributes = [
            'id' => 'options_' . $id . '_text',
            'name' => $selector,
            'type' => 'text',
            'data-selector' => $selector,
            'data-validate' => json_encode($validation)
        ];

        foreach ($attributes as $key => $value) {
            if ($key === 'data-validate' && $value) echo sprintf('%s=%s', $key, $value);
            else if ($value) echo sprintf('%s="%s"', $key, $value);
        }
    }


    /**
     * @return array
     */
    public function getAvailableFonts(): array
    {
        $fonts = [];
        try {
            $options = $this->attributeRepository->get('pp_label_font_family')->getOptions();
            if($options) {
                foreach ($options as $font) {
                    if ($font->getValue() !== '') {
                        $fonts[] = "1em " . $font->getLabel();
                    }
                }
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->error('Personalized Product Error', ['exception' => $e]);
        }
        return $fonts;
    }
}
