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

$sql = array();

$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ever_seo_redirect`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ever_seo_backlink`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ever_seo_product`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ever_seo_image`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ever_seo_category`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ever_seo_manufacturer`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ever_seo_supplier`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ever_seo_cms`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ever_seo_cms_category`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ever_seo_pagemeta`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ever_seo_lang`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ever_seo_shortcode`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ever_seo_shortcode_lang`';

foreach ($sql as $s) {
    if (!Db::getInstance()->execute($s)) {
        return false;
    }
}
