<?php

namespace Coke\Whitelist\Api\Data;

interface WhitelistTypeInterface
{
    const NAME = 'name';
    const LABEL = 'label';

    /**
     * @return null|string
     */
    public function getName(): ?string;

    /**
     * @param string $name
     * @return \Coke\Whitelist\Api\Data\WhitelistTypeInterface
     */
    public function setName(string $name);

    /**
     * @return null|string
     */
    public function getLabel(): ?string;

    /**
     * @param string $label
     * @return \Coke\Whitelist\Api\Data\WhitelistTypeInterface
     */
    public function setLabel(string $label);
}
