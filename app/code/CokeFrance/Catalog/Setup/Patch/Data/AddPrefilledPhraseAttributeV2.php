<?php

namespace CokeFrance\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Setup\CategorySetupFactory;

class AddPrefilledPhraseAttributeV2 implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * InstallAttributes constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    /**
     * @return DataPatchInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        try {
            $eavSetup->removeAttribute(Product::ENTITY, 'prefilled_phrase');
        } catch (\Exception $e) {
            // empty..
        }

        $eavSetup->addAttribute(
            Product::ENTITY,
            'prefilled_phrase',
            [
                'type' => 'int',
                'label' => 'Prefilled Phrase',
                'backend' => '',
                'input' => 'select',
                'source' => '',
                'required' => false,
                'sort_order' => 5,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'used_in_product_listing' => false,
                'visible_on_front' => false,
                'attribute_set_id' => 'Open Like Never Before',
                'option' => ['values' => [
                    "2021 Année réussie !",
                    "Félicitation ! Année réussie",
                    "2021 Année historique",
                    "Merci Belle année 2021",
                    "Bonne Année 2021",
                    "Quelle belle année !",
                    "Joyeux Noël !",
                    "2020 Année réussie !",
                    "Enfin ! en famille",
                    "Meilleurs Vœux 2021 !",
                    "Vivement 2021 !",
                    "2021 On repart !",
                    "Mon cœur",
                    "Ma reine",
                    "Je t'aime",
                    "Mon amour",
                    "Pour toujours",
                    "À nous deux",
                    "Mon ange",
                    "Mon chéri",
                    "Ma vie",
                    "Ma chérie",
                    "Mon homme",
                    "Ma femme",
                    "+1",
                    "1 an de plus !",
                    "18 ans !",
                    "Happy Birthday!",
                    "Joyeux anniversaire !",
                    "Toujours au top",
                    "Bon anniversaire !",
                    "Birthday girl",
                    "Birthday boy",
                    "Trentenaire !",
                    "Quadra !",
                    "Quinqua !",
                    "Family Time !",
                    "Enfin en famille !",
                    "Enfin réunis !",
                    "La plus belle famille !",
                    "Bienvenue dans la famille !",
                    "Bienvenu chez moi",
                    "Bienvenu chez nous",
                    "Nouvelle maison !",
                    "Félicitations !",
                    "Congratulations !",
                    "Jour de fête",
                    "Family forever !",
                    "Vive les mariés",
                    "Vive les mariées",
                    "Le marié",
                    "La mariée",
                    "Le témoin",
                    "La témoin",
                    "Best Man",
                    "Demoiselle d’honneur",
                    "Jour J",
                    "La table d'honneur",
                    "Famille des mariés",
                    "Famille des mariées",
                    "Le meilleur collègue",
                    "La meilleure collègue",
                    "Bienvenue",
                    "Stagiaire",
                    "Bonne retraite !",
                    "Bon vent !",
                    "Équipe de choc",
                    "Une pause ?",
                    "La boss",
                    "Le boss",
                    "Le retardataire",
                    "La retardataire",
                    "Champion",
                    "Championne",
                    "Meilleure équipe !",
                    "On a gagné !",
                    "Victoire !",
                    "Teammates",
                    "Meilleure coéquipière !",
                    "Meilleur coéquipier !",
                    "Capitaine",
                    "Coach",
                    "La passoire",
                    "Les Halles",
                    "Palais-Royal",
                    "Bonne-Nouvelle",
                    "Sentier",
                    "Les Archives",
                    "Temple",
                    "Hôtel de ville",
                    "Le Marais",
                    "Jussieu",
                    "Le Quartier Latin",
                    "Saint-Victor",
                    "Saint-Germain",
                    "Odéon",
                    "Saint-Sulpice",
                    "Le Champ-de-Mars",
                    "Les Invalides",
                    "Les Champs-Elysées",
                    "Madeleine",
                    "Chaussée-d ’Antin",
                    "Opéra",
                    "Pigalle",
                    "Rochechouart",
                    "Château d’Eau",
                    "Gare du Nord",
                    "Saint-Ambroise",
                    "Bastille",
                    "République",
                    "Bel-Air",
                    "Bercy",
                    "Croulebarbe",
                    "Maison Blanche",
                    "Olympiade",
                    "La Salpêtrière",
                    "Montparnasse",
                    "Le Petit-Montrouge",
                    "Plaisance",
                    "Cambronne",
                    "Javel",
                    "La Motte Picquet Grenelle",
                    "Auteuil",
                    "La Muette",
                    "Le Trocadéro",
                    "Batignolles",
                    "Épinette",
                    "Ternes",
                    "Clignancourt",
                    "La Goutte D’or",
                    "Montmartre",
                    "Jaurès",
                    "La Villette",
                    "Belleville",
                    "Charonne",
                    "Le Père-Lachaise",
                ]],
            ]
        );
    }
}