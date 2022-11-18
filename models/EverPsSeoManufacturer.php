<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link https://www.team-ever.com
 */

class EverPsSeoManufacturer extends ObjectModel
{
    public $id_seo_manufacturer;
    public $id_shop;
    public $id_seo_lang;
    public $meta_title;
    public $meta_description;
    public $social_title;
    public $social_description;
    public $social_img_url;
    public $bottom_content;
    public $keywords;
    public $indexable;
    public $follow;
    public $allowed_sitemap;
    public $count;
    public $status_code;

    public static $definition = array(
        'table' => 'ever_seo_manufacturer',
        'primary' => 'id_ever_seo_manufacturer',
        'multilang' => false,
        'fields' => array(
            'id_seo_manufacturer' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
                'required' => true
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt'
            ),
            'id_seo_lang' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt'
            ),
            'meta_title' => array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isString'
            ),
            'meta_description' => array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isString'
            ),
            'social_title' => array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isString'
            ),
            'social_description' => array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isCleanHtml'
            ),
            'social_img_url' => array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isUrl'
            ),
            'bottom_content' => array(
                'type' => self::TYPE_HTML,
                'lang' => false,
                'validate' => 'isCleanHtml'
            ),
            'keywords' => array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isString'
            ),
            'indexable' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isBool'
            ),
            'follow' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isBool'
            ),
            'allowed_sitemap' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isBool'
            ),
            'count' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt'
            ),
            'status_code' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt'
            ),
        )
    );

    public static function getAllSeoManufacturersIds($id_shop)
    {
        $cache_id = 'EverPsSeoManufacturer::getAllSeoManufacturersIds_'
        .(int)$id_shop;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_manufacturer');
            $sql->where('id_shop = '.(int)$id_shop);
            $return = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getManufacturerNameBySeoId($id_seo_manufacturer)
    {
        $cache_id = 'EverPsSeoManufacturer::getManufacturerNameBySeoId_'
        .(int)$id_seo_manufacturer;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('name');
            $sql->from('manufacturer');
            $sql->where('id_manufacturer = '.(int)$id_seo_manufacturer);
            $return = Db::getInstance()->getValue($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getSeoManufacturer($id_seo_manufacturer, $id_shop, $id_seo_lang)
    {
        $cache_id = 'EverPsSeoManufacturer::getSeoManufacturer_'
        .(int)$id_seo_manufacturer
        .'_'
        .(int)$id_shop
        .'_'
        .(int)$id_seo_lang;
        if (!Cache::isStored($cache_id)) {
            if (!$id_shop) {
                $id_shop = (int)Context::getContext()->shop->id;
            }
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_manufacturer');
            $sql->where(
                'id_seo_manufacturer = '.(int)$id_seo_manufacturer
            );
            $sql->where(
                'id_seo_lang = '.(int)$id_seo_lang
            );
            $sql->where(
                'id_shop = '.(int)$id_shop
            );
            $return = new self(Db::getInstance()->getValue($sql));
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function changeManufacturerTitleShortcodes($id_ever_seo_manufacturer, $id_seo_lang, $id_shop)
    {
        if (!$id_shop) {
            $id_shop = (int)Context::getContext()->shop->id;
        }
        $manufacturer = new Manufacturer(
            (int)$id_ever_seo_manufacturer,
            (int)$id_seo_lang
        );
        $message = Configuration::getInt(
            'EVERSEO_MANUFACTURER_TITLE_AUTO'
        );
        $shortcodes = array(
            '[manufacturer_title]' => $manufacturer->name ? $manufacturer->name : '',
            '[manufacturer_desc]' => $manufacturer->description ? $manufacturer->description : '',
            '[manufacturer_tags]' => $manufacturer->meta_keywords ? $manufacturer->meta_keywords : '',
            '[shop_name]' => (string)Configuration::get('PS_SHOP_NAME'),
            'NULL' => '', // Useful : remove empty strings in case of NULL
            'null' => '', // Useful : remove empty strings in case of null
        );
        foreach ($shortcodes as $key => $value) {
            $message[(int)$id_seo_lang] = str_replace(
                (string)$key,
                (string)$value,
                (string)$message[(int)$id_seo_lang]
            );
            $message[(int)$id_seo_lang] = Hook::exec('actionChangeSeoShortcodes', array(
                'content' => $message[(int)$id_seo_lang]
            ));
        }
        if (!empty($message[(int)$id_seo_lang])) {
            return $message[(int)$id_seo_lang];
        }
    }

    public static function changeManufacturerMetadescShortcodes($id_seo_manufacturer, $id_seo_lang, $id_shop)
    {
        if (!$id_shop) {
            $id_shop = (int)Context::getContext()->shop->id;
        }
        $manufacturer = new Manufacturer(
            (int)$id_seo_manufacturer,
            (int)$id_seo_lang
        );
        $message = Configuration::getInt(
            'EVERSEO_MANUFACTURER_METADESC_AUTO'
        );
        $shortcodes = array(
            '[manufacturer_title]' => $manufacturer->name ? $manufacturer->name : '',
            '[manufacturer_desc]' => $manufacturer->description ? $manufacturer->description : '',
            '[manufacturer_tags]' => $manufacturer->meta_keywords ? $manufacturer->meta_keywords : '',
            '[shop_name]' => (string)Configuration::get('PS_SHOP_NAME'),
            'NULL' => '', // Useful : remove empty strings in case of NULL
            'null' => '', // Useful : remove empty strings in case of null
        );
        foreach ($shortcodes as $key => $value) {
            $message[(int)$id_seo_lang] = str_replace(
                (string)$key,
                (string)$value,
                (string)$message[(int)$id_seo_lang]
            );
            $message[(int)$id_seo_lang] = Hook::exec('actionChangeSeoShortcodes', array(
                'content' => $message[(int)$id_seo_lang]
            ));
        }
        if (!empty($message[(int)$id_seo_lang])) {
            return $message[(int)$id_seo_lang];
        }
    }

    public static function changeManufacturerDescShortcodes($id_seo_manufacturer, $id_seo_lang, $id_shop)
    {
        if (!$id_shop) {
            $id_shop = (int)Context::getContext()->shop->id;
        }
        $manufacturer = new Manufacturer(
            (int)$id_seo_manufacturer,
            (int)$id_seo_lang
        );
        $message = Configuration::getInt(
            'EVERSEO_MANUFACTURER_METADESC_AUTO'
        );
        $shortcodes = array(
            '[manufacturer_title]' => $manufacturer->name ? $manufacturer->name : '',
            '[manufacturer_desc]' => $manufacturer->description ? $manufacturer->description : '',
            '[manufacturer_tags]' => $manufacturer->meta_keywords ? $manufacturer->meta_keywords : '',
            '[shop_name]' => (string)Configuration::get('PS_SHOP_NAME'),
            'NULL' => '', // Useful : remove empty strings in case of NULL
            'null' => '', // Useful : remove empty strings in case of null
        );
        foreach ($shortcodes as $key => $value) {
            $message[(int)$id_seo_lang] = str_replace(
                (string)$key,
                (string)$value,
                (string)$message[(int)$id_seo_lang]
            );
            $message[(int)$id_seo_lang] = Hook::exec('actionChangeSeoShortcodes', array(
                'content' => $message[(int)$id_seo_lang]
            ));
        }
        if (!empty($message[(int)$id_seo_lang])) {
            return $message[(int)$id_seo_lang];
        }
    }
}
