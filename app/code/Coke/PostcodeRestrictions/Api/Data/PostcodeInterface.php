<?php

namespace Coke\PostcodeRestrictions\Api\Data;

interface PostcodeInterface
{
    const postcode = 'postcode';
    const CITY = 'city';
    const IS_ACTIVE = 'is_active';

    /**
     * @return null|string
     */
    public function getPostcode(): ?string;

    /**
     * @param string $postcode
     * @return \Coke\PostcodeRestrictions\Api\Data\PostcodeInterface
     */
    public function setPostcode(string $postcode);

    /**
     * @return null|string
     */
    public function getCity(): ?string;

    /**
     * @param string $city
     * @return \Coke\PostcodeRestrictions\Api\Data\PostcodeInterface
     */
    public function setCity(string $city);

    /**
     * @return null|bool
     */
    public function getIsActive(): ?bool;

    /**
     * @param bool $isActive
     * @return \Coke\PostcodeRestrictions\Api\Data\PostcodeInterface
     */
    public function setIsActive(bool $isActive);
}
