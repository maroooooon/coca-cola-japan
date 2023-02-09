<?php
// enable, adjust and copy this code for each store you run
// Store #0, default one
//if (isHttpHost("example.com")) {
//    $_SERVER["MAGE_RUN_CODE"] = "default";
//    $_SERVER["MAGE_RUN_TYPE"] = "store";
//}
function isHttpHost($host)
{
    if (!isset($_SERVER['HTTP_HOST'])) {
        return false;
    }
    return $_SERVER['HTTP_HOST'] === $host;
}

/**
 * This array is to facilitate dynamic integration environments that need to access other markets.
 * These subdomains are prepended to the $urls below to test if maybe we are on
 * a subdomain without having to constantly re-define all the URLs with all of these
 * subdomains.
 *
 * d = direct. The prefix is there because I'm paranoid of a conflict with a real subdomain one day
 * in the future.
 */
$universalSubdomains = [

    'd-coke-eu-fi' => ['store', 'finland_finnish'],
    'd-coke-eu-ie' => ['store', 'ireland_english'],
    'd-coke-eu-uk' => ['store', 'great_britain_english'],
    'd-coke-eu-fr' => ['store', 'france_french'],
    'd-coke-eu-be' => ['store', 'belgium_french'],
    'd-coke-eu-de' => ['store', 'germany_german'],
    'd-coke-eu-nl' => ['store', 'netherlands_dutch'],
    'd-coke-eu-ie-ni' => ['store', 'northern_ireland_english'],
    'd-coke-eu-tr' => ['store', 'turkey_turkish'],
    'd-coke-eu-no' => ['store', 'norway_norwegian'],
    'd-egypt' => ['website', 'egypt_website'],
    'd-topochico-gr' => ['website', 'topo_chico_gr_website'],
    'd-jp-marche' => ['website', 'jp_marche']

];

