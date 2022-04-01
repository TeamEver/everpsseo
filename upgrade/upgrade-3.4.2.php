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

function upgrade_module_3_4_2()
{
    set_time_limit(0);
    $result = false;
    $sql = array();
    $sql[] =
        'ALTER TABLE '._DB_PREFIX_.'ever_seo_product
         ADD `social_title` VARCHAR(255) NULL DEFAULT NULL
         AFTER `meta_description`
    ';
    $sql[] =
        'ALTER TABLE '._DB_PREFIX_.'ever_seo_product
         ADD `social_description` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_title`
    ';
    $sql[] =
        'ALTER TABLE '._DB_PREFIX_.'ever_seo_category
         ADD `social_title` VARCHAR(255) NULL DEFAULT NULL
         AFTER `meta_description`
    ';
    $sql[] =
        'ALTER TABLE '._DB_PREFIX_.'ever_seo_category
         ADD `social_description` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_title`
    ';
    $sql[] =
        'ALTER TABLE '._DB_PREFIX_.'ever_seo_manufacturer
         ADD `social_title` VARCHAR(255) NULL DEFAULT NULL
         AFTER `meta_description`
    ';
    $sql[] =
        'ALTER TABLE '._DB_PREFIX_.'ever_seo_manufacturer
         ADD `social_description` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_title`
    ';
    $sql[] =
        'ALTER TABLE '._DB_PREFIX_.'ever_seo_supplier
         ADD `social_title` VARCHAR(255) NULL DEFAULT NULL
         AFTER `meta_description`
    ';
    $sql[] =
        'ALTER TABLE '._DB_PREFIX_.'ever_seo_supplier
         ADD `social_description` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_title`
    ';
    $sql[] =
        'ALTER TABLE '._DB_PREFIX_.'ever_seo_pagemeta
         ADD `social_title` VARCHAR(255) NULL DEFAULT NULL
         AFTER `meta_description`
    ';
    $sql[] =
        'ALTER TABLE '._DB_PREFIX_.'ever_seo_pagemeta
         ADD `social_description` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_title`
    ';
    $sql[] =
        'ALTER TABLE '._DB_PREFIX_.'ever_seo_cms_category
         ADD `social_title` VARCHAR(255) NULL DEFAULT NULL
         AFTER `meta_description`
    ';
    $sql[] =
        'ALTER TABLE '._DB_PREFIX_.'ever_seo_cms_category
         ADD `social_description` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_title`
    ';
    $sql[] =
        'ALTER TABLE '._DB_PREFIX_.'ever_seo_cms
         ADD `social_title` VARCHAR(255) NULL DEFAULT NULL
         AFTER `meta_description`
    ';
    $sql[] =
        'ALTER TABLE '._DB_PREFIX_.'ever_seo_cms
         ADD `social_description` VARCHAR(255) NULL DEFAULT NULL
         AFTER `social_title`
    ';
    foreach ($sql as $s) {
        $result &= Db::getInstance()->execute($s);
    }
    return $result;
}
