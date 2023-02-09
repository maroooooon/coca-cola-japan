<?php

namespace FortyFour\Directory\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddJapanRegionsV2 implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * AddGreeceRegions constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
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
     * @throws \Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        //Hokkaido already added
        $regions = [
            //["北海道", "Hokkaido", "JP-Hokkaido", "JP"],
            ["青森", "Aomori", "JP-Aomori", "JP"],
            ["岩手", "Iwate", "JP-Iwate", "JP"],
            ["宮城", "Miyagi", "JP-Miyagi", "JP"],
            ["秋田", "Akita", "JP-Akita", "JP"],
            ["山形", "Yamagata", "JP-Yamagata", "JP"],
            ["福島", "Fukushima", "JP-Fukushima", "JP"],
            ["茨城", "Ibaraki", "JP-Ibaraki", "JP"],
            ["栃木", "Tochigi", "JP-Tochigi", "JP"],
            ["群馬", "Gunma", "JP-Gunma", "JP"],
            ["埼玉", "Saitama", "JP-Saitama", "JP"],
            ["千葉", "Chiba", "JP-Chiba", "JP"],
            ["東京", "Tokyo", "JP-Tokyo", "JP"],
            ["神奈川", "Kanagawa", "JP-Kanagawa", "JP"],
            ["新潟", "Niigata", "JP-Niigata", "JP"],
            ["富山", "Toyama", "JP-Toyama", "JP"],
            ["石川", "Ishikawa", "JP-Ishikawa", "JP"],
            ["福井", "Fukui", "JP-Fukui", "JP"],
            ["山梨", "Yamanashi", "JP-Yamanashi", "JP"],
            ["長野", "Nagano", "JP-Nagano", "JP"],
            ["岐阜", "Gifu", "JP-Gifu", "JP"],
            ["静岡", "Shizuoka", "JP-Shizuoka", "JP"],
            ["愛知", "Aichi", "JP-Aichi", "JP"],
            ["三重", "Triple", "JP-Triple", "JP"],
            ["滋賀", "Shiga", "JP-Shiga", "JP"],
            ["京都", "Kyoto", "JP-Kyoto", "JP"],
            ["大阪", "Osaka", "JP-Osaka", "JP"],
            ["兵庫", "Hyogo", "JP-Hyogo", "JP"],
            ["奈良", "Nara", "JP-Nara", "JP"],
            ["和歌山", "Wakayama", "JP-Wakayama", "JP"],
            ["鳥取", "Tottori", "JP-Tottori", "JP"],
            ["島根", "Shimane", "JP-Shimane", "JP"],
            ["岡山", "Okayama", "JP-Okayama", "JP"],
            ["広島", "Hiroshima", "JP-Hiroshima", "JP"],
            ["山口", "Yamaguchi", "JP-Yamaguchi", "JP"],
            ["徳島", "Tokushima", "JP-Tokushima", "JP"],
            ["香川", "Kagawa", "JP-Kagawa", "JP"],
            ["愛媛", "Ehime", "JP-Ehime", "JP"],
            ["高知", "Kochi", "JP-Kochi", "JP"],
            ["福岡", "Fukuoka", "JP-Fukuoka", "JP"],
            ["佐賀", "Saga", "JP-Saga", "JP"],
            ["長崎", "Nagasaki", "JP-Nagasaki", "JP"],
            ["熊本", "Kumamoto", "JP-Kumamoto", "JP"],
            ["大分", "Oita", "JP-Oita", "JP"],
            ["宮崎", "Miyazaki", "JP-Miyazaki", "JP"],
            ["鹿児島", "Kagoshima", "JP-Kagoshima", "JP"],
            ["沖縄", "Okinawa", "JP-Okinawa", "JP"]
        ];

        $this->moduleDataSetup->getConnection()->endSetup();

        foreach ($regions as $region) {
            $bind = ['default_name' => $region[1], 'country_id' => $region[3], 'code' => $region[2]];
            $this->moduleDataSetup->getConnection()->insert(
                $this->moduleDataSetup->getTable('directory_country_region'),
                $bind
            );

            $regionId = $this->moduleDataSetup->getConnection()->lastInsertId(
                $this->moduleDataSetup->getTable('directory_country_region')
            );

            $this->moduleDataSetup->getConnection()->insert(
                $this->moduleDataSetup->getTable('directory_country_region_name'),
                ['locale' => 'ja_JP', 'region_id' => $regionId, 'name' => $region[0]]
            );
        }

        return $this;
    }
}
