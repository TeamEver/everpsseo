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

require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoImage.php';
require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoKeywordsStrategy.php';

class AdminEverPsSeoImageController extends ModuleAdminController
{
    private $html;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = false;
        $this->table = 'ever_seo_image';
        $this->className = 'EverPsSeoImage';
        $this->context = Context::getContext();
        $this->identifier = 'id_ever_seo_image';
        $this->isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;
        $this->imageType = 'jpg';
        $this->max_file_size = (int)(Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE') * 1000000);
        $this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        $this->allow_export = true;
        $moduleConfUrl  = 'index.php?controller=AdminModules&configure=everpsseo&token=';
        $moduleConfUrl .= Tools::getAdminTokenLite('AdminModules');

        $this->context->smarty->assign(array(
            'moduleConfUrl' => (string)$moduleConfUrl,
            'image_dir' => _PS_BASE_URL_.__PS_BASE_URI__ . '/modules/everpsseo/views/img/'
        ));

        $this->_select = 'l.iso_code, il.legend, il.id_image, pl.name, cl.name AS category_name';

        $this->_join =
            'LEFT JOIN `'._DB_PREFIX_.'ever_seo_lang` l
                ON (
                    l.`id_seo_lang` = a.`id_seo_lang`
                )
            LEFT JOIN `'._DB_PREFIX_.'image_lang` il
                ON (
                    il.`id_lang` = a.`id_seo_lang`
                    AND il.id_image = a.id_seo_img
                )
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                ON (
                    pl.id_product = a.id_seo_product
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

        $this->_group = 'GROUP BY a.id_ever_seo_image';

        $this->fields_list = array(
            'id_ever_seo_image' => array(
                'title' => $this->l('ID SEO Image'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'id_seo_img' => array(
                'title' => $this->l('ID Image'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'image' => array(
                'title' => $this->l('Image'),
                'align' => 'center',
                'image' => 'p',
                'orderby' => false,
                'filter' => false,
                'search' => false
            ),
            'name' => array(
                'title' => $this->l('Product'),
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
            'legend' => array(
                'title' => $this->l('Alt/Title'),
                'align' => 'left',
                'width' => 'auto'
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
            'status_code' => array(
                'title' => $this->l('Http code'),
                'align' => 'left',
                'width' => 'auto'
            )
        );

        $this->colorOnBackground = true;
        $this->isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;

        $imageType = ImageType::getFormattedName('large');

        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;

        if (Tools::getValue('id_ever_seo_image')) {
            $seoImage = new EverPsSeoImage(
                (int)Tools::getValue('id_ever_seo_image')
            );
            $seoProduct = new EverPsSeoProduct(
                (int)$seoImage->id_seo_product
            );
            $link = new Link();
            $product = new Product(
                (int)$seoImage->id_seo_product,
                false,
                (int)$id_lang,
                (int)$id_shop
            );
            $richImage = $product->getCover(
                (int)$product->id
            );
            $objectUrl = Tools::getShopProtocol().$link->getImageLink(
                $product->link_rewrite,
                $product->id.'-'.$richImage['id_image'],
                $imageType
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

                case ((int)$keywordsQlty['note'] <= 100):
                    $color = 'ever-success';
                    break;

                default:
                    $color = 'badge-secondary';
                    break;
            }
            $this->context->smarty->assign(array(
                'headerObjectName' => $product->name.' image',
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

        $this->addCSS(_PS_MODULE_DIR_.'everpsseo/views/css/ever.css');
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ($this->isSeven) {
            return Context::getContext()->getTranslator()->trans(
                $string,
                [],
                'Modules.Everpsseo.Admineverpsseoimagecontroller'
            );
        }

        return parent::l($string, $class, $addslashes, $htmlentities);
    }

    public function renderList()
    {
        $this->html = '';

        $this->addRowAction('edit');

        $this->bulk_actions = array(
            'sitemap' => array(
                'text' => $this->l('Sitemap or not'),
                'confirm' => $this->l('Switch allow/disallow sitemap on those selected items ?')
            ),
            'legendalt' => array(
                'text' => $this->l('Use default legend as alt'),
                'confirm' => $this->l('Set default alt/title using current legend ?')
            ),
            'shortdescalt' => array(
                'text' => $this->l('Use product short description as alt'),
                'confirm' => $this->l('Set default alt/title using current product short desc ?')
            ),
            'descalt' => array(
                'text' => $this->l('Use product description as alt'),
                'confirm' => $this->l('Set default alt/title using current product description ?')
            ),
            'metaaltname' => array(
                'text' => $this->l('Use product name as alt'),
                'confirm' => $this->l('Set product name as alt/title ?')
            ),
            'metaalttitle' => array(
                'text' => $this->l('Use product SEO title as alt'),
                'confirm' => $this->l('Set product name as alt/title ?')
            ),
            'metaaltmetadesc' => array(
                'text' => $this->l('Use product SEO meta description as alt'),
                'confirm' => $this->l('Set product meta description as alt/title ?')
            ),
            'altshortcodes' => array(
                'text' => $this->l('Use shortcodes for alt'),
                'confirm' => $this->l('Set alt using shortcodes ?')
            ),
            'indexnow' => array(
                'text' => $this->l('Index now'),
                'confirm' => $this->l('Index now ?')
            ),
        );

        if (Tools::isSubmit('submitBulksitemap'.$this->table)) {
            $this->processBulkSitemap();
        }

        if (Tools::isSubmit('submitBulklegendalt'.$this->table)) {
            $this->processBulkLegendalt();
        }

        if (Tools::isSubmit('submitBulkshortdescalt'.$this->table)) {
            $this->processBulkShortdescalt();
        }

        if (Tools::isSubmit('submitBulkdescalt'.$this->table)) {
            $this->processBulkdescriptionAlt();
        }

        if (Tools::isSubmit('submitBulkmetaaltname'.$this->table)) {
            $this->processBulkMetaaltname();
        }

        if (Tools::isSubmit('submitBulkmetaalttitle'.$this->table)) {
            $this->processBulkTitleAlt();
        }

        if (Tools::isSubmit('submitBulkmetaaltmetadesc'.$this->table)) {
            $this->processBulkMetaDescAlt();
        }

        if (Tools::isSubmit('submitBulkaltshortcodes'.$this->table)) {
            $this->processBulkAltShortcodes();
        }

        if (Tools::isSubmit('submitBulkindexnow'.$this->table)) {
            $this->processBulkIndexNow();
        }

        if (Tools::isSubmit('allowed_sitemap'.$this->table)) {
            $this->processSitemap();
        }

        $this->toolbar_title = $this->l('SEO setting : Images');

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

        $this->fields_form = array(
            'legend' => array(
            'title' => '',
            ),
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
                    'label' => $this->l('Alt/title (aka legend)'),
                    'required' => true,
                    'name' => 'alt',
                    'lang' => false,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Allow on sitemap'),
                    'name' => 'allowed_sitemap',
                    'lang' => false,
                    'is_bool' => true,
                    'desc' => $this->l('Set image on sitemap'),
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
        $this->html .= $lists;
        $this->html .= $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . '/everpsseo/views/templates/admin/footer.tpl'
        );

        return $this->html;
    }

    public function processSitemap()
    {
        $everImg = new EverPsSeoImage(
            (int)Tools::getValue('id_ever_seo_image')
        );

        $everImg->allowed_sitemap = !$everImg->allowed_sitemap;

        if (!$everImg->save()) {
            $this->errors[] = $this->l('Can\'t update the current object');
        }
    }

    public function postProcess()
    {
        parent::postProcess();

        if (Tools::isSubmit('save')) {
            if (!Tools::getValue('alt')
                || !Validate::isGenericName(Tools::getValue('alt'))
            ) {
                 $this->errors[] = $this->l('alt is invalid');
            }
            if (Tools::getValue('allowed_sitemap')
                && !Validate::isBool(Tools::getValue('allowed_sitemap'))
            ) {
                 $this->errors[] = $this->l('allowed_sitemap is invalid');
            }
            if (!count($this->errors)) {
                $everImg = new EverPsSeoImage(
                    (int)Tools::getValue('id_ever_seo_image')
                );
                $image = new Image(
                    (int)$everImg->id_seo_img,
                    (int)$everImg->id_seo_lang,
                    (int)$this->context->shop->id
                );
                $image->id_product = $everImg->id_seo_product;
                $image->legend = Tools::substr(Tools::getValue('alt'), 0, 125);
                $everImg->alt = Tools::substr(Tools::getValue('alt'), 0, 125);
                $everImg->allowed_sitemap = Tools::getValue('allowed_sitemap');
                // Hook update triggered
                if (!$image->save() || !$everImg->save()) {
                    $this->errors[] = $this->l('Can\'t update the native object');
                } else {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                }
            }
        }
    }

    protected function processBulkSitemap()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverImg) {
            $everImg = new EverPsSeoImage(
                (int)$idEverImg
            );

            $everImg->allowed_sitemap = !$everImg->allowed_sitemap;

            if (!$everImg->save()) {
                $this->errors[] = $this->l('Can\'t update the current object');
            }
        }
    }

    protected function processBulkShortdescalt()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverImg) {
            $everImg = new EverPsSeoImage(
                (int)$idEverImg
            );
            $image = new Image(
                (int)$everImg->id_seo_img,
                (int)$everImg->id_seo_lang,
                (int)$this->context->shop->id
            );

            $description = Db::getInstance()->getValue(
                'SELECT description_short FROM `'._DB_PREFIX_.'product_lang`
                WHERE id_product = '.pSQL($everImg->id_seo_product).'
                AND id_lang = '.pSQL($everImg->id_seo_lang)
            );

            if (!$description) {
                continue;
            }

            $everImg->alt = $description;
            $image->legend = strip_tags(Tools::substr($description, 0, 125).'');
            // Hook update triggered
            if (!$image->save()) {
                $this->errors[] = $this->l('Can\'t update the native object');
            }
        }
    }

    protected function processBulkLegendalt()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverImg) {
            $everImg = new EverPsSeoImage(
                (int)$idEverImg
            );
            $image = new Image(
                (int)$everImg->id_seo_img,
                (int)$everImg->id_seo_lang,
                (int)$this->context->shop->id
            );

            $everImg->alt = strip_tags(Tools::substr($image->legend, 0, 125).'');
            // Hook update triggered
            if (!$everImg->save()) {
                $this->errors[] = $this->l('Can\'t update the native object');
            }
        }
    }

    protected function processBulkMetaaltname()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverImg) {
            $everImg = new EverPsSeoImage(
                (int)$idEverImg
            );
            $image = new Image(
                $everImg->id_seo_img,
                (int)$everImg->id_seo_lang,
                (int)$this->context->shop->id
            );

            $name = Db::getInstance()->getValue(
                'SELECT name FROM `'._DB_PREFIX_.'product_lang`
                WHERE id_product = '.pSQL($everImg->id_seo_product).'
                AND id_lang = '.pSQL($everImg->id_seo_lang)
            );

            if (!$name) {
                continue;
            }

            $everImg->alt = $name;
            $image->legend = strip_tags(Tools::substr($name, 0, 125).'');
            // Hook update triggered
            if (!$image->save()) {
                $this->errors[] = $this->l('Can\'t update the native object');
            }
        }
    }

    protected function processBulkdescriptionAlt()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverImg) {
            $everImg = new EverPsSeoImage(
                (int)$idEverImg
            );
            $image = new Image(
                (int)$everImg->id_seo_img,
                (int)$everImg->id_seo_lang,
                (int)$this->context->shop->id
            );

            $description = Db::getInstance()->getValue(
                'SELECT description FROM `'._DB_PREFIX_.'product_lang`
                WHERE id_product = '.pSQL($everImg->id_seo_product).'
                AND id_lang = '.pSQL($everImg->id_seo_lang)
            );

            if (!$description) {
                continue;
            }

            $everImg->alt = $description;
            $image->legend = strip_tags(Tools::substr($description, 0, 125).'');
            // Hook update triggered
            if (!$image->save()) {
                $this->errors[] = $this->l('Can\'t update the native object');
            }
        }
    }

