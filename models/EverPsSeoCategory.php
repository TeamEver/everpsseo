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

class EverPsSeoCategory extends ObjectModel
{
    public $id_seo_category;
    public $id_shop;
    public $id_seo_lang;
    public $meta_title;
    public $meta_description;
    public $social_title;
    public $social_description;
    public $social_img_url;
    public $bottom_content;
    public $link_rewrite;
    public $canonical;
    public $keywords;
    public $indexable;
    public $follow;
    public $allowed_sitemap;
    public $count;
    public $status_code;

    public static $definition = [
        'table' => 'ever_seo_category',
        'primary' => 'id_ever_seo_category',
        'multilang' => false,
        'fields' => [
            'id_seo_category' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
                'required' => true,
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
            'meta_title' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isString',
            ],
            'meta_description' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isString',
            ],
            'social_title' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isString',
            ],
            'social_description' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isCleanHtml',
            ],
            'social_img_url' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isUrl',
            ],
            'bottom_content' => [
                'type' => self::TYPE_HTML,
                'lang' => false,
                'validate' => 'isCleanHtml',
            ],
            'link_rewrite' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isLinkRewrite',
            ],
            'canonical' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isLinkRewrite',
            ],
            'keywords' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isString',
            ],
            'indexable' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isBool',
            ],
            'follow' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isBool',
            ],
            'allowed_sitemap' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isBool',
            ],
            'count' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
            ],
            'status_code' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
            ],
        ],
    ];

    public static function getAllSeoCategoriesIds($id_shop)
    {
        $cache_id = 'EverPsSeoCategory::getAllSeoCategoriesIds_'
        . (int) $id_shop;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_category');
            $sql->where('id_shop = ' . (int) $id_shop);
            $return = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getCategoryNameBySeoId($id_seo_category, $id_lang)
    {
        $cache_id = 'EverPsSeoCategory::getCategoryNameBySeoId_'
        . (int) $id_seo_category
        . '_'
        . (int) $id_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = 'SELECT name
            FROM ' . _DB_PREFIX_ . 'category_lang
            WHERE id_category = ' . (int) $id_seo_category . '
            AND id_lang = ' . (int) $id_lang . '';
            $return = Db::getInstance()->getValue($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getSeoCategory($id_seo_category, $id_shop, $id_seo_lang)
    {
        $cache_id = 'EverPsSeoCategory::getSeoCategory_'
        . (int) $id_seo_category
        . '_'
        . (int) $id_shop
        . '_'
        . (int) $id_seo_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_category');
            $sql->where(
                'id_seo_category = ' . (int) $id_seo_category
            );
            $sql->where(
                'id_seo_lang = ' . (int) $id_seo_lang
            );
            $sql->where(
                'id_shop = ' . (int) $id_shop
            );
            $return = new self(Db::getInstance()->getValue($sql));
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function changeCategoryTitleShortcodes($id_seo_category, $id_seo_lang, $id_shop)
    {
        $category = new Category(
            (int) $id_seo_category,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $parent_name = '';
        if ((int) $category->id_parent > 0) {
            $parent = new Category(
                (int) $category->id_parent,
                (int) $id_seo_lang,
                (int) $id_shop
            );
            $parent_name = $parent->name;
        }
        $message = Configuration::getConfigInMultipleLangs(
            'EVERSEO_CATEGORY_TITLE_AUTO'
        );
        $shortcodes = [
            '[category_title]' => $category->name ? $category->name : '',
            '[category_desc]' => $category->description ? $category->description : '',
            '[category_tags]' => $category->meta_keywords ? $category->meta_keywords : '',
            '[parent]' => $parent_name,
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

    public static function changeCategoryMetadescShortcodes($id_seo_category, $id_seo_lang, $id_shop)
    {
        $category = new Category(
            (int) $id_seo_category,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $parent_name = '';
        if ((int) $category->id_parent > 0) {
            $parent = new Category(
                (int) $category->id_parent,
                (int) $id_seo_lang,
                (int) $id_shop
            );
            $parent_name = $parent->name;
        }
        $message = Configuration::getConfigInMultipleLangs(
            'EVERSEO_CATEGORY_METADESC_AUTO'
        );
        $shortcodes = [
            '[category_title]' => $category->name ? $category->name : '',
            '[category_desc]' => $category->description ? $category->description : '',
            '[category_tags]' => $category->meta_keywords ? $category->meta_keywords : '',
            '[parent]' => $parent_name,
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

    public static function changeCategoryDescShortcodes($id_seo_category, $id_seo_lang, $id_shop)
    {
        $category = new Category(
            (int) $id_seo_category,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $children_names = '';
        if (Category::hasChildren((int) $category->id, (int) $id_seo_lang, true, (int) $id_shop)) {
            $children = Category::getChildren(
                (int) $category->id,
                (int) $id_seo_lang,
                true,
                (int) $id_shop
            );
            foreach ($children as $key => $child) {
                if ($key == 0) {
                    $children_names .= $child['name'];
                } else {
                    $children_names .= $child['name'].', ';
                }
            }
        }
        $parent_name = '';
        if ((int) $category->id_parent > 0) {
            $parent = new Category(
                (int) $category->id_parent,
                (int) $id_seo_lang,
                (int) $id_shop
            );
            $parent_name = $parent->name;
        }
        $message = Configuration::getConfigInMultipleLangs(
            'CATEGORY_DESC_GENERATE'
        );
        $shortcodes = [
            '[category_title]' => $category->name ? $category->name : '',
            '[category_desc]' => $category->description ? $category->description : '',
            '[children]' => $children_names,
            '[parent]' => $parent_name,
            '[category_tags]' => $category->meta_keywords ? $category->meta_keywords : '',
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
