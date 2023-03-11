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
require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoCategory.php';
require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoKeywordsStrategy.php';

class AdminEverPsSeoCategoryController extends ModuleAdminController
{
    private $html;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = false;
        $this->table = 'ever_seo_category';
        $this->className = 'EverPsSeoCategory';
        $this->context = Context::getContext();
        $this->identifier = 'id_ever_seo_category';
        $moduleConfUrl  = 'index.php?controller=AdminModules&configure=everpsseo&token=';
        $moduleConfUrl .= Tools::getAdminTokenLite('AdminModules');
        $this->img_folder = _PS_MODULE_DIR_ . 'everpsseo/views/img/c/';
        $this->img_url = Tools::getHttpHost(true) . __PS_BASE_URI__ . '/modules/everpsseo/views/img/c/';
        $this->context->smarty->assign(array(
            'moduleConfUrl' => (string) $moduleConfUrl,
            'image_dir' => _PS_BASE_URL_ . '/modules/everpsseo/views/img/'
        ));

        $this->_select = 'l.iso_code, cl.name';

        $this->_join =
            'LEFT JOIN `' . _DB_PREFIX_ . 'ever_seo_lang` l
                ON (
                    l.`id_seo_lang` = a.`id_seo_lang`
                )
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
                ON (
                    cl.`id_lang` = a.`id_seo_lang`
                    AND cl.id_category = a.id_seo_category
                )';

        $this->_where = 'AND a.id_shop = ' . (int) $this->context->shop->id;

        $this->_group = 'GROUP BY a.id_ever_seo_category';

