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

class EverPsSeoCms extends ObjectModel
{
    public $id_seo_cms;
    public $id_shop;
    public $id_seo_lang;
    public $meta_title;
    public $meta_description;
    public $social_title;
    public $social_description;
    public $social_img_url;
    public $keywords;
    public $indexable;
    public $follow;
    public $allowed_sitemap;
    public $count;
    public $status_code;

    public static $definition = [
        'table' => 'ever_seo_cms',
        'primary' => 'id_ever_seo_cms',
        'multilang' => false,
        'fields' => [
            'id_seo_cms' => [
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

    public static function getAllSeoCmsIds($id_shop)
    {
        $cache_id = 'EverPsSeoCms::getAllSeoCmsIds_'
        . (int) $id_shop;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_cms');
            $sql->where('id_shop = ' . (int) $id_shop);
            $return = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getCmsNameBySeoId($id_seo_cms, $id_lang)
    {
        $cache_id = 'EverPsSeoCms::getCmsNameBySeoId_'
        . (int) $id_seo_cms
        .'_'
        .$id_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = 'SELECT meta_title
            FROM ' . _DB_PREFIX_ . 'cms_lang
            WHERE id_cms = ' . (int) $id_seo_cms.'
            AND id_lang = ' . (int) $id_lang.'';
            $return = Db::getInstance()->getValue($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getSeoCms($id_seo_cms, $id_shop, $id_seo_lang)
    {
        $cache_id = 'EverPsSeoCms::getSeoCms_'
        . (int) $id_seo_cms
        . '_'
        . (int) $id_shop
        . '_'
        . (int) $id_seo_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_cms');
            $sql->where(
                'id_seo_cms = ' . (int) $id_seo_cms
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

    public static function changeCmsTitleShortcodes($id_seo_cms, $id_seo_lang, $id_shop)
    {
        $cms = new CMS(
            (int) $id_seo_cms,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $message = Configuration::getConfigInMultipleLangs(
            'EVERSEO_CMS_TITLE_AUTO'
        );
        $shortcodes = [
            '[cms_title]' => $cms->meta_title ? $cms->meta_title : '',
            '[cms_desc]' => $cms->content ? $cms->content : '',
            '[cms_tags]' => $cms->meta_keywords ? $cms->meta_keywords : '',
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

    public static function changeCmsMetadescShortcodes($id_seo_cms, $id_seo_lang, $id_shop)
    {
        $cms = new CMS(
            (int) $id_seo_cms,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $message = Configuration::getConfigInMultipleLangs(
            'EVERSEO_CMS_METADESC_AUTO'
        );
        $shortcodes = [
            '[cms_title]' => $cms->meta_title ? $cms->meta_title : '',
            '[cms_desc]' => $cms->content ? $cms->content : '',
            '[cms_tags]' => $cms->meta_keywords ? $cms->meta_keywords : '',
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
