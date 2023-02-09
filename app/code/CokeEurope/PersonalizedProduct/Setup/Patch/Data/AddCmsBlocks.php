<?php

namespace CokeEurope\PersonalizedProduct\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Store\Model\StoreManagerInterface;

class AddCmsBlocks implements DataPatchInterface, PatchRevertableInterface
{
    const BLOCK_PENDING_APPROVAL = 'personalized_product_pending_approval';

    private BlockFactory $blockFactory;
    private StoreManagerInterface $storeManager;

    /**
     * @param BlockFactory $blockFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        BlockFactory $blockFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->blockFactory = $blockFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $blockData = [
            'title' => 'Pending Approval',
            'identifier' => self::BLOCK_PENDING_APPROVAL,
            'content' => '<style>#html-body [data-pb-style=BHENST5]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}</style><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="BHENST5"><div data-content-type="text" data-appearance="default" data-element="main"><p><span style="font-size: 18px;"><strong>Pending Approval</strong></span></p><p><span style="font-size: 16px;">This message needs to be approved before it is shipped. A process that will delay your full order by approximately 48 hours. If you want your order as soon as possible please choose an already approved message.</span></p><p><span style="font-size: 16px;">If your message is denied you will receive a full refund of your order.</span></p><p><span style="color: #000000;"><a style="color: #000000;" title="More Information" href="#"><span style="font-size: 16px;">More Information</span></a></span></p></div></div></div>',
            'stores' => [0],
            'is_active' => 1,
        ];

        $pendingApprovalBlock = $this->blockFactory->create()->load($blockData['identifier'], 'identifier');

        /**
         * Create the block if it does not exists, otherwise update the content
         */
        if (!$pendingApprovalBlock->getId()) {
            $pendingApprovalBlock->setData($blockData)->save();
        } else {
            $pendingApprovalBlock->setContent($blockData['content'])->save();
        }
    }

    /**
     * Delete the block when module is uninstalled
     */
    public function revert()
    {
        $pendingApprovalBlock = $this->blockFactory->create()->load(self::BLOCK_PENDING_APPROVAL, 'identifier');
        if($pendingApprovalBlock->getId()) {
            $pendingApprovalBlock->delete();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
