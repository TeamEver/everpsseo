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

function upgrade_module_1_9_0()
{
    $result = false;
    $sql = array();
    $sql[] =
        'ALTER TABLE '._DB_PREFIX_.'ever_seo_redirect
         ADD `everfrom` VARCHAR(255) NULL DEFAULT NULL
         AFTER `not_found`
    ';
    foreach ($sql as $s) {
        $result &= Db::getInstance()->execute($s);
    }
    return $result;
}
