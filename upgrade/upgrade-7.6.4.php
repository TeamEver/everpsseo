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

function upgrade_module_7_6_4()
{
    $theme_dir = _PS_ALL_THEMES_DIR_._THEME_NAME_.'/assets/css/';
    if (!empty(_PARENT_THEME_NAME_)) {
        $parent_theme_dir = _PS_ALL_THEMES_DIR_._PARENT_THEME_NAME_.'/assets/css/';
    }
    $all_theme_files = glob($theme_dir.'*');
    $all_parent_theme_files = glob($parent_theme_dir.'*');
    foreach ($all_theme_files as $index) {
        $info = new SplFileInfo(basename($index));
        if (is_file($index)
            && ($info->getExtension() == 'svg'
                || $info->getExtension() == 'ttf'
                || $info->getExtension() == 'woff'
                || $info->getExtension() == 'woff2'
                || $info->getExtension() == 'eot'
        )) {
            copy($index, dirname(__FILE__).'/views/cache/css/'.basename($index));
        }
    }
    foreach ($all_parent_theme_files as $index) {
        $info = new SplFileInfo(basename($index));
        if (is_file($index)
            && ($info->getExtension() == 'svg'
                || $info->getExtension() == 'ttf'
                || $info->getExtension() == 'woff'
                || $info->getExtension() == 'woff2'
                || $info->getExtension() == 'eot'
        )) {
            copy($index, dirname(__FILE__).'/views/cache/css/'.basename($index));
        }
    }
    return true;
}
