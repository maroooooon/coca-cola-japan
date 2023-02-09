<?php

namespace FortyFour\Directory\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddGreeceRegions implements DataPatchInterface
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

        $regions = [
            ["Έβρου", "Evros", "GR-ES", "GR"],
            ["Ροδόπης", "Rodopi", "GR-RD", "GR"],
            ["Ξάνθης", "Xanthi", "GR-XN", "GR"],
            ["Δράμας", "Drama", "GR-DR", "GR"],
            ["Καβάλας", "Kavala", "GR-KV", "GR"],
            ["Θεσσαλονίκης", "Thessaloniki", "GR-TN", "GR"],
            ["Χαλκιδικής", "Chalkidiki", "GR-CH", "GR"],
            ["Ημαθίας", "Imathia", "GR-IM", "GR"],
            ["Κιλκίς", "Kilkis", "GR-KK", "GR"],
            ["Πέλλας", "Pella", "GR-PL", "GR"],
            ["Πιερίας", "Pieria", "GR-PI", "GR"],
            ["Σερρών", "Serres", "GR-SE", "GR"],
            ["Κοζάνης", "Kozani", "GR-KZ", "GR"],
            ["Φλώρινας", "Florina", "GR-FL", "GR"],
            ["Γρεβενών", "Grevena", "GR-GR", "GR"],
            ["Καστοριάς", "Kastoria", "GR-KS", "GR"],
            ["Ιωαννίνων", "Ioannina", "GR-IO", "GR"],
            ["Άρτας", "Arta", "GR-AR", "GR"],
            ["Πρέβεζας", "Preveza", "GR-PV", "GR"],
            ["Θεσπρωτίας", "Thesprwtia", "GR-TP", "GR"],
            ["Λάρισας", "Larisa", "GR-LR", "GR"],
            ["Καρδίτσας", "Karditsa", "GR-KT", "GR"],
            ["Μαγνησίας", "Magnesia", "GR-MG", "GR"],
            ["Τρικάλων", "Trikala", "GR-TR", "GR"],
            ["Βοιωτίας", "Voiotia", "GR-VO", "GR"],
            ["Ευβοίας", "Evoia", "GR-EVO", "GR"],
            ["Ευρυτανίας", "Evritania", "GR-ET", "GR"],
            ["Φωκίδας", "Fokida", "GR-FOK", "GR"],
            ["Φθιώτιδας", "Fthiotida", "GR-FTH", "GR"],
            ["Κεφαλληνίας", "Kefalinia", "GR-KF", "GR"],
            ["Κέρκυρας", "Kerkura", "GR-KER", "GR"],
            ["Λευκάδας", "Leukada", "GR-LEU", "GR"],
            ["Ζακύνθου", "Zakynthos", "GR-ZK", "GR"],
            ["Αχαΐας", "Ahaia", "GR-AH", "GR"],
            ["Ηλείας", "Hlia", "GR-HIL", "GR"],
            ["Αιτωλοακαρνανίας", "Aitoloakarnania", "GR-AI", "GR"],
            ["Αρκαδίας", "Arkadia", "GR-AK", "GR"],
            ["Αργολίδας", "Argolida", "GR-AG", "GR"],
            ["Κορινθίας", "Korinthia", "GR-KOR", "GR"],
            ["Λακωνίας", "Lakonia", "GR-LK", "GR"],
            ["Μεσσηνίας", "Messinia", "GR-MS", "GR"],
            ["Αττικής", "Attica", "GR-AT", "GR"],
            ["Χίου", "Chios", "GR-CHI", "GR"],
            ["Λέσβου", "Lesvos", "GR-LS", "GR"],
            ["Σάμου", "Samos", "GR-SM", "GR"],
            ["Κυκλάδων", "Kuklades", "GR-KL", "GR"],
            ["Δωδεκανήσου", "Dodekanisa", "GR-DO", "GR"],
            ["Ηρακλείου", "Hraklio", "GR-HRA", "GR"],
            ["Χανίων", "Chania", "GR-CHA", "GR"],
            ["Λασιθίου", "Lasithi", "GR-LT", "GR"],
            ["Ρεθύμνης", "Rethumno", "GR-RMO", "GR"],
            ["Άγιο Όρος", "Agio Oros", "GR-MA", "GR"]
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
                ['locale' => 'en_US', 'region_id' => $regionId, 'name' => $region[1]]
            );

            $this->moduleDataSetup->getConnection()->insert(
                $this->moduleDataSetup->getTable('directory_country_region_name'),
                ['locale' => 'el_GR', 'region_id' => $regionId, 'name' => $region[0]]
            );
        }

        return $this;
    }
}