        $this->fields_list = array(
            'id_ever_seo_category' => array(
                'title' => $this->l('ID SEO Category'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'id_seo_category' => array(
                'title' => $this->l('ID Category'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'left',
                'width' => 'auto'
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
                'width' => 'auto'
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
            ),
            'status_code' => array(
                'title' => $this->l('Http code'),
                'align' => 'left',
                'width' => 'auto'
            )
        );

        $id_shop = (int) $this->context->shop->id;
        $id_lang = (int) $this->context->language->id;

        if (Tools::getValue('id_ever_seo_category')) {
            $seoCategory = new EverPsSeoCategory(Tools::getValue('id_ever_seo_category'));
            $category = new Category(
                (int) $seoCategory->id_seo_category,
                (int) $id_lang,
                (int) $id_shop
            );
            $link = new Link();
            $objectUrl = $link->getCategoryLink(
                $category,
                null,
                null,
                null,
                (int) $this->context->language->id,
                (int) $this->context->shop->id
            );
            $editUrl  = 'index.php?controller=AdminCategories&id_category=' . (int) $category->id;
            $editUrl .= '&updatecategory&token='.Tools::getAdminTokenLite('AdminCategories');
            $objectGSearch = str_replace(' ', '+', $category->name);

            $keywordsQlty = EverPsSeoKeywordsStrategy::getSeoCategoryNote(
                $seoCategory,
                $category
            );
            switch (true) {
                case ((int) $keywordsQlty['note'] <= 25):
                    $color = 'ever-danger';
                    break;

                case ((int) $keywordsQlty['note'] <= 50):
                    $color = 'ever-alert';
                    break;

                case ((int) $keywordsQlty['note'] <= 75):
                    $color = 'ever-warning';
                    break;

                case ((int) $keywordsQlty['note'] > 75):
                    $color = 'ever-success';
                    break;

                default:
                    $color = 'badge-secondary';
                    break;
            }
            $this->context->smarty->assign(array(
                'headerObjectName' => $category->name,
                'objectGSearch' => $objectGSearch,
                'objectUrl' => $objectUrl,
                'editUrl' => $editUrl,
                'keywordsQlty' => $keywordsQlty['note'],
                'colorNotation' => $color,
                'errors' => $keywordsQlty['errors'],
            ));
        }

        $this->colorOnBackground = true;

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
        $this->addCSS(_PS_MODULE_DIR_ . 'everpsseo/views/css/ever.css');
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Context::getContext()->getTranslator()->trans(
            $string,
            [],
            'Modules.Everpsseo.Admineverpsseocategorycontroller'
        );
    }

    public function renderList()
    {
        $this->html = '';

        $this->addRowAction('edit');

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
                'confirm' => $this->l('Switch allow/disallow sitemap on selected items ?')
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
            'linkrewrite' => array(
                'text' => $this->l('Generate link rewrite'),
                'confirm' => $this->l('Generate link rewrite ?')
            ),
            'indexnow' => array(
                'text' => $this->l('Index now'),
                'confirm' => $this->l('Index now ?')
            ),
        );

        if (Tools::isSubmit('submitBulkindex' . $this->table)) {
            $this->processBulkIndex();
        }

        if (Tools::isSubmit('submitBulkfollow' . $this->table)) {
            $this->processBulkFollow();
        }

        if (Tools::isSubmit('submitBulksitemap' . $this->table)) {
            $this->processBulkSitemap();
        }

        if (Tools::isSubmit('submitBulkmetatitle' . $this->table)) {
            $this->processBulkCopyMetaTitle();
        }

        if (Tools::isSubmit('submitBulkmetadescription' . $this->table)) {
            $this->processBulkCopyMetaDescription();
        }

        if (Tools::isSubmit('submitBulkmetatitlename' . $this->table)) {
            $this->processBulkSetNameAsMetaTitle();
        }

        if (Tools::isSubmit('submitBulkmetadescriptiondesc' . $this->table)) {
            $this->processBulkSetDescriptionAsMetaDescription();
        }

        if (Tools::isSubmit('submitBulkmetadescshortcodes' . $this->table)) {
            $this->processBulkMetadescShortcodes();
        }

        if (Tools::isSubmit('submitBulktitleshortcodes' . $this->table)) {
            $this->processBulkTitleShortcodes();
        }

        if (Tools::isSubmit('submitBulklinkrewrite' . $this->table)) {
            $this->processBulkLinkRewrite();
        }

        if (Tools::isSubmit('submitBulkindexnow' . $this->table)) {
            $this->processBulkIndexNow();
        }

        if (Tools::isSubmit('indexable' . $this->table)) {
            $this->processIndexable();
        }

        if (Tools::isSubmit('follow' . $this->table)) {
            $this->processFollow();
        }

        if (Tools::isSubmit('allowed_sitemap' . $this->table)) {
            $this->processSitemap();
        }

        $this->toolbar_title = $this->l('SEO setting : Categories');

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

        if (Tools::getValue('id_ever_seo_category')) {
            $seoCategory = new EverPsSeoCategory(
                (int) Tools::getValue('id_ever_seo_category')
            );
            if (file_exists($this->img_folder.$seoCategory->id_seo_category . '.jpg')) {
                $defaultUrlImage = $this->img_url.$seoCategory->id_seo_category . '.jpg';
            } else {
                $defaultUrlImage = Tools::getHttpHost(true) . '/img/' . Configuration::get(
                    'PS_LOGO'
                );
            }
        } else {
            $defaultUrlImage = Tools::getHttpHost(true) . '/img/' . Configuration::get(
                'PS_LOGO'
            );
        }
        $defaultImage = '<image src="' . (string) $defaultUrlImage . '" style="max-width:80px;"/>';

        $this->fields_form = array(
            'submit' => array(
                'name' => 'save',
                'title' => $this->l('Save'),
                'class' => 'button pull-right'
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Save and stay'),
                    'name' => 'submitAdd' . $this->table . 'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                ),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta title'),
                    'required' => true,
                    'name' => 'meta_title',
                    'maxchar' => 65,
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta description'),
                    'required' => true,
                    'name' => 'meta_description',
                    'maxchar' => 165,
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
                    'desc' => $this->l('Allow category index'),
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
                    'desc' => $this->l('Allow category follow'),
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
                    'desc' => $this->l('Set category on sitemap'),
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

    public function processIndexable()
    {
        $everCategory = new EverPsSeoCategory(
            (int) Tools::getValue('id_ever_seo_category')
        );

        $everCategory->indexable = !$everCategory->indexable;

        if (!$everCategory->save()) {
            $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
        }
    }

    public function processFollow()
    {
        $everCategory = new EverPsSeoCategory(
            (int) Tools::getValue('id_ever_seo_category')
        );

        $everCategory->follow = !$everCategory->follow;

        if (!$everCategory->save()) {
            $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
        }
    }

    public function processSitemap()
    {
        $everCategory = new EverPsSeoCategory((int) Tools::getValue('id_ever_seo_category'));
        $category = new Category(
            (int) $everCategory->id_seo_category,
            (int) $everCategory->id_seo_lang,
            (int) $this->context->shop->id
        );

        if ((int) $category->active) {
            $everCategory->allowed_sitemap = !$everCategory->allowed_sitemap;
        } else {
            $everCategory->allowed_sitemap = 0;
        }

        if (!$everCategory->save()) {
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
                $everCategory = new EverPsSeoCategory(
                    (int) Tools::getValue('id_ever_seo_category')
                );
                $category = new Category(
                    (int) $everCategory->id_seo_category,
                    (int) $everCategory->id_seo_lang,
                    (int) $this->context->shop->id
                );
                $oldUrl = $category->link_rewrite;
                $newUrl = Tools::getValue('link_rewrite');
                if ($newUrl != $oldUrl
                    && EverPsSeoRedirect::ifRedirectExists($oldUrl, (int) $this->context->shop->id)
                ) {
                    $redirect = new EverPsSeoRedirect();
                    $redirect->not_found = $oldUrl;
                    $redirect->redirection = $newUrl;
                    $redirect->id_shop = (int) $this->context->shop->id;
                    $redirect->active = true;
                    $redirect->save();
                }
                // SEO Object
                $everCategory->meta_title = Tools::getValue('meta_title');
                $everCategory->meta_description = Tools::getValue('meta_description');
                $everCategory->social_title = Tools::getValue('social_title');
                $everCategory->social_description = Tools::getValue('social_description');
                $everCategory->bottom_content = Tools::getValue('bottom_content');
                $everCategory->link_rewrite = Tools::getValue('link_rewrite');
                $everCategory->canonical = Tools::getValue('canonical');
                $everCategory->keywords = Tools::getValue('keywords');
                $everCategory->indexable = Tools::getValue('indexable');
                $everCategory->follow = Tools::getValue('follow');
                $everCategory->allowed_sitemap = Tools::getValue('allowed_sitemap');
                // PS Object
                $category->link_rewrite = Tools::getValue('link_rewrite');
                $category->meta_title = Tools::getValue('meta_title');
                $category->meta_description = Tools::getValue('meta_description');
                /* upload the image */
                if (isset($_FILES['social_media'])
                    && isset($_FILES['social_media']['tmp_name'])
                    && !empty($_FILES['social_media']['tmp_name'])
                ) {
                    Configuration::set('PS_IMAGE_GENERATION_METHOD', 1);
                    if (file_exists($this->img_folder . (int) $everCategory->id_seo_category . '.jpg')) {
                        unlink($this->img_folder . (int) $everCategory->id_seo_category . '.jpg');
                    }
                    if ($error = ImageManager::validateUpload($_FILES['social_media'])) {
                        $this->errors[] = $error;
                    } elseif (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS'))
                        || !move_uploaded_file($_FILES['social_media']['tmp_name'], $tmp_name)
                    ) {
                        return false;
                    } elseif (!ImageManager::resize(
                        $tmp_name,
                        $this->img_folder . (int) $everCategory->id_seo_category . '.jpg'
                    )) {
                        $this->errors[] = $this->l('An error occurred while attempting to upload the image.');
                    }
                    if (isset($tmp_name)) {
                        unlink($tmp_name);
                    }
                    $everCategory->social_img_url = $this->img_url
                    . (int) $everCategory->id_seo_category
                    . '.jpg';
                }
                if (!$everCategory->save()) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return parent::postProcess();
                }
            }
        }
        return parent::postProcess();
    }

    protected function processBulkIndex()
    {
        foreach (Tools::getValue($this->table . 'Box') as $idEverCategory) {
            $everCategory = new EverPsSeoCategory(
                (int) $idEverCategory
            );

            $everCategory->indexable = !$everCategory->indexable;

            if (!$everCategory->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkFollow()
    {
        foreach (Tools::getValue($this->table . 'Box') as $idEverCategory) {
            $everCategory = new EverPsSeoCategory(
                (int) $idEverCategory
            );

            $everCategory->follow = !$everCategory->follow;

            if (!$everCategory->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkSitemap()
    {
        foreach (Tools::getValue($this->table . 'Box') as $idEverCategory) {
            $everCategory = new EverPsSeoCategory(
                (int) $idEverCategory
            );

            $everCategory->allowed_sitemap = !$everCategory->allowed_sitemap;

            if (!$everCategory->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkCopyMetaTitle()
    {
        foreach (Tools::getValue($this->table . 'Box') as $idEverCategory) {
            $everCategory = new EverPsSeoCategory(
                (int) $idEverCategory
            );
            $category = new Category(
                (int) $everCategory->id_seo_category,
                (int) $everCategory->id_seo_lang,
                (int) $this->context->shop->id
            );

            $meta_title = Db::getInstance()->getValue(
                'SELECT meta_title FROM `' . _DB_PREFIX_ . 'category_lang`
                WHERE id_category = ' . (int) $everCategory->id_seo_category . '
                AND id_lang = ' . (int) $everCategory->id_seo_lang
            );

            if (!$meta_title) {
                continue;
            }

            $category->meta_title = $meta_title;
            // Update hook triggered
            if (!$category->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkCopyMetaDescription()
    {
        foreach (Tools::getValue($this->table . 'Box') as $idEverCategory) {
            $everCategory = new EverPsSeoCategory(
                (int) $idEverCategory
            );
            $category = new Category(
                (int) $everCategory->id_seo_category,
                (int) $everCategory->id_seo_lang,
                (int) $this->context->shop->id
            );

            $meta_description = Db::getInstance()->getValue(
                'SELECT meta_description FROM `' . _DB_PREFIX_ . 'category_lang`
                WHERE id_category = ' . (int) $everCategory->id_seo_category . '
                AND id_lang = ' . (int) $everCategory->id_seo_lang
            );

            if (!$meta_description) {
                continue;
            }

            $category->meta_description = $meta_description;
            // Update hook triggered
            if (!$category->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkSetNameAsMetaTitle()
    {
        foreach (Tools::getValue($this->table . 'Box') as $idEverCategory) {
            $everCategory = new EverPsSeoCategory(
                (int) $idEverCategory
            );
            $category = new Category(
                (int) $everCategory->id_seo_category,
                (int) $everCategory->id_seo_lang,
                (int) $this->context->shop->id
            );

            $name = Db::getInstance()->getValue(
                'SELECT name FROM `' . _DB_PREFIX_ . 'category_lang`
                WHERE id_category = ' . (int) $everCategory->id_seo_category . '
                AND id_lang = ' . (int) $everCategory->id_seo_lang
            );

            if (!$name) {
                continue;
            }

            $category->meta_title = $name;
            // Update hook triggered
            if (!$category->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkSetDescriptionAsMetaDescription()
    {
        foreach (Tools::getValue($this->table . 'Box') as $idEverCategory) {
            $everCategory = new EverPsSeoCategory(
                (int) $idEverCategory
            );
            $category = new Category(
                (int) $everCategory->id_seo_category,
                (int) $everCategory->id_seo_lang,
                (int) $this->context->shop->id
            );
            if (!Validate::isLoadedObject($category)) {
                continue;
            }

            $description = Db::getInstance()->getValue(
                'SELECT description FROM `' . _DB_PREFIX_ . 'category_lang`
                WHERE id_category = ' . (int) $everCategory->id_seo_category . '
                AND id_lang = ' . (int) $everCategory->id_seo_lang
            );

            if (!$description) {
                continue;
            }

            $category->meta_description = Tools::substr(
                strip_tags($description),
                0,
                160
            );
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'category_lang`
            SET meta_description = "' . pSQL($category->meta_description) . '"
            WHERE id_lang = ' . (int) $everCategory->id_seo_lang . '
            AND id_shop = ' . (int) $this->context->shop->id . '
            AND id_category = ' . (int) $category->id;

            $sql2 = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_category`
            SET meta_description = "' . pSQL($description) . '"
            WHERE id_seo_lang = ' . (int) $everCategory->id_seo_lang . '
            AND id_shop = ' . (int) $this->context->shop->id . '
            AND id_seo_category = ' . (int) $category->id;
            if (!Db::getInstance()->execute($sql) || !Db::getInstance()->execute($sql2)) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkTitleShortcodes()
    {
        foreach (Tools::getValue($this->table . 'Box') as $idEverCategory) {
            $everCategory = new EverPsSeoCategory(
                (int) $idEverCategory
            );
            $category = new Category(
                (int) $everCategory->id_seo_category,
                (int) $everCategory->id_seo_lang,
                (int) $this->context->shop->id
            );

            $title = EverPsSeoCategory::changeCategoryTitleShortcodes(
                (int) $everCategory->id_seo_category,
                (int) $everCategory->id_seo_lang,
                (int) $this->context->shop->id
            );

            if (!$title) {
                continue;
            }

            $category->meta_title = Tools::substr(
                strip_tags($title),
                0,
                60
            );
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'category_lang`
            SET meta_title = "' . pSQL($category->meta_title) . '"
            WHERE id_lang = ' . (int) $everCategory->id_seo_lang . '
            AND id_shop = ' . (int) $this->context->shop->id . '
            AND id_category = ' . (int) $category->id;

            $sql2 = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_category`
            SET meta_title = "' . pSQL($title) . '"
            WHERE id_seo_lang = ' . (int) $everCategory->id_seo_lang . '
            AND id_shop = ' . (int) $this->context->shop->id . '
            AND id_seo_category = ' . (int) $category->id;
            if (!Db::getInstance()->execute($sql)) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            } else {
                Db::getInstance()->execute($sql2);
            }
        }
    }

    protected function processBulkLinkRewrite()
    {
        foreach (Tools::getValue($this->table . 'Box') as $idEverCategory) {
            $everProduct = new EverPsSeoProduct(
                (int) $idEverCategory
            );
            $everCategory = new EverPsSeoCategory(
                (int) $idEverCategory
            );
            $category = new Category(
                (int) $everCategory->id_seo_category,
                (int) $everCategory->id_seo_lang,
                (int) $this->context->shop->id
            );
            $linkRewrite = \Tools::link_rewrite($category->name);
            $canonical = \Tools::link_rewrite($category->name);

            $sql = 'UPDATE `' . _DB_PREFIX_ . 'category_lang`
            SET link_rewrite = "' . pSQL($linkRewrite) . '"
            WHERE id_lang = ' . (int) $everCategory->id_seo_lang . '
            AND id_shop = ' . (int) $this->context->shop->id . '
            AND id_category = ' . (int) $product->id;

            $sql2 = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_category`
            SET link_rewrite = "' . pSQL($linkRewrite) . '"
            WHERE id_seo_lang = ' . (int) $everCategory->id_seo_lang . '
            AND id_shop = ' . (int) $this->context->shop->id . '
            AND id_seo_category = ' . (int) $product->id;

            $sql2 = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_category`
            SET canonical = "' . pSQL($canonical) . '"
            WHERE id_seo_lang = ' . (int) $everCategory->id_seo_lang . '
            AND id_shop = ' . (int) $this->context->shop->id . '
            AND id_seo_category = ' . (int) $product->id;
            if (!Db::getInstance()->execute($sql)) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            } else {
                Db::getInstance()->execute($sql2);
            }
        }
    }

    protected function processBulkMetadescShortcodes()
    {
        foreach (Tools::getValue($this->table . 'Box') as $idEverCategory) {
            $everCategory = new EverPsSeoCategory(
                (int) $idEverCategory
            );
            $category = new Category(
                (int) $everCategory->id_seo_category,
                (int) $everCategory->id_seo_lang,
                (int) $this->context->shop->id
            );

            if (!Validate::isLoadedObject($category)) {
                continue;
            }

            $description = EverPsSeoCategory::changeCategoryMetadescShortcodes(
                (int) $everCategory->id_seo_category,
                (int) $everCategory->id_seo_lang,
                (int) $this->context->shop->id
            );

            if (!$description) {
                continue;
            }

            $category->meta_description = Tools::substr(
                strip_tags($description),
                0,
                160
            );

            $sql = 'UPDATE `' . _DB_PREFIX_ . 'category_lang`
            SET meta_description = "' . pSQL($category->meta_description) . '"
            WHERE id_lang = ' . (int) $everCategory->id_seo_lang . '
            AND id_shop = ' . (int) $this->context->shop->id . '
            AND id_category = ' . (int) $category->id;

            $sql2 = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_category`
            SET meta_description = "' . pSQL($description) . '"
            WHERE id_seo_lang = ' . (int) $everCategory->id_seo_lang . '
            AND id_shop = ' . (int) $this->context->shop->id . '
            AND id_seo_category = ' . (int) $category->id;
            if (!Db::getInstance()->execute($sql)) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            } else {
                Db::getInstance()->execute($sql2);
            }
        }
    }

    protected function processBulkIndexNow()
    {
        foreach (Tools::getValue($this->table . 'Box') as $idEverCategory) {
            $everCategory = new EverPsSeoCategory(
                (int) $idEverCategory
            );
            $category = new Category(
                (int) $everCategory->id_seo_category,
                (int) $everCategory->id_seo_lang,
                (int) $this->context->shop->id
            );

            if (!Validate::isLoadedObject($category)) {
                continue;
            }
            $link = new Link();
            $url = $link->getCategoryLink(
                $category,
                null,
                null,
                null,
                (int) $everCategory->id_seo_lang,
                (int) $this->context->shop->id
            );
            $httpCode = EverPsSeoTools::indexNow(
                $url
            );
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_category`
            SET status_code = "' . (int) $httpCode . '"
            WHERE id_seo_lang = ' . (int) $everCategory->id_seo_lang . '
            AND id_shop = ' . (int) $this->context->shop->id . '
            AND id_seo_category = ' . (int) $category->id;
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
