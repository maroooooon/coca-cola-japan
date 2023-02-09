<?php

namespace Coke\Cms\Model\Content;

abstract class AbstractContent
{
    /**
     * @param string $identifier
     * @return mixed
     */
    abstract protected function getEntity(string $identifier);

    /**
     * @param string $identifier
     * @return mixed
     */
    abstract protected function createEntity(string $identifier);

    /**
     * @param string $identifier
     * @param string $content
     * @param array $changes
     * @return mixed
     */
    abstract public function applyChanges(string $identifier, string $content, array $changes);
}
