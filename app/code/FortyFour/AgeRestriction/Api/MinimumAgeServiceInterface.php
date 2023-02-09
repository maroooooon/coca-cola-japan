<?php

namespace FortyFour\AgeRestriction\Api;

interface MinimumAgeServiceInterface
{
    /**
     * @param string $date
     * @param string $successfulRedirectUrl
     * @return string
     */
    public function validate(string $date, string $successfulRedirectUrl): string;
}
