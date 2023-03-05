<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @see https://www.team-ever.com
 */

class EverPsSeoStats extends ObjectModel
{
    public static function getBestViewedProducts()
    {
        $link = new Link();
        $return = [];
        $sql = new DbQuery();
        $sql->select('id_seo_product, count');
        $sql->from('ever_seo_product');
        $sql->groupBy('id_seo_product');
        $sql->orderBy('count DESC');
        $sql->limit(10);
        $best = Db::getInstance()->executeS($sql);
        foreach ($best as $prod) {
            $product = new Product(
                (int)$prod['id_seo_product'],
                false,
                (int)Context::getContext()->language->id,
                (int)Context::getContext()->shop->id
            );
            $product->count = (int)$prod['count'];
            $product->url = $link->getProductLink(
                (int)$prod['id_seo_product'],
                null,
                null,
                null,
                (int)Context::getContext()->language->id,
                (int)Context::getContext()->shop->id
            );
            $return[] = $product;
        }
        if ($return) {
            return $return;
        }
    }

    public static function getBestViewedCategories()
    {
        $link = new Link();
        $return = [];
        $sql = new DbQuery();
        $sql->select('id_seo_category, count');
        $sql->from('ever_seo_category');
        $sql->groupBy('id_seo_category');
        $sql->orderBy('count DESC');
        $sql->limit(10);
        $best = Db::getInstance()->executeS($sql);
        foreach ($best as $cat) {
            $category = new Category(
                (int)$cat['id_seo_category'],
                (int)Context::getContext()->language->id,
                (int)Context::getContext()->shop->id
            );
            $category->count = (int)$cat['count'];
            $category->url = $link->getCategoryLink(
                (object)$category,
                null,
                (int)Context::getContext()->language->id,
                null,
                (int)Context::getContext()->shop->id
            );
            $return[] = $category;
        }
        if ($return) {
            return $return;
        }
    }

    public static function getBestViewed404()
    {
        $return = [];
        $sql = new DbQuery();
        $sql->select('id_ever_seo_redirect');
        $sql->from('ever_seo_redirect');
        $sql->groupBy('not_found');
        $sql->orderBy('count DESC');
        $sql->limit(10);
        $best = Db::getInstance()->executeS($sql);
        foreach ($best as $redir) {
            $redirect = new EverPsSeoRedirect(
                (int)$redir['id_ever_seo_redirect']
            );
            $return[] = $redirect;
        }
        if ($return) {
            return $return;
        }
    }

    public static function getBestViewedReferals()
    {
        $return = [];
        $sql = new DbQuery();
        $sql->select('id_ever_seo_backlink');
        $sql->from('ever_seo_backlink');
        $sql->groupBy('everfrom');
        $sql->orderBy('count DESC');
        $sql->limit(10);
        $best = Db::getInstance()->executeS($sql);
        foreach ($best as $ref) {
            $referal = new EverPsSeoBacklink(
                (int)$ref['id_ever_seo_backlink']
            );
            $return[] = $referal;
        }
        if ($return) {
            return $return;
        }
    }
}
