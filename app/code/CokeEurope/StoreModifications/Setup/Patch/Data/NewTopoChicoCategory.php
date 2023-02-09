<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use \Magento\Catalog\Model\ResourceModel\Category;
use \Magento\Catalog\Setup\CategorySetup;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use \Magento\Framework\Setup\UpgradeDataInterface;
use \Magento\Framework\Setup\ModuleDataSetupInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class NewTopoChicoCategory implements DataPatchInterface
{
    private Category $categoryResourceModel;
    private CategorySetup $categorySetup;
    private State $appState;

    /**
     * @param Category $categoryResourceModel
     * @param CategorySetup $categorySetup
     */
    public function __construct(
        Category      $categoryResourceModel,
        CategorySetup $categorySetup,
        State $appState
    )
    {
        $this->categorySetup = $categorySetup;
        $this->categoryResourceModel = $categoryResourceModel;
        $this->appState = $appState;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function apply()
    {
        try {
            $this->appState->setAreaCode(Area::AREA_ADMINHTML);
        } catch (\Exception $e) {}

        $topoChicoCategory = $this->categorySetup->createCategory(
            [
                'data' => [
                    'parent_id' => 45,
                    'name' => 'Topo Chico',
                    'path' => '1/45',
                    'is_active' => 1,
                    'include_in_menu' => 1,
                ],
            ]
        );
        $this->categoryResourceModel->save($topoChicoCategory);
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}
