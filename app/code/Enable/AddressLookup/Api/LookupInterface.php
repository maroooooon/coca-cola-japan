<?php

namespace Enable\AddressLookup\Api;

interface LookupInterface
{
    /**
     * @param string $data
     * @return string
     */
    public function lookup(string $data): string;
}
