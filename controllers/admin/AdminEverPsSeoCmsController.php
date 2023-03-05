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

require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoCms.php';
require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoKeywordsStrategy.php';

class AdminEverPsSeoCmsController extends ModuleAdminController
{
    private $html;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = false;
        $this->table = 'ever_seo_cms';
        $this->className = 'EverPsSeoCms';
        $this->context = Context::getContext();
        $this->identifier = 'id_ever_seo_cms';

        $this->isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;
        $moduleConfUrl  = 'index.php?controller=AdminModules&configure=everpsseo&token=';
        $moduleConfUrl .= Tools::getAdminTokenLite('AdminModules');
        $this->img_folder = _PS_MODULE_DIR_.'everpsseo/views/img/cms/';
        $this->img_url = Tools::getHttpHost(true).__PS_BASE_URI__.'/modules/everpsseo/views/img/cms/';
        $this->context->smarty->assign(array(
            'moduleConfUrl' => (string)$moduleConfUrl,
            'image_dir' => _PS_BASE_URL_ . '/modules/everpsseo/views/img/'
        ));

        $this->_select = 'l.iso_code';

        $this->_join =
            'LEFT JOIN `'._DB_PREFIX_.'ever_seo_lang` l
                ON (
                    l.`id_seo_lang` = a.`id_seo_lang`
                )';

        $this->_where = 'AND a.id_shop = '.(int)$this->context->shop->id;

        $this->_group = 'GROUP BY a.id_ever_seo_cms';

