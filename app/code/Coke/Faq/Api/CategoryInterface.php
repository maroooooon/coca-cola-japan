<?php

namespace Coke\Faq\Api;

interface CategoryInterface
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
