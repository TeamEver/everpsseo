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

require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoCmsCategory.php';
require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoKeywordsStrategy.php';

class AdminEverPsSeoCmsCategoryController extends ModuleAdminController
{
    private $html;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = false;
        $this->table = 'ever_seo_cms_category';
        $this->className = 'EverPsSeoCmsCategory';
        $this->context = Context::getContext();
        $this->identifier = 'id_ever_seo_cms_category';
        $moduleConfUrl  = 'index.php?controller=AdminModules&configure=everpsseo&token=';
        $moduleConfUrl .= Tools::getAdminTokenLite('AdminModules');

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
            LEFT JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl
                ON (
                    cl.`id_lang` = a.`id_seo_lang`
                    AND cl.id_cms_category = a.id_seo_cms_category
                )';

        $this->_where = 'AND a.id_shop = '.(int) $this->context->shop->id;

        $this->_group = 'GROUP BY a.id_ever_seo_cms_category';

        $this->fields_list = array(
            'id_ever_seo_cms_category' => array(
                'title' => $this->l('ID SEO CmsCategory'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'id_seo_cms_category' => array(
                'title' => $this->l('ID CmsCategory'),
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
                'width' => 'auto'
            ),
            'meta_description' => array(
                'title' => $this->l('Meta description'),
                'align' => 'left',
                'width' => 'auto'
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

        if (Tools::getValue('id_ever_seo_cms_category')) {
            $seoCmsCategory = new EverPsSeoCmsCategory(Tools::getValue('id_ever_seo_cms_category'));
            $cms_category = new CMSCategory(
                (int) $seoCmsCategory->id_seo_cms_category,
                (int) $id_lang,
                (int) $id_shop
            );
            $link = new Link();
            $objectUrl = $link->getCmsCategoryLink(
                $cms_category,
                null,
                null,
                null,
                (int) $this->context->language->id,
                (int) $this->context->shop->id
            );
            $editUrl  = 'index.php?controller=AdminCategories&id_cms_category='.(int) $cms_category->id.'';
            $editUrl .= '&updatecms_category&token='.Tools::getAdminTokenLite('AdminCategories');
            $objectGSearch = str_replace(' ', '+', $cms_category->name);

            $keywordsQlty = EverPsSeoKeywordsStrategy::getSeoCmsCategoryNote(
                $seoCmsCategory,
                $cms_category
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
                'headerObjectName' => $cms_category->name,
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
        $this->addCSS(_PS_MODULE_DIR_.'everpsseo/views/css/ever.css');
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Context::getContext()->getTranslator()->trans(
            $string,
            [],
            'Modules.Everpsseo.Admineverpsseocmscategorycontroller'
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
            'indexnow' => array(
                'text' => $this->l('Index now'),
                'confirm' => $this->l('Index now ?')
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
            $this->processBulkCopyMetaTitle();
        }

        if (Tools::isSubmit('submitBulkmetadescription'.$this->table)) {
            $this->processBulkCopyMetaDescription();
        }

        if (Tools::isSubmit('submitBulkmetatitlename'.$this->table)) {
            $this->processBulkSetNameAsMetaTitle();
        }

        if (Tools::isSubmit('submitBulkmetadescriptiondesc'.$this->table)) {
            $this->processBulkSetDescriptionAsMetaDescription();
        }

        if (Tools::isSubmit('submitBulkindexnow'.$this->table)) {
            $this->processBulkIndexNow();
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
                    'desc' => $this->l('Allow cms_category index'),
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
                    'desc' => $this->l('Allow cms_category follow'),
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
                    'desc' => $this->l('Set cms_category on sitemap'),
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
        $everCmsCategory = new EverPsSeoCmsCategory(
            (int) Tools::getValue('id_ever_seo_cms_category')
        );

        $everCmsCategory->indexable = !$everCmsCategory->indexable;

        if (!$everCmsCategory->save()) {
            $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
        }
    }

    public function processFollow()
    {
        $everCmsCategory = new EverPsSeoCmsCategory(
            (int) Tools::getValue('id_ever_seo_cms_category')
        );

        $everCmsCategory->follow = !$everCmsCategory->follow;

        if (!$everCmsCategory->save()) {
            $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
        }
    }

    public function processSitemap()
    {
        $everCmsCategory = new EverPsSeoCmsCategory(
            (int) Tools::getValue('id_ever_seo_cms_category')
        );
        $cms_category = new CMSCategory(
            (int) $everCmsCategory->id_seo_cms_category,
            (int) $everCmsCategory->id_seo_lang,
            (int) $this->context->shop->id
        );

        if ((int) $cms_category->active) {
            $everCmsCategory->allowed_sitemap = !$everCmsCategory->allowed_sitemap;
        } else {
            $everCmsCategory->allowed_sitemap = 0;
        }

        if (!$everCmsCategory->save()) {
            $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
        }
    }

    public function postProcess()
    {
        parent::postProcess();

        if (Tools::isSubmit('save')) {
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
            if (Tools::getValue('keywords')
                && !Validate::isString(Tools::getValue('keywords'))
            ) {
                 $this->errors[] = $this->l('keywords is invalid');
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

            if (!count($this->errors)) {
                $everCmsCategory = new EverPsSeoCmsCategory(
                    (int) Tools::getValue('id_ever_seo_cms_category')
                );
                $cmsCategory = new CMSCategory(
                    (int) $everCmsCategory->id_seo_cms,
                    (int) $everCmsCategory->id_seo_lang,
                    (int) $this->context->shop->id
                );
                // SEO object
                $everCmsCategory->meta_title = Tools::getValue('meta_title');
                $everCmsCategory->meta_description = Tools::getValue('meta_description');
                $everCmsCategory->social_title = Tools::getValue('social_title');
                $everCmsCategory->social_description = Tools::getValue('social_description');
                $everCmsCategory->keywords = Tools::getValue('keywords');
                $everCmsCategory->indexable = Tools::getValue('indexable');
                $everCmsCategory->follow = Tools::getValue('follow');
                $everCmsCategory->allowed_sitemap = Tools::getValue('allowed_sitemap');
                // Native object
                $cmsCategory->meta_title = Tools::getValue('meta_title');
                $cmsCategory->meta_description = Tools::getValue('meta_description');

                if (!$cmsCategory->save() || !$everCmsCategory->save()) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                }
            }
        }
    }

    public static function getDescriptionClean($description)
    {
        return Tools::getDescriptionClean($description);
    }

    protected function processBulkIndex()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverCmsCategory) {
            $everCmsCategory = new EverPsSeoCmsCategory(
                (int) $idEverCmsCategory
            );

            $everCmsCategory->indexable = !$everCmsCategory->indexable;

            if (!$everCmsCategory->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkFollow()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverCmsCategory) {
            $everCmsCategory = new EverPsSeoCmsCategory(
                (int) $idEverCmsCategory
            );

            $everCmsCategory->follow = !$everCmsCategory->follow;

            if (!$everCmsCategory->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkSitemap()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverCmsCategory) {
            $everCmsCategory = new EverPsSeoCmsCategory(
                (int) $idEverCmsCategory
            );

            $everCmsCategory->allowed_sitemap = !$everCmsCategory->allowed_sitemap;

            if (!$everCmsCategory->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkCopyMetaTitle()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverCmsCategory) {
            $everCmsCategory = new EverPsSeoCmsCategory(
                (int) $idEverCmsCategory
            );
            $cmsCategory = new CMSCategory(
                (int) $everCmsCategory->id_seo_cms,
                (int) $everCmsCategory->id_seo_lang,
                (int) $this->context->shop->id
            );

            $meta_title = Db::getInstance()->getValue(
                'SELECT meta_title FROM `' . _DB_PREFIX_ . 'cms_category_lang`
                WHERE id_cms_category = '.(int) $everCmsCategory->id_seo_cms_category.'
                AND id_lang = '.(int) $everCmsCategory->id_seo_lang
            );

            if (!$meta_title) {
                continue;
            }

            $cmsCategory->meta_title = $meta_title;
            // Hook update triggered
            if (!$cmsCategory->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkCopyMetaDescription()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverCmsCategory) {
            $everCmsCategory = new EverPsSeoCmsCategory(
                (int) $idEverCmsCategory
            );
            $cmsCategory = new CMSCategory(
                (int) $everCmsCategory->id_seo_cms,
                (int) $everCmsCategory->id_seo_lang,
                (int) $this->context->shop->id
            );

            $meta_description = Db::getInstance()->getValue(
                'SELECT meta_description FROM `' . _DB_PREFIX_ . 'cms_category_lang`
                WHERE id_cms_category = '.(int) $everCmsCategory->id_seo_cms_category.'
                AND id_lang = '.(int) $everCmsCategory->id_seo_lang
            );

            if (!$meta_description) {
                continue;
            }

            $cmsCategory->meta_description = $meta_description;
            // Hook update triggered
            if (!$cmsCategory->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkSetNameAsMetaTitle()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverCmsCategory) {
            $everCmsCategory = new EverPsSeoCmsCategory(
                (int) $idEverCmsCategory
            );
            $cmsCategory = new CMSCategory(
                (int) $everCmsCategory->id_seo_cms,
                (int) $everCmsCategory->id_seo_lang,
                (int) $this->context->shop->id
            );

            $name = Db::getInstance()->getValue(
                'SELECT name FROM `' . _DB_PREFIX_ . 'cms_category_lang`
                WHERE id_cms_category = '.(int) $everCmsCategory->id_seo_cms_category.'
                AND id_lang = '.(int) $everCmsCategory->id_seo_lang
            );

            if (!$name) {
                continue;
            }

            $cmsCategory->meta_title = $name;
            // Hook update triggered
            if (!$cmsCategory->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkSetDescriptionAsMetaDescription()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverCmsCategory) {
            $everCmsCategory = new EverPsSeoCmsCategory(
                (int) $idEverCmsCategory
            );
            $cmsCategory = new CMSCategory(
                (int) $everCmsCategory->id_seo_cms,
                (int) $everCmsCategory->id_seo_lang,
                (int) $this->context->shop->id
            );

            $description = Db::getInstance()->getValue(
                'SELECT content FROM `' . _DB_PREFIX_ . 'cms_category_lang`
                WHERE id_cms_category = '.(int) $everCmsCategory->id_seo_cms_category.'
                AND id_lang = '.(int) $everCmsCategory->id_seo_lang
            );

            if (!$description) {
                continue;
            }

            $cmsCategory->meta_description = Tools::substr(
                strip_tags($description),
                0,
                160
            );
            // Hook update triggered
            if (!$cmsCategory->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkIndexNow()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverCmsCategory) {
            $everCmsCategory = new EverPsSeoCmsCategory(
                (int) $idEverCmsCategory
            );
            $cmsCategory = new CMSCategory(
                (int) $everCmsCategory->id_seo_cms,
                (int) $everCmsCategory->id_seo_lang,
                (int) $this->context->shop->id
            );

            if (!Validate::isLoadedObject($cmsCategory)) {
                continue;
            }
            $link = new Link();
            $url = $link->getCmsCategoryLink(
                $cms_category,
                null,
                null,
                null,
                (int) $everCmsCategory->id_seo_lang,
                (int) $this->context->shop->id
            );
            $httpCode = EverPsSeoTools::indexNow(
                $url
            );
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_cms_category`
            SET status_code = '.(int) $httpCode.'
            WHERE id_seo_lang = '.(int) $everCmsCategory->id_seo_lang.'
            AND id_shop = '.(int) $this->context->shop->id.'
            AND id_seo_cms_category = '.(int) $cmsCategory->id;
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
