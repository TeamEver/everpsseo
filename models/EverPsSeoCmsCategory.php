<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @see https://www.team-ever.com
 */

class EverPsSeoCmsCategory extends ObjectModel
{
    public $id_seo_cms_category;
    public $id_shop;
    public $id_seo_lang;
    public $meta_title;
    public $meta_description;
    public $social_title;
    public $social_description;
    public $social_img_url;
    public $indexable;
    public $follow;
    public $allowed_sitemap;
    public $count;
    public $status_code;

    public static $definition = array(
        'table' => 'ever_seo_cms_category',
        'primary' => 'id_ever_seo_cms_category',
        'multilang' => false,
        'fields' => array(
            'id_seo_cms_category' => array(
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

    public static function getCmsCategoryNameBySeoId($id_seo_cms_category, $id_lang)
    {
        $cache_id = 'EverPsSeoCmsCategory::getCmsCategoryNameBySeoId_'
        .(int)$id_seo_cms_category
        .'_'
        .(int)$id_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = 'SELECT name
            FROM '._DB_PREFIX_.'cms_category_lang
            WHERE id_cms_category = '.(int)$id_seo_cms_category.'
            AND id_lang = '.(int)$id_lang.'';
            $return = Db::getInstance()->getValue($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getSeoCmsCategory($id_seo_cms_category, $id_shop, $id_seo_lang)
    {
        $cache_id = 'EverPsSeoCmsCategory::getSeoCmsCategory_'
        .(int)$id_seo_cms_category
        .'_'
        .(int)$id_shop
        .'_'
        .(int)$id_seo_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_cms_category');
            $sql->where(
                'id_seo_cms_category = '.(int)$id_seo_cms_category
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
}
