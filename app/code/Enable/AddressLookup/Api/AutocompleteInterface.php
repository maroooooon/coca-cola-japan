<?php

namespace Enable\AddressLookup\Api;

interface AutocompleteInterface
{
    /**
     * @param string $data
     * @return string
     */
    public function getSuggestions(string $data): string;
}
