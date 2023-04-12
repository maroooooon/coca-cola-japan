<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CokeJapan\Customer\Helper;

use Magento\Checkout\Block\Cart;
use Magento\Framework\App\Helper\AbstractHelper;

class Login  extends AbstractHelper
{

    protected $customerSession;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     */

    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    public function isLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }
}
