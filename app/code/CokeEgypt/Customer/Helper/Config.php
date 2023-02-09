<?php
/**
 * Copyright © bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEgypt\Customer\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{

    const XML_CONFIG_ENABLED = 'coke_egypt/customer/enabled';

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_ENABLED, ScopeInterface::SCOPE_STORE);
    }
}

