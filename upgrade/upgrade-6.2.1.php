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

function upgrade_module_6_2_1()
{
    set_time_limit(0);
    $result = true;
    Configuration::deleteByName('EVERSEO_KEYWORDS_STRATEGY');
    $sql = [];
    // Update SEO products
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_product`
         ADD `canonical` VARCHAR(255) NULL DEFAULT NULL
         AFTER `link_rewrite`
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_product`
         ADD `keywords` VARCHAR(255) NULL DEFAULT NULL
         AFTER `canonical`
    ';
    // Update SEO categories
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_category`
         ADD `canonical` VARCHAR(255) NULL DEFAULT NULL
         AFTER `link_rewrite`
    ';
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_category`
         ADD `keywords` VARCHAR(255) NULL DEFAULT NULL
         AFTER `canonical`
    ';
    // Update SEO Manufacturers
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_manufacturer`
         ADD `keywords` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_img_url`
    ';
    // Update SEO Suppliers
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_supplier`
         ADD `keywords` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_img_url`
    ';
    // Update SEO Page metas
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_pagemeta`
         ADD `keywords` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_img_url`
    ';
    // Update SEO CMS Category
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_cms_category`
         ADD `keywords` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_img_url`
    ';
    // Update SEO CMS
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_cms`
         ADD `keywords` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_img_url`
    ';
    // Insert product canonical
    $sql[] =
    'UPDATE '._DB_PREFIX_.'ever_seo_product
        SET canonical = (
            SELECT link_rewrite
            FROM '._DB_PREFIX_.'product_lang
            WHERE '._DB_PREFIX_.'ever_seo_product.id_seo_product = '._DB_PREFIX_.'product_lang.id_product
            AND '._DB_PREFIX_.'ever_seo_product.id_seo_lang = '._DB_PREFIX_.'product_lang.id_lang
        );
    ';
    // Insert category canonical
    $sql[] =
    'UPDATE '._DB_PREFIX_.'ever_seo_category
        SET canonical = (
            SELECT link_rewrite
            FROM '._DB_PREFIX_.'category_lang
            WHERE '._DB_PREFIX_.'ever_seo_category.id_seo_category = '._DB_PREFIX_.'category_lang.id_category
            AND '._DB_PREFIX_.'ever_seo_category.id_seo_lang = '._DB_PREFIX_.'category_lang.id_lang
        );
    ';
    foreach ($sql as $s) {
        $result &= Db::getInstance()->execute($s);
    }
    return $result;
}
