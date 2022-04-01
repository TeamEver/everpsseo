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

function upgrade_module_7_9_0()
{
    set_time_limit(0);
    $result = true;
    $sql = array();
    // Products
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_product`
         ALTER `indexable` SET DEFAULT 1
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_product`
         ALTER `follow` SET DEFAULT 1
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_product`
         ALTER `allowed_sitemap` SET DEFAULT 1
    ';
    // Categories
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_category`
         ALTER `indexable` SET DEFAULT 1
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_category`
         ALTER `follow` SET DEFAULT 1
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_category`
         ALTER `allowed_sitemap` SET DEFAULT 1
    ';
    // Manufacturers
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_manufacturer`
         ALTER `indexable` SET DEFAULT 1
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_manufacturer`
         ALTER `follow` SET DEFAULT 1
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_manufacturer`
         ALTER `allowed_sitemap` SET DEFAULT 1
    ';
    // Suppliers
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_supplier`
         ALTER `indexable` SET DEFAULT 1
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_supplier`
         ALTER `follow` SET DEFAULT 1
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_supplier`
         ALTER `allowed_sitemap` SET DEFAULT 1
    ';
    // Pages
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_pagemeta`
         ALTER `indexable` SET DEFAULT 1
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_pagemeta`
         ALTER `follow` SET DEFAULT 1
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_pagemeta`
         ALTER `allowed_sitemap` SET DEFAULT 1
    ';
    // CMS categories
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_cms_category`
         ALTER `indexable` SET DEFAULT 1
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_cms_category`
         ALTER `follow` SET DEFAULT 1
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_cms_category`
         ALTER `allowed_sitemap` SET DEFAULT 1
    ';
    // CMS
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_cms`
         ALTER `indexable` SET DEFAULT 1
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_cms`
         ALTER `follow` SET DEFAULT 1
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_cms`
         ALTER `allowed_sitemap` SET DEFAULT 1
    ';
    foreach ($sql as $s) {
        $result &= Db::getInstance()->execute($s);
    }
    return $result;
}
