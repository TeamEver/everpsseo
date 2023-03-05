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

function upgrade_module_1_1_6()
{
    $result = false;
    $sql = [];
    if (Db::getInstance()->ExecuteS(
        'SHOW COLUMNS FROM `'._DB_PREFIX_.'ever_seo_product`LIKE \'link_rewrite\''
    ) == false) {
        $sql[] =
            'ALTER TABLE '._DB_PREFIX_.'ever_seo_product
            ADD COLUMN link_rewrite varchar(255) DEFAULT NULL
            AFTER meta_description
        ';
    }
    if (Db::getInstance()->ExecuteS(
        'SHOW COLUMNS FROM `'._DB_PREFIX_.'ever_seo_category` LIKE \'link_rewrite\''
    ) == false) {
        $sql[] =
            'ALTER TABLE '._DB_PREFIX_.'ever_seo_category
            ADD COLUMN link_rewrite varchar(255) DEFAULT NULL
            AFTER meta_description
        ';
    }
    //Need to update now
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
        INNER JOIN '._DB_PREFIX_.'product_lang pl
        ON esp.id_seo_product = pl.id_product
        SET esp.link_rewrite = pl.link_rewrite
        WHERE esp.id_seo_lang = pl.id_lang
    ';
    foreach ($sql as $s) {
        $result &= Db::getInstance()->execute($s);
    }
    return $result;
}
