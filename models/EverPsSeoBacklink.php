<?php
/**
 * Project : everpsbacklink
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @see https://www.team-ever.com
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class EverPsSeoBacklink extends ObjectModel
{
    public $id_ever_seo_backlink;
    public $everfrom;
    public $everto;
    public $id_shop;
    public $count;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'ever_seo_backlink',
        'primary' => 'id_ever_seo_backlink',
        'fields' => array(
            'everfrom' => array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isUrl',
                'required' => true,
                'size' => 255
            ),
            'everto' => array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isUrl',
                'required' => false,
                'size' => 255
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isunsignedInt',
                'required' => true
            ),
            'count' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isunsignedInt'),

        ),
    );

    public static function ifBacklinkExists($everfrom, $everto, $id_shop)
    {
        $cache_id = 'EverPsSeoBacklink::ifBacklinkExists_'
        .(string) $everfrom
        .'_'
        .(string) $everto
        .'_'
        .(int) $id_shop;
        if (!Cache::isStored($cache_id)) {
            $backlink =
                'SELECT id_ever_seo_backlink
                FROM `' . _DB_PREFIX_ . 'ever_seo_backlink`
                WHERE everfrom = "'.pSQL($everfrom).'"
                    AND everto = "'.pSQL($everto).'"
                    AND id_shop = '.(int) $id_shop;
            $return = Db::getInstance()->getValue($backlink);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function incrementCounter($id_ever_seo_backlink, $id_shop)
    {
        $count =
            'SELECT count
            FROM `' . _DB_PREFIX_ . 'ever_seo_backlink`
            WHERE id_ever_seo_backlink = "'.(int) $id_ever_seo_backlink.'"
                AND id_shop = '.(int) $id_shop;

        $currentCount = Db::getInstance()->getValue($count);

        $update = Db::getInstance()->update(
            'ever_seo_backlink',
            array(
                'count'=>(int) $currentCount + 1,
            ),
            'id_ever_seo_backlink = '.(int) $id_ever_seo_backlink
        );

        return $update;
    }
}
