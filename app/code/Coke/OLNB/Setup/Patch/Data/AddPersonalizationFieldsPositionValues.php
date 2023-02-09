<?php

namespace Coke\OLNB\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddPersonalizationFieldsPositionValues implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public static function getDependencies()
    {
        return [
            AddPersonalizationFieldsPositionAttribute::class
        ];
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return DataPatchInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        $mapping = [
            'IE-EN-SLK330-01-DKO' => [ 0 => [ 'start_x' => 55, 'start_y' => 325, 'end_x' => 274, 'end_y' => 363, ], 1 => [ 'start_x' => 55, 'start_y' => 388, 'end_x' => 274, 'end_y' => 426, ], ],
            'IE-EN-SLK330-02-DKO' => [ 0 => [ 'start_x' => 55, 'start_y' => 325, 'end_x' => 274, 'end_y' => 363, ], 1 => [ 'start_x' => 55, 'start_y' => 388, 'end_x' => 274, 'end_y' => 426, ], ],
            'IE-EN-SLK330-03-DKO' => [ 0 => [ 'start_x' => 55, 'start_y' => 362, 'end_x' => 274, 'end_y' => 400, ], 1 => [ 'start_x' => 55, 'start_y' => 425, 'end_x' => 274, 'end_y' => 463, ], ],
            'IE-EN-SLK330-04-DKO' => [ 0 => [ 'start_x' => 55, 'start_y' => 362, 'end_x' => 274, 'end_y' => 400, ], 1 => [ 'start_x' => 55, 'start_y' => 425, 'end_x' => 274, 'end_y' => 463, ], ],
            'IE-EN-SLK330-05-DKO' => [ 0 => [ 'start_x' => 55, 'start_y' => 410, 'end_x' => 274, 'end_y' => 448, ], 1 => [ 'start_x' => 55, 'start_y' => 473, 'end_x' => 274, 'end_y' => 511, ], ],
            'IE-EN-SLK330-06-DKO' => [ 0 => [ 'start_x' => 55, 'start_y' => 325, 'end_x' => 274, 'end_y' => 363, ], 1 => [ 'start_x' => 55, 'start_y' => 388, 'end_x' => 274, 'end_y' => 426, ], ],
            'IE-EN-SLK330-07-DKO' => [ 0 => [ 'start_x' => 55, 'start_y' => 368, 'end_x' => 274, 'end_y' => 406, ], 1 => [ 'start_x' => 55, 'start_y' => 431, 'end_x' => 274, 'end_y' => 469, ], ],

            'GB-EN-STD330-01-DKO' => [ 0 => [ 'start_x' => 55, 'start_y' => 250, 'end_x' => 274, 'end_y' => 288, ], 1 => [ 'start_x' => 55, 'start_y' => 313, 'end_x' => 274, 'end_y' => 351, ], ],
            'GB-EN-STD330-02-DKO' => [ 0 => [ 'start_x' => 55, 'start_y' => 250, 'end_x' => 274, 'end_y' => 288, ], 1 => [ 'start_x' => 55, 'start_y' => 313, 'end_x' => 274, 'end_y' => 351, ], ],
            'GB-EN-STD330-03-DKO' => [ 0 => [ 'start_x' => 55, 'start_y' => 292, 'end_x' => 274, 'end_y' => 330, ], 1 => [ 'start_x' => 55, 'start_y' => 355, 'end_x' => 274, 'end_y' => 393, ], ],
            'GB-EN-STD330-04-DKO' => [ 0 => [ 'start_x' => 55, 'start_y' => 292, 'end_x' => 274, 'end_y' => 330, ], 1 => [ 'start_x' => 55, 'start_y' => 355, 'end_x' => 274, 'end_y' => 393, ], ],
            'GB-EN-STD330-05-DKO' => [ 0 => [ 'start_x' => 55, 'start_y' => 292, 'end_x' => 274, 'end_y' => 330, ], 1 => [ 'start_x' => 55, 'start_y' => 355, 'end_x' => 274, 'end_y' => 393, ], ],
            'GB-EN-STD330-06-DKO' => [ 0 => [ 'start_x' => 55, 'start_y' => 250, 'end_x' => 274, 'end_y' => 288, ], 1 => [ 'start_x' => 55, 'start_y' => 313, 'end_x' => 274, 'end_y' => 351, ], ],
            'GB-EN-STD330-07-DKO' => [ 0 => [ 'start_x' => 55, 'start_y' => 300, 'end_x' => 274, 'end_y' => 338, ], 1 => [ 'start_x' => 55, 'start_y' => 363, 'end_x' => 274, 'end_y' => 401, ], ],

        ];

        foreach ($mapping as $sku => $positions) {
            $this->moduleDataSetup->getConnection()->query(
                "INSERT INTO catalog_product_entity_varchar (attribute_id, row_id, store_id, value) values (" .
                    "(select attribute_id from eav_attribute where attribute_code = 'personalization_fields_pos'), " .
                    "(select entity_id from catalog_product_entity where sku = '" . $sku . "'), " .
                    "0, " .
                    $this->moduleDataSetup->getConnection()->quote(json_encode($positions)) . ") " .
                "on duplicate key update value = values(value)"
            );
        }
    }
}