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

function upgrade_module_7_1_3()
{
    set_time_limit(0);
    $result = true;
    $sql = [];
    // Shortcodes
    $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ever_seo_shortcode` (
            `id_ever_seo_shortcode` int(10) unsigned NOT NULL auto_increment,
            `id_shop` int(10) unsigned NOT NULL,
            `shortcode` text NOT NULL,
            PRIMARY KEY (`id_ever_seo_shortcode`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

    $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ever_seo_shortcode_lang` (
            `id_ever_seo_shortcode` int(10) unsigned NOT NULL,
            `id_lang` int(10) unsigned NOT NULL,
            `title` text NOT NULL,
            `content` text NOT NULL,
            PRIMARY KEY (`id_ever_seo_shortcode`, `id_lang`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
    foreach ($sql as $s) {
        $result &= Db::getInstance()->execute($s);
    }
    // Add shortcode tab
    $tab = new Tab();
    $tab->active = 1;
    $tab->class_name = 'AdminEverPsSeoShortcode';
    $tab->id_parent = (int)Tab::getIdFromClassName('AdminEverPsSeo');
    $tab->position = Tab::getNewLastPosition($tab->id_parent);
    $tab->module = 'everpsseo';
    foreach (Language::getLanguages(false) as $lang) {
        $tab->name[(int)$lang['id_lang']] = 'Shortcodes';
    }
    $result &= $tab->add();
    // Add shortcode hook
    $hook = new Hook();
    $hook->name = 'actionChangeSeoShortcodes';
    $hook->title = 'Action change Ever SEO shortcodes';
    $hook->description = 'This hook change SEO shortcodes while Ever SEO module is triggered';
    $result &= $hook->save();
    return $result;
}
