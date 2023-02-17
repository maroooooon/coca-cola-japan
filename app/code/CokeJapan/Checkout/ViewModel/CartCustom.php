<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CokeJapan\Checkout\ViewModel;

use Magento\Checkout\Block\Cart;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CartCustom extends Cart implements ArgumentInterface
{
   public function isLoggedIn()
   {
       return $this->_customerSession->isLoggedIn();
   }
}
