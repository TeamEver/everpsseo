<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @see https://www.team-ever.com
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_7_8_3()
{
    Configuration::updateValue('EVERHTACCESS_PREPEND', '');
    set_time_limit(0);
    $result = true;
    $sql = [];
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_product`
         ADD `note` varchar(255) DEFAULT 0
         AFTER `count`
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_category`
         ADD `note` varchar(255) DEFAULT 0
         AFTER `count`
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_manufacturer`
         ADD `note` varchar(255) DEFAULT 0
         AFTER `count`
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_supplier`
         ADD `note` varchar(255) DEFAULT 0
         AFTER `count`
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_pagemeta`
         ADD `note` varchar(255) DEFAULT 0
         AFTER `count`
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_cms_category`
         ADD `note` varchar(255) DEFAULT 0
         AFTER `count`
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_cms`
         ADD `note` varchar(255) DEFAULT 0
         AFTER `count`
    ';
    foreach ($sql as $s) {
        $result &= Db::getInstance()->execute($s);
    }
    return $result;
}
