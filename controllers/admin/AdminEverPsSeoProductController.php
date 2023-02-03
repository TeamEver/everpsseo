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

require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoProduct.php';
require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoKeywordsStrategy.php';
require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoRedirect.php';

class AdminEverPsSeoProductController extends ModuleAdminController
{
    private $html;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = false;
        $this->table = 'ever_seo_product';
        $this->className = 'EverPsSeoProduct';
        $this->context = Context::getContext();
        $this->identifier = 'id_ever_seo_product';
        $this->isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;
        $this->img_folder = _PS_MODULE_DIR_.'everpsseo/views/img/p/';
        $this->img_url = Tools::getHttpHost(true).__PS_BASE_URI__.'/modules/everpsseo/views/img/p/';
        $moduleConfUrl  = 'index.php?controller=AdminModules&configure=everpsseo&token=';
        $moduleConfUrl .= Tools::getAdminTokenLite('AdminModules');
        $this->context->smarty->assign(array(
            'moduleConfUrl' => (string)$moduleConfUrl,
            'image_dir' => _PS_BASE_URL_.'/modules/everpsseo/views/img/'
        ));
        $this->_select = 'l.iso_code, pl.name, cl.name AS category_name';
        $this->_join =
            'LEFT JOIN `'._DB_PREFIX_.'ever_seo_lang` l
                ON (
                    l.`id_seo_lang` = a.`id_seo_lang`
                )
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                ON (
                    pl.`id_lang` = a.`id_seo_lang`
                    AND pl.id_product = a.id_seo_product
                )
            LEFT JOIN `'._DB_PREFIX_.'product` p
                ON (
                    p.id_product = a.id_seo_product
                )
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
                ON (
                    cl.`id_lang` = a.`id_seo_lang`
                    AND p.id_category_default = cl.id_category
                )';

        $this->_where = 'AND a.id_shop ='.(int)$this->context->shop->id;

        $this->_group = 'GROUP BY a.id_ever_seo_product';

