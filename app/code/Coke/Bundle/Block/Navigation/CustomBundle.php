<?php

namespace Coke\Bundle\Block\Navigation;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Store\Model\ScopeInterface;

/**
 * Catalog layered navigation view block
 *
 * @api
 * @since 100.0.2
 */
class CustomBundle extends \Magento\Framework\View\Element\Template
{
    /**
     * Product listing toolbar block name
     */
    private const PRODUCT_LISTING_TOOLBAR_BLOCK = 'product_list_toolbar';

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_customBundleLayer;

    /**
     * @var \Magento\Catalog\Model\Layer\FilterList
     */
    protected $filterList;

    /**
     * @var \Magento\Catalog\Model\Layer\AvailabilityFlagInterface
     */
    protected $visibilityFlag;

    /**
     * @param Template\Context $context
     * @param \Coke\Bundle\Model\Layer\CustomBundle\Resolver $layerResolver
     * @param \Magento\Catalog\Model\Layer\FilterList $filterList
     * @param \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $visibilityFlag
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Coke\Bundle\Model\Layer\CustomBundle\Resolver $layerResolver,
        \Magento\Catalog\Model\Layer\FilterList $filterList,
        \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $visibilityFlag,
        array $data = []
    ) {
        $this->_customBundleLayer = $layerResolver->get();
        $this->filterList = $filterList;
        $this->visibilityFlag = $visibilityFlag;
        parent::__construct($context, $data);
    }

    /**
     * Apply layer
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        foreach ($this->filterList->getFilters($this->_customBundleLayer) as $filter) {
            $filter->apply($this->getRequest());
        }
        $this->getLayer()->apply();

        return parent::_prepareLayout();
    }

    /**
     * @inheritdoc
     * @since 100.3.4
     */
    protected function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }

    /**
     * Get layer object
     *
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer()
    {
        return $this->_customBundleLayer;
    }

    /**
     * Get all layer filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filterList->getFilters($this->_customBundleLayer);
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        return $this->getLayer()->getCurrentCategory()->getDisplayMode() !== \Magento\Catalog\Model\Category::DM_PAGE
            && $this->visibilityFlag->isEnabled($this->getLayer(), $this->getFilters());
    }

    /**
     * 0 or 1
     * @param null $store
     * @return bool
     */
    public function getFilterDrillDown($store = null): bool
    {
        return $this->_scopeConfig->isSetFlag(
            'coke/bundle/filter_drill_down',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
