<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link https://www.team-ever.com
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Search\SearchProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
class SearchController extends SearchControllerCore

{
    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function init()
    {
        parent::init();
        $this->search_string = Tools::getValue('s');
        if (!$this->search_string) {
            $this->search_string = Tools::getValue('search_query');
        }
        
        $redirect = $this->redirectToObject(
            $this->search_string,
            (int)$this->context->language->id,
            (int)$this->context->shop->id
        );
        if (Validate::isUrl($redirect)) {
            Tools::redirect(
                $redirect,
                __PS_BASE_URI__,
                null,
                'Status: 301 Moved Permanently, false, 301'
            );
        }
        
        $this->search_tag = Tools::getValue('tag');
        $this->context->smarty->assign(
            array(
                'search_string' => $this->search_string,
                'search_tag' => $this->search_tag,
            )
        );
    }
    
    /**
     * Get redirection link to obj
     * @param searched string
     * @param int id lang
     * @param int id shop
     * @return link | false
     * @see module configuration
    */
    protected function redirectToObject($search_string, $id_lang, $id_shop)
    {
        $sql = new DbQuery;
        $search = Search::sanitize(
            $search_string,
            (int)$id_lang,
            false,
            Context::getContext()->language->iso_code
        );
        if ((bool)Configuration::get('EVER_SEARCH_CATEGORIES', null, null, $id_shop) === true) {
            $sql->select('cl.id_category');
            $sql->from('category_lang', 'cl');
            $sql->leftJoin(
                'category_shop',
                'cs',
                'cl.id_category = cs.id_category'
            );
            $sql->leftJoin(
                'category',
                'c',
                'cl.id_category = c.id_category'
            );
            $sql->where('cl.name LIKE "'.pSQL($search).'"');
            $sql->where('cs.id_shop = '.(int)$id_shop);
            $sql->where('c.active = 1');
            $sql->where('cl.id_lang = '.(int)$id_lang);
            $result = Db::getInstance()->executeS($sql);
            if (count($result)) {
                $obj = new Category(
                    (int)$result[0]['id_category'],
                    (int)$id_lang,
                    (int)$id_shop
                );
                if (!Validate::isLoadedObject($obj)) {
                    return false;
                }
                if ($obj->checkAccess((int)Context::getContext()->customer->id) === false) {
                    return;
                }
                return Context::getContext()->link->getCategoryLink(
                    $obj,
                    null,
                    (int)$id_lang,
                    null,
                    (int)$id_shop
                );
            }
        }
        if ((bool)Configuration::get('EVER_SEARCH_MANUFACTURERS', null, null, $id_shop) === true) {
            $id_obj = Manufacturer::getIdByName($search);
            $obj = new Manufacturer(
                $id_obj,
                (int)$id_lang
            );
            if (!Validate::isLoadedObject($obj)
                || !$obj->active
                || !$obj->isAssociatedToShop()
            ) {
                return false;
            }
            return Context::getContext()->link->getManufacturerLink(
                $obj,
                null,
                (int)$id_lang,
                (int)$id_shop
            );
        }
        if ((bool)Configuration::get('EVER_SEARCH_SUPPLIERS', null, null, $id_shop) === true) {
            $sql->select('s.id_supplier');
            $sql->from('supplier', 's');
            $sql->leftJoin(
                'supplier_shop',
                'ss',
                's.id_supplier = ss.id_supplier'
            );
            $sql->where('s.active = 1');
            $sql->where('ss.id_shop = '.(int)$id_shop);
            $sql->where('s.name LIKE "'.pSQL($search).'"');
            $result = Db::getInstance()->executeS($sql);
            if (count($result)) {
                $obj = new Supplier(
                    $result[0]['id_supplier'],
                    (int)$id_lang
                );
                if (!Validate::isLoadedObject($obj)) {
                    return false;
                }
                return Context::getContext()->link->getSupplierLink(
                    $obj,
                    null,
                    (int)$id_lang,
                    (int)$id_shop
                );
            }
        }
        if ((bool)Configuration::get('EVER_SEARCH_PRODUCTS', null, null, $id_shop) === true) {
            $sql->select('pl.id_product');
            $sql->from('product_lang', 'pl');
            $sql->leftJoin(
                'product_shop',
                'ps',
                'pl.id_product = ps.id_product'
            );
            $sql->where('pl.id_lang = '.(int)$id_lang);
            $sql->where('pl.name LIKE "'.pSQL($search).'"');
            $sql->where('ps.active = 1');
            $sql->where('ps.visibility = "both" OR visibility = "search"');
            $sql->where('ps.id_shop = '.(int)$id_shop);
            $result = Db::getInstance()->executeS($sql);
            if (count($result)) {
                $obj = new Product(
                    (int)$result[0]['id_product'],
                    false,
                    (int)$id_lang,
                    (int)$id_shop
                );
                if (!Validate::isLoadedObject($obj)) {
                    return false;
                }
                if ($obj->checkAccess((int)Context::getContext()->customer->id) === false) {
                    return;
                }
                return Context::getContext()->link->getProductLink(
                    $obj,
                    null,
                    null,
                    null,
                    (int)$id_lang,
                    (int)$id_shop
                );
            }
        }
        return false;
    }
}
