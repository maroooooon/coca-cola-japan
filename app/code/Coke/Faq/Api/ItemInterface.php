<?php

namespace Coke\Faq\Api;

interface ItemInterface
{
    /**
     * Get identities
     * 
     * @return []
     */
    public function getIdentities();
    
    /**
     * Check is active
     * 
     * @return bool
     */
    public function isActive();
}
