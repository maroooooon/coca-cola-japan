<?php

namespace Coke\PersonalizedBottle\Setup\Patch\Data;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Psr\Log\LoggerInterface;

class UpdatePersonalizedBottleSteps implements DataPatchInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        LoggerInterface $logger,
        ResourceConnection $resourceConnection
    ) {
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
    }

    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return UpdatePersonalizedBottleSteps|void
     */
    public function apply()
    {
        $personalizedBottleHeaderSteps = /** @lang text */'<style>#html-body [data-pb-style=A0ROPAM],#html-body [data-pb-style=X83AY1H]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=A0ROPAM]{padding-top:40px;padding-bottom:40px}#html-body [data-pb-style=X83AY1H]{width:6.25%;align-self:stretch}#html-body [data-pb-style=SHIYDU3]{justify-content:center;background-color:#ed1c16;width:37.5%;padding:5px;align-self:stretch}#html-body [data-pb-style=IC24T74],#html-body [data-pb-style=NCWFUGQ],#html-body [data-pb-style=SHIYDU3],#html-body [data-pb-style=WJXSVHH]{display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=NCWFUGQ]{justify-content:center;text-align:center;min-height:104px;width:12.5%;padding:15px;align-self:center}#html-body [data-pb-style=IC24T74],#html-body [data-pb-style=WJXSVHH]{align-self:stretch}#html-body [data-pb-style=WJXSVHH]{justify-content:center;background-color:#ed1c16;width:37.5%;padding:5px}#html-body [data-pb-style=IC24T74]{justify-content:flex-start;width:6.25%}</style><div class="personalized-bottle-header-steps" data-content-type="row" data-appearance="full-width" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="main" data-pb-style="A0ROPAM"><div class="row-full-width-inner" data-element="inner"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="16" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="X83AY1H"></div><div class="pagebuilder-column callout-box" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="SHIYDU3"><div data-content-type="html" data-appearance="default" data-element="main">&lt;div class="flex"&gt;
&lt;div class="col col-2 step-icon"&gt;&lt;img src="{{media url=wysiwyg/japan/personalized/mylabel_pdp_icon1_step1.png}}" alt="" /&gt;&lt;/div&gt;
&lt;p class="col col-8 step-text"&gt;シチュエーションや気分に合わせて好みのラベルデザインを選択&lt;/p&gt;
&lt;div class="col col-2 step-img"&gt;&lt;img src="{{media url=wysiwyg/japan/personalized/mylabel_pdp_icon1_step1_cracker.png}}" alt="" /&gt;&lt;/div&gt;
&lt;/div&gt;</div></div><div class="pagebuilder-column callout-box" data-content-type="column" data-appearance="align-center" data-background-images="{}" data-element="main" data-pb-style="NCWFUGQ"><div data-content-type="html" data-appearance="default" data-element="main">&lt;img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/japan/personalized/icon-right-arrow.png}}" alt="" /&gt;
&lt;img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/japan/personalized/icon-down-arrow.png}}" alt="" /&gt;</div></div><div class="pagebuilder-column callout-box" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="WJXSVHH"><div data-content-type="html" data-appearance="default" data-element="main">&lt;div class="flex"&gt;
&lt;div class="col col-2 step-icon"&gt;&lt;img src="{{media url=wysiwyg/japan/personalized/mylabel_pdp_icon1_step2.png}}" alt="" /&gt;&lt;/div&gt;
&lt;p class="col col-8 step-text"&gt;大事な人、贈りたい人に向けて自由にメッセージや名前を入力&lt;/p&gt;
&lt;div class="col col-2 step-img"&gt;&lt;img src="{{media url=wysiwyg/japan/personalized/mylabel_pdp_icon1_step2_cart.png}}" alt="" /&gt;&lt;/div&gt;
&lt;/div&gt;</div></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="IC24T74"></div></div></div></div>';

        $attributeId = $this->getPersonalizedBottleAttributeId();
        $product = $this->productRepository->get('personalized-bottle');
        $this->setPersonalizedBottleHeaderStepsValue($attributeId, $product, $personalizedBottleHeaderSteps);
    }

    /**
     * @param int $attributeId
     * @param ProductInterface $product
     * @param string $value
     */
    private function setPersonalizedBottleHeaderStepsValue(int $attributeId, ProductInterface $product, string $value)
    {
        $connection = $this->resourceConnection->getConnection();
        $connection->insertOnDuplicate(
            'catalog_product_entity_text',
            [
                'attribute_id' => $attributeId,
                'store_id' => 0,
                'row_id' => $product->getData('row_id'),
                'value' => $value
            ],
            ['value']
        );
    }

    /**
     * @return string
     */
    private function getPersonalizedBottleAttributeId()
    {
        $connection = $this->resourceConnection->getConnection();
        $query = $connection->select()->from(
            'eav_attribute',
            'attribute_id'
        )->where('attribute_code = ?', 'personalized_bottle_header_ste');

        return $connection->fetchOne($query);
    }
}