        $this->fields_list = array(
            'id_ever_seo_cms' => array(
                'title' => $this->l('ID'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'id_seo_cms' => array(
                'title' => $this->l('ID CMS'),
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

        $this->colorOnBackground = true;

        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;

        if (Tools::getValue('id_ever_seo_cms')) {
            $seoCMS = new EverPsSeoCMS(Tools::getValue('id_ever_seo_cms'));
            $cms = new CMS(
                (int)$seoCMS->id_seo_cms,
                (int)$id_lang,
                (int)$id_shop
            );
            $link = new Link();
            $objectUrl = $link->getCMSLink(
                $cms,
                null,
                null,
                null,
                (int)$this->context->language->id,
                (int)$this->context->shop->id
            );
            $editUrl  = 'index.php?controller=AdminCMS&id_cms='.(int)$cms->id.'';
            $editUrl .= '&updatecms&token='.Tools::getAdminTokenLite('AdminCMS');
            $objectGSearch = str_replace(' ', '+', $cms->meta_title);

            $keywordsQlty = EverPsSeoKeywordsStrategy::getSeoCmsNote(
                $seoCMS,
                $cms
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
            $this->context->smarty->assign(array(
                'headerObjectName' => $cms->meta_title,
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
                'Modules.Everpsseo.Admineverpsseocmscontroller'
            );
        }

        return parent::l($string, $class, $addslashes, $htmlentities);
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
            'metadescriptioncontent' => array(
                'text' => $this->l('Short desc as meta desc'),
                'confirm' => $this->l('Set default CMS content as meta description ?')
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

        if (Tools::isSubmit('submitBulkmetadescriptioncontent'.$this->table)) {
            $this->processBulkSetContentAsMetaDescription();
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

        $this->toolbar_title = $this->l('SEO setting : CMS');

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

        if (Tools::getValue('id_ever_seo_cms')) {
            $seoCMS = new EverPsSeoCms(
                (int)Tools::getValue('id_ever_seo_cms')
            );
            if (file_exists($this->img_folder.$seoCMS->id_seo_cms.'.jpg')) {
                $defaultUrlImage = $this->img_url.$seoCMS->id_seo_cms.'.jpg';
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
        $defaultImage = '<image src="'.(string)$defaultUrlImage.'" style="max-width:80px;"/>';

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
                    'autoload_rte' => true
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
                    'desc' => $this->l('Allow cms index'),
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
                    'desc' => $this->l('Allow cms follow'),
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
                    'desc' => $this->l('Set cms on sitemap'),
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
        $everCms = new EverPsSeoCms(
            (int)Tools::getValue('id_ever_seo_cms')
        );

        $everCms->indexable = !$everCms->indexable;

        if (!$everCms->save()) {
            $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
        }
    }

    public function processFollow()
    {
        $everCms = new EverPsSeoCms(
            (int)Tools::getValue('id_ever_seo_cms')
        );

        $everCms->follow = !$everCms->follow;

        if (!$everCms->save()) {
            $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
        }
    }

    public function processSitemap()
    {
        $everCms = new EverPsSeoCms(
            (int)Tools::getValue('id_ever_seo_cms')
        );

        $everCms->allowed_sitemap = !$everCms->allowed_sitemap;

        if (!$everCms->save()) {
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
                $everCms = new EverPsSeoCms(
                    Tools::getValue('id_ever_seo_cms')
                );
                $cms = new CMS(
                    (int)$everCms->id_seo_cms,
                    (int)$everCms->id_seo_lang,
                    (int)$this->context->shop->id
                );
                // SEO Object
                $everCms->meta_title = Tools::getValue('meta_title');
                $everCms->meta_description = Tools::getValue('meta_description');
                $everCms->social_title = Tools::getValue('social_title');
                $everCms->social_description = Tools::getValue('social_description');
                $everCms->keywords = Tools::getValue('keywords');
                $everCms->indexable = Tools::getValue('indexable');
                $everCms->follow = Tools::getValue('follow');
                $everCms->allowed_sitemap = Tools::getValue('allowed_sitemap');
                // PS Object
                $cms->meta_title = Tools::getValue('meta_title');
                $cms->meta_description = Tools::getValue('meta_description');

                /* upload the image */
                if (isset($_FILES['social_media'])
                    && isset($_FILES['social_media']['tmp_name'])
                    && !empty($_FILES['social_media']['tmp_name'])
                ) {
                    Configuration::set('PS_IMAGE_GENERATION_METHOD', 1);
                    if (file_exists($this->img_folder.(int)$everCms->id_seo_cms.'.jpg')) {
                        unlink($this->img_folder.(int)$everCms->id_seo_cms.'.jpg');
                    }
                    if ($error = ImageManager::validateUpload($_FILES['social_media'])) {
                        $this->errors[] = $error;
                    } elseif (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS'))
                        || !move_uploaded_file($_FILES['social_media']['tmp_name'], $tmp_name)
                    ) {
                        return false;
                    } elseif (!ImageManager::resize(
                        $tmp_name,
                        $this->img_folder.(int)$everCms->id_seo_cms.'.jpg'
                    )) {
                        $this->errors[] = $this->l('An error occurred while attempting to upload the image.');
                    }
                    if (isset($tmp_name)) {
                        unlink($tmp_name);
                    }
                    $everCms->social_img_url = $this->img_url
                    .(int)$everCms->id_seo_cms
                    .'.jpg';
                }
                $everCms->save();
                if (!$cms->save() || !$everCms->save()) {
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
        foreach (Tools::getValue($this->table.'Box') as $idEverCms) {
            $everCms = new EverPsSeoCms(
                (int)$idEverCms
            );

            $everCms->indexable = !$everCms->indexable;

            if (!$everCms->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkFollow()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverCms) {
            $everCms = new EverPsSeoCms(
                (int)$idEverCms
            );

            $everCms->follow = !$everCms->follow;

            if (!$everCms->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkSitemap()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverCms) {
            $everCms = new EverPsSeoCms((int)$idEverCms);

            $everCms->allowed_sitemap = !$everCms->allowed_sitemap;

            if (!$everCms->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkCopyMetaTitle()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverCms) {
            $everCms = new EverPsSeoCms(
                (int)$idEverCms
            );

            $meta_title = Db::getInstance()->getValue(
                'SELECT meta_title FROM `'._DB_PREFIX_.'cms_lang`
                WHERE id_cms = '.(int)$everCms->id_seo_cms.'
                AND id_lang = '.(int)$everCms->id_seo_lang
            );

            if (!$meta_title) {
                continue;
            }

            $everCms->meta_title = $meta_title;
            // Hook update triggered
            if (!$everCms->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkCopyMetaDescription()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverCms) {
            $everCms = new EverPsSeoCms(
                (int)$idEverCms
            );
            $cms = new CMS(
                (int)$everCms->id_seo_cms,
                (int)$everCms->id_seo_lang,
                (int)$this->context->shop->id
            );

            $meta_description = Db::getInstance()->getValue(
                'SELECT meta_description FROM `'._DB_PREFIX_.'cms_lang`
                WHERE id_cms = '.(int)$everCms->id_seo_cms.'
                AND id_lang = '.(int)$everCms->id_seo_lang
            );

            if (!$meta_description) {
                continue;
            }

            $cms->meta_description = $meta_description;
            // Hook update triggered
            if (!$cms->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkSetContentAsMetaDescription()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverCms) {
            $everCms = new EverPsSeoCms(
                (int)$idEverCms
            );
            $cms = new CMS(
                (int)$everCms->id_seo_cms,
                (int)$everCms->id_seo_lang,
                (int)$this->context->shop->id
            );

            $content = Db::getInstance()->getValue(
                'SELECT content FROM `'._DB_PREFIX_.'cms_lang`
                WHERE id_cms = '.(int)$everCms->id_seo_cms.'
                AND id_lang = '.(int)$everCms->id_seo_lang
            );

            if (!$content) {
                continue;
            }

            $cms->meta_description = Tools::substr(
                strip_tags($content),
                0,
                160
            );
            // Hook update triggered
            if (!$cms->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
            }
        }
    }

    protected function processBulkIndexNow()
    {
        foreach (Tools::getValue($this->table.'Box') as $idEverCms) {
            $everCms = new EverPsSeoCms(
                (int)$idEverCms
            );
            $cms = new CMS(
                (int)$everCms->id_seo_cms,
                (int)$everCms->id_seo_lang,
                (int)$this->context->shop->id
            );

            if (!Validate::isLoadedObject($cms)) {
                continue;
            }
            $link = new Link();
            $url = $link->getCMSLink(
                $cms,
                null,
                null,
                null,
                (int)$everCms->id_seo_lang,
                (int)$this->context->shop->id
            );
            $httpCode = EverPsSeoTools::indexNow(
                $url
            );
            $sql = 'UPDATE `'._DB_PREFIX_.'ever_seo_cms`
            SET status_code = '.(int)$httpCode.'
            WHERE id_seo_lang = '.(int)$everCms->id_seo_lang.'
            AND id_shop = '.(int)$this->context->shop->id.'
            AND id_ever_seo_cms = '.(int)$cms->id;
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
