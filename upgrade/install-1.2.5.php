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

function upgrade_module_1_2_5()
{
    $result = false;
    $sql = array();
    $sql[] = 'DROP FUNCTION IF EXISTS ever_wordcount';
    $sql[] = 'DROP FUNCTION IF EXISTS ever_strip_tags';
    foreach ($sql as $s) {
        $result &= Db::getInstance()->execute($s);
    }
    return $result;
}
