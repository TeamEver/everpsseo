<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @see https://www.team-ever.com
 */

class EverPsSeoShortcode extends ObjectModel
{
    public $id_ever_seo_shortcode;
    public $shortcode;
    public $id_shop;
    public $id_lang;
    public $title;
    public $content;

    public static $definition = array(
        'table' => 'ever_seo_shortcode',
        'primary' => 'id_ever_seo_shortcode',
        'multilang' => true,
        'fields' => array(
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isunsignedInt'
            ),
            'shortcode' => array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isString',
                'required' => true
            ),
            // lang fields
            'title' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
                'required' => true
            ),
            'content' => array(
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isCleanHtml',
                'required' => false
            ),
        )
    );

    public static function getAllSeoShortcodes($id_shop, $id_lang)
    {
        $cache_id = 'EverPsSeoShortcode::getAllSeoShortcodes_'
        .(int) $id_shop
        .'_'
        .(int) $id_shop;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_shortcode');
            $shortcodes = Db::getInstance()->executeS($sql);
            $return = [];
            foreach ($shortcodes as $short_array) {
                $shortcode = new self(
                    (int) $short_array['id_ever_seo_shortcode'],
                    (int) $id_lang,
                    (int) $id_shop
                );
                $return[] = $shortcode;
            }
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getAllSeoShortcodeIds($id_shop)
    {
        $cache_id = 'EverPsSeoShortcode::getAllSeoShortcodeIds_'
        .(int) $id_shop;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_shortcode');
            $sql->where('id_shop = '.(int) $id_shop);
            $return = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getSeoShortcodeById($id_seo_shortcode, $id_shop, $id_lang)
    {
        $cache_id = 'EverPsSeoShortcode::getSeoShortcodeById_'
        .(int) $id_seo_shortcode
        .'_'
        .(int) $id_shop
        .'_'
        .(int) $id_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_shortcode');
            $sql->where(
                'id_ever_seo_shortcode = '.(int) $id_seo_shortcode
            );
            $sql->where(
                'id_lang = '.(int) $id_lang
            );
            $sql->where(
                'id_shop = '.(int) $id_shop
            );
            $return = new self(
                (int)Db::getInstance()->getValue($sql),
                (int) $id_lang,
                (int) $id_shop
            );
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getEverSeoShortcode($shortcode, $id_shop, $id_lang)
    {
        $cache_id = 'EverPsSeoShortcode::getEverSeoShortcode_'
        .(string) $shortcode
        .'_'
        .(int) $id_shop
        .'_'
        .(int) $id_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_shortcode');
            $sql->where(
                'shortcode = '.pSQL($shortcode)
            );
            $sql->where(
                'id_lang = '.(int) $id_lang
            );
            $sql->where(
                'id_shop = '.(int) $id_shop
            );
            $shortcode = new self(
                (int)Db::getInstance()->getValue($sql),
                (int) $id_lang,
                (int) $id_shop
            );
            $return = $shortcode->content;
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }
}
