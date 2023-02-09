<?php
/**
 * Copyright © bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Bounteous\MaintenanceMode\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    protected const XML_CONFIG_MAINTENANCE_ENABLED  = 'bounteous_maintenance/general/enabled';
    protected const XML_CONFIG_MAINTENANCE_CMS_PAGE = 'bounteous_maintenance/general/cms_page';

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_MAINTENANCE_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getCmsPage()
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_MAINTENANCE_CMS_PAGE, ScopeInterface::SCOPE_STORE);
    }
}
