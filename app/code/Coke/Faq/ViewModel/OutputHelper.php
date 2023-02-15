<?php

namespace Coke\Faq\ViewModel;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class OutputHelper implements ArgumentInterface
{
    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * OutputHelper constructor.
     * @param FilterProvider $filterProvider
     */
    public function __construct(FilterProvider $filterProvider)
    {
        $this->filterProvider = $filterProvider;
    }

    /**
     * @return \Magento\Framework\Filter\Template
     */
    public function getFilterProvider()
    {
        return $this->filterProvider->getBlockFilter();
    }
}
