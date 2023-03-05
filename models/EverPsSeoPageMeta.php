<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @see https://www.team-ever.com
 */

class EverPsSeoPageMeta extends ObjectModel
{
    public $id_seo_pagemeta;
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

    public static $definition = array(
        'table' => 'ever_seo_pagemeta',
        'primary' => 'id_ever_seo_pagemeta',
        'multilang' => false,
        'fields' => array(
            'id_seo_pagemeta' => array(
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

    public static function getAllSeoPagemetasIds($id_shop)
    {
        $cache_id = 'EverPsSeoPageMeta::getAllSeoPagemetasIds_'
        .(int)$id_shop;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_pagemeta');
            $sql->where('id_shop = '.(int)$id_shop);
            $return = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getPageNameBySeoId($id_seo_pagemeta, $id_lang)
    {
        $cache_id = 'EverPsSeoPageMeta::getPageNameBySeoId_'
        .(int)$id_seo_pagemeta
        .'_'
        .(int)$id_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = 'SELECT title
            FROM '._DB_PREFIX_.'meta_lang
            WHERE id_meta = '.(int)$id_seo_pagemeta.'
            AND id_lang = '.(int)$id_lang.'';
            $return = Db::getInstance()->getValue($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getSeoPageMeta($id_seo_pagemeta, $id_shop, $id_seo_lang)
    {
        $cache_id = 'EverPsSeoPageMeta::getSeoPageMeta_'
        .(int)$id_seo_pagemeta
        .'_'
        .(int)$id_shop
        .'_'
        .(int)$id_seo_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_pagemeta');
            $sql->where(
                'id_seo_pagemeta = '.(int)$id_seo_pagemeta
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

    public static function changePagemetaTitleShortcodes($id_seo_pagemeta, $id_seo_lang, $id_shop)
    {
        $pagemeta = new Meta(
            (int)$id_seo_pagemeta,
            (int)$id_seo_lang,
            (int)$id_shop
        );
        $message = Configuration::getConfigInMultipleLangs(
            'EVERSEO_PAGEMETA_TITLE_AUTO'
        );
        $shortcodes = array(
            '[pagemeta_title]' => $pagemeta->title ? $pagemeta->title : '',
            '[pagemeta_desc]' => $pagemeta->description ? $pagemeta->description : '',
            '[pagemeta_tags]' => $pagemeta->keywords ? $pagemeta->keywords : '',
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

    public static function changePagemetaMetadescShortcodes($id_seo_pagemeta, $id_seo_lang, $id_shop)
    {
        $pagemeta = new Meta(
            (int)$id_seo_pagemeta,
            (int)$id_seo_lang,
            (int)$id_shop
        );
        $message = Configuration::getConfigInMultipleLangs(
            'EVERSEO_PAGEMETA_METADESC_AUTO'
        );
        $shortcodes = array(
            '[pagemeta_title]' => $pagemeta->title ? $pagemeta->title : '',
            '[pagemeta_desc]' => $pagemeta->description ? $pagemeta->description : '',
            '[pagemeta_tags]' => $pagemeta->keywords ? $pagemeta->keywords : '',
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

    public static function cleanNonConfigurableMetas()
    {
        $sql = new DbQuery();
        $sql->select('id_meta');
        $sql->from('meta');
        $sql->where('configurable = 0');
        $metas = Db::getInstance()->executeS($sql);
        foreach ($metas as $m) {
            Db::getInstance()->delete(
                'ever_seo_pagemeta',
                'id_seo_pagemeta = '.(int)$m['id_meta']
            );
        }
    }
}
