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

function upgrade_module_7_5_5()
{
    Configuration::deleteByName('EVERSEO_HRELANGS_PERSO');
    Configuration::deleteByName('EVERSEO_HREFLANG_URL');
    return true;
}
