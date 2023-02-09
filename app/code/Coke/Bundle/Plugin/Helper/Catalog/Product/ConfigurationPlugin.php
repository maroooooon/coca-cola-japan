<?php

namespace Coke\Bundle\Plugin\Helper\Catalog\Product;

use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Bundle\Helper\Catalog\Product\Configuration as BundleConfiguration;

class ConfigurationPlugin extends BundleConfiguration
{
    /**
     * Core data
     *
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * Catalog product configuration
     *
     * @var \Magento\Catalog\Helper\Product\Configuration
     */
    protected $productConfiguration;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * Serializer interface instance.
     *
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * ConfigurationPlugin constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfiguration
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $productConfiguration,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->productConfiguration = $productConfiguration;
        $this->pricingHelper = $pricingHelper;
        $this->escaper = $escaper;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        parent::__construct(
            $context,
            $productConfiguration,
            $pricingHelper,
            $escaper,
            $serializer
        );
    }


    public function afterGetBundleOptions(\Magento\Bundle\Helper\Catalog\Product\Configuration $subject, $result, ItemInterface $item)
    {
        foreach ($result as $oKey => $options) {
            foreach ($options['value'] as $vKey => $value) {
                preg_match('/<span class="price">￥(.*?)<\/span>/', $value, $matches);
                if (isset($matches[1]) && (int)$matches[1] == 0) {
                    $result[$oKey]['value'][$vKey] = preg_replace('/<span class="price">.*?<\/span>/', '', $value);
                }
            }
        }

        return $result;
    }

    public function aroundGetBundleOptions(BundleConfiguration $subject, callable $proceed, ItemInterface $item) {
        $options = [];
        $product = $item->getProduct();

        /** @var \Magento\Bundle\Model\Product\Type $typeInstance */
        $typeInstance = $product->getTypeInstance();

        // get bundle options
        $optionsQuoteItemOption = $item->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = $optionsQuoteItemOption
            ? $this->serializer->unserialize($optionsQuoteItemOption->getValue())
            : [];

        if ($bundleOptionsIds) {
            /** @var \Magento\Bundle\Model\ResourceModel\Option\Collection $optionsCollection */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $item->getOptionByCode('bundle_selection_ids');

            $bundleSelectionIds = $this->serializer->unserialize($selectionsQuoteItemOption->getValue());

            if (!empty($bundleSelectionIds)) {
                $selectionsCollection = $typeInstance->getSelectionsByIds($bundleSelectionIds, $product);

                $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
                foreach ($bundleOptions as $bundleOption) {
                    if ($bundleOption->getSelections()) {
                        $option = ['label' => $bundleOption->getTitle(), 'value' => []];

                        $bundleSelections = $bundleOption->getSelections();

                        foreach ($bundleSelections as $bundleSelection) {
                            $qty = $this->getSelectionQty($product, $bundleSelection->getSelectionId()) * 1;
                            if ($qty) {
                                //Move qty after product title
                                $option['value'][] = $this->escaper->escapeHtml($bundleSelection->getName()) . ' x '
                                    . $qty
                                    . ': '
                                    . $this->pricingHelper->currency(
                                        $this->getSelectionFinalPrice($item, $bundleSelection)
                                    );
                                $option['has_html'] = true;
                            }
                        }

                        if ($option['value']) {
                            $options[] = $option;
                        }
                    }
                }
            }
        }

        return $options;
    }
}
