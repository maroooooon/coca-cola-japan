<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdatePasswordDisclaimer implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var ContentUpgrader
     */
    private $contentUpgrader;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepositoryInterface;

    /**
     * UpdateHomePage constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ContentUpgrader $contentUpgrader
     * @param BlockFactory $blockFactory
     * @param BlockRepositoryInterface $blockRepositoryInterface
     * @param Data $helper
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ContentUpgrader $contentUpgrader,
        BlockFactory $blockFactory,
        BlockRepositoryInterface $blockRepositoryInterface,
        Data $helper
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->contentUpgrader = $contentUpgrader;
        $this->blockFactory = $blockFactory;
        $this->blockRepositoryInterface = $blockRepositoryInterface;
        $this->helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return $this|DataPatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $storeEn = $this->helper->getEgyptEnglishStore();
        $storeAr = $this->helper->getEgyptArabicStore();

        $this->contentUpgrader->upgradeBlocks([
            'password-strength-disclaimer' => [
                'stores' => [$storeEn->getId()]
            ]
        ]);

        /** @var BlockInterface $block */
        $block = $this->blockFactory->create();
        $content = <<<END
<div class="password-strength-disclaimer">
    <p>يجب أن تتكون كلمة المرور الخاصة بك من 8 أحرف على الأقل ويجب أن تحتوي على واحد من كلاً من حرف كبير وحرف صغير ورقم على الأقل.</p>
</div>
END;
        $block->setIdentifier('password-strength-disclaimer');
        $block->setContent($content);
        $block->setTitle('Password Strength Disclaimer Arabic');
        $block->setData('stores', [$storeAr->getId()]);
        $this->blockRepositoryInterface->save($block);

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
