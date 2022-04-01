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

function upgrade_module_7_6_3()
{
    $baseSitemap = _PS_ROOT_DIR_.'/modules/everpsseo/everseositemapcron.php';
    unlink($baseSitemap);
    $baseObjects = _PS_ROOT_DIR_.'/modules/everpsseo/everseoobjectscron.php';
    unlink($baseObjects);
    return true;
}
