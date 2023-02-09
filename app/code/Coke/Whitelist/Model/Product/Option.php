<?php

namespace Coke\Whitelist\Model\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option\Type\Date;
use Magento\Catalog\Model\Product\Option\Type\DefaultType;
use Magento\Catalog\Model\Product\Option\Type\File;
use Magento\Catalog\Model\Product\Option\Type\Select;
use Magento\Catalog\Model\Product\Option\Type\Text;
use Magento\Framework\Exception\LocalizedException;

class Option extends \Magento\Catalog\Model\Product\Option
{
    const OPTION_TYPE_WHITELIST_TEXT = 'whitelist_text';
    const OPTION_TYPE_WHITELIST_DROPDOWN = 'whitelist_dropdown';

    private $optionTypesToGroups;

    /**
     * Group model factory
     *
     * @param string $type Option type
     * @return DefaultType
     * @throws LocalizedException
     */
    public function groupFactory($type)
    {
        $group = $this->getGroupByType($type);

        $optionGroups = [
            self::OPTION_GROUP_DATE => Date::class,
            self::OPTION_GROUP_FILE => File::class,
            self::OPTION_GROUP_SELECT => Select::class,
            self::OPTION_GROUP_TEXT => Text::class,
            self::OPTION_TYPE_WHITELIST_TEXT => Text::class,
            self::OPTION_TYPE_WHITELIST_DROPDOWN => Text::class
        ];

        if (!empty($group) && isset($optionGroups[$group])) {
            return $this->optionTypeFactory->create($optionGroups[$group]);
        }
        throw new LocalizedException(__('The option type to get group instance is incorrect.'));
    }

    public function getGroupByType($type = null)
    {
        if ($type === null) {
            $type = $this->getType();
        }
        $optionTypesToGroups = [
            self::OPTION_TYPE_FIELD => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_WHITELIST_TEXT => self::OPTION_TYPE_WHITELIST_TEXT,
            self::OPTION_TYPE_AREA => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_FILE => self::OPTION_GROUP_FILE,
            self::OPTION_TYPE_DROP_DOWN => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_RADIO => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_CHECKBOX => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_MULTIPLE => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_WHITELIST_DROPDOWN => self::OPTION_TYPE_WHITELIST_DROPDOWN,
            self::OPTION_TYPE_DATE => self::OPTION_GROUP_DATE,
            self::OPTION_TYPE_DATE_TIME => self::OPTION_GROUP_DATE,
            self::OPTION_TYPE_TIME => self::OPTION_GROUP_DATE,
        ];
        return isset($optionTypesToGroups[$type]) ? $optionTypesToGroups[$type] : '';
    }
}
