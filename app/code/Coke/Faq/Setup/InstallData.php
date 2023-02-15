<?php

namespace Coke\Faq\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
 
    /**
     * @var \Coke\Faq\Api\CategoryRepositoryInterface 
     */
    protected $categoryRepository;
    
    /**
     * @var \Coke\Faq\Api\ItemRepositoryInterface 
     */
    protected $itemRepository;
    
    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Coke\Faq\Api\CategoryRepositoryInterface $categoryRepository,
        \Coke\Faq\Api\ItemRepositoryInterface $itemRepository    
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->categoryRepository = $categoryRepository;
        $this->itemRepository = $itemRepository;
    }    
    
    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        // Categories dummy data
        $categories = [
            [
                'name' => 'FAQ Category 1',
                'is_active' => 1,
                'sort_order' => 2,
                'url_key' => 'faqcategory1',
                'store_id' => 1
            ],
            [
                'name' => 'FAQ Category 2',
                'is_active' => 0,
                'sort_order' => 2,
                'url_key' => 'faqcategory2',
                'store_id' => 1
            ],
            [
                'name' => 'FAQ Category 3',
                'is_active' => 1,
                'sort_order' => 1,
                'url_key' => 'faqcategory3',
                'store_id' => 1
            ],
            [
                'name' => 'FAQ Category 4',
                'is_active' => 1,
                'sort_order' => 10,
                'url_key' => 'faqcategory4',
                'store_id' => 1
            ],
        ];
        
        foreach ($categories as $category) {
            $model = $this->categoryRepository->create();
            $model->addData($category);
            $this->categoryRepository->save($model);
            unset($model);
        }
        
        // FAQ Items dummy data
        $faqItems = [
            [
                'title' => 'FAQ Item 1',
                'description' => 'This is the FAQ item #1',
                'faq_category_id' => 1,
                'url_key' => 'faqitem1',
                'sort_order' => 2,
                'is_active' => 1
            ],
            [
                'title' => 'FAQ Item 2',
                'description' => 'This is the FAQ item #2',
                'faq_category_id' => 2,
                'url_key' => 'faqitem2',
                'sort_order' => 1,
                'is_active' => 1
            ],
            [
                'title' => 'FAQ Item 3',
                'description' => 'This is the FAQ item #3',
                'faq_category_id' => 3,
                'url_key' => 'faqitem3',
                'sort_order' => 1,
                'is_active' => 1
            ],
            [
                'title' => 'FAQ Item 4',
                'description' => 'This is the FAQ item #4',
                'faq_category_id' => 1,
                'url_key' => 'faqitem4',
                'sort_order' => 1,
                'is_active' => 1
            ],
            [
                'title' => 'FAQ Item 5',
                'description' => 'This is the FAQ item #5',
                'faq_category_id' => 1,
                'url_key' => 'faqitem5',
                'sort_order' => 2,
                'is_active' => 0
            ],
            [
                'title' => 'FAQ Item 6',
                'description' => 'This is the FAQ item #6',
                'faq_category_id' => 1,
                'url_key' => 'faqitem6',
                'sort_order' => 10,
                'is_active' => 1
            ],
        ];
        
        foreach ($faqItems as $item) {
            $model = $this->itemRepository->create();
            $model->addData($item);
            $this->itemRepository->save($model);
            unset($model);
        }
    }
}