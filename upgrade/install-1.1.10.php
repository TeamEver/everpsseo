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

function upgrade_module_1_1_10()
{
    $isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;
    $result = false;
    $sql = array();
    // First update Ever SEO tables
    $sql[] =
        'UPDATE '._DB_PREFIX_.'ever_seo_category esc
        SET esc.link_rewrite = (
            SELECT cl.link_rewrite FROM '._DB_PREFIX_.'category_lang cl
            WHERE esc.id_seo_lang = cl.id_lang
            AND esc.id_seo_category = cl.id_category
        )
    ';
    $sql[] =
        'UPDATE '._DB_PREFIX_.'ever_seo_product esp
        SET esp.link_rewrite = (
            SELECT pl.link_rewrite FROM '._DB_PREFIX_.'product_lang pl
            WHERE esp.id_seo_lang = pl.id_lang
            AND esp.id_seo_product = pl.id_product
        )
    ';
    // Then update PS tables
    // categories
    $sql[] =
        'UPDATE '._DB_PREFIX_.'category_lang cl
        INNER JOIN '._DB_PREFIX_.'ever_seo_category esc
        ON esc.id_seo_category = cl.id_category
        SET cl.meta_title = esc.meta_title
        WHERE esc.id_seo_lang = cl.id_lang
    ';
    $sql[] =
        'UPDATE '._DB_PREFIX_.'category_lang cl
        INNER JOIN '._DB_PREFIX_.'ever_seo_category esc
        SET cl.meta_description = esc.meta_description
        WHERE esc.id_seo_lang = cl.id_lang
    ';
    // products
    $sql[] =
        'UPDATE '._DB_PREFIX_.'product_lang pl
        INNER JOIN '._DB_PREFIX_.'ever_seo_product esc
        ON esc.id_seo_product = pl.id_product
        SET pl.meta_title = esc.meta_title
        WHERE esc.id_seo_lang = pl.id_lang
    ';
    $sql[] =
        'UPDATE '._DB_PREFIX_.'product_lang pl
        INNER JOIN '._DB_PREFIX_.'ever_seo_product esc
        ON esc.id_seo_product = pl.id_product
        SET pl.meta_description = esc.meta_description
        WHERE esc.id_seo_lang = pl.id_lang
    ';
    if ($isSeven) {
        $sql[] =
            'UPDATE '._DB_PREFIX_.'tab
            SET icon = "icon-team-ever"
            WHERE module = "everpsseo"
        ';
    }
    foreach ($sql as $s) {
        $result &= Db::getInstance()->execute($s);
    }
    return $result;
}
