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

$sql = array();

// Redirect 404
$sql[] =
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ever_seo_redirect` (
        `id_ever_seo_redirect` int(10) unsigned NOT NULL auto_increment,
        `not_found` text NOT NULL,
        `everfrom` text DEFAULT NULL,
        `redirection` text DEFAULT NULL,
        `id_shop` int(10) unsigned DEFAULT 1,
        `count` int(10) unsigned DEFAULT NULL,
        `active` int(10) unsigned NOT NULL,
        `code` int(10) unsigned NOT NULL,
        PRIMARY KEY (`id_ever_seo_redirect`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

// Backlinks
$sql[] =
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ever_seo_backlink` (
        `id_ever_seo_backlink` int(10) unsigned NOT NULL auto_increment,
        `everfrom` text NOT NULL,
        `everto` text DEFAULT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `count` int(10) unsigned DEFAULT NULL,
        PRIMARY KEY (`id_ever_seo_backlink`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

// Products SEO
$sql[] =
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ever_seo_lang` (
        `id_ever_lang` int(10) unsigned NOT NULL auto_increment,
        `id_seo_lang` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `iso_code` varchar(255) NOT NULL,
        `language_code` varchar(255) NOT NULL,
        PRIMARY KEY (`id_ever_lang`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

// Products lang SEO
$sql[] =
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ever_seo_product` (
        `id_ever_seo_product` int(10) unsigned NOT NULL auto_increment,
        `id_seo_product` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `id_seo_lang` int(10) unsigned NOT NULL,
        `meta_title` varchar(255) DEFAULT NULL,
        `meta_description` varchar(255) DEFAULT NULL,
        `social_title` varchar(255) DEFAULT NULL,
        `social_description` varchar(255) DEFAULT NULL,
        `social_img_url` varchar(255) DEFAULT NULL,
        `bottom_content` text DEFAULT NULL,
        `link_rewrite` varchar(255) DEFAULT NULL,
        `canonical` varchar(255) DEFAULT NULL,
        `keywords` varchar(255) DEFAULT NULL,
        `indexable` int(10) NOT NULL DEFAULT 1,
        `follow` int(10) NOT NULL DEFAULT 1,
        `allowed_sitemap` int(10) NOT NULL DEFAULT 1,
        `count` int(10) unsigned DEFAULT 0,
        `note` varchar(255) DEFAULT 0,
        PRIMARY KEY (`id_ever_seo_product`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

// Images lang SEO
$sql[] =
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ever_seo_image` (
        `id_ever_seo_image` int(10) unsigned NOT NULL auto_increment,
        `id_seo_img` int(10) unsigned NOT NULL,
        `id_seo_product` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `id_seo_lang` int(10) unsigned NOT NULL,
        `alt` varchar(255) DEFAULT NULL,
        `allowed_sitemap` int(10) unsigned NOT NULL,
        PRIMARY KEY (`id_ever_seo_image`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

// Category lang SEO
$sql[] =
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ever_seo_category` (
        `id_ever_seo_category` int(10) unsigned NOT NULL auto_increment,
        `id_seo_category` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `id_seo_lang` int(10) unsigned NOT NULL,
        `meta_title` varchar(255) DEFAULT NULL,
        `meta_description` varchar(255) DEFAULT NULL,
        `social_title` varchar(255) DEFAULT NULL,
        `social_description` varchar(255) DEFAULT NULL,
        `social_img_url` varchar(255) DEFAULT NULL,
        `bottom_content` text DEFAULT NULL,
        `link_rewrite` varchar(255) DEFAULT NULL,
        `canonical` varchar(255) DEFAULT NULL,
        `keywords` varchar(255) DEFAULT NULL,
        `indexable` int(10) NOT NULL DEFAULT 1,
        `follow` int(10) NOT NULL DEFAULT 1,
        `allowed_sitemap` int(10) NOT NULL DEFAULT 1,
        `count` int(10) unsigned DEFAULT 0,
        `note` varchar(255) DEFAULT 0,
        PRIMARY KEY (`id_ever_seo_category`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

// Manufacturer SEO
$sql[] =
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ever_seo_manufacturer` (
        `id_ever_seo_manufacturer` int(10) unsigned NOT NULL auto_increment,
        `id_seo_manufacturer` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `id_seo_lang` int(10) unsigned NOT NULL,
        `meta_title` varchar(255) DEFAULT NULL,
        `meta_description` varchar(255) DEFAULT NULL,
        `social_title` varchar(255) DEFAULT NULL,
        `social_description` varchar(255) DEFAULT NULL,
        `social_img_url` varchar(255) DEFAULT NULL,
        `bottom_content` text DEFAULT NULL,
        `keywords` varchar(255) DEFAULT NULL,
        `indexable` int(10) NOT NULL DEFAULT 1,
        `follow` int(10) NOT NULL DEFAULT 1,
        `allowed_sitemap` int(10) NOT NULL DEFAULT 1,
        `count` int(10) unsigned DEFAULT 0,
        `note` varchar(255) DEFAULT 0,
        PRIMARY KEY (`id_ever_seo_manufacturer`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

// Suppliers lang SEO
$sql[] =
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ever_seo_supplier` (
        `id_ever_seo_supplier` int(10) unsigned NOT NULL auto_increment,
        `id_seo_supplier` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `id_seo_lang` int(10) unsigned NOT NULL,
        `meta_title` varchar(255) DEFAULT NULL,
        `meta_description` varchar(255) DEFAULT NULL,
        `social_title` varchar(255) DEFAULT NULL,
        `social_description` varchar(255) DEFAULT NULL,
        `social_img_url` varchar(255) DEFAULT NULL,
        `bottom_content` text DEFAULT NULL,
        `keywords` varchar(255) DEFAULT NULL,
        `indexable` int(10) NOT NULL DEFAULT 1,
        `follow` int(10) NOT NULL DEFAULT 1,
        `allowed_sitemap` int(10) NOT NULL DEFAULT 1,
        `count` int(10) unsigned DEFAULT 0,
        `note` varchar(255) DEFAULT 0,
        PRIMARY KEY (`id_ever_seo_supplier`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

// Page metas lang SEO
$sql[] =
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ever_seo_pagemeta` (
        `id_ever_seo_pagemeta` int(10) unsigned NOT NULL auto_increment,
        `id_seo_pagemeta` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `id_seo_lang` int(10) unsigned NOT NULL,
        `meta_title` varchar(255) DEFAULT NULL,
        `meta_description` varchar(255) DEFAULT NULL,
        `social_title` varchar(255) DEFAULT NULL,
        `social_description` varchar(255) DEFAULT NULL,
        `social_img_url` varchar(255) DEFAULT NULL,
        `keywords` varchar(255) DEFAULT NULL,
        `indexable` int(10) NOT NULL DEFAULT 1,
        `follow` int(10) NOT NULL DEFAULT 1,
        `allowed_sitemap` int(10) NOT NULL DEFAULT 1,
        `count` int(10) unsigned DEFAULT 0,
        `note` varchar(255) DEFAULT 0,
        PRIMARY KEY (`id_ever_seo_pagemeta`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

// CMS category lang SEO
$sql[] =
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ever_seo_cms_category` (
        `id_ever_seo_cms_category` int(10) unsigned NOT NULL auto_increment,
        `id_seo_cms_category` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `id_seo_lang` int(10) unsigned NOT NULL,
        `meta_title` varchar(255) DEFAULT NULL,
        `meta_description` varchar(255) DEFAULT NULL,
        `social_title` varchar(255) DEFAULT NULL,
        `social_description` varchar(255) DEFAULT NULL,
        `social_img_url` varchar(255) DEFAULT NULL,
        `keywords` varchar(255) DEFAULT NULL,
        `indexable` int(10) NOT NULL DEFAULT 1,
        `follow` int(10) NOT NULL DEFAULT 1,
        `allowed_sitemap` int(10) NOT NULL DEFAULT 1,
        `count` int(10) unsigned DEFAULT 0,
        `note` varchar(255) DEFAULT 0,
        PRIMARY KEY (`id_ever_seo_cms_category`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

// CMS lang SEO
$sql[] =
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ever_seo_cms` (
        `id_ever_seo_cms` int(10) unsigned NOT NULL auto_increment,
        `id_seo_cms` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `id_seo_lang` int(10) unsigned NOT NULL,
        `meta_title` varchar(255) DEFAULT NULL,
        `meta_description` varchar(255) DEFAULT NULL,
        `social_title` varchar(255) DEFAULT NULL,
        `social_description` varchar(255) DEFAULT NULL,
        `social_img_url` varchar(255) DEFAULT NULL,
        `keywords` varchar(255) DEFAULT NULL,
        `indexable` int(10) NOT NULL DEFAULT 1,
        `follow` int(10) NOT NULL DEFAULT 1,
        `allowed_sitemap` int(10) NOT NULL DEFAULT 1,
        `count` int(10) unsigned DEFAULT 0,
        `note` varchar(255) DEFAULT 0,
        PRIMARY KEY (`id_ever_seo_cms`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

// Shortcodes
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ever_seo_shortcode` (
        `id_ever_seo_shortcode` int(10) unsigned NOT NULL auto_increment,
        `shortcode` text DEFAULT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        PRIMARY KEY (`id_ever_seo_shortcode`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ever_seo_shortcode_lang` (
        `id_ever_seo_shortcode` int(10) unsigned NOT NULL,
        `id_lang` int(10) unsigned NOT NULL,
        `title` text DEFAULT NULL,
        `content` text DEFAULT NULL,
        PRIMARY KEY (`id_ever_seo_shortcode`, `id_lang`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

foreach ($sql as $s) {
    if (!Db::getInstance()->execute($s)) {
        return false;
    }
}
