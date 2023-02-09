<?php


namespace Coke\Whitelist\Ui\DataProvider\Product\Form\Modifier;

use Coke\Whitelist\Model\ModuleConfig;
use Coke\Whitelist\Model\Source\WhitelistType;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;

class Steps extends AbstractModifier
{
    const KEY_STEP_ID = 'step_id';
    const KEY_STEP_LABEL = 'step_label';

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

        $this->addStepIdField();
        $this->addStepLabelField();

        return $this->meta;
    }

    /**
     * Adds additional options config
     */
    protected function addStepIdField()
    {

        if (isset($this->meta['custom_options'])) {
            $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children'] = array_replace_recursive(
                $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children'],
                [static::KEY_STEP_ID => $this->getStepIdFieldConfig()]
            );

            $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children']
            ['type']['arguments']['data']['config']['groupsConfig']['text']['indexes'][] = self::KEY_STEP_ID;
        }
    }

    /**
     * Adds additional options config
     */
    protected function addStepLabelField()
    {
        if (isset($this->meta['custom_options'])) {
            $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children'] = array_replace_recursive(
                $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children'],
                [static::KEY_STEP_LABEL => $this->getStepLabelFieldConfig()]
            );

            $this->meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children']
            ['type']['arguments']['data']['config']['groupsConfig']['text']['indexes'][] = self::KEY_STEP_LABEL;
        }
    }

    /**
     * Get config for the option gallery field
     *
     * @return array
     */
    protected function getStepIdFieldConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Whitelist: Select Step'),
                        'componentType' => Field::NAME,
                        'component'     => 'Magento_Ui/js/form/element/select',
                        'formElement'   => Select::NAME,
                        'dataScope'     => static::KEY_STEP_ID,
                        'dataType'      => Number::NAME,
                        'sortOrder'     => 80,
                        'options'       => [
                            [
                                'label' => __('None'),
                                'value' => null,
                            ],
                            [
                                'label' => __('1'),
                                'value' => '1',
                            ],
                            [
                                'label' => __('2'),
                                'value' => '2',
                            ],
                            [
                                'label' => __('3'),
                                'value' => '3',
                            ],
                            [
                                'label' => __('4'),
                                'value' => '4',
                            ],

                        ],
                        'disableLabel'  => true,
                        'multiple'      => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for the option gallery field
     *
     * @return array
     */
    protected function getStepLabelFieldConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Whitelist: Step Label'),
                        'componentType' => Field::NAME,
//                        'component'     => 'Magento_Ui/js/form/element/text',
                        'formElement'   => Input::NAME,
                        'dataScope'     => static::KEY_STEP_LABEL,
                        'dataType'      => Text::NAME,
                        'sortOrder'     => 80,
                        'disableLabel'  => true,
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
