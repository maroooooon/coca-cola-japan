<?php

namespace Coke\Faq\Model\Category\Attribute\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Category status functionality model
 */
class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var null|array
     */
    protected $options = null;

	public function __construct(
        \Coke\Faq\Model\ResourceModel\Category\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

	/**
     * @return array|null
     */
    public function toOptionArray()
    {
        if (is_null($this->options)) {
            $collection = $this->collectionFactory->create();

            foreach ($collection as $elem) {
                $option = [];
                $option['label'] = $elem->getName();
                $option['value'] = $elem->getEntityId();
                $this->options[strtolower($elem->getName())] = $option;
            }

            ksort($this->options);
        }
        return $this->options;
    }
}
