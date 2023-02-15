<?php

namespace Coke\Faq\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class TestActions
 */
class CategoryListing extends Column
{

    /**
     * @var $faqCategoryFactory
     */

    protected $faqCategoryFactory;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Coke\Faq\Model\CategoryFactory $faqCategoryFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Coke\Faq\Model\CategoryFactory $faqCategoryFactory,
        array $components = [],
        array $data = []
    ) {
        $this->faqCategoryFactory = $faqCategoryFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($this->getData('name') == "faq_category_id") {
                    $faqCategory = $this->faqCategoryFactory->create()->load($item['faq_category_id']);
                    $item[$this->getData('name')] = [$faqCategory->getName()];
                }
            }
        }

        return $dataSource;
    }

}