<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;

class EnableTermsAndConditionsCheckoutCheckboxPatch implements DataPatchInterface
{
    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * @param WriterInterface $configWriter
     * @param WebsiteRepositoryInterface $websiteRepository
     */
    public function __construct(
        WriterInterface $configWriter,
        WebsiteRepositoryInterface $websiteRepository
    ) {
        $this->configWriter = $configWriter;
        $this->websiteRepository = $websiteRepository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $europeWebsite = $this->getEuropeWebsite();
        $this->configWriter->save(
            'checkout/options/enable_agreements',
            1,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
            $europeWebsite->getId()
        );
    }

    /**
     * @return WebsiteInterface
     */
    private function getEuropeWebsite(): WebsiteInterface
    {
        return $this->websiteRepository->get('coke_eu');
    }
}
