<?php

/**
 * @category Bounteous
 * @copyright Copyright (c) 2021 Bounteous LLC
 */

declare(strict_types=1);

use \Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Coke_WhitelistBulkOrder',
    __DIR__
);