        $this->fields_list = array(
            'id_ever_seo_product' => array(
                'title' => $this->l('ID'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'id_seo_product' => array(
                'title' => $this->l('ID Product'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'left',
                'width' => 'auto',
                'havingFilter' => true,
                'filter_key' => 'pl!name'
            ),
            'category_name' => array(
                'title' => $this->l('Default category'),
                'align' => 'left',
                'width' => 'auto',
                'havingFilter' => true,
                'filter_key' => 'cl!name'
            ),
            'meta_title' => array(
                'title' => $this->l('Meta title'),
                'align' => 'left',
                'width' => 'auto',
                'havingFilter' => true,
                'filter_key' => 'a!meta_title'
            ),
            'meta_description' => array(
                'title' => $this->l('Meta description'),
                'align' => 'left',
                'width' => 'auto',
                'havingFilter' => true,
                'filter_key' => 'a!meta_description'
            ),
            'link_rewrite' => array(
                'title' => $this->l('Link rewrite'),
                'align' => 'left',
                'width' => 'auto',
                'havingFilter' => true,
                'filter_key' => 'a!link_rewrite'
            ),
            'canonical' => array(
                'title' => $this->l('Canonical'),
                'align' => 'left',
                'width' => 'auto',
            ),
            'indexable' => array(
                'title' => $this->l('Indexable'),
                'type' => 'bool',
                'active' => 'indexable',
                'orderby' => false,
                'class' => 'fixed-width-sm'
            ),
            'follow' => array(
                'title' => $this->l('Follow'),
                'type' => 'bool',
                'active' => 'follow',
                'orderby' => false,
                'class' => 'fixed-width-sm'
            ),
            'allowed_sitemap' => array(
                'title' => $this->l('On sitemap'),
                'type' => 'bool',
                'active' => 'allowed_sitemap',
                'orderby' => false,
                'class' => 'fixed-width-sm'
            ),
            'iso_code' => array(
                'title' => $this->l('Lang'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'count' => array(
                'title' => $this->l('Views count'),
                'align' => 'left',
                'width' => 'auto'
            )
        );

        $this->colorOnBackground = true;

        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;

        if (Tools::getValue('id_ever_seo_product')) {
            $seoProduct = new EverPsSeoProduct(Tools::getValue('id_ever_seo_product'));
            $product = new Product(
                (int)$seoProduct->id_seo_product,
                false,
                (int)$id_lang,
                (int)$id_shop
            );
            $link = new Link();
            $objectUrl = $link->getProductLink(
                $product,
                null,
                null,
                null,
                (int)$this->context->language->id,
                (int)$this->context->shop->id
            );
            $editUrl  = 'index.php?controller=AdminProducts&id_product='.(int)$product->id.'';
            $editUrl .= '&updateproduct&token='.Tools::getAdminTokenLite('AdminProducts');
            $objectGSearch = str_replace(' ', '+', $product->name);

            $keywordsQlty = EverPsSeoKeywordsStrategy::getSeoProductNote(
                $seoProduct,
                $product
            );
            switch (true) {
                case ((int)$keywordsQlty['note'] <= 25):
                    $color = 'ever-danger';
                    break;

                case ((int)$keywordsQlty['note'] <= 50):
                    $color = 'ever-alert';
                    break;

                case ((int)$keywordsQlty['note'] <= 75):
                    $color = 'ever-warning';
                    break;

                case ((int)$keywordsQlty['note'] > 75):
                    $color = 'ever-success';
                    break;

                default:
                    $color = 'badge-secondary';
                    break;
            }
            $seoProduct->note = (int)$keywordsQlty['note'];
            $seoProduct->save();
            $this->context->smarty->assign(array(
                'headerObjectName' => $product->name,
                'objectGSearch' => $objectGSearch,
                'objectUrl' => $objectUrl,
                'editUrl' => $editUrl,
                'keywordsQlty' => $keywordsQlty['note'],
                'colorNotation' => $color,
                'errors' => $keywordsQlty['errors'],
            ));
        }

        parent::__construct();
    }

    public function initToolbar()
    {
        //Empty because of reasons :-)
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addjQueryPlugin('tagify', null, false);
        $this->addCSS(_PS_MODULE_DIR_.'everpsseo/views/css/ever.css');
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ($this->isSeven) {
            return Context::getContext()->getTranslator()->trans(
                $string,
                [],
                'Modules.Everpsseo.Admineverpsseoproductcontroller'
            );
        }

        return parent::l($string, $class, $addslashes, $htmlentities);
    }

    public function renderList()
    {
        $this->html = '';

        $this->addRowAction('edit');
        $this->addRowAction('ViewProduct');
        $this->addRowAction('EditProduct');

        $this->bulk_actions = array(
            'index' => array(
                'text' => $this->l('Index/Noindex'),
                'confirm' => $this->l('Switch index/noindex on selected items ?')
            ),
            'follow' => array(
                'text' => $this->l('Follow/Nofollow'),
                'confirm' => $this->l('Switch follow/nofollow on selected items ?')
            ),
            'sitemap' => array(
                'text' => $this->l('Sitemap or not'),
                'confirm' => $this->l('Switch allow/disallow sitemap on those selected items ?')
            ),
            'metatitle' => array(
                'text' => $this->l('Meta_title'),
                'confirm' => $this->l('Get default meta title from Prestashop ?')
            ),
            'metadescription' => array(
                'text' => $this->l('Meta description'),
                'confirm' => $this->l('Get default meta description from Prestashop ?')
            ),
            'metatitlename' => array(
                'text' => $this->l('Name as meta title'),
                'confirm' => $this->l('Set default name as meta title ?')
            ),
            'metadescriptiondesc' => array(
                'text' => $this->l('Short desc as meta desc'),
                'confirm' => $this->l('Set default short description as meta description ?')
            ),
            'metadescriptionlongdesc' => array(
                'text' => $this->l('Description as meta desc'),
                'confirm' => $this->l('Set default description as meta description ?')
            ),
            'metadescshortcodes' => array(
                'text' => $this->l('Use shortcodes for meta desc'),
                'confirm' => $this->l('Set meta description using shortcodes ?')
            ),
            'titleshortcodes' => array(
                'text' => $this->l('Use shortcodes for title'),
                'confirm' => $this->l('Set title using shortcodes ?')
            ),
        );

        if (Tools::isSubmit('submitBulkindex'.$this->table)) {
            $this->processBulkIndex();
        }

        if (Tools::isSubmit('submitBulkfollow'.$this->table)) {
            $this->processBulkFollow();
        }

        if (Tools::isSubmit('submitBulksitemap'.$this->table)) {
            $this->processBulkSitemap();
        }

        if (Tools::isSubmit('submitBulkmetatitle'.$this->table)) {
            $this->processBulkMetatitle();
        }

        if (Tools::isSubmit('submitBulkmetadescription'.$this->table)) {
            $this->processBulkMetadescription();
        }

        if (Tools::isSubmit('submitBulkmetatitlename'.$this->table)) {
            $this->processBulkMetatitlename();
        }

        if (Tools::isSubmit('submitBulkmetadescriptiondesc'.$this->table)) {
            $this->processBulkMetadescriptiondesc();
        }

        if (Tools::isSubmit('submitBulkmetadescriptionlongdesc'.$this->table)) {
            $this->processBulkMetadescriptionlongdesc();
        }

        if (Tools::isSubmit('submitBulkmetadescshortcodes'.$this->table)) {
            $this->processBulkMetadescShortcodes();
        }

        if (Tools::isSubmit('submitBulktitleshortcodes'.$this->table)) {
            $this->processBulkTitleShortcodes();
        }

        if (Tools::isSubmit('indexable'.$this->table)) {
            $this->processIndexable();
        }

        if (Tools::isSubmit('follow'.$this->table)) {
            $this->processFollow();
        }

        if (Tools::isSubmit('allowed_sitemap'.$this->table)) {
            $this->processSitemap();
        }

        $this->toolbar_title = $this->l('SEO setting : Products');

        $lists = parent::renderList();

        $this->html .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . '/everpsseo/views/templates/admin/header.tpl');
        if (count($this->errors)) {
            foreach ($this->errors as $error) {
                $this->html .= Tools::displayError($error);
            }
        }
        $this->html .= $lists;
        $this->html .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . '/everpsseo/views/templates/admin/footer.tpl');

        return $this->html;
    }

    public function renderForm()
    {
        if (Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            $this->errors[] = $this->l('You have to select a shop before creating or editing new SEO rules.');
        }

        if (count($this->errors)) {
            return false;
        }

        if (Tools::getValue('id_ever_seo_product')) {
            $seoProduct = new EverPsSeoProduct(
                (int)Tools::getValue('id_ever_seo_product')
            );
            if (file_exists($this->img_folder.$seoProduct->id_seo_product.'.jpg')) {
                $defaultUrlImage = $this->img_url.$seoProduct->id_seo_product.'.jpg';
            } else {
                $defaultUrlImage = Tools::getHttpHost(true).'/img/'.Configuration::get(
                    'PS_LOGO'
                );
            }
        } else {
            $defaultUrlImage = Tools::getHttpHost(true).'/img/'.Configuration::get(
                'PS_LOGO'
            );
        }
        $defaultImage = '<image src="'.(string)$defaultUrlImage.'"/>';

        $this->fields_form = array(
            'submit' => array(
                'name' => 'save',
                'title' => $this->l('Save'),
                'class' => 'button pull-right'
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Save and stay'),
                    'name' => 'submitAdd'.$this->table.'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                ),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta title'),
                    'maxchar' => 65,
                    'required' => true,
                    'name' => 'meta_title',
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta description'),
                    'maxchar' => 165,
                    'required' => true,
                    'name' => 'meta_description',
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Social networks title'),
                    'required' => true,
                    'name' => 'social_title',
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Social networks description'),
                    'required' => true,
                    'name' => 'social_description',
                    'lang' => false,
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Social networks media'),
                    'desc' => $this->l('Will replace product media'),
                    'hint' => $this->l('For sharing on social networks only'),
                    'name' => 'social_media',
                    'display_image' => true,
                    'image' => $defaultImage
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Bottom SEO content'),
                    'name' => 'bottom_content',
                    'lang' => false,
                    'autoload_rte' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('URL rewrite'),
                    'required' => true,
                    'name' => 'link_rewrite',
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('URL canonical'),
                    'required' => true,
                    'name' => 'canonical',
                    'lang' => false,
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->l('Keywords strategy'),
                    'desc' => $this->l('Will impact SEO note'),
                    'hint' => $this->l('Add keywords to your content !'),
                    'name' => 'keywords',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Allow index'),
                    'name' => 'indexable',
                    'lang' => false,
                    'is_bool' => true,
                    'desc' => $this->l('Allow product index'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Allow follow'),
                    'name' => 'follow',
                    'lang' => false,
                    'is_bool' => true,
                    'desc' => $this->l('Allow product follow'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Allow on sitemap'),
                    'name' => 'allowed_sitemap',
                    'lang' => false,
                    'is_bool' => true,
                    'desc' => $this->l('Set product on sitemap'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
            )
        );
        $lists = parent::renderForm();

        $this->html .= $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . '/everpsseo/views/templates/admin/headerobject.tpl'
        );
        if (count($this->errors)) {
            foreach ($this->errors as $error) {
                $this->html .= Tools::displayError($error);
            }
        }
        $this->html .= $lists;
        $this->html .= $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . '/everpsseo/views/templates/admin/footer.tpl'
        );

        return $this->html;
    }

    public function processIndexable()
    {
        $everProduct = new EverPsSeoProduct(
            (int)Tools::getValue('id_ever_seo_product')
        );

        $everProduct->indexable = !$everProduct->indexable;

        if (!$everProduct->save()) {
            $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
        }
    }

    public function processFollow()
    {
        $everProduct = new EverPsSeoProduct(
            (int)Tools::getValue('id_ever_seo_product')
        );

        $everProduct->follow = !$everProduct->follow;

        if (!$everProduct->save()) {
            $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
        }
    }

    public function processSitemap()
    {
        $everProduct = new EverPsSeoProduct(
            (int)Tools::getValue('id_ever_seo_product')
        );
        $product = new Product(
            (int)$everProduct->id_seo_product
        );

        if ((bool)$product->active) {
            $everProduct->allowed_sitemap = !$everProduct->allowed_sitemap;
        } else {
            $everProduct->allowed_sitemap = 0;
        }

        if (!$everProduct->save()) {
            $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('save')) {
            if (!Tools::getValue('link_rewrite')
                || !Validate::isLinkRewrite(Tools::getValue('link_rewrite'))
            ) {
                 $this->errors[] = $this->l('link_rewrite is invalid');
            }
            if (!Tools::getValue('canonical')
                || !Validate::isLinkRewrite(Tools::getValue('canonical'))
            ) {
                 $this->errors[] = $this->l('canonical is invalid');
            }
            if (Tools::getValue('keywords')
                && !Validate::isString(Tools::getValue('keywords'))
            ) {
                 $this->errors[] = $this->l('keywords is invalid');
            }
            if (!Tools::getValue('meta_title')
                || !Validate::isGenericName(Tools::getValue('meta_title'))
            ) {
                 $this->errors[] = $this->l('meta_title is invalid');
            }
            if (!Tools::getValue('meta_description')
                || !Validate::isGenericName(Tools::getValue('meta_description'))
            ) {
                 $this->errors[] = $this->l('meta_description is invalid');
            }
            if (Tools::getValue('social_title')
                && !Validate::isGenericName(Tools::getValue('social_title'))
            ) {
                 $this->errors[] = $this->l('social_title is invalid');
            }
            if (Tools::getValue('social_description')
                && !Validate::isGenericName(Tools::getValue('social_description'))
            ) {
                 $this->errors[] = $this->l('social_description is invalid');
            }
            if (Tools::getValue('indexable')
                && !Validate::isBool(Tools::getValue('indexable'))
            ) {
                 $this->errors[] = $this->l('indexable is invalid');
            }
            if (Tools::getValue('follow')
                && !Validate::isBool(Tools::getValue('follow'))
            ) {
                 $this->errors[] = $this->l('follow is invalid');
            }
            if (Tools::getValue('allowed_sitemap')
                && !Validate::isBool(Tools::getValue('allowed_sitemap'))
            ) {
                 $this->errors[] = $this->l('allowed_sitemap is invalid');
            }
            if (Tools::getValue('bottom_content')
                && !Validate::isCleanHtml(Tools::getValue('bottom_content'))
            ) {
                 $this->errors[] = $this->l('Bottom content is invalid');
            }
            if (!count($this->errors)) {
                $everProduct = new EverPsSeoProduct(
                    (int)Tools::getValue('id_ever_seo_product')
                );
                $product = new Product(
                    (int)$everProduct->id_seo_product,
                    false,
                    (int)$everProduct->id_seo_lang,
                    (int)$this->context->shop->id
                );
                $oldUrl = $product->link_rewrite;
                $newUrl = Tools::getValue('link_rewrite');
                // SEO Object
                $everProduct->meta_title = Tools::getValue('meta_title');
                $everProduct->meta_description = Tools::getValue('meta_description');
                $everProduct->social_title = Tools::getValue('social_title');
                $everProduct->social_description = Tools::getValue('social_description');
                $everProduct->bottom_content = Tools::getValue('bottom_content');
                $everProduct->link_rewrite = Tools::getValue('link_rewrite');
                $everProduct->canonical = Tools::getValue('canonical');
                $everProduct->keywords = Tools::getValue('keywords');
                $everProduct->indexable = Tools::getValue('indexable');
                $everProduct->follow = Tools::getValue('follow');
                $everProduct->allowed_sitemap = Tools::getValue('allowed_sitemap');
                // PS Object (saving triggers hook update object)
                $product->link_rewrite = Tools::getValue('link_rewrite');
                $product->meta_title = Tools::getValue('meta_title');
                $product->meta_description = Tools::getValue('meta_description');
                if ($newUrl != $oldUrl
                    && EverPsSeoRedirect::ifRedirectExists($oldUrl, (int)$this->context->shop->id)
                ) {
                    $redirect = new EverPsSeoRedirect();
                    $redirect->not_found = $oldUrl;
                    $redirect->redirection = $newUrl;
                    $redirect->id_shop = (int)$this->context->shop->id;
                    $redirect->active = true;
                    $redirect->save();
                }
                /* upload the image */
                if (isset($_FILES['social_media'])
                    && isset($_FILES['social_media']['tmp_name'])
                    && !empty($_FILES['social_media']['tmp_name'])
                ) {
                    Configuration::set('PS_IMAGE_GENERATION_METHOD', 1);
                    if (file_exists($this->img_folder.(int)$everProduct->id_seo_product.'.jpg')) {
                        unlink($this->img_folder.(int)$everProduct->id_seo_product.'.jpg');
                    }
                    if ($error = ImageManager::validateUpload($_FILES['social_media'])) {
                        $this->errors[] = $error;
                    } elseif (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS'))
                        || !move_uploaded_file($_FILES['social_media']['tmp_name'], $tmp_name)
                    ) {
                        return false;
                    } elseif (!ImageManager::resize(
                        $tmp_name,
                        $this->img_folder.(int)$everProduct->id_seo_product.'.jpg'
                    )) {
                        $this->errors[] = $this->l('An error occurred while attempting to upload the image.');
                    }
                    if (isset($tmp_name)) {
                        unlink($tmp_name);
                    }
                    $everProduct->social_img_url = $this->img_url
                    .(int)$everProduct->id_seo_product
                    .'.jpg';
                }
                $everProduct->save();
                // Beware, update hook is triggered
                if (!$product->save()) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return parent::postProcess();
                }
            }
        }
        return parent::postProcess();
    }

    public function displayViewProductLink($token, $id_ever_seo_product)
    {
        if (!$token) {
            return;
        }
        $everProduct = new EverPsSeoProduct(
            (int)$id_ever_seo_product
        );
        $edit_url  = 'index.php?controller=AdminProducts&id_product='.(int)$everProduct->id_seo_product.'';
        $edit_url .= '&updateproduct&token='.Tools::getAdminTokenLite('AdminProducts');

        $this->context->smarty->assign(array(
            'href' => $edit_url,
            'confirm' => null,
            'action' => $this->l('Edit Product')
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'everpsseo/views/templates/admin/helpers/lists/list_action_view_order.tpl'
        );
    }

    public function displayEditProductLink($token, $id_ever_seo_product)
    {
        if (!$token) {
            return;
        }
        $everProduct = new EverPsSeoProduct(
            (int)$id_ever_seo_product
        );
        $product = new Product(
            (int)$everProduct->id_seo_product,
            false,
            (int)Context::getContext()->language->id,
            (int)Context::getContext()->shop->id
        );
        $link = new Link();
        $view_url = $link->getProductLink(
            $product,
            null,
            null,
            null,
            (int)Context::getContext()->language->id,
            (int)Context::getContext()->shop->id
        );

        $this->context->smarty->assign(array(
            'href' => $view_url,
            'confirm' => null,
            'action' => $this->l('View Product')
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'everpsseo/views/templates/admin/helpers/lists/list_action_view_order.tpl'
        );
    }

    protected function processBulkIndex()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverProduct) {
            $everProduct = new EverPsSeoProduct(
                (int)$idEverProduct
            );

            $everProduct->indexable = !$everProduct->indexable;

            if (!$everProduct->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkFollow()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverProduct) {
            $everProduct = new EverPsSeoProduct(
                (int)$idEverProduct
            );

            $everProduct->follow = !$everProduct->follow;

            if (!$everProduct->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkSitemap()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverProduct) {
            $everProduct = new EverPsSeoProduct(
                (int)$idEverProduct
            );

            $everProduct->allowed_sitemap = !$everProduct->allowed_sitemap;

            if (!$everProduct->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkMetatitle()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverProduct) {
            $everProduct = new EverPsSeoProduct(
                (int)$idEverProduct
            );
            $product = new Product(
                (int)$everProduct->id_seo_product,
                false,
                (int)$everProduct->id_seo_lang,
                (int)$this->context->shop->id
            );

            $meta_title = Db::getInstance()->getValue(
                'SELECT meta_title FROM `'._DB_PREFIX_.'product_lang`
                WHERE id_product = '.pSQL($everProduct->id_seo_product).'
                AND id_lang = '.pSQL($everProduct->id_seo_lang)
            );

            if (!$meta_title) {
                continue;
            }

            $product->meta_title = $meta_title;

            if (!$product->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkMetadescription()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverProduct) {
            $everProduct = new EverPsSeoProduct(
                (int)$idEverProduct
            );
            $product = new Product(
                (int)$everProduct->id_seo_product,
                false,
                (int)$everProduct->id_seo_lang,
                (int)$this->context->shop->id
            );

            $meta_description = Db::getInstance()->getValue(
                'SELECT meta_description FROM `'._DB_PREFIX_.'product_lang`
                WHERE id_product = '.(int)$everProduct->id_seo_product.'
                AND id_lang = '.(int)$everProduct->id_seo_lang
            );

            if (!$meta_description) {
                continue;
            }

            $product->meta_description = $meta_description;
            // Hook update triggered
            if (!$product->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkMetatitlename()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverProduct) {
            $everProduct = new EverPsSeoProduct(
                (int)$idEverProduct
            );
            $product = new Product(
                (int)$everProduct->id_seo_product,
                false,
                (int)$everProduct->id_seo_lang,
                (int)$this->context->shop->id
            );

            $name = Db::getInstance()->getValue(
                'SELECT name FROM `'._DB_PREFIX_.'product_lang`
                WHERE id_product = '.(int)$everProduct->id_seo_product.'
                AND id_lang = '.(int)$everProduct->id_seo_lang
            );

            if (!$name) {
                continue;
            }

            $product->meta_title = $name;
            // Hook update triggered
            if (!$product->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkMetadescriptiondesc()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverProduct) {
            $everProduct = new EverPsSeoProduct(
                (int)$idEverProduct
            );
            $product = new Product(
                (int)$everProduct->id_seo_product,
                false,
                (int)$everProduct->id_seo_lang,
                (int)$this->context->shop->id
            );

            $description = Db::getInstance()->getValue(
                'SELECT description_short FROM `'._DB_PREFIX_.'product_lang`
                WHERE id_product = '.(int)$everProduct->id_seo_product.'
                AND id_lang = '.(int)$everProduct->id_seo_lang
            );

            if (!$description) {
                continue;
            }

            $product->meta_description = Tools::substr(
                strip_tags($description),
                0,
                160
            );
            // Hook update triggered
            if (!$product->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkMetadescriptionlongdesc()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverProduct) {
            $everProduct = new EverPsSeoProduct(
                (int)$idEverProduct
            );
            $product = new Product(
                (int)$everProduct->id_seo_product,
                false,
                (int)$everProduct->id_seo_lang,
                (int)$this->context->shop->id
            );

            $description = Db::getInstance()->getValue(
                'SELECT description FROM `'._DB_PREFIX_.'product_lang`
                WHERE id_product = '.(int)$everProduct->id_seo_product.'
                AND id_lang = '.(int)$everProduct->id_seo_lang
            );

            if (!$description) {
                continue;
            }

            $product->meta_description = Tools::substr(
                strip_tags($description),
                0,
                160
            );
            // Hook update triggered
            if (!$product->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkTitleShortcodes()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverProduct) {
            $everProduct = new EverPsSeoProduct(
                (int)$idEverProduct
            );
            $product = new Product(
                (int)$everProduct->id_seo_product,
                false,
                (int)$everProduct->id_seo_lang,
                (int)$this->context->shop->id
            );

            $title = EverPsSeoProduct::changeProductTitleShortcodes(
                (int)$everProduct->id_seo_product,
                (int)$everProduct->id_seo_lang,
                (int)$this->context->shop->id
            );

            if (!$title) {
                continue;
            }

            $product->meta_title = Tools::substr(
                strip_tags($title),
                0,
                60
            );

            $sql = 'UPDATE `'._DB_PREFIX_.'product_lang`
            SET meta_title = "'.pSQL($product->meta_title).'"
            WHERE id_lang = '.(int)$everProduct->id_seo_lang.'
            AND id_shop = '.(int)$this->context->shop->id.'
            AND id_product = '.(int)$product->id;

            $sql2 = 'UPDATE `'._DB_PREFIX_.'ever_seo_product`
            SET meta_title = "'.pSQL($product->meta_title).'"
            WHERE id_seo_lang = '.(int)$everProduct->id_seo_lang.'
            AND id_shop = '.(int)$this->context->shop->id.'
            AND id_seo_product = '.(int)$product->id;
            if (!Db::getInstance()->execute($sql)) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            } else {
                Db::getInstance()->execute($sql2);
            }
        }
    }

    protected function processBulkMetadescShortcodes()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverProduct) {
            $everProduct = new EverPsSeoProduct(
                (int)$idEverProduct
            );
            $product = new Product(
                (int)$everProduct->id_seo_product,
                false,
                (int)$everProduct->id_seo_lang,
                (int)$this->context->shop->id
            );

            $description = EverPsSeoProduct::changeProductMetadescShortcodes(
                (int)$everProduct->id_seo_product,
                (int)$everProduct->id_seo_lang,
                (int)$this->context->shop->id
            );
            if (!$description) {
                continue;
            }

            $product->meta_description = Tools::substr(
                strip_tags($description),
                0,
                160
            );

            $sql = 'UPDATE `'._DB_PREFIX_.'product_lang`
            SET meta_description = "'.pSQL($product->meta_description).'"
            WHERE id_lang = '.(int)$everProduct->id_seo_lang.'
            AND id_shop = '.(int)$this->context->shop->id.'
            AND id_product = '.(int)$product->id;

            $sql2 = 'UPDATE `'._DB_PREFIX_.'ever_seo_product`
            SET meta_description = "'.pSQL($product->meta_description).'"
            WHERE id_seo_lang = '.(int)$everProduct->id_seo_lang.'
            AND id_shop = '.(int)$this->context->shop->id.'
            AND id_seo_product = '.(int)$product->id;
            // die(var_dump($sql2));
            if (!Db::getInstance()->execute($sql)) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            } else {
                Db::getInstance()->execute($sql2);
            }
        }
    }

    protected function displayError($message, $description = false)
    {
        /**
         * Set error message and description for the template.
         */
        array_push($this->errors, $this->module->l($message), $description);

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . '/everpsseo/views/templates/admin/error.tpl'
        );
    }
}