    protected function processBulkTitleAlt()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverImg) {
            $everImg = new EverPsSeoImage(
                (int)$idEverImg
            );
            $image = new Image(
                (int)$everImg->id_seo_img,
                (int)$everImg->id_seo_lang,
                (int)$this->context->shop->id
            );

            $description = Db::getInstance()->getValue(
                'SELECT meta_title FROM `'._DB_PREFIX_.'ever_seo_product`
                WHERE id_seo_product = '.(int)$everImg->id_seo_product.'
                AND id_seo_lang = '.(int)$everImg->id_seo_lang
            );
            if (!$description) {
                continue;
            }
            $sql =
                'UPDATE `'._DB_PREFIX_.'ever_seo_image`
                SET alt = '.pSQl($description).'
                WHERE id_seo_product = '.(int)$everImg->id_seo_product.'
                AND id_seo_lang = '.(int)$everImg->id_seo_lang;
                Db::getInstance()->execute($sql);
            $everImg->alt = $description;
            $image->legend = strip_tags(Tools::substr($description, 0, 125).'');
            // Hook update triggered
            if (!$image->save()) {
                $this->errors[] = $this->l('Can\'t update the native object');
            }
        }
    }

    protected function processBulkMetaDescAlt()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverImg) {
            $everImg = new EverPsSeoImage(
                (int)$idEverImg
            );
            $image = new Image(
                (int)$everImg->id_seo_img,
                (int)$everImg->id_seo_lang,
                (int)$this->context->shop->id
            );

            $description = Db::getInstance()->getValue(
                'SELECT meta_description FROM `'._DB_PREFIX_.'ever_seo_product`
                WHERE id_seo_product = '.pSQL($everImg->id_seo_product).'
                AND id_seo_lang = '.pSQL($everImg->id_seo_lang)
            );

            if (!$description) {
                continue;
            }

            $everImg->alt = $description;
            $image->legend = strip_tags(Tools::substr($description, 0, 125).'');
            // Hook update triggered
            if (!$image->save()) {
                $this->errors[] = $this->l('Can\'t update the native object');
            }
        }
    }

    protected function processBulkAltShortcodes()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverImg) {
            $everImg = new EverPsSeoImage(
                (int)$idEverImg
            );
            $image = new Image(
                (int)$everImg->id_seo_img,
                (int)$everImg->id_seo_lang,
                (int)$this->context->shop->id
            );

            if (!Validate::isLoadedObject($image)) {
                continue;
            }

            $legend = EverPsSeoImage::changeImageAltShortcodes(
                (int)$everImg->id_seo_img,
                (int)$everImg->id_seo_lang,
                (int)$this->context->shop->id
            );

            $everImg->alt = $legend;
            $image->legend = strip_tags(Tools::substr($legend, 0, 125).'');
            // Hook update triggered
            if (!$image->save()) {
                $this->errors[] = $this->l('Can\'t update the native object');
            }
        }
    }

    protected function processBulkIndexNow()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverImg) {
            $everImg = new EverPsSeoImage(
                (int)$idEverImg
            );
            $image = new Image(
                (int)$everImg->id_seo_img,
                (int)$everImg->id_seo_lang,
                (int)$this->context->shop->id
            );
            if (!Validate::isLoadedObject($image)) {
                continue;
            }
            $link = new Link();
            $product = new Product(
                (int)$everImg->id_seo_product,
                false,
                (int)$everImg->id_seo_lang,
                (int)$this->context->shop->id
            );
            if (!Validate::isLoadedObject($product)) {
                continue;
            }
            $richImage = $product->getCover(
                (int)$product->id
            );
            $imageType = ImageType::getFormattedName('large');
            $url = Tools::getShopProtocol().$link->getImageLink(
                $product->link_rewrite,
                $product->id.'-'.$richImage['id_image'],
                $imageType
            );
            $httpCode = EverPsSeoTools::indexNow(
                $url
            );
            $sql = 'UPDATE `'._DB_PREFIX_.'ever_seo_image`
            SET status_code = "'.(int)$httpCode.'"
            WHERE id_seo_lang = '.(int)$everImg->id_seo_lang.'
            AND id_shop = '.(int)$this->context->shop->id.'
            AND id_ever_seo_image = '.(int)$everImg->id;
            if (!Db::getInstance()->execute($sql)) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function displayError($message, $description = false)
    {
        /**
         * Set error message and description for the template.
         */
        array_push($this->errors, $this->module->l($message), $description);

        return $this->setTemplate('error.tpl');
    }
}