$urls = [

    // Coke France

    'coke-france-hj3u53a-gs6zpn2ap6eoc.eu-5.magentosite.cloud' => ['website', 'france_d2c'],
    'www.cocacolastore.fr' => ['website', 'france_d2c'],
    'qa.cocacolastore.fr' => ['website', 'france_d2c'],

    // Topochico Greece

    'topochicogr.qa-delivery.coca-cola.eg.c.gs6zpn2ap6eoc.dev.ent.magento.cloud' => ['website', 'topo_chico_gr_website'],
    'topochicogr.integration-5ojmyuq-gs6zpn2ap6eoc.eu-5.magentosite.cloud' => ['website', 'topo_chico_gr_website'],
    'qa.topochico.gr.c.gs6zpn2ap6eoc.dev.ent.magento.cloud' => ['website', 'topo_chico_gr_website'],
    'qa.topochico.gr' => ['website', 'topo_chico_gr_website'],
    'www.topochico.gr' => ['website', 'topo_chico_gr_website'],

    // Coke Egypt

    'delivery.coca-cola.eg' => ['website', 'egypt_website'],
    'qa-delivery.coca-cola.eg' => ['website', 'egypt_website'],
    'integration-5ojmyuq-gs6zpn2ap6eoc.eu-5.magentosite.cloud' => ['website', 'egypt_website'],

    // Coke Japan Marche


    'coke-japan-marche-kuiezqq-gs6zpn2ap6eoc.eu-5.magentosite.cloud' => ['website', 'jp_marche'],
    'mcstaging-mycokestore.cocacola.co.jp' => ['website', 'jp_marche'],
    'mycokestore.cocacola.co.jp' => ['website', 'jp_marche'],

    // Open Like Never Before --> Renamed to Coke Europe

    'coke-europe-g3bazwa-gs6zpn2ap6eoc.eu-5.magentosite.cloud/' => ['website', 'coke_eu'],
    'd-coke-eu-fi.coke-europe-g3bazwa-gs6zpn2ap6eoc.eu-5.magentosite.cloud/' => ['store', 'finland_finnish'],
    'd-coke-eu-ie.coke-europe-g3bazwa-gs6zpn2ap6eoc.eu-5.magentosite.cloud/' => ['store', 'ireland_english'],
    'd-coke-eu-uk.coke-europe-g3bazwa-gs6zpn2ap6eoc.eu-5.magentosite.cloud/' => ['store', 'great_britain_english'],
    'd-coke-eu-fr.coke-europe-g3bazwa-gs6zpn2ap6eoc.eu-5.magentosite.cloud/' => ['store', 'france_french'],
    'd-coke-eu-be.coke-europe-g3bazwa-gs6zpn2ap6eoc.eu-5.magentosite.cloud/' => ['store', 'belgium_french'],
    'd-coke-eu-de.coke-europe-g3bazwa-gs6zpn2ap6eoc.eu-5.magentosite.cloud/' => ['store', 'germany_german'],
    'd-coke-eu-nl.coke-europe-g3bazwa-gs6zpn2ap6eoc.eu-5.magentosite.cloud/' => ['store', 'netherlands_dutch'],
    'd-coke-eu-ie-ni.coke-europe-g3bazwa-gs6zpn2ap6eoc.eu-5.magentosite.cloud/' => ['store', 'northern_ireland_english'],

    'qa-store.coca-cola.fi' => ['store', 'finland_finnish'],
    'qa-store.coca-cola.ie' => ['store', 'ireland_english'],
    'qa-store.coca-cola.uk' => ['store', 'great_britain_english'],
    'qa-store.coca-cola.co.uk' => ['store', 'great_britain_english'],
    'qa-store.coca-cola.fr' => ['store', 'france_french'],
    'qa-store.cocacolabelgium.be' => ['store', 'belgium_french'],
    'qa-store.cocacoladeutschland.de' => ['store', 'germany_german'],
    'qa-store.coca-cola-deutschland.de' => ['store', 'germany_german'],
    'qa-store.cocacolanederland.nl' => ['store', 'netherlands_dutch'],
    'qa-store.ni.coca-cola.ie' => ['store', 'northern_ireland_english'],
    'qa-dahaiyisiicin.coca-cola.com.tr' => ['store', 'turkey_turkish'],

    // temporary
//    'qa-store.coca-cola.no' => ['store', 'norway_norwegian'],
    'qa-store.coca-cola.no' => ['store', 'france_d2c'],

    'store.coca-cola.fi' => ['store', 'finland_finnish'],
    'store.coca-cola.ie' => ['store', 'ireland_english'],
    'store.coca-cola.uk' => ['store', 'great_britain_english'],
    'store.coca-cola.fr' => ['store', 'france_french'],
    'store.coca-cola.co.uk' => ['store', 'great_britain_english'],
    'store.cocacolabelgium.be' => ['store', 'belgium_french'],
//    'store.cocacoladeutschland.de' => ['store', 'germany_german'],
    'store.cocacoladeutschland.de' => ['store', 'france_d2c'],
    'store.coca-cola-deutschland.de' => ['store', 'germany_german'],
    'store.cocacolanederland.nl' => ['store', 'netherlands_dutch'],
    'store.ni.coca-cola.ie' => ['store', 'northern_ireland_english'],
    'dahaiyisiicin.coca-cola.com.tr' => ['store', 'turkey_turkish'],
    'store.coca-cola.no' => ['store', 'norway_norwegian'],

];

$matched = false;
foreach ($urls as $url => $meta) {
    if (isHttpHost($url)) {
        $matched = true;
        $_SERVER['MAGE_RUN_TYPE'] = $meta[0];
        $_SERVER['MAGE_RUN_CODE'] = $meta[1];
        break;
    }
}

/**
 * We do this in an if statement to prevent foreach in a foreach in the loop above.
 * 99% of cases will NOT be accessing a universal subdomain, so dont force every request
 * to go through a nested foreach.
 */
if (!$matched) {
    foreach ($urls as $url => $parentMeta) {
        foreach ($universalSubdomains as $subdomain => $meta) {
            if (isHttpHost($subdomain . "." . $url)) {
                $_SERVER['MAGE_RUN_TYPE'] = $meta[0];
                $_SERVER['MAGE_RUN_CODE'] = $meta[1];
                break;
            }
        }
    }
}
