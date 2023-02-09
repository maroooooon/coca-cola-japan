<?php

namespace Coke\Whitelist\Ui\DataProvider\Product\Form\Modifier;

use Coke\Whitelist\Model\ModuleConfig;
use Coke\Whitelist\Model\Product\Option;
use Coke\Whitelist\Model\Source\WhitelistType;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Field;

class WhiteListTypes extends AbstractModifier
{
    const KEY_WHITELIST_TYPE_ID                      = 'whitelist_type_id';
    const KEY_ALLOW_NON_WHITELISTED_VALUES           = 'allow_non_whitelisted_values';
    const KEY_REQUIRE_NON_WHITELISTED_VALUE_APPROVAL = 'require_non_whitelisted_value_approval';

    /**
     * @var \Magento\Framework\Stdlib\ArrayManager
     */
    protected $arrayManager;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var WhitelistType
     */
    private $whitelistType;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * Features constructor.
     * @param ArrayManager $arrayManager
     * @param WhitelistType $whitelistType
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        ArrayManager $arrayManager,
        WhitelistType $whitelistType,
        ModuleConfig $moduleConfig
    ) {
        $this->arrayManager = $arrayManager;
        $this->whitelistType = $whitelistType;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * Get sort order of modifier to load modifiers in the right order
     *
     * @return int
     */
    public function getSortOrder()
    {
        return 50;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        $this->addNewCustomOptionTypes();
        $this->addWhiteListOptionType();
        $this->addAllowNonWhitelistedValuesField();
        $this->addRequireNonWhitelistedValueApprovalField();

        return $this->meta;
    }

    protected function addNewCustomOptionTypes()
    {
        $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children']
        ['type']['arguments']['data']['config']['groupsConfig']['text']['values'][] = Option::OPTION_TYPE_WHITELIST_TEXT;

        $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children']
        ['type']['arguments']['data']['config']['groupsConfig']['text']['values'][] = Option::OPTION_TYPE_WHITELIST_DROPDOWN;
    }

    /**
     * Adds additional options config
     */
    protected function addWhiteListOptionType()
    {

        $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children'] = array_replace_recursive(
            $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children'],
            [static::KEY_WHITELIST_TYPE_ID => $this->getWhiteListTypeFieldsConfig()]
        );

        $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children']
        ['type']['arguments']['data']['config']['groupsConfig']['text']['indexes'][] = self::KEY_WHITELIST_TYPE_ID;
    }

    /**
     * Adds checkbox for allow_non_whitelisted_values field
     */
    protected function addAllowNonWhitelistedValuesField()
    {
        $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children'] = array_replace_recursive(
            $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children'],
            [static::KEY_ALLOW_NON_WHITELISTED_VALUES => $this->getAllowNonWhitelistedValuesFieldConfig()]
        );

        $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children']
        ['type']['arguments']['data']['config']['groupsConfig']['text']['indexes'][] = self::KEY_ALLOW_NON_WHITELISTED_VALUES;
    }

    /**
     * Adds checkbox for require_non_whitelisted_value approval field
     */
    protected function addRequireNonWhitelistedValueApprovalField()
    {
        $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children'] = array_replace_recursive(
            $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children'],
            [static::KEY_REQUIRE_NON_WHITELISTED_VALUE_APPROVAL => $this->getRequireNonWhitelistedValueApprovalFieldConfig()]
        );

        $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children']
        ['type']['arguments']['data']['config']['groupsConfig']['text']['indexes'][] = self::KEY_REQUIRE_NON_WHITELISTED_VALUE_APPROVAL;
    }

    /**
     * Get config for the option gallery field
     *
     * @return array
     */
    protected function getWhiteListTypeFieldsConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Select Whitelist Type'),
                        'componentType' => Field::NAME,
                        'component'     => 'Magento_Ui/js/form/element/select',
                        'formElement'   => Select::NAME,
                        'dataScope'     => static::KEY_WHITELIST_TYPE_ID,
                        'dataType'      => Number::NAME,
                        'sortOrder'     => 80,
                        'options'       => $this->whitelistType->toOptionArray(),
                        'disableLabel'  => true,
                        'multiple'      => false,
                    ],
                ],
            ],
        ];
    }

    protected function getAllowNonWhitelistedValuesFieldConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Allow non-whitelisted values'),
                        'componentType' => Field::NAME,
                        'formElement'   => Checkbox::NAME,
                        'dataScope'     => static::KEY_ALLOW_NON_WHITELISTED_VALUES,
                        'dataType'      => Text::NAME,
                        'sortOrder'     => 85,
                        'prefer'        => 'toggle',
                        'valueMap'      => [
                            'false' => '0',
                            'true'  => '1'
                        ]
                    ],
                ],
            ],
        ];
    }

    protected function getRequireNonWhitelistedValueApprovalFieldConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Require non-whitelisted value approval'),
                        'componentType' => Field::NAME,
                        'formElement'   => Checkbox::NAME,
                        'dataScope'     => static::KEY_REQUIRE_NON_WHITELISTED_VALUE_APPROVAL,
                        'dataType'      => Text::NAME,
                        'sortOrder'     => 86,
                        'prefer'        => 'toggle',
                        'valueMap'      => [
                            'false' => '0',
                            'true'  => '1'
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
