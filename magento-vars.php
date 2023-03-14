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
//$universalSubdomains = [
//    'd-jp-marche' => ['website', 'jp_marche']
//];

$urls = [
    'integration-5ojmyuq-xqng52md7niyo.ap-3.magentosite.cloud' => ['website', 'jp_marche'],
    'mcstaging.cocacola.co.jp' => ['website', 'jp_marche'],
    'mycokestore.cocacola.co.jp' => ['website', 'jp_marche'],
    'mcprod.cocacola.co.jp' => ['website', 'jp_marche'],
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
//if (!$matched) {
//    foreach ($urls as $url => $parentMeta) {
//        foreach ($universalSubdomains as $subdomain => $meta) {
//            if (isHttpHost($subdomain . "." . $url)) {
//                $_SERVER['MAGE_RUN_TYPE'] = $meta[0];
//                $_SERVER['MAGE_RUN_CODE'] = $meta[1];
//                break;
//            }
//        }
//    }
//}
