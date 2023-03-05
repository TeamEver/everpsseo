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

function upgrade_module_3_4_8()
{
    set_time_limit(0);
    $result = false;
    $sql = [];
    // Update SEO products
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_product`
        CHANGE `id_ever_product` `id_ever_seo_product`
        int(10) unsigned NOT NULL auto_increment';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_product`
         ADD `social_img_url` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_description`
    ';
    // Update SEO categories
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_category`
        CHANGE `id_ever_category` `id_ever_seo_category`
        int(10) unsigned NOT NULL auto_increment';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_category`
         ADD `social_img_url` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_description`
    ';
    // Update SEO CMS
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_cms`
        CHANGE `id_ever_cms` `id_ever_seo_cms`
        int(10) unsigned NOT NULL auto_increment';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_cms`
         ADD `social_img_url` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_description`
    ';
    // Update SEO CMS categories
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_cms_category`
        CHANGE `id_ever_cms_category` `id_ever_seo_cms_category`
        int(10) unsigned NOT NULL auto_increment';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_cms_category`
         ADD `social_img_url` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_description`
    ';
    // Update SEO pages
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_pagemeta`
        CHANGE `id_ever_pagemeta` `id_ever_seo_pagemeta`
        int(10) unsigned NOT NULL auto_increment';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_pagemeta`
         ADD `social_img_url` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_description`
    ';
    // Update SEO manufacturers
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_manufacturer`
        CHANGE `id_ever_manufacturer` `id_ever_seo_manufacturer`
        int(10) unsigned NOT NULL auto_increment';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_manufacturer`
         ADD `social_img_url` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_description`
    ';
    // Update SEO suppliers
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_supplier`
        CHANGE `id_ever_supplier` `id_ever_seo_supplier`
        int(10) unsigned NOT NULL auto_increment';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_supplier`
         ADD `social_img_url` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_description`
    ';
    // Update SEO redirects
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_redirect`
        CHANGE `id_ever_redirect` `id_ever_seo_redirect`
        int(10) unsigned NOT NULL auto_increment';
    // Update SEO backlinks
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_backlink`
        CHANGE `id_ever_backlink` `id_ever_seo_backlink`
        int(10) unsigned NOT NULL auto_increment';
    // Update SEO backlinks
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_image`
        CHANGE `id_ever_img` `id_ever_seo_image`
        int(10) unsigned NOT NULL auto_increment';
    foreach ($sql as $s) {
        $result &= Db::getInstance()->execute($s);
    }
    return $result;
}
