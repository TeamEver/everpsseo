<?php
/**
 * Project : everpsredirect
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link https://www.team-ever.com
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class EverPsSeoRedirect extends ObjectModel
{
    public $id_ever_seo_redirect;
    public $not_found;
    public $everfrom;
    public $redirection;
    public $id_shop;
    public $active;
    public $count;
    public $code;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'ever_seo_redirect',
        'primary' => 'id_ever_seo_redirect',
        'fields' => array(
            'not_found' => array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isUrl',
                'required' => true,
                'size' => 255
            ),
            'everfrom' => array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isUrl',
                'required' => false,
                'size' => 255
            ),
            'redirection' => array(
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
                'required' => false
            ),
            'count' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isunsignedInt'
            ),
            'active' => array(
                'type' => self::TYPE_BOOL,
                'lang' => false,
                'validate' => 'isBool'
            ),
            'code' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isunsignedInt'
            ),

        ),
    );

    public static function ifRedirectExists($urlNotFound, $id_shop)
    {
        $notfound =
            'SELECT redirection
            FROM `'._DB_PREFIX_.'ever_seo_redirect`
            WHERE not_found = "'.pSQL($urlNotFound).'"
                AND active = 1
                AND id_shop = '.(int)$id_shop;

        return Db::getInstance()->getValue($notfound);
    }

    public static function ifNotFoundExists($urlNotFound, $id_shop, $from = false)
    {
        $notfound =
            'SELECT id_ever_seo_redirect
            FROM `'._DB_PREFIX_.'ever_seo_redirect`
            WHERE not_found = "'.pSQL($urlNotFound).'"
                AND id_shop = '.(int)$id_shop;

        $id_redirect = Db::getInstance()->getValue($notfound);

        if ((int)$id_redirect) {
            $increment = self::incrementCounter(
                (int)$id_redirect,
                (int)$id_shop
            );
            return $increment;
        } else {
            $notFound = new EverPsSeoRedirect();
            if ($from) {
                $notFound->everfrom = $from;
            }
            $notFound->not_found = $urlNotFound;
            $notFound->id_shop = (int)$id_shop;
            $notFound->count = 1;
            $notFound->active = 0;
            $notFound->code = (int)Configuration::get('EVERSEO_REDIRECT');
            $notFound->add();
            //returning false for searching new URL
            return false;
        }
    }

    public static function incrementCounter($id_redirect, $id_shop)
    {
        $count =
            'SELECT count
            FROM `'._DB_PREFIX_.'ever_seo_redirect`
            WHERE id_ever_seo_redirect = "'.(int)$id_redirect.'"
                AND id_shop = '.(int)$id_shop;

        $currentCount = Db::getInstance()->getValue($count);

        $update = Db::getInstance()->update(
            'ever_seo_redirect',
            array(
                'count'=>(int)$currentCount + 1,
            ),
            'id_ever_seo_redirect = '.(int)$id_redirect
        );

        return $update;
    }

    public static function getRedirectUrl($urls, $id_shop, $id_lang)
    {
        $sql =
            'SELECT DISTINCT physical_uri
                FROM `'._DB_PREFIX_.'shop_url`
                WHERE id_shop = '.(int)$id_shop;

        $pu =  Db::getInstance()->getValue($sql);

        $urls = array_filter(array_diff($urls, preg_split("#/#", $pu)));
        $urls = array_filter(array_diff($urls, preg_split("#-#", $pu)));
        $urls = preg_replace('/[0-9]+/', '', $urls);
        $urls = str_replace('_', '-', $urls);
        $urls = implode('-', $urls);

        if ((int)Configuration::get('EVERSEO_ORDER_BY') == 1) {
            $orderby = 'ASC';
        } else {
            $orderby = 'DESC';
        }

        $searchedTerms = array();
        $keyword = explode('-', $urls);
        foreach ($keyword as $word) {
            if (Tools::strlen($word) > 3) {
                $searchedTerms[] = $word;
            }
        }

        $priorities = (int)Configuration::get('EVERSEO_PRIORITY');
        $redirection = array();
        foreach ($searchedTerms as $term) {
            if (isset($term)) {
                switch ($priorities) {
                    case 1:
                        $redirection[] = self::searchProduct($term, (int)$id_shop, (int)$id_lang, $orderby);
                        $redirection[] = self::searchCategory($term, (int)$id_shop, (int)$id_lang, $orderby);
                        $redirection[] = self::searchTag($term, (int)$id_shop, (int)$id_lang, $orderby);
                        break;

                    case 2:
                        $redirection[] = self::searchProduct($term, (int)$id_shop, (int)$id_lang, $orderby);
                        $redirection[] = self::searchTag($term, (int)$id_shop, (int)$id_lang, $orderby);
                        $redirection[] = self::searchCategory($term, (int)$id_shop, (int)$id_lang, $orderby);
                        break;

                    case 3:
                        $redirection[] = self::searchCategory($term, (int)$id_shop, (int)$id_lang, $orderby);
                        $redirection[] = self::searchProduct($term, (int)$id_shop, (int)$id_lang, $orderby);
                        $redirection[] = self::searchTag($term, (int)$id_shop, (int)$id_lang, $orderby);
                        break;

                    case 4:
                        $redirection[] = self::searchCategory($term, (int)$id_shop, (int)$id_lang, $orderby);
                        $redirection[] = self::searchTag($term, (int)$id_shop, (int)$id_lang, $orderby);
                        $redirection[] = self::searchProduct($term, (int)$id_shop, (int)$id_lang, $orderby);
                        break;

                    case 5:
                        $redirection[] = self::searchProduct($term, (int)$id_shop, (int)$id_lang, $orderby);
                        $redirection[] = self::searchCategory($term, (int)$id_shop, (int)$id_lang, $orderby);
                        $redirection[] = self::searchTag($term, (int)$id_shop, (int)$id_lang, $orderby);
                        break;

                    case 6:
                        $redirection[] = self::searchTag($term, (int)$id_shop, (int)$id_lang, $orderby);
                        $redirection[] = self::searchProduct($term, (int)$id_shop, (int)$id_lang, $orderby);
                        $redirection[] = self::searchCategory($term, (int)$id_shop, $id_lang, $orderby);
                        break;

                    default:
                        $redirection[] = self::searchTag($term, (int)$id_shop, (int)$id_lang, $orderby);
                        $redirection[] = self::searchCategory($term, (int)$id_shop, (int)$id_lang, $orderby);
                        $redirection[] = self::searchProduct($term, (int)$id_shop, (int)$id_lang, $orderby);
                        break;
                }
            }
        }
        $return = array_reverse(array_filter($redirection));
        if (is_array($return) && count($return) > 0) {
            $return = $return[0];
        }
        return $return;
    }

    public static function searchProduct($string, $id_shop, $id_lang, $orderby)
    {
        if ((int)Configuration::get('EVERSEO_PRODUCT')) {
            $sql = 'SELECT DISTINCT pl.id_product
            FROM `'._DB_PREFIX_.'product_lang` pl
            INNER JOIN `'._DB_PREFIX_.'product` p
            ON p.id_product = pl.id_product
            WHERE pl.id_shop = '.(int)$id_shop.'
            AND pl.id_lang = '.(int)$id_lang.'
            AND p.active = 1
            AND (
                pl.name LIKE "%'.pSQL($string).'%"
                OR pl.description LIKE "%'.pSQL($string).'%"
                OR pl.meta_title LIKE "%'.pSQL($string).'%"
                OR pl.meta_description LIKE "%'.pSQL($string).'%"
                OR pl.link_rewrite LIKE "%'.pSQL($string).'%"
                OR INSTR(pl.name, "'.pSQL($string).'") > 0
                OR INSTR(pl.description, "'.pSQL($string).'") > 0
                OR INSTR(pl.description_short, "'.pSQL($string).'") > 0
                OR INSTR(pl.meta_title, "'.pSQL($string).'") > 0
                OR INSTR(pl.meta_description, "'.pSQL($string).'") > 0
                OR INSTR(pl.link_rewrite, "'.pSQL($string).'") > 0
            )
            ORDER BY pl.id_product '.pSQL($orderby);
            $id_product = Db::getInstance()->getValue($sql);
            if ((int)$id_product) {
                $link = new Link();
                $productUrl = $link->getProductLink((int)$id_product);
                return $productUrl;
            } else {
                return false;
            }
        }
    }

    public static function searchCategory($string, $id_shop, $id_lang, $orderby)
    {
        if ((int)Configuration::get('EVERSEO_CATEGORY')) {
            $sql = 'SELECT DISTINCT cl.id_category
            FROM `'._DB_PREFIX_.'category_lang` cl
            INNER JOIN `'._DB_PREFIX_.'category` c
            ON c.id_category = cl.id_category
            WHERE cl.id_shop = '.(int)$id_shop.'
            AND cl.id_lang = '.(int)$id_lang.'
            AND c.active = 1
            AND (
                cl.name LIKE "%'.pSQL($string).'%"
                OR cl.description LIKE "%'.pSQL($string).'%"
                OR cl.meta_title LIKE "%'.pSQL($string).'%"
                OR cl.meta_description LIKE "%'.pSQL($string).'%"
                OR cl.link_rewrite LIKE "%'.pSQL($string).'%"
            )
            ORDER BY cl.id_category '.pSQL($orderby);
            $id_category = Db::getInstance()->getValue($sql);
            if ((int)$id_category) {
                $link = new Link();
                $categoryUrl = $link->getCategoryLink((int)$id_category);
                return $categoryUrl;
            } else {
                return false;
            }
        }
    }

    public static function searchTag($string, $id_shop, $id_lang, $orderby)
    {
        if ((int)Configuration::get('EVERSEO_TAGS')) {
            $sql = 'SELECT DISTINCT pt.id_product
            FROM `'._DB_PREFIX_.'product_tag` pt
            INNER JOIN `'._DB_PREFIX_.'tag` t
            ON pt.id_tag = t.id_tag
            INNER JOIN `'._DB_PREFIX_.'tag_count` tc
            ON tc.id_tag = t.id_tag
            WHERE tc.id_shop = '.(int)$id_shop.'
            AND tc.id_lang = '.(int)$id_lang.'
            AND t.name LIKE "%'.pSQL($string).'%"
            ORDER BY t.id_tag '.pSQL($orderby);
            $id_product = Db::getInstance()->getValue($sql);
            if ((int)$id_product) {
                $link = new Link();
                $productUrl = $link->getProductLink((int)$id_product);
                return $productUrl;
            } else {
                return false;
            }
        }
    }

    public static function getRedirects($id_shop, $active = true)
    {
        $siteUrl = Tools::getHttpHost(true).__PS_BASE_URI__;
        $return = array();
        $query = 'SELECT * FROM '._DB_PREFIX_.'ever_seo_redirect
        WHERE active = '.(bool)$active.'
        AND id_shop = '.(int)$id_shop;
        $redirects = Db::getInstance()->ExecuteS($query);
        foreach ($redirects as $redirect) {
            $redirect_obj = new self(
                (int)$redirect['id_ever_seo_redirect']
            );
            $redirect_obj->not_found = '/'.str_replace($siteUrl, '', $redirect_obj->not_found);
            $redirect_obj->not_found = str_replace('//', '/', $redirect_obj->not_found);
            $return[] = $redirect_obj;
        }
        return $return;
    }
}
