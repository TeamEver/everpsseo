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

function upgrade_module_1_1_4()
{
    $result = false;

    $sql =
        'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ever_seo_cms_category` (
            `id_ever_seo_cms_category` int(10) unsigned NOT NULL auto_increment,
            `id_seo_cms_category` int(10) unsigned NOT NULL,
            `id_shop` int(10) unsigned NOT NULL,
            `id_seo_lang` int(10) unsigned NOT NULL,
            `meta_title` varchar(255) DEFAULT NULL,
            `meta_description` varchar(255) DEFAULT NULL,
            `indexable` int(10) unsigned NOT NULL,
            `follow` int(10) unsigned NOT NULL,
            `allowed_sitemap` int(10) unsigned NOT NULL,
            `count` int(10) unsigned DEFAULT NULL,
            PRIMARY KEY (`id_ever_seo_cms_category`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8
    ';

    $result = Db::getInstance()->execute($sql);

    $sql =
        'INSERT INTO `'._DB_PREFIX_.'ever_seo_cms_category` (
            id_seo_cms_category,
            id_shop,
            id_seo_lang,
            meta_title,
            meta_description,
            indexable,
            follow,
            allowed_sitemap
        )
        SELECT
            id_cms_category,
            id_shop,
            id_lang,
            null,
            null,
            1,
            1,
            1
        FROM `'._DB_PREFIX_.'cms_category_lang`
    ';

    $result &= Db::getInstance()->execute($sql);

    $tab = new Tab();

    $tab->active = 1;
    $tab->class_name = 'AdminEverPsSeoCmsCategory';
    $tab->id_parent = (int)Tab::getIdFromClassName('AdminEverPsSeo');
    $tab->position = Tab::getNewLastPosition($tab->id_parent);
    $tab->module = 'everpsseo';

    foreach (Language::getLanguages(false) as $lang) {
        $tab->name[(int)$lang['id_lang']] = 'SEO CMS Categories';
    }

    return $tab->add();
}
