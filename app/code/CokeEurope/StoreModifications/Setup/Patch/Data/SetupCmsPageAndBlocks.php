<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use Magento\Cms\Helper\Page;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use function Aws\map;

class SetupCmsPageAndBlocks implements DataPatchInterface
{
    /** @var BlockFactory  */
    protected $blockFactory;

    /** @var PageFactory  */
    protected $pageFactory;

    /** @var WriterInterface */
    protected $writer;

    /** @var UrlRewriteCollectionFactory  */
    protected $urlRewriteCollectionFactory;

    /** @var array  */
    protected $howItWorkBlocks;

    /** @var array  */
    protected $somethingCustomBlocks;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        BlockFactory $blockFactory,
        PageFactory $pageFactory,
        WriterInterface $writer,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->blockFactory = $blockFactory;
        $this->pageFactory = $pageFactory;
        $this->writer = $writer;
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->howItWorkBlocks = [];
        $this->somethingCustomBlocks = [];
        $this->storeManager = $storeManager;
    }

    public function apply()
    {
        $this->clearHomeUrlRewrites();
        $this->createBlocks();
        $this->createPages();
    }

    protected function clearHomeUrlRewrites()
    {
        $homeRewrites = $this->urlRewriteCollectionFactory->create();
        $homeRewrites->addFieldToFilter('request_path', ['eq' => 'home']);
        $homeRewrites->addFieldToFilter('store_id', ['in' => $this->getAllStoreIds()]);

        foreach($homeRewrites as $rewrite) {
            $rewrite->delete();
        }
    }

    protected function getAllStoreIds()
    {
        return array_map(function($store) {
           return $store['id'];
        }, $this->getApplicableStoreViews());
    }

    protected function createBlocks()
    {
        foreach ($this->getEuropeBlocks() as $block) {
            foreach ($this->getApplicableStoreViews() as $key => $storeView) {
                $newBlock = $this->blockFactory->create()->setStoreId($storeView['id'])->load($block['identifier']);
                $newBlock->setIdentifier($block['identifier']);
                $newBlock->setData('stores', [$storeView['id']]);
                $newBlock->setTitle($block['title'] . " | store: " . $storeView['code']);
                $newBlock->setContent($block['content']);
                $newBlock->setIsActive(1);
                $newBlock->save();

                // Hydrating the mappings for page builder, since widget blocks don't take string identifiers.
                if ($newBlock->getIdentifier() === 'eu_looking_something_custom') {
                    $this->somethingCustomBlocks[$storeView['id']] = $newBlock->getId();
                }

                if ($newBlock->getIdentifier() === 'eu_how_it_works') {
                    $this->howItWorkBlocks[$storeView['id']] = $newBlock->getId();
                }
            }
        }
    }

    protected function createPages()
    {
        foreach ($this->getEuropePages() as $page) {
            // Foreach within a foreach! Hurray
            foreach ($this->getApplicableStoreViews() as $key => $storeView) {
                $storeId = $storeView['id'];

                $newPage = $this->pageFactory->create()->setStoreId($storeView['id'])->load($page['identifier']);
                $newPage->setIdentifier($page['identifier']);
                $newPage->setOrigData('identifier', ''); // Force trigger a url-rewrite generation
                $newPage->setTitle(__($page['title'] . " | store: " . $storeView['code'], $key)->render());
                $newPage->setMetaTitle(__($page['title'], $key)->render());
                $newPage->setContentHeading('');
                $newPage->setData('stores', [$storeId]);
                $newPage->setIsActive(1);
                $newPage->setSortOrder(0);
                $newPage->setPageLayout('cms-full-width');

                // Manually replacing block ids with the correct ones for the store view.
                if ($newPage->getIdentifier() === 'home') {
                    $newPage->setContent(__($page['content'], $this->howItWorkBlocks[$storeId] ?? '', $this->somethingCustomBlocks[$storeId] ?? '')->render());
                } else {
                    $newPage->setContent($page['content']);
                }

                $newPage->save();

                if ($newPage->getIdentifier() === 'home') {
                    $this->writer
                        ->save(
                            Page::XML_PATH_HOME_PAGE,
                            'home|' . $newPage->getId(),
                            ScopeInterface::SCOPE_STORES,
                            $storeView['id']
                        );
                }

                // Delete home3, we don't need it anymore
                $home3 = $this->pageFactory->create()->setStoreId($storeView['id'])->load('home3');
                if ($home3->getId()) {
                    $home3->delete();
                }
            }
        }

    }

    protected function getApplicableStoreViews()
    {
        return [
            'EN' => ['id' => $this->storeManager->getStore('ireland_english')->getId(), 'code' => 'ireland_english'],
            'EN - North' => ['id' => $this->storeManager->getStore('northern_ireland_english')->getId(), 'code' => 'northern_ireland_english'],
            'NE' => ['id' => $this->storeManager->getStore('netherlands_dutch')->getId(), 'code' => 'netherlands_dutch'],
            'EN - GB' => ['id' => $this->storeManager->getStore('great_britain_english')->getId(), 'code' => 'great_britain_english'],
            'FI' => ['id' => $this->storeManager->getStore('finland_finnish')->getId(), 'code' => 'finland_finnish'],
            'GE' => ['id' => $this->storeManager->getStore('germany_german')->getId(), 'code' => 'germany_german'],
            'FR' => ['id' => $this->storeManager->getStore('france_french')->getId(), 'code' => 'france_french'],
            'BE - Dutch' => ['id' => $this->storeManager->getStore('belgium_dutch')->getId(), 'code' => 'belgium_dutch'],
            'BE - French' => ['id' => $this->storeManager->getStore('belgium_french')->getId(), 'code' => 'belgium_french']
        ];
    }

    protected function getEuropePages()
    {
        return [
            [
                'identifier' => 'home',
                'title' => 'Coke Europe - Home - %1',
                'content' => '
                <style>#html-body [data-pb-style=L6CHUCJ]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;margin-bottom:75px}#html-body [data-pb-style=FSNPHDP]{margin-bottom:100px;padding-left:0;padding-right:0}#html-body [data-pb-style=FSNPHDP],#html-body [data-pb-style=XN1Y71G]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=G3R9CMG]{margin-bottom:100px}#html-body [data-pb-style=MS8PNG7],#html-body [data-pb-style=SD7KHYY]{justify-content:flex-start;display:flex;flex-direction:column;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=MS8PNG7]{background-position:center center;padding-left:0;padding-right:0}#html-body [data-pb-style=SD7KHYY]{background-color:#000;background-position:left top;margin-bottom:120px;padding:20px 0}#html-body [data-pb-style=DF098M6]{margin-top:30px;margin-bottom:30px}#html-body [data-pb-style=QT9VVGN]{width:100%;border-width:1px;border-color:#fff;display:inline-block}#html-body [data-pb-style=V075TAM]{padding-left:15px;padding-right:15px}#html-body [data-pb-style=D5MO9MF],#html-body [data-pb-style=QPSSARH]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;width:41.6667%;align-self:stretch}#html-body [data-pb-style=QPSSARH]{width:58.3333%}#html-body [data-pb-style=E07TI9F],#html-body [data-pb-style=LFA2BCX],#html-body [data-pb-style=TR60X1S],#html-body [data-pb-style=U5RT3UN]{justify-content:center;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;text-align:center;min-height:290px;width:calc(50% - 30px);margin:15px;align-self:stretch}#html-body [data-pb-style=BAD8TWM],#html-body [data-pb-style=DVVC9AM],#html-body [data-pb-style=O3SCOF2]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;width:50%;align-self:stretch}#html-body [data-pb-style=BAD8TWM]{width:28.5714%;padding:15px}#html-body [data-pb-style=CWECUHD]{width:7.14286%}#html-body [data-pb-style=CWECUHD],#html-body [data-pb-style=LYF3SCI],#html-body [data-pb-style=STA0KCB],#html-body [data-pb-style=T3XS8RN]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;align-self:stretch}#html-body [data-pb-style=T3XS8RN]{width:28.5714%;padding:15px}#html-body [data-pb-style=LYF3SCI],#html-body [data-pb-style=STA0KCB]{width:7.14286%}#html-body [data-pb-style=STA0KCB]{width:28.5714%;padding:15px}#html-body [data-pb-style=FV09U6I]{border-style:none}#html-body [data-pb-style=KWNMUCN],#html-body [data-pb-style=YDT44XI]{max-width:100%;height:auto}#html-body [data-pb-style=WV0YEI6]{margin-bottom:20px;border-style:none}#html-body [data-pb-style=MSFIVOK],#html-body [data-pb-style=PD2F5V2]{max-width:100%;height:auto}#html-body [data-pb-style=XNOU5XP]{margin-bottom:20px;border-style:none}#html-body [data-pb-style=NLS8RJ9],#html-body [data-pb-style=TTBNG06]{max-width:100%;height:auto}#html-body [data-pb-style=OEHH4WQ]{margin-bottom:20px;border-style:none}#html-body [data-pb-style=OL0VU86],#html-body [data-pb-style=QTW2TF9]{max-width:100%;height:auto}#html-body [data-pb-style=M8K4AQC]{display:inline-block}#html-body [data-pb-style=VC95EM8]{text-align:center}#html-body [data-pb-style=O50UMIV]{display:inline-block}#html-body [data-pb-style=PGKX0GF]{text-align:center}#html-body [data-pb-style=R3AG9YX]{display:inline-block}#html-body [data-pb-style=MMMQO7C]{text-align:center}#html-body [data-pb-style=S7P39GR]{display:inline-block}#html-body [data-pb-style=U3QANS2]{text-align:center}#html-body [data-pb-style=IIDEAW5]{display:inline-block}#html-body [data-pb-style=EW4IQAG]{text-align:center}@media only screen and (max-width: 768px) { #html-body [data-pb-style=FV09U6I],#html-body [data-pb-style=OEHH4WQ],#html-body [data-pb-style=WV0YEI6],#html-body [data-pb-style=XNOU5XP]{border-style:none} }</style><div class="hero" data-content-type="row" data-appearance="full-width" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/europe/home/home-hero.png}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="main" data-pb-style="MS8PNG7"><div class="row-full-width-inner" data-element="inner"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column text-block" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="O3SCOF2"><div class="title" data-content-type="text" data-appearance="default" data-element="main"><p>Make your own</p></div><div data-content-type="text" data-appearance="default" data-element="main"><p>Design your custom label and create your own unique creation. Give a personal touch for your event, party or a memorable gift.</p></div><div data-content-type="buttons" data-appearance="inline" data-same-width="false" data-element="main"><div data-content-type="button-item" data-appearance="default" data-element="main" data-pb-style="IIDEAW5"><a class="pagebuilder-button-primary" href="{{widget type=\'Magento\Catalog\Block\Product\Widget\Link\' id_path=\'product/3564\' template=\'Magento_PageBuilder::widget/link_href.phtml\' type_name=\'Catalog Product Link\' }}" target="" data-link-type="product" data-element="link" data-pb-style="EW4IQAG"><span data-element="link_text">Create your design</span></a></div></div></div><div class="pagebuilder-column image-block" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="DVVC9AM"><figure data-content-type="image" data-appearance="full-width" data-element="main" data-pb-style="FV09U6I"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/europe/home/home-hero-bottles.png}}" alt="" title="" data-element="desktop_image" data-pb-style="YDT44XI"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/europe/home/home-hero-bottles.png}}" alt="" title="" data-element="mobile_image" data-pb-style="KWNMUCN"></figure></div></div></div></div><div data-content-type="row" data-appearance="full-width" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="main" data-pb-style="SD7KHYY"><div class="row-full-width-inner" data-element="inner"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="14" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="BAD8TWM"><figure data-content-type="image" data-appearance="full-width" data-element="main" data-pb-style="WV0YEI6"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/europe/home/icon-background-heart.png}}" alt="" title="" data-element="desktop_image" data-pb-style="MSFIVOK"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/europe/home/icon-background-heart.png}}" alt="" title="" data-element="mobile_image" data-pb-style="PD2F5V2"></figure><div data-content-type="text" data-appearance="default" data-element="main"><p><span style="color: #ffffff;">CRAFTED WITH CARE</span></p>
 <p><span style="color: #ffffff;">We are creating all orders on demand which means a completely unique product made by you.</span></p></div></div><div class="pagebuilder-column desktop-only divider" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="CWECUHD"></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="T3XS8RN"><figure data-content-type="image" data-appearance="full-width" data-element="main" data-pb-style="XNOU5XP"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/europe/home/icon-background-sustainability.png}}" alt="" title="" data-element="desktop_image" data-pb-style="TTBNG06"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/europe/home/icon-background-sustainability.png}}" alt="" title="" data-element="mobile_image" data-pb-style="NLS8RJ9"></figure><div data-content-type="text" data-appearance="default" data-element="main"><p><span style="color: #ffffff;">SUSTAINABLE MATERIAL</span></p>
<p><span style="color: #ffffff;">All we do impacts people and planet. Our products and packaging come from sustainable and recycled material.</span></p></div></div><div class="pagebuilder-column desktop-only divider" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="LYF3SCI"></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="STA0KCB"><figure data-content-type="image" data-appearance="full-width" data-element="main" data-pb-style="OEHH4WQ"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/europe/home/icon-background-delivery.png}}" alt="" title="" data-element="desktop_image" data-pb-style="OL0VU86"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/europe/home/icon-background-delivery.png}}" alt="" title="" data-element="mobile_image" data-pb-style="QTW2TF9"></figure><div data-content-type="text" data-appearance="default" data-element="main"><p><span style="color: #ffffff;">ADAPTED PRODUCTION</span></p>
<p><span style="color: #ffffff;">Production and printing made through a sustainable process that means less energy, water and waste.</span></p></div></div></div><div data-content-type="divider" data-appearance="default" data-element="main" data-pb-style="DF098M6"><hr data-element="line" data-pb-style="QT9VVGN"></div><div data-content-type="block" data-appearance="default" data-element="main">{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="284" type_name="CMS Static Block"}}</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="suggested-products" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="L6CHUCJ"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="D5MO9MF"><h2 data-content-type="heading" data-appearance="default" data-element="main">Our suggestions</h2><div data-content-type="text" data-appearance="default" data-element="main"><p><span style="font-size: 18px;">Need inspiration? See the most popular messages and browse our collections of suggestions.</span></p></div></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="QPSSARH"></div></div><div class="product-carousel" data-content-type="products" data-appearance="grid" data-element="main">{{widget type="Magento\CatalogWidget\Block\Product\ProductsList" template="Magento_CatalogWidget::product/widget/content/grid.phtml" anchor_text="" id_path="" show_pager="0" products_count="5" condition_option="category_ids" condition_option_value="48" type_name="Catalog Products List" conditions_encoded="^[`1`:^[`aggregator`:`all`,`new_child`:``,`type`:`Smile||ElasticsuiteVirtualCategory||Model||Rule||WidgetCondition||Combine`,`value`:`1`^],`1--1`:^[`operator`:`==`,`type`:`Smile||ElasticsuiteVirtualCategory||Model||Rule||WidgetCondition||Product`,`attribute`:`category_ids`,`value`:`48`^]^]" sort_order="position"}}</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="collections-row" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="FSNPHDP"><h2 data-content-type="heading" data-appearance="default" data-element="main" data-pb-style="V075TAM">Enjoy your best moments</h2><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column collection-tile" data-content-type="column" data-appearance="full-height" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/Collection-Birthday.jpg}}\&quot;}" data-element="main" data-pb-style="LFA2BCX"><div data-content-type="text" data-appearance="default" data-element="main"><p>Share a Birthday</p></div><div data-content-type="buttons" data-appearance="inline" data-same-width="false" data-element="main"><div class="btn-white-outline" data-content-type="button-item" data-appearance="default" data-element="main" data-pb-style="M8K4AQC"><a class="pagebuilder-button-primary" href="{{widget type=\'Magento\Catalog\Block\Category\Widget\Link\' id_path=\'category/158\' template=\'Magento_PageBuilder::widget/link_href.phtml\' type_name=\'Catalog Category Link\' }}" target="" data-link-type="category" data-element="link" data-pb-style="VC95EM8"><span data-element="link_text">Shop collection</span></a></div></div></div><div class="pagebuilder-column collection-tile" data-content-type="column" data-appearance="full-height" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/Collection-All.jpg}}\&quot;}" data-element="main" data-pb-style="TR60X1S"><div data-content-type="text" data-appearance="default" data-element="main"><p>Share Love &amp; Pride</p></div><div data-content-type="buttons" data-appearance="inline" data-same-width="false" data-element="main"><div class="btn-white-outline" data-content-type="button-item" data-appearance="default" data-element="main" data-pb-style="O50UMIV"><a class="pagebuilder-button-primary" href="{{widget type=\'Magento\Catalog\Block\Category\Widget\Link\' id_path=\'category/161\' template=\'Magento_PageBuilder::widget/link_href.phtml\' type_name=\'Catalog Category Link\' }}" target="" data-link-type="category" data-element="link" data-pb-style="PGKX0GF"><span data-element="link_text">Shop collection</span></a></div></div></div></div><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column collection-tile" data-content-type="column" data-appearance="full-height" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/Collection-Celebration.jpg}}\&quot;}" data-element="main" data-pb-style="U5RT3UN"><div data-content-type="text" data-appearance="default" data-element="main"><p>Share Celebrations</p></div><div data-content-type="buttons" data-appearance="inline" data-same-width="false" data-element="main"><div class="btn-white-outline" data-content-type="button-item" data-appearance="default" data-element="main" data-pb-style="R3AG9YX"><a class="pagebuilder-button-primary" href="{{widget type=\'Magento\Catalog\Block\Category\Widget\Link\' id_path=\'category/164\' template=\'Magento_PageBuilder::widget/link_href.phtml\' type_name=\'Catalog Category Link\' }}" target="" data-link-type="category" data-element="link" data-pb-style="MMMQO7C"><span data-element="link_text">Shop collection</span></a></div></div></div><div class="pagebuilder-column collection-tile" data-content-type="column" data-appearance="full-height" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/Collection-Seasonal.jpg}}\&quot;}" data-element="main" data-pb-style="E07TI9F"><div data-content-type="text" data-appearance="default" data-element="main"><p>Seasonal</p></div><div data-content-type="buttons" data-appearance="inline" data-same-width="false" data-element="main"><div class="btn-white-outline" data-content-type="button-item" data-appearance="default" data-element="main" data-pb-style="S7P39GR"><a class="pagebuilder-button-primary" href="{{widget type=\'Magento\Catalog\Block\Category\Widget\Link\' id_path=\'category/167\' template=\'Magento_PageBuilder::widget/link_href.phtml\' type_name=\'Catalog Category Link\' }}" target="" data-link-type="category" data-element="link" data-pb-style="U3QANS2"><span data-element="link_text">Shop collection</span></a></div></div></div></div></div></div><div data-content-type="block" data-appearance="default" data-element="main" data-pb-style="G3R9CMG">{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="257" type_name="CMS Static Block"}}</div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="XN1Y71G"><div data-content-type="html" data-appearance="default" data-element="main">{{block class="Magento\Framework\View\Element\Template" template="Magento_Cms::cms-account-create.phtml" title="Stay up to date" para="Subscribe to newsletter, order your creation and manage your orders. Create your account today."}} </div></div></div>
                '
            ]
        ];
    }

    protected function getEuropeBlocks()
    {
        return [
            [
                'title' => 'Europe Mega Menu Collections',
                'identifier' => 'eu_mega_menu_collections',
                'content' => '<style>#html-body [data-pb-style=DVO64RI],#html-body [data-pb-style=FQ13Y3G],#html-body [data-pb-style=LG8K0C7],#html-body [data-pb-style=V6FD68R]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=FQ13Y3G],#html-body [data-pb-style=LG8K0C7],#html-body [data-pb-style=V6FD68R]{width:25%;align-self:stretch}#html-body [data-pb-style=FQ13Y3G],#html-body [data-pb-style=V6FD68R]{width:16.6667%}#html-body [data-pb-style=FQ13Y3G]{width:58.3333%}#html-body [data-pb-style=WX37UV9]{border-style:none}#html-body [data-pb-style=E7XLMRL],#html-body [data-pb-style=VP3DO0S]{max-width:100%;height:auto}#html-body [data-pb-style=RF4M5BL]{display:inline-block}#html-body [data-pb-style=A6D06PP]{text-align:center}@media only screen and (max-width: 768px) { #html-body [data-pb-style=WX37UV9]{border-style:none} }</style><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="DVO64RI"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="LG8K0C7"><div data-content-type="html" data-appearance="default" data-element="main">&lt;div class="title"&gt;
 &lt;span&gt;TRENDING NOW&lt;/span&gt;
 &lt;/div&gt;</div><div data-content-type="text" data-appearance="default" data-element="main"><ul>
 <li><a tabindex="0" href="#">Christmas</a></li>
 <li><a tabindex="0" href="#">New Year</a></li>
 <li><a href="#">Carnival</a></li>
 </ul></div></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="V6FD68R"></div><div class="pagebuilder-column mega-menu-featured" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="FQ13Y3G"><figure data-content-type="image" data-appearance="full-width" data-element="main" data-pb-style="WX37UV9"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/europe/nav/placeholder-245x180.png}}" alt="" title="" data-element="desktop_image" data-pb-style="E7XLMRL"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/europe/nav/placeholder-245x150.png}}" alt="" title="" data-element="mobile_image" data-pb-style="VP3DO0S"></figure><div data-content-type="text" data-appearance="default" data-element="main"><p><strong>Are you getting ready for Christmas?</strong></p>
 <p>See our favorite suggestion for celebrating the Christmas spirit</p></div><div data-content-type="buttons" data-appearance="inline" data-same-width="false" data-element="main"><div data-content-type="button-item" data-appearance="default" data-element="main" data-pb-style="RF4M5BL"><a class="pagebuilder-button-link" href="#" target="" data-link-type="default" data-element="link" data-pb-style="A6D06PP"><span data-element="link_text">Shop collection</span></a></div></div></div></div></div></div>'
            ],
            [
                'title' => 'Europe Global Banner',
                'identifier' => 'eu_global_banner',
                'content' => '<style>#html-body [data-pb-style=V0G5TMR]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;text-align:center}#html-body [data-pb-style=UTT44C0]{text-align:left}#html-body [data-pb-style=C3UOM9G],#html-body [data-pb-style=G7TA33O],#html-body [data-pb-style=UTT44C0]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;width:33.3333%;align-self:stretch}#html-body [data-pb-style=C3UOM9G]{text-align:right}</style><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="V0G5TMR"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="UTT44C0"><div data-content-type="text" data-appearance="default" data-element="main"><p>Free shipping over 15€</p></div></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="G7TA33O"><div data-content-type="text" data-appearance="default" data-element="main"><p>2-3 days delivery</p></div></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="C3UOM9G"><div data-content-type="text" data-appearance="default" data-element="main"><p>Approved orders shipped the same day*</p></div></div></div></div></div>'
            ],
            [
                'title' => 'Europe Footer Social',
                'identifier' => 'eu_footer_social',
                'content' => '<div data-content-type="html" data-appearance="default" data-element="main">&lt;div class="flex"&gt;
 &lt;img class="footer-logo" src="{{view url="images/logo-white.svg"}}" alt="Coca-Cola" /&gt;
 &lt;ul&gt;
 &lt;li&gt;&lt;a href="#" target="_blank"&gt;&lt;img src="{{view url="images/icons/icon-facebook.svg"}}" alt="Facebook" /&gt;&lt;/a&gt;&lt;/li&gt;
 &lt;li&gt;&lt;a href="#" target="_blank"&gt;&lt;img src="{{view url="images/icons/icon-instagram.svg"}}" alt="Instagram" /&gt;&lt;/a&gt;&lt;/li&gt;
 &lt;li&gt;&lt;a href="#" target="_blank"&gt;&lt;img src="{{view url="images/icons/icon-twitter.svg"}}" alt="Twitter" /&gt;&lt;/a&gt;&lt;/li&gt;
 &lt;/ul&gt;
 &lt;/div&gt;</div><div data-content-type="html" data-appearance="default" data-element="main">&lt;style&gt;
 #switcher-store{display:inline-block !important}
 &lt;/style&gt;</div>'
            ],
            [
                'title' => 'Europe Looking for something custom',
                'identifier' => 'eu_looking_something_custom',
                'content' => '<style>#html-body [data-pb-style=ICDPX1M],#html-body [data-pb-style=LFWIPK4],#html-body [data-pb-style=U0O234L]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=U0O234L]{width:100%;align-self:stretch}#html-body [data-pb-style=QSTP44F]{display:inline-block}#html-body [data-pb-style=HPLX9VO]{text-align:center}#html-body [data-pb-style=F8O1KPN]{border-style:none}#html-body [data-pb-style=EV9YIDS],#html-body [data-pb-style=IG7G0FQ]{max-width:100%;height:auto}@media only screen and (max-width: 768px) { #html-body [data-pb-style=F8O1KPN]{border-style:none} }</style><div data-content-type="row" data-appearance="contained" data-element="main"><div class="looking-for-custom-title" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="ICDPX1M"><h2 data-content-type="heading" data-appearance="default" data-element="main">Looking for something custom?</h2></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="something-custom-banner" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="LFWIPK4"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column text-block" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="U0O234L"><div data-content-type="text" data-appearance="default" data-element="main"><p>Create your own label and design your own unique and memorable beverage to enjoy yourself or as a special gift for someone.</p></div><div data-content-type="buttons" data-appearance="inline" data-same-width="false" data-element="main"><div data-content-type="button-item" data-appearance="default" data-element="main" data-pb-style="QSTP44F"><a class="pagebuilder-button-primary" href="{{widget type=\'Magento\Catalog\Block\Product\Widget\Link\' id_path=\'product/3564\' template=\'Magento_PageBuilder::widget/link_href.phtml\' type_name=\'Catalog Product Link\' }}" target="" data-link-type="product" data-element="link" data-pb-style="HPLX9VO"><span data-element="link_text">Create your design</span></a></div></div></div></div><figure class="something-custom-image" data-content-type="image" data-appearance="full-width" data-element="main" data-pb-style="F8O1KPN"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/europe/home/something-custom-bottles.png}}" alt="" title="" data-element="desktop_image" data-pb-style="EV9YIDS"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/europe/home/something-custom-bottles.png}}" alt="" title="" data-element="mobile_image" data-pb-style="IG7G0FQ"></figure></div></div>'
            ],
            [
                'title' => 'Europe How It Works',
                'identifier' => 'eu_how_it_works',
                'content' => '<style>#html-body [data-pb-style=YYIWOXO]{justify-content:flex-start;display:flex;flex-direction:column;background-color:#000;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;padding-left:0;padding-right:0}#html-body [data-pb-style=O5AEYRC]{margin-bottom:20px;padding-left:15px;padding-right:15px}#html-body [data-pb-style=X7TM7PK]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;width:28.5714%;padding:15px;align-self:stretch}#html-body [data-pb-style=CXH1F0N]{width:7.14286%}#html-body [data-pb-style=CHJ9NQF],#html-body [data-pb-style=CXH1F0N],#html-body [data-pb-style=JF4SQIC],#html-body [data-pb-style=LUD7EJF]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;align-self:stretch}#html-body [data-pb-style=LUD7EJF]{width:28.5714%;padding:15px}#html-body [data-pb-style=CHJ9NQF],#html-body [data-pb-style=JF4SQIC]{width:7.14286%}#html-body [data-pb-style=JF4SQIC]{width:28.5714%;padding:15px}</style><div class="how-it-works" data-content-type="row" data-appearance="full-width" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="main" data-pb-style="YYIWOXO"><div class="row-full-width-inner" data-element="inner"><div data-content-type="text" data-appearance="default" data-element="main" data-pb-style="O5AEYRC"><p><span style="color: #ffffff;">HOW IT WORKS</span></p></div><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="14" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="X7TM7PK"><div class="number-outline" data-content-type="text" data-appearance="default" data-element="main"><p>01</p></div><div data-content-type="text" data-appearance="default" data-element="main"><p><span style="font-size: 24px;"><strong><span id="U6L0VVV" style="color: #ffffff;">Choose your product</span></strong></span></p>
 <p><span style="color: #ffffff; font-size: 18px;">Choose your favorite beverage and package to base your creation on</span></p></div></div><div class="pagebuilder-column desktop-only spacer" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="CXH1F0N"></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="LUD7EJF"><div class="number-outline" data-content-type="text" data-appearance="default" data-element="main"><p>02</p></div><div data-content-type="text" data-appearance="default" data-element="main"><p><span style="font-size: 24px;"><strong><span id="U6L0VVV" style="color: #ffffff;">Design your creation</span></strong></span></p>
 <p><span style="color: #ffffff; font-size: 18px;">Personalise your label and add a message or choose between our inspiration</span></p></div></div><div class="pagebuilder-column desktop-only spacer" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="CHJ9NQF"></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="JF4SQIC"><div class="number-outline" data-content-type="text" data-appearance="default" data-element="main"><p>03</p></div><div data-content-type="text" data-appearance="default" data-element="main"><p><span style="font-size: 24px;"><strong><span id="U6L0VVV" style="color: #ffffff;">Enjoy a unique product</span></strong></span></p>
 <p><span style="color: #ffffff; font-size: 18px;">Our unique products is made to order which guarantees a special and unique order.</span></p></div></div></div></div></div>'
            ],
            [
                'title' => 'Europe Footer Top',
                'identifier' => 'eu_footer_top',
                'content' => '<style>#html-body [data-pb-style=W4PW4TH]{justify-content:flex-start;display:flex;flex-direction:column;background-color:#f5f5f5;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=BVLI451],#html-body [data-pb-style=L2HBJWK],#html-body [data-pb-style=M76SX4M],#html-body [data-pb-style=QE3UO3E]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;width:25%;padding:20px 40px;align-self:stretch}</style><div class="footer-top-content" data-content-type="row" data-appearance="full-width" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="main" data-pb-style="W4PW4TH"><div class="row-full-width-inner" data-element="inner"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="L2HBJWK"><div data-content-type="html" data-appearance="default" data-element="main">&lt;img src="{{view url="images/icons/icon-background-delivery-black.svg"}}" alt="" /&gt;</div><div data-content-type="text" data-appearance="default" data-element="main"><p>Every approved design placed before 15h ships the same day</p></div></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="M76SX4M"><div data-content-type="html" data-appearance="default" data-element="main">&lt;img src="{{view url="images/icons/icon-background-locked.svg"}}" alt="" /&gt;</div><div data-content-type="text" data-appearance="default" data-element="main"><p>Payment processed over an secure SSL encrypted connection</p></div></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="QE3UO3E"><div data-content-type="html" data-appearance="default" data-element="main">&lt;img src="{{view url="images/icons/icon-background-phone.svg"}}" alt="" /&gt;</div><div data-content-type="text" data-appearance="default" data-element="main"><p>Contact us via <a tabindex="0" href="mailto:help@d2c.com">help@d2c.com</a> or through the <a class="contact-modal-trigger" tabindex="0" href="#">contact form</a></p></div></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="BVLI451"><div data-content-type="html" data-appearance="default" data-element="main">&lt;img src="{{view url="images/icons/icon-background-sustainability-black.svg"}}" alt="" /&gt;</div><div data-content-type="text" data-appearance="default" data-element="main"><p>Packaging and products made from sustainable material.</p></div></div></div></div></div>'
            ],
            [
                'title' => 'Europe Footer Links',
                'identifier' => 'eu_footer_links',
                'content' => '<div data-content-type="html" data-appearance="default" data-element="main">&lt;div class="flex"&gt;
 &lt;ul class="col col-12 col-md-3 footer-links"&gt;
 &lt;li&gt;&lt;a href="contact-us"&gt;Contact Us&lt;/a&gt;&lt;/li&gt;
 &lt;/ul&gt;
 &lt;ul class="col col-12 col-md-9 footer-links footer-links-2"&gt;
 &lt;li&gt;&lt;a href="terms-of-use"&gt;Site Terms of Use&lt;/a&gt;&lt;/li&gt;
 &lt;li&gt;&lt;a href="cookie-policy"&gt;Coca-Cola Cookies Policy&lt;/a&gt;&lt;/li&gt;
 &lt;li&gt;&lt;a href="privacy-policy"&gt;Coca-Cola Privacy Policy&lt;/a&gt;&lt;/li&gt;
 &lt;/ul&gt;
 &lt;/div&gt;
 </div>'
            ],
            [
                'title' => 'Europe Additional Nav',
                'identifier' => 'eu_nav_additional',
                'content' => '<div data-content-type="html" data-appearance="default" data-element="main">&lt;li class="level0 nav-cms level-top ui-menu-item" role="presentation"&gt;     &lt;a href="{{store _direct="personalized-product.html"}}" class="level-top ui-corner-all" tabindex="-1" role="menuitem"&gt;&lt;span&gt;{{trans "Create your own"}}&lt;/span&gt;&lt;/a&gt;     &lt;/li&gt; </div>'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
}
