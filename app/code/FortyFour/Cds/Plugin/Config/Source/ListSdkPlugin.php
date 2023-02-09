<?php

namespace FortyFour\Cds\Plugin\Config\Source;

use Coke\Cds\Model\Config\Source\ListSdk;

class ListSdkPlugin
{
    /**
     * Add additional SDK options
     * * Make sure to add new SDK files to the theme
     *
     * @param ListSdk $subject
     * @param array $result
     * @return array
     */
    public function afterToOptionArray(
        ListSdk $subject,
        $result
    ) {
        if (!empty($result)) {
            $result[] = ['value' => 'topo-chico-cds-sdk-stage.min', 'label' => __('Topo Chico Stage')];
            $result[] = ['value' => 'topo-chico-cds-sdk-prod.min', 'label' => __('Topo Chico Prod')];
            $result[] = ['value' => 'otb-emea-cds-sdk-prod.min', 'label' => __('OTB EMEA Prod')];
            $result[] = ['value' => 'otb-turkey-emea-cds-sdk-prod.min', 'label' => __('OTB Turkey EMEA Prod')];
            $result[] = ['value' => 'otb-fr-emea-cds-sdk-stage.min', 'label' => __('OTB France EMEA Stage')];
            $result[] = ['value' => 'otb-fr-emea-cds-sdk-prod.min', 'label' => __('OTB France EMEA Prod')];
        }
        return $result;
    }
}
