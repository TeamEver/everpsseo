<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @see https://www.team-ever.com
 */
class EverPsSeoImage extends ObjectModel
{
    public $id_ever_seo_image;
    public $id_seo_img;
    public $id_seo_product;
    public $id_shop;
    public $id_seo_lang;
    public $alt;
    public $allowed_sitemap;
    public $status_code;

    public static $definition = [
        'table' => 'ever_seo_image',
        'primary' => 'id_ever_seo_image',
        'multilang' => false,
        'fields' => [
            'id_seo_img' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
                'required' => true,
            ],
            'id_seo_product' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
            ],
            'id_shop' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
            ],
            'id_seo_lang' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
            ],
            'alt' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isString',
            ],
            'allowed_sitemap' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isBool',
            ],
            'status_code' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
            ],
        ],
    ];

    public static function getAllSeoImagesIds($id_shop)
    {
        $cache_id = 'EverPsSeoImage::getAllSeoImagesIds_'
        . (int) $id_shop;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_image');
            $sql->where('id_shop = '.(int) $id_shop);
            $return = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getSeoImage($id_ever_seo_image, $id_shop, $id_seo_lang)
    {
        $cache_id = 'EverPsSeoImage::getSeoImage_'
        . (int) $id_ever_seo_image
        . '_'
        . (int) $id_shop
        . '_'
        . (int) $id_seo_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_image');
            $sql->where(
                'id_ever_seo_image = '.(int) $id_ever_seo_image
            );
            $sql->where(
                'id_seo_lang = '.(int) $id_seo_lang
            );
            $sql->where(
                'id_shop = '.(int) $id_shop
            );
            $return = new self(Db::getInstance()->getValue($sql));
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function changeImageAltShortcodes($id_ever_seo_image, $id_seo_lang, $id_shop)
    {
        $image = new Image(
            (int) $id_ever_seo_image
        );
        $product = new Product(
            (int) $image->id_product,
            false,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $category = new Category(
            (int) $product->id_category_default,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $manufacturer = new Manufacturer(
            (int) $product->id_manufacturer,
            (int) $id_seo_lang
        );
        $supplier = new Supplier(
            (int) $product->id_supplier,
            (int) $id_seo_lang
        );
        $message = Configuration::getConfigInMultipleLangs(
            'EVERSEO_IMAGE_ALT_AUTO'
        );
        $shortcodes = [
            '[product_title]' => $product->name ? $product->name : '',
            '[product_reference]' => $product->reference ? $product->reference : '',
            '[product_desc]' => $product->description ? $product->description : '',
            '[product_short_desc]' => $product->description_short ? $product->description_short : '',
            '[product_default_category]' => $category->name ? $category->name : '',
            '[product_manufacturer]' => $manufacturer->name ? $manufacturer->name : '',
            '[product_supplier]' => $supplier->name ? $supplier->name : '',
            '[product_tags]' => $product->meta_keywords ? $product->meta_keywords : '',
            '[shop_name]' => (string) Configuration::get('PS_SHOP_NAME'),
            'NULL' => '', // Useful : remove empty strings in case of NULL
            'null' => '', // Useful : remove empty strings in case of null
        ];
        foreach ($shortcodes as $key => $value) {
            $message[(int) $id_seo_lang] = str_replace(
                (string) $key,
                (string) $value,
                (string) $message[(int) $id_seo_lang]
            );
            $message[(int) $id_seo_lang] = Hook::exec('actionChangeSeoShortcodes', [
                'content' => $message[(int) $id_seo_lang],
            ]);
        }
        if (!empty($message[(int) $id_seo_lang])) {
            return $message[(int) $id_seo_lang];
        }
    }
}
