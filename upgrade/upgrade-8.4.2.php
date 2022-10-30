<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link https://www.team-ever.com
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_8_4_2()
{
    set_time_limit(0);
    $result = true;
    $sql = array();
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_product`
         ADD `status_code` varchar(255) DEFAULT 0
         AFTER `note`
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_image`
         ADD `status_code` varchar(255) DEFAULT 0
         AFTER `allowed_sitemap`
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_category`
         ADD `status_code` varchar(255) DEFAULT 0
         AFTER `note`
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_manufacturer`
         ADD `status_code` varchar(255) DEFAULT 0
         AFTER `note`
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_supplier`
         ADD `status_code` varchar(255) DEFAULT 0
         AFTER `note`
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_pagemeta`
         ADD `status_code` varchar(255) DEFAULT 0
         AFTER `note`
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_cms_category`
         ADD `status_code` varchar(255) DEFAULT 0
         AFTER `note`
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_cms`
         ADD `status_code` varchar(255) DEFAULT 0
         AFTER `note`
    ';
    foreach ($sql as $s) {
        $result &= Db::getInstance()->execute($s);
    }
    return $result;
}
