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

function upgrade_module_7_7_9()
{
    Configuration::updateValue('EVERHTACCESS_PREPEND', '');
    set_time_limit(0);
    $result = true;
    $sql = [];
    // Update SEO categories
    $sql[] =
        'ALTER TABLE `'._DB_PREFIX_.'ever_seo_product`
         ADD `bottom_content` text DEFAULT NULL
         AFTER `social_img_url`
    ';
    foreach ($sql as $s) {
        $result &= Db::getInstance()->execute($s);
    }
    return $result;
}
