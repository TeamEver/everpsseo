<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link https://www.team-ever.com
 */

class EverPsSeoSupplier extends ObjectModel
{
    public $id_seo_supplier;
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
        'table' => 'ever_seo_supplier',
        'primary' => 'id_ever_seo_supplier',
        'multilang' => false,
        'fields' => array(
            'id_seo_supplier' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isunsignedInt',
                'required' => true
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isunsignedInt'
            ),
            'id_seo_lang' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isunsignedInt'
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
                'validate' => 'isunsignedInt'
            ),
            'status_code' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt'
            ),
        )
    );

    public static function getAllSeoSuppliersIds($id_shop)
    {
        $cache_id = 'EverPsSeoSupplier::getAllSeoSuppliersIds_'
        .(int)$id_shop;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_supplier');
            $sql->where('id_shop = '.(int)$id_shop);
            $return = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getSupplierNameBySeoId($id_seo_supplier)
    {
        $cache_id = 'EverPsSeoSupplier::getSupplierNameBySeoId_'
        .(int)$id_seo_supplier;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('name');
            $sql->from('supplier');
            $sql->where('id_supplier = '.(int)$id_seo_supplier);
            $return = Db::getInstance()->getValue($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getSeoSupplier($id_seo_supplier, $id_shop, $id_seo_lang)
    {
        $cache_id = 'EverPsSeoSupplier::getSeoSupplier_'
        .(int)$id_seo_supplier
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
            $sql->from('ever_seo_supplier');
            $sql->where(
                'id_seo_supplier = '.(int)$id_seo_supplier
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

    public static function changeSupplierTitleShortcodes($id_seo_supplier, $id_seo_lang, $id_shop)
    {
        if (!$id_shop) {
            $id_shop = (int)Context::getContext()->shop->id;
        }
        $supplier = new Supplier(
            (int)$id_seo_supplier,
            (int)$id_seo_lang
        );
        $message = Configuration::getConfigInMultipleLangs(
            'EVERSEO_SUPPLIER_TITLE_AUTO'
        );
        $shortcodes = array(
            '[supplier_title]' => $supplier->name ? $supplier->name : '',
            '[supplier_desc]' => $supplier->description ? $supplier->description : '',
            '[supplier_tags]' => $supplier->meta_keywords ? $supplier->meta_keywords : '',
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

    public static function changeSupplierMetadescShortcodes($id_seo_supplier, $id_seo_lang, $id_shop)
    {
        if (!$id_shop) {
            $id_shop = (int)Context::getContext()->shop->id;
        }
        $supplier = new Supplier(
            (int)$id_seo_supplier,
            (int)$id_seo_lang
        );
        $message = Configuration::get(
            'EVERSEO_SUPPLIER_METADESC_AUTO'
        );
        $shortcodes = array(
            '[supplier_title]' => $supplier->name,
            '[supplier_desc]' => $supplier->description,
            '[supplier_tags]' => $supplier->meta_keywords,
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

    public static function changeSupplierDescShortcodes($id_seo_supplier, $id_seo_lang, $id_shop)
    {
        if (!$id_shop) {
            $id_shop = (int)Context::getContext()->shop->id;
        }
        $supplier = new Supplier(
            (int)$id_seo_supplier,
            (int)$id_seo_lang
        );
        $message = Configuration::getConfigInMultipleLangs(
            'EVERSEO_SUPPLIER_METADESC_AUTO'
        );
        $shortcodes = array(
            '[supplier_title]' => $supplier->name ? $supplier->name : '',
            '[supplier_desc]' => $supplier->description ? $supplier->description : '',
            '[supplier_tags]' => $supplier->meta_keywords ? $supplier->meta_keywords : '',
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
