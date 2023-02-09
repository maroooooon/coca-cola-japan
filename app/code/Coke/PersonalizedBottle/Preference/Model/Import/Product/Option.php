<?php

namespace Coke\PersonalizedBottle\Preference\Model\Import\Product;

class Option extends \Magento\CatalogImportExport\Model\Import\Product\Option
{

    protected $_specificTypes = [
        'date' => ['price', 'sku'],
        'date_time' => ['price', 'sku'],
        'time' => ['price', 'sku'],
        'field' => ['price', 'sku', 'max_characters'],
        'area' => ['price', 'sku', 'max_characters'],
        'drop_down' => true,
        'radio' => true,
        'checkbox' => true,
        'multiple' => true,
        'file' => ['sku', 'file_extension', 'image_size_x', 'image_size_y'],
        'whitelist_text' => true,
        'whitelist_dropdown' => true
    ];

}
