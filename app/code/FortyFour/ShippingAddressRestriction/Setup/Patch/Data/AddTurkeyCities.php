<?php

namespace FortyFour\ShippingAddressRestriction\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;

class AddTurkeyCities implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var WriterInterface
     */
    private $writer;
    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * UpdateTopoChicoGreeceHomePage constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param WriterInterface $writer
     * @param WebsiteRepositoryInterface $websiteRepository
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        WriterInterface $writer,
        WebsiteRepositoryInterface $websiteRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->writer = $writer;
        $this->websiteRepository = $websiteRepository;
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

        $turkeyWebsite = $this->websiteRepository->get('olnb_turkey');
        $configs = [
            'shipping_address_restriction/general/enabled' => 1,
            'shipping_address_restriction/city_region/apply_to_region' => 1,
            'shipping_address_restriction/city_region/allowed_city_region_list' => '{"_val_4":{"city":"Adana","region":""},"_val_5":{"city":"Adiyaman","region":""},"_val_6":{"city":"Afyonkarahisar","region":""},"_val_7":{"city":"Au011fru0131","region":""},"_val_8":{"city":"Aksaray","region":""},"_val_9":{"city":"Amasya","region":""},"_val_10":{"city":"Ankara","region":""},"_val_11":{"city":"Antalya","region":""},"_val_12":{"city":"Ardahan","region":""},"_val_13":{"city":"Artvin","region":""},"_val_14":{"city":"Aydu0131n","region":""},"_val_15":{"city":"Balu0131kesir","region":""},"_val_16":{"city":"Bartu0131n","region":""},"_val_17":{"city":"Batman","region":""},"_val_18":{"city":"Bayburt","region":""},"_val_19":{"city":"Bilecik","region":""},"_val_20":{"city":"Bingu00f6l","region":""},"_val_21":{"city":"Bitlis","region":""},"_val_22":{"city":"Bolu","region":""},"_val_23":{"city":"Burdur","region":""},"_val_24":{"city":"Bursa","region":""},"_val_25":{"city":"u00c7anakkale","region":""},"_val_26":{"city":"u00c7anku0131ru0131","region":""},"_val_27":{"city":"u00c7orum","region":""},"_val_28":{"city":"Denizli","region":""},"_val_29":{"city":"Diyarbaku0131r","region":""},"_val_30":{"city":"Du00fczce","region":""},"_val_31":{"city":"Edirne","region":""},"_val_32":{"city":"Elazu0131u011f","region":""},"_val_33":{"city":"Erzincan","region":""},"_val_34":{"city":"Erzurum","region":""},"_val_35":{"city":"Eskiu015fehir","region":""},"_val_36":{"city":"Gaziantep","region":""},"_val_37":{"city":"Giresun","region":""},"_val_38":{"city":"Gu00fcmu00fcu015fhane","region":""},"_val_39":{"city":"Hakkari","region":""},"_val_40":{"city":"Hatay","region":""},"_val_41":{"city":"Iu011fdu0131r","region":""},"_val_42":{"city":"Isparta","region":""},"_val_43":{"city":"u0130stanbul","region":""},"_val_44":{"city":"u0130zmir","region":""},"_val_45":{"city":"Kahramanmarau015f","region":""},"_val_46":{"city":"Karabu00fck","region":""},"_val_47":{"city":"Karaman","region":""},"_val_48":{"city":"Kars","region":""},"_val_49":{"city":"Kastamonu","region":""},"_val_50":{"city":"Kayseri","region":""},"_val_51":{"city":"Kilis","region":""},"_val_52":{"city":"Ku0131ru0131kkale","region":""},"_val_53":{"city":"Ku0131rklareli","region":""},"_val_54":{"city":"Ku0131ru015fehir","region":""},"_val_55":{"city":"Kocaeli","region":""},"_val_56":{"city":"Konya","region":""},"_val_57":{"city":"Ku00fctahya","region":""},"_val_58":{"city":"Malatya","region":""},"_val_59":{"city":"Manisa","region":""},"_val_60":{"city":"Mardin","region":""},"_val_61":{"city":"Mersin","region":""},"_val_62":{"city":"Muu011fla","region":""},"_val_63":{"city":"Muu015f","region":""},"_val_64":{"city":"Nevu015fehir","region":""},"_val_65":{"city":"Niu011fde","region":""},"_val_66":{"city":"Ordu","region":""},"_val_67":{"city":"Osmaniye","region":""},"_val_68":{"city":"Rize","region":""},"_val_69":{"city":"Sakarya","region":""},"_val_70":{"city":"Samsun","region":""},"_val_71":{"city":"u015eanlu0131urfa","region":""},"_val_72":{"city":"Siirt","region":""},"_val_73":{"city":"Sinop","region":""},"_val_74":{"city":"u015eu0131rnak","region":""},"_val_75":{"city":"Sivas","region":""},"_val_76":{"city":"Tekirdau011f","region":""},"_val_77":{"city":"Tokat","region":""},"_val_78":{"city":"Trabzon","region":""},"_val_79":{"city":"Tunceli","region":""},"_val_80":{"city":"Uu015fak","region":""},"_val_81":{"city":"Van","region":""},"_val_82":{"city":"Yalova","region":""},"_val_83":{"city":"Yozgat","region":""},"_val_84":{"city":"Zonguldak","region":""}}'
        ];
        foreach ($configs as $path => $value) {
            $this->writer->save($path,
                $value,
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
                $turkeyWebsite->getId()
            );
        }

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
