<?php

namespace Coke\Whitelist\Ui\Component\Whitelist\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Actions constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param array $item
     * @param string $idField
     * @return string
     */
    public function getEditUrl(array $item, $idField)
    {
        return $this->urlBuilder->getUrl('coke_whitelist/manage/edit', [
            'id' => $item[$idField]
        ]);
    }

    /**
     * @param array $item
     * @param $idField
     * @return string
     */
    public function getDeleteUrl(array $item, $idField)
    {
        return $this->urlBuilder->getUrl('coke_whitelist/manage/delete', [
            'id' => $item[$idField]
        ]);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $config = $this->getData('config');

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $idField = $config['indexField'];
                $name = $this->getData('name');

                if (isset($item[$idField])) {
                    $item[$name]['edit'] = [
                        'href' => $this->getEditUrl($item, $idField),
                        'label' => __('Edit'),
                        '__disableTmpl' => true,
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->getDeleteUrl($item, $idField),
                        'label' => __('Delete'),
                        '__disableTmpl' => true,
                        'confirm' => [
                            'title' => __('Delete \'%1\'', $item['value']),
                            'message' => __('Are you sure you want to delete \'%1\'?', $item['value']),
                        ],
                        'post' => true,
                    ];
                }
            }
        }

        return $dataSource;
    }
}
