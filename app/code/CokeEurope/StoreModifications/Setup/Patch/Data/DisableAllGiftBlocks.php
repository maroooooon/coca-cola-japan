<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use Magento\Cms\Model\Block;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\BlockRepository;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class DisableAllGiftBlocks implements DataPatchInterface, PatchRevertableInterface
{
	public const UPDATE_GIFT_BLOCK_IDENTIFIER = "eu_megamenu_gift_ideas";
	public const UPDATE_NAV_BLOCK_IDENTIFIER = "eu_nav_additional";
	public const CMS_CONTENT_WITH_GIFT_NAV = '<div data-content-type="html" data-appearance="default" data-element="main">&lt;li class="level0 nav-cms level-top ui-menu-item" role="presentation"&gt;
    &lt;a href="{{store _direct="personalized-product.html"}}" class="level-top ui-corner-all" tabindex="-1" role="menuitem"&gt;&lt;span&gt;{{trans "Create your own"}}&lt;/span&gt;&lt;/a&gt;
    &lt;/li&gt;
    &lt;li class="level0 nav-cms level-top ui-menu-item parent" role="presentation"&gt;
    &lt;a href="{{store _direct="gift-ideas.html"}}" class="level-top ui-corner-all" tabindex="-1" role="menuitem"&gt;&lt;span&gt;{{trans "Gift ideas"}}&lt;/span&gt;&lt;/a&gt;
    &lt;div class="mega-menu-container submenu ui-menu ui-widget ui-widget-content ui-corner-all" role="menu" aria-expanded="false" aria-hidden="true"&gt;
    {{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="eu_megamenu_gift_ideas"}}
    &lt;/div&gt;
    &lt;/li&gt;
</div>
';
	public const CMS_CONTENT_WITHOUT_GIFT_NAV = '<div data-content-type="html" data-appearance="default" data-element="main">&lt;li class="level0 nav-cms level-top ui-menu-item" role="presentation"&gt;
    &lt;a href="{{store _direct="personalized-product.html"}}" class="level-top ui-corner-all" tabindex="-1" role="menuitem"&gt;&lt;span&gt;{{trans "Create your own"}}&lt;/span&gt;&lt;/a&gt;
    &lt;/li&gt;
</div>
';
	
	private BlockFactory $blockFactory;
	private BlockRepository $blockRepository;
	private Filter $filter;
	private FilterGroup $filterGroup;
	private SearchCriteriaInterface $criteria;
	
	/**
	 * @param BlockFactory $blockFactory
	 * @param BlockRepository $blockRepository
	 * @param Filter $filter
	 * @param FilterGroup $filterGroup
	 * @param SearchCriteriaInterface $criteria
	 */
	public function __construct(
		BlockFactory $blockFactory,
		BlockRepository $blockRepository,
		Filter $filter,
		FilterGroup $filterGroup,
		SearchCriteriaInterface $criteria
	)
	{
		$this->blockFactory = $blockFactory;
		$this->blockRepository = $blockRepository;
		$this->filter = $filter;
		$this->filterGroup = $filterGroup;
		$this->criteria = $criteria;
	}

    /**
     * It updates the cms block.
     */
    public function apply()
    {
		$this->updateCmsBlock(self::UPDATE_GIFT_BLOCK_IDENTIFIER, null, false);
		$this->updateCmsBlock(self::UPDATE_NAV_BLOCK_IDENTIFIER, self::CMS_CONTENT_WITHOUT_GIFT_NAV);
    }
	
	/**
	 * It updates the CMS block with the content from the file
	 */
	public function revert()
	{
		$this->updateCmsBlock(self::UPDATE_GIFT_BLOCK_IDENTIFIER, null, true);
		$this->updateCmsBlock(self::UPDATE_NAV_BLOCK_IDENTIFIER, self::CMS_CONTENT_WITH_GIFT_NAV);
	}
	

	public function updateCmsBlock(string $identifier, string $content = null, bool $status = null): void
	{
		$filter = $this->filter
			->setField('identifier')
			->setValue($identifier)
			->setConditionType('eq');
		$filterGroup = $this->filterGroup->setFilters([$filter]);
		$criteria = $this->criteria->setFilterGroups([$filterGroup]);
		$blocks = $this->blockRepository->getList($criteria);
		
		/** @var Block $block */
		foreach ($blocks->getItems() as $id => $item) {
			$updateBlock = $this->blockFactory->create()->load($id);
			if (!$updateBlock->getId()) {
				continue;
			}
			
			if ($content) {
				$updateBlock->setContent($content);
			}
			if ($status) {
				$updateBlock->setIsActive($status);
			}
			
			$updateBlock->save();
		}
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	public static function getDependencies(): array
	{
		return [];
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getAliases(): array
	{
		return [];
	}
}
