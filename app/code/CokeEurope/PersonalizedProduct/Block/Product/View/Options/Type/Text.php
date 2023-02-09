<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CokeEurope\PersonalizedProduct\Block\Product\View\Options\Type;

use Magento\Catalog\Block\Product\View\Options\AbstractOptions;

class Text extends AbstractOptions
{

    public function getDefaultValue()
    {
        return  $this->getProduct()->getPreconfiguredValues()->getData('options/' . $this->getOption()->getId());
    }

    public function getOptionClass(): string
    {
        $option = $this->getOption();
        $class  = "custom-option custom-option--".$option->getType();
        $class .= " custom-option--".strtolower($option->getTitle());
        $class .= $option->getIsRequire() ? ' required' : '';
        return $class;
    }

    public function getInputAttributes()
    {
        $option = $this->getOption();
        $optionId = $this->getOption()->getId();
        $optionTitle = $option->getTitle();

        // Input Attributes
        $attributes = [
            'id' => 'options_'.$optionId.'_text',
            'name' => 'options['.$optionId.']',
            'type' => 'text',
            'data-selector' => 'options['.$optionId.']',
        ];

        // Set the input value from url params for Message
        $messageId = $this->getRequest()->getParam('prefilled_message');
        $message = $this->getProduct()->getResource()->getAttribute('prefilled_message');
		if ($messageId && $message && $optionTitle === "Message") {
            $attributes['value'] = $message->getSource()->getOptionText($messageId);
		}
		
		if ($urlOption = $this->getRequest()->getParam(sprintf('options-%s', $optionId))) {
			echo sprintf(' value="%s" ', $urlOption);
		}

        // Add Validation
        $validation = [
            "validate-no-utf8mb4-characters" => true,
            "validate-character-limit" => true,
			"validate-label-regex" => true,
		];

        if($option->getIsRequire()) $validation['required'] = true;
        if($option->getMaxCharacters()) $validation['maxlength'] = $option->getMaxCharacters();

        // Add Moderation
        if($optionTitle === "Message" || $optionTitle === "Name") $attributes['data-enable-moderation'] = true;

        // Loop through attributes array and echo output
        foreach ($attributes as $key => $value) {
            if($value) echo sprintf('%s="%s"', $key, $value);
        }

        echo "data-validate=". json_encode($validation);

    }

}
