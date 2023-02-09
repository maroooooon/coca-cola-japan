<?php
/**
 * Copyright © Bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Coke\France\Observer\Frontend\Controller;

use Coke\France\Helper\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;

class ActionPredispatchCatalogProductView implements ObserverInterface
{
    /**
     * @var Http
     */
    private $redirect;
    /**
     * @var Config
     */
    private $configHelper;
    /**
     * @var Configurable
     */
    private $configurable;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;


    /**
     * ActionPredispatchCatalogProductView constructor.
     * @param Http $redirect
     * @param Config $configHelper
     * @param Configurable $configurable
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Http $redirect,
        Config $configHelper,
        Configurable $configurable,
        ProductRepositoryInterface $productRepository
    )
    {
        $this->redirect = $redirect;
        $this->configHelper = $configHelper;
        $this->configurable = $configurable;
        $this->productRepository = $productRepository;
    }


    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     * @throws NoSuchEntityException
     */
    public function execute(
        Observer $observer
    ) {

        /* Skip if module is not enabled */
        if (!$this->configHelper->isEnabled()) {
            return;
        }
        $productId = $observer->getEvent()->getRequest()->getParam('id');

        /* Skip if there isn't an id param */
        if (!$productId) {
            return;
        }

        /* If the product has a parent id load the parent and redirect with phrase params */
        $configurable = $this->configurable->getParentIdsByChild($productId);
        if (isset($configurable[0])) {
            $simple = $this->productRepository->getById($productId);
            $product = $this->productRepository->get($this->configHelper->primaryBottleSku());
            $source = $simple->getResource()->getAttribute('prefilled_phrase')->getSource();
            $phrase = $source->getOptionText($simple->getData('prefilled_phrase'));
            $line1 = $simple->getData('prefilled_phrase_line_1');
            $line2 = $simple->getData('prefilled_phrase_line_2');
            $urlWithParams = $product->getUrlModel()->getUrl($product, ['_query' => [
                'line1' => $line1 ?? $phrase,
                'line2' => $line2
            ]]);
            $this->redirect->setRedirect($urlWithParams, 301);
        }
    }
}
