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
require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoCms.php';
require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoImage.php';
require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoManufacturer.php';
require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoPageMeta.php';
require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoProduct.php';
require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoRedirect.php';
require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoSupplier.php';
require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoSitemap.php';
require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoBacklink.php';
require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoKeywordsStrategy.php';
require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoStats.php';
require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoShortcode.php';
require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoTools.php';

class Everpsseo extends Module
{
    private $html;
    private $postErrors = [];
    private $postSuccess = [];
    private $objectsList = [
        _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoCategory.php',
        _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoCms.php',
        _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoImage.php',
        _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoManufacturer.php',
        _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoPageMeta.php',
        _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoProduct.php',
        _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoRedirect.php',
        _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoSupplier.php',
        _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoSitemap.php',
        _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoBacklink.php',
        _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoKeywordsStrategy.php',
        _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoStats.php',
        _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoShortcode.php',
        _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoTools.php'
    ];

    public function __construct()
    {
        $this->name = 'everpsseo';
        $this->tab = 'seo';
        $this->version = '9.3.5';
        $this->author = 'Team Ever';
        $this->need_instance = false;
        $this->module_key = '5ddabba8ec414cd5bd646fad24368472';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Ever SEO');
        $this->description = $this->l('Global optimize and work on your shop SEO');
        $this->confirmUninstall = $this->l('Are you really sure to remove all seo settings ?');
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_,
        ];
        $this->siteUrl = Tools::getHttpHost(true) . __PS_BASE_URI__;
        $this->imageType = ImageType::getFormattedName('large');
        $this->protocol_link = (Configuration::get('PS_SSL_ENABLED')
            || Tools::usingSecureMode()) ? 'https://' : 'http://';
        if (strpos(Tools::getValue('controller'), 'EverPsSeo') !== false
            || Tools::getValue('configure') == $this->name
        ) {
            $everToken = Tools::substr(Tools::encrypt('everpsseo/cron'), 0, 10);
            $sitemaps_cron_url = Context::getContext()->link->getModuleLink(
                $this->name,
                'eversitemaps',
                [
                    'token' => $everToken,
                    'id_shop' => (int) Context::getContext()->shop->id,
                ],
                true
            );
            $objects_cron_url = Context::getContext()->link->getModuleLink(
                $this->name,
                'everobjects',
                [
                    'token' => $everToken,
                    'id_shop' => (int) Context::getContext()->shop->id,
                ],
                true
            );
            $searchconsole = str_replace('://', '%3A%2F%2F', $this->siteUrl);
            if ((bool) Configuration::get('PS_REWRITING_SETTINGS') === false) {
                $rewrite_enabled = false;
            } else {
                $rewrite_enabled = true;
            }
            if ((bool) Configuration::get('PS_SSL_ENABLED') === false) {
                $ssl_enabled = false;
            } else {
                $ssl_enabled = true;
            }
            if ((int) Configuration::get('PS_CANONICAL_REDIRECT')) {
                $canonical = false;
            } else {
                $canonical = true;
            }
            // test if example files exist
            if (file_exists($this->_path . 'modules/everpsseo/output/categories.xlsx')) {
                $categoriesFileExample = $this->siteUrl . 'modules/everpsseo/output/categories.xlsx';
            } else {
                $categoriesFileExample = false;
            }
            if (file_exists($this->_path . 'modules/everpsseo/output/products.xlsx')) {
                $productsFileExample = $this->siteUrl . 'modules/everpsseo/output/products.xlsx';
            } else {
                $productsFileExample = false;
            }
            $this->context->smarty->assign([
                'categoriesFileExample' => $categoriesFileExample,
                'productsFileExample' => $productsFileExample,
                'image_dir' => $this->_path . 'views/img',
                'input_dir' => $this->siteUrl . 'modules/everpsseo/output/',
                'everpsseo_cron' => $sitemaps_cron_url,
                'everpsseo_objects' => $objects_cron_url,
                'indexes' => EverPsSeoSitemap::getSitemapIndexes(),
                'sitemaps' => EverPsSeoSitemap::getSitemaps(),
                'searchconsole' => pSQL($searchconsole),
                'rewrite_enabled' => $rewrite_enabled,
                'ssl_enabled' => $ssl_enabled,
                'canonical' => $canonical,
                'ever_seo_version' => $this->version,
            ]);
        }
    }

#################### START INSTALL & UNINSTALL ####################

    public function install()
    {
        if (extension_loaded('curl') == false) {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
            return false;
        }
        // Install SQL
        include dirname(__FILE__) . '/sql/install.php';
        // Insert SQL
        include dirname(__FILE__) . '/sql/insert.php';
        EverPsSeoTools::registerEverConfiguration();
        return parent::install()
            && $this->createSeoHook()
            && $this->registerEverHook()
            && $this->installModuleTab('AdminEverPsSeo', 'SELL', $this->l('SEO'))
            && $this->installModuleTab('AdminEverPsSeoProduct', 'AdminEverPsSeo', $this->l('SEO Products'))
            && $this->installModuleTab('AdminEverPsSeoImage', 'AdminEverPsSeo', $this->l('SEO Images'))
            && $this->installModuleTab('AdminEverPsSeoCategory', 'AdminEverPsSeo', $this->l('SEO  Categories'))
            && $this->installModuleTab('AdminEverPsSeoCms', 'AdminEverPsSeo', $this->l('SEO CMS'))
            && $this->installModuleTab('AdminEverPsSeoCmsCategory', 'AdminEverPsSeo', $this->l('SEO CMS Categories'))
            && $this->installModuleTab('AdminEverPsSeoManufacturer', 'AdminEverPsSeo', $this->l('SEO Manufacturers'))
            && $this->installModuleTab('AdminEverPsSeoSupplier', 'AdminEverPsSeo', $this->l('SEO Suppliers'))
            && $this->installModuleTab('AdminEverPsSeoPageMeta', 'AdminEverPsSeo', $this->l('SEO Pages Meta'))
            && $this->installModuleTab('AdminEverPsSeoRedirect', 'AdminEverPsSeo', $this->l('404 Redirections'))
            && $this->installModuleTab('AdminEverPsSeoBacklink', 'AdminEverPsSeo', $this->l('Backlinks'))
            && $this->installModuleTab('AdminEverPsSeoShortcode', 'AdminEverPsSeo', $this->l('Shortcodes'));
    }

    protected function installModuleTab($tabClass, $parent, $tabName)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $tabClass;
        $tab->id_parent = (int) Tab::getIdFromClassName($parent);
        $tab->position = Tab::getNewLastPosition($tab->id_parent);
        $tab->module = $this->name;
        if ($tabClass == 'AdminEverPsSeo') {
            $tab->icon = 'icon-team-ever';
        }
        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[(int) $lang['id_lang']] = $tabName;
        }
        return $tab->add();
    }

    protected function createSeoHook()
    {
        if (!Hook::getIdByName('actionChangeSeoShortcodes')) {
            $hook = new Hook();
            $hook->name = 'actionChangeSeoShortcodes';
            $hook->title = 'Action change Ever SEO shortcodes';
            $hook->description = 'This hook change SEO shortcodes';
            return $hook->save();
        } else {
            return true;
        }
    }

    protected function registerEverHook()
    {
        return $this->registerHook('actionChangeSeoShortcodes')
            && $this->registerHook('displayAdminProductsSeoStepBottom')
            && $this->registerHook('actionAdminControllerSetMedia')
            && $this->registerHook('header')
            && $this->registerHook('footer')
            && $this->registerHook('displayLeftColumn')
            && $this->registerHook('displayRightColumn')
            && $this->registerHook('displayAfterBodyOpeningTag')
            && $this->registerHook('orderConfirmation')
            && $this->registerHook('actionObjectLanguageAddAfter')
            && $this->registerHook('actionObjectProductAddAfter')
            && $this->registerHook('actionObjectCategoryAddAfter')
            && $this->registerHook('actionObjectCmsAddAfter')
            && $this->registerHook('actionObjectManufacturerAddAfter')
            && $this->registerHook('actionObjectSupplierAddAfter')
            && $this->registerHook('actionObjectImageAddAfter')
            && $this->registerHook('actionObjectLanguageDeleteAfter')
            && $this->registerHook('actionObjectProductDeleteAfter')
            && $this->registerHook('actionObjectCategoryDeleteAfter')
            && $this->registerHook('actionObjectCmsDeleteAfter')
            && $this->registerHook('actionObjectManufacturerDeleteAfter')
            && $this->registerHook('actionObjectSupplierDeleteAfter')
            && $this->registerHook('actionObjectImageDeleteAfter')
            && $this->registerHook('actionObjectProductUpdateAfter')
            && $this->registerHook('actionObjectCategoryUpdateAfter')
            && $this->registerHook('actionObjectCmsUpdateAfter')
            && $this->registerHook('actionObjectCmsCategoryUpdateAfter')
            && $this->registerHook('actionObjectManufacturerUpdateAfter')
            && $this->registerHook('actionObjectSupplierUpdateAfter')
            && $this->registerHook('actionObjectImageUpdateAfter')
            && $this->registerHook('actionAdminMetaAfterWriteRobotsFile')
            && $this->registerHook('actionHtaccessCreate')
            && $this->registerHook('displayReassurance')
            && $this->registerHook('displayOrderConfirmation')
            && $this->registerHook('displayContentWrapperBottom')
            && $this->registerHook('actionOutputHTMLBefore');
    }

    public function uninstall()
    {
        include dirname(__FILE__) . '/sql/uninstall.php';

        return parent::uninstall()
            && $this->uninstallModuleTab('AdminEverPsSeoConfigure')
            && $this->uninstallModuleTab('AdminEverPsSeo')
            && $this->uninstallModuleTab('AdminEverPsSeoProduct')
            && $this->uninstallModuleTab('AdminEverPsSeoImage')
            && $this->uninstallModuleTab('AdminEverPsSeoCategory')
            && $this->uninstallModuleTab('AdminEverPsSeoCms')
            && $this->uninstallModuleTab('AdminEverPsSeoManufacturer')
            && $this->uninstallModuleTab('AdminEverPsSeoSupplier')
            && $this->uninstallModuleTab('AdminEverPsSeoPageMeta')
            && $this->uninstallModuleTab('AdminEverPsSeoRedirect')
            && $this->uninstallModuleTab('AdminEverPsSeoBacklink')
            && $this->uninstallModuleTab('AdminEverPsSeoCmsCategory');
    }

    protected function uninstallModuleTab($tabClass)
    {
        $tab = new Tab((int) Tab::getIdFromClassName($tabClass));

        return $tab->delete();
    }

#################### END INSTALL & UNINSTALL ####################
#################### START CONFIG FORM ####################

    public function getContent()
    {
        $this->html = '';
        $languages = Language::getIDs(true);
        $allowedLangs = $this->getAllowedSitemapLangs();
        // XLSX files upload
        if (Tools::isSubmit('submitUploadRedirectionFile')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->postProcess();
                $this->uploadRedirectionFile();
            }
        }
        if (Tools::isSubmit('submitUploadProductsFile')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->postProcess();
                $this->uploadProductsFile();
            }
        }
        if (Tools::isSubmit('submitUploadCategoriesFile')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->uploadCategoriesFile();
            }
        }
        if (Tools::isSubmit('submitUploadFeatureValuesFile')) {
            $this->postValidation();

            if (!count($this->postErrors)) {
                $this->uploadFeatureValuesFile();
            }
        }

        // Main form submition
        if (Tools::isSubmit('submiteverpsseoModule')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->postProcess();
            }
        }

        // Products content generator
        if (Tools::isSubmit('submitGenerateProductsContent')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $allowedLangs = $this->getAllowedShortcodesLangs(
                    'EVERSEO_PGENERATOR_LANGS'
                );
                $seoArray = EverPsSeoProduct::getAllSeoProductsIds(
                    (int) $this->context->shop->id,
                    $allowedLangs
                );
                foreach ($seoArray as $seo) {
                    $this->autoSetContentShortDesc(
                        'id_seo_product',
                        (int) $seo['id_seo_product'],
                        (int) $this->context->shop->id,
                        (int) $seo['id_seo_lang']
                    );
                    $this->autoSetContentDesc(
                        'id_seo_product',
                        (int) $seo['id_seo_product'],
                        (int) $this->context->shop->id,
                        (int) $seo['id_seo_lang']
                    );
                }
            }
        }

        // Categories content generator
        if (Tools::isSubmit('submitGenerateCategoriesContent')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $seoArray = EverPsSeoCategory::getAllSeoCategoriesIds(
                    (int) $this->context->shop->id
                );
                $allowedLangs = $this->getAllowedShortcodesLangs(
                    'EVERSEO_AUTO_CATEGORY_LANGS'
                );
                foreach ($seoArray as $seo) {
                    if (in_array((int) $seo['id_seo_lang'], $allowedLangs)) {
                        $this->autoSetContentDesc(
                            'id_seo_category',
                            (int) $seo['id_seo_category'],
                            (int) $this->context->shop->id,
                            (int) $seo['id_seo_lang']
                        );
                    }
                }
            }
        }

        // Manufacturers content generator
        if (Tools::isSubmit('submitGenerateManufacturersContent')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $seoArray = EverPsSeoManufacturer::getAllSeoManufacturersIds(
                    (int) $this->context->shop->id
                );
                $allowedLangs = $this->getAllowedShortcodesLangs(
                    'EVERSEO_AUTO_MANUFACTURER_LANGS'
                );
                foreach ($seoArray as $seo) {
                    if (in_array((int) $seo['id_seo_lang'], $allowedLangs)) {
                        $this->autoSetContentDesc(
                            'id_seo_manufacturer',
                            (int) $seo['id_seo_manufacturer'],
                            (int) $this->context->shop->id,
                            (int) $seo['id_seo_lang']
                        );
                    }
                }
            }
        }

        // Suppliers content generator
        if (Tools::isSubmit('submitGenerateSuppliersContent')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $seoArray = EverPsSeoSupplier::getAllSeoSuppliersIds(
                    (int) $this->context->shop->id
                );
                $allowedLangs = $this->getAllowedShortcodesLangs(
                    'EVERSEO_AUTO_SUPPLIER_LANGS'
                );
                foreach ($seoArray as $seo) {
                    if (in_array((int) $seo['id_seo_lang'], $allowedLangs)) {
                        $this->autoSetContentDesc(
                            'id_seo_supplier',
                            (int) $seo['id_seo_supplier'],
                            (int) $this->context->shop->id,
                            (int) $seo['id_seo_lang']
                        );
                    }
                }
            }
        }

        // SEO meta automation
        if (Tools::isSubmit('submitAutoProduct')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $allowedLangs = $this->getAllowedShortcodesLangs(
                    'EVERSEO_AUTO_PRODUCT_LANGS'
                );
                $seoArray = EverPsSeoProduct::getAllSeoProductsIds(
                    (int) $this->context->shop->id,
                    $allowedLangs
                );

                foreach ($seoArray as $seo) {
                    $this->autoSetTitle(
                        'id_seo_product',
                        (int) $seo['id_seo_product'],
                        (int) $this->context->shop->id,
                        (int) $seo['id_seo_lang']
                    );
                    $this->autoSetDescription(
                        'id_seo_product',
                        (int) $seo['id_seo_product'],
                        (int) $this->context->shop->id,
                        (int) $seo['id_seo_lang']
                    );
                }
            }
        }

        if (Tools::isSubmit('submitAutoPagemeta')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $seoArray = EverPsSeoPageMeta::getAllSeoPagemetasIds(
                    (int) $this->context->shop->id
                );
                $allowedLangs = $this->getAllowedShortcodesLangs(
                    'EVERSEO_AUTO_PAGEMETA_LANGS'
                );
                foreach ($seoArray as $seo) {
                    if (in_array((int) $seo['id_seo_lang'], $allowedLangs)) {
                        $this->autoSetTitle(
                            'id_seo_pagemeta',
                            (int) $seo['id_seo_pagemeta'],
                            (int) $this->context->shop->id,
                            (int) $seo['id_seo_lang']
                        );
                        $this->autoSetDescription(
                            'id_seo_pagemeta',
                            (int) $seo['id_seo_pagemeta'],
                            (int) $this->context->shop->id,
                            (int) $seo['id_seo_lang']
                        );
                    }
                }
            }
        }

        if (Tools::isSubmit('submitAutoCms')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $seoArray = EverPsSeoCms::getAllSeoCmsIds(
                    (int) $this->context->shop->id
                );
                $allowedLangs = $this->getAllowedShortcodesLangs(
                    'EVERSEO_AUTO_CMS_LANGS'
                );
                foreach ($seoArray as $seo) {
                    if (in_array((int) $seo['id_seo_lang'], $allowedLangs)) {
                        $this->autoSetTitle(
                            'id_seo_cms',
                            (int) $seo['id_seo_cms'],
                            (int) $this->context->shop->id,
                            (int) $seo['id_seo_lang']
                        );
                        $this->autoSetDescription(
                            'id_seo_cms',
                            (int) $seo['id_seo_cms'],
                            (int) $this->context->shop->id,
                            (int) $seo['id_seo_lang']
                        );
                    }
                }
            }
        }

        if (Tools::isSubmit('submitAutoSupplier')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $seoArray = EverPsSeoSupplier::getAllSeoSuppliersIds(
                    (int) $this->context->shop->id
                );
                $allowedLangs = $this->getAllowedShortcodesLangs(
                    'EVERSEO_AUTO_SUPPLIER_LANGS'
                );
                foreach ($seoArray as $seo) {
                    if (in_array((int) $seo['id_seo_lang'], $allowedLangs)) {
                        $this->autoSetTitle(
                            'id_seo_supplier',
                            (int) $seo['id_seo_supplier'],
                            (int) $this->context->shop->id,
                            (int) $seo['id_seo_lang']
                        );
                        $this->autoSetDescription(
                            'id_seo_supplier',
                            (int) $seo['id_seo_supplier'],
                            (int) $this->context->shop->id,
                            (int) $seo['id_seo_lang']
                        );
                    }
                }
            }
        }

        if (Tools::isSubmit('submitAutoManufacturer')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $seoArray = EverPsSeoManufacturer::getAllSeoManufacturersIds(
                    (int) $this->context->shop->id
                );
                $allowedLangs = $this->getAllowedShortcodesLangs(
                    'EVERSEO_AUTO_MANUFACTURER_LANGS'
                );
                foreach ($seoArray as $seo) {
                    if (in_array((int) $seo['id_seo_lang'], $allowedLangs)) {
                        $this->autoSetTitle(
                            'id_seo_manufacturer',
                            (int) $seo['id_seo_manufacturer'],
                            (int) $this->context->shop->id,
                            (int) $seo['id_seo_lang']
                        );
                        $this->autoSetDescription(
                            'id_seo_manufacturer',
                            (int) $seo['id_seo_manufacturer'],
                            (int) $this->context->shop->id,
                            (int) $seo['id_seo_lang']
                        );
                    }
                }
            }
        }

        if (Tools::isSubmit('submitAutoCategory')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $seoArray = EverPsSeoCategory::getAllSeoCategoriesIds(
                    (int) $this->context->shop->id
                );
                $allowedLangs = $this->getAllowedShortcodesLangs(
                    'EVERSEO_AUTO_CATEGORY_LANGS'
                );
                foreach ($seoArray as $seo) {
                    if (in_array((int) $seo['id_seo_lang'], $allowedLangs)) {
                        $this->autoSetTitle(
                            'id_seo_category',
                            (int) $seo['id_seo_category'],
                            (int) $this->context->shop->id,
                            (int) $seo['id_seo_lang']
                        );
                        $this->autoSetDescription(
                            'id_seo_category',
                            (int) $seo['id_seo_category'],
                            (int) $this->context->shop->id,
                            (int) $seo['id_seo_lang']
                        );
                    }
                }
            }
        }

        if (Tools::isSubmit('submitAutoAlt')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $seoArray = EverPsSeoImage::getAllSeoImagesIds(
                    (int) $this->context->shop->id
                );
                $allowedLangs = $this->getAllowedShortcodesLangs(
                    'EVERSEO_AUTO_IMAGE_LANGS'
                );
                foreach ($seoArray as $seo) {
                    if (in_array((int) $seo['id_seo_lang'], $allowedLangs)) {
                        $this->autoSetAlt(
                            (int) $seo['id_seo_img'],
                            (int) $this->context->shop->id,
                            (int) $seo['id_seo_lang']
                        );
                        $this->autoSetAltSeoImage(
                            (int) $seo['id_ever_seo_image'],
                            (int) $seo['id_seo_img'],
                            (int) $this->context->shop->id,
                            (int) $seo['id_seo_lang']
                        );
                    }
                }
            }
        }

        // SEO objects, useful for modules like Store Commander
        if (Tools::isSubmit('submitUpdateSeoProducts')) {
            $this->updateSeoProducts();
        }

        if (Tools::isSubmit('submitUpdateSeoCategories')) {
            $this->updateSeoCategories();
        }

        if (Tools::isSubmit('submitUpdateSeoManufacturers')) {
            $this->updateSeoManufacturers();
        }

        if (Tools::isSubmit('submitUpdateSeoSuppliers')) {
            $this->updateSeoSuppliers();
        }

        if (Tools::isSubmit('submitUpdateSeoCms')) {
            $this->updateSeoCms();
        }

        if (Tools::isSubmit('submitUpdateSeoPageMetas')) {
            $this->updateSeoPageMetas();
        }

        if (Tools::isSubmit('submitUpdateSeoImages')) {
            $this->updateSeoImages();
        }

        if (Tools::isSubmit('submitTruncateStatsData')) {
            EverPsSeoTools::truncateStatsData();
        }

        if (Tools::isSubmit('submitTruncateSeo404')) {
            EverPsSeoTools::truncateSeo404();
        }

        // Sitemaps generation
        if (Tools::isSubmit('submitSitemapCategory')) {
            foreach ($languages as $id_lang) {
                if (in_array((int) $id_lang, $allowedLangs)) {
                    $this->processSitemapCategory((int) $this->context->shop->id, (int) $id_lang);
                }
            }
        }

        if (Tools::isSubmit('submitSitemapProduct')) {
            foreach ($languages as $id_lang) {
                if (in_array((int) $id_lang, $allowedLangs)) {
                    $this->processSitemapProduct((int) $this->context->shop->id, (int) $id_lang);
                }
            }
        }

        if (Tools::isSubmit('submitSitemapCms')) {
            foreach ($languages as $id_lang) {
                if (in_array((int) $id_lang, $allowedLangs)) {
                    $this->processSitemapCms((int) $this->context->shop->id, (int) $id_lang);
                }
            }
        }

        if (Tools::isSubmit('submitSitemapManufacturer')) {
            foreach ($languages as $id_lang) {
                if (in_array((int) $id_lang, $allowedLangs)) {
                    $this->processSitemapManufacturer((int) $this->context->shop->id, (int) $id_lang);
                }
            }
        }

        if (Tools::isSubmit('submitSitemapSupplier')) {
            foreach ($languages as $id_lang) {
                if (in_array((int) $id_lang, $allowedLangs)) {
                    $this->processSitemapSupplier((int) $this->context->shop->id, (int) $id_lang);
                }
            }
        }

        if (Tools::isSubmit('submitSitemapImage')) {
            foreach ($languages as $id_lang) {
                if (in_array((int) $id_lang, $allowedLangs)) {
                    $this->processSitemapImage((int) $this->context->shop->id, (int) $id_lang);
                }
            }
        }

        if (Tools::isSubmit('submitSitemapPageMeta')) {
            foreach ($languages as $id_lang) {
                if (in_array((int) $id_lang, $allowedLangs)) {
                    $this->processSitemapPageMeta((int) $this->context->shop->id, (int) $id_lang);
                }
            }
        }

        // Delete unused lang objects
        if (Tools::isSubmit('submitDeleteUnusedObjects')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->postProcess();
                $this->processDeleteUnusedObjects();
            }
        }

        // Check multishop indexation and sitemaps
        if (Tools::isSubmit('submitMultishopSitemapIndex')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->postProcess();
                EverPsSeoTools::updateMultishopSitemapIndex();
            }
        }

        // Delete duplicate objects
        if (Tools::isSubmit('submitDeleteDuplicate')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->postProcess();
                $this->deleteDuplicate();
            }
        }

        // Process internal linking
        if (Tools::isSubmit('submitInternalLinking')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->processInternalLinking((int) $this->context->shop->id);
            }
        }

        // Bulk noindex lang
        if (Tools::isSubmit('submitNoindexLang')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->postProcess();
                foreach ($this->getBannedLangs() as $id_lang) {
                    EverPsSeoTools::noIndexLang(
                        (int) $id_lang
                    );
                }
            }
        }

        // Bulk nofollow lang
        if (Tools::isSubmit('submitNofollowLang')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->postProcess();
                foreach ($this->getBannedLangs() as $id_lang) {
                    EverPsSeoTools::noFollowLang(
                        (int) $id_lang
                    );
                }
            }
        }

        // Bulk no sitemap lang
        if (Tools::isSubmit('submitNositemapLang')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->postProcess();
                foreach ($this->getBannedLangs() as $id_lang) {
                    EverPsSeoTools::noSitemapLang(
                        (int) $id_lang
                    );
                }
            }
        }

        // Bulk index lang
        if (Tools::isSubmit('submitIndexLang')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->postProcess();
                foreach ($this->getBannedLangs() as $id_lang) {
                   EverPsSeoTools::indexLang(
                        (int) $id_lang
                    );
                }
            }
        }

        // Bulk follow lang
        if (Tools::isSubmit('submitFollowLang')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->postProcess();
                foreach ($this->getBannedLangs() as $id_lang) {
                    EverPsSeoTools::followLang(
                        (int) $id_lang
                    );
                }
            }
        }

        // Bulk follow lang
        if (Tools::isSubmit('submitSitemapLang')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->postProcess();
                foreach ($this->getBannedLangs() as $id_lang) {
                    EverPsSeoTools::sitemapLang(
                        (int) $id_lang
                    );
                }
            }
        }

        // Display errors
        if (count($this->postErrors)) {
            foreach ($this->postErrors as $error) {
                $this->html .= $this->displayError($error);
            }
        }

        // Display confirmations
        if (count($this->postSuccess)) {
            foreach ($this->postSuccess as $success) {
                $this->html .= $this->displayConfirmation($success);
            }
        }

        $this->html .= $this->context->smarty->fetch(
            $this->local_path . 'views/templates/admin/header.tpl'
        );
        $this->html .= $this->renderForm();
        $this->html .= $this->context->smarty->fetch(
            $this->local_path . 'views/templates/admin/footer.tpl'
        );
        return $this->html;
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submiteverpsseoModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => (int) $this->context->language->id,
        ];
        return $helper->generateForm($this->getConfigForm());
    }

    protected function getConfigForm()
    {
        $employees = Employee::getEmployees();
        // Allowed products categories for products content generator
        $categories = Category::getAllCategoriesName(
            (int)$this->context->language->id
        );
        foreach ($categories as &$cat) {
            $cat['name'] = $cat['id_category'] . ' - ' . $cat['name'];
        }

        $orderby = [
            [
                'id_orderby' => 0,
                'name' => $this->l('Oldest value found'),
            ],
            [
                'id_orderby' => 1,
                'name' => $this->l('Most recent value found'),
            ],
        ];

        $priority = [
            [
                'id_priority' => 1,
                'name' => $this->l('Products, categories, tags'),
            ],
            [
                'id_priority' => 2,
                'name' => $this->l('Products, tags, categories'),
            ],
            [
                'id_priority' => 3,
                'name' => $this->l('Categories, products, tags'),
            ],
            [
                'id_priority' => 4,
                'name' => $this->l('Categories, tags, products'),
            ],
            [
                'id_priority' => 5,
                'name' => $this->l('Tags, products, categories'),
            ],
            [
                'id_priority' => 6,
                'name' => $this->l('Tags, categories, products'),
            ],
        ];

        $qualityRiskLevel = [
            [
                'id_quality_risk_level' => 0,
                'name' => $this->l('"I am Iron Man" quality risk'),
            ],
            [
                'id_quality_risk_level' => 1,
                'name' => $this->l('"I am ineluctable" quality risk'),
            ],
            [
                'id_quality_risk_level' => 2,
                'name' => $this->l('Shaggy quality risk'),
            ],
            [
                'id_quality_risk_level' => 3,
                'name' => $this->l('God quality risk'),
            ],
            [
                'id_quality_risk_level' => 4,
                'name' => $this->l('Expert quality risk'),
            ],
            [
                'id_quality_risk_level' => 5,
                'name' => $this->l('High quality risk'),
            ],
            [
                'id_quality_risk_level' => 6,
                'name' => $this->l('Advanced quality risk'),
            ],
            [
                'id_quality_risk_level' => 7,
                'name' => $this->l('Normal quality risk'),
            ],
            [
                'id_quality_risk_level' => 8,
                'name' => $this->l('Low quality risk'),
            ],
        ];

        $redirectCodes = [
            [
                'id_redirect' => '301',// 301 Moved Permanently
                'name' => '301',
            ],
            [
                'id_redirect' => '302',// 302 Found
                'name' => '302',
            ],
            [
                'id_redirect' => '303',// 303 See Other
                'name' => '303',
            ],
            [
                'id_redirect' => '307',// 307 Temporary Redirect
                'name' => '307',
            ],
        ];

        $frequency = [
            [
                'id_frequency' => 'daily',
                'name' => 'daily',
            ],
            [
                'id_frequency' => 'weekly',
                'name' => 'weekly',
            ],
            [
                'id_frequency' => 'monthly',
                'name' => 'monthly',
            ],
            [
                'id_frequency' => 'yearly',
                'name' => 'yearly',
            ],
        ];

        $sitemapPriority = [
            [
                'id_sitemap_priority' => '0',
                'name' => '0',
            ],
            [
                'id_sitemap_priority' => '0.1',
                'name' => '0.1',
            ],
            [
                'id_sitemap_priority' => '0.2',
                'name' => '0.2',
            ],
            [
                'id_sitemap_priority' => '0.3',
                'name' => '0.3',
            ],
            [
                'id_sitemap_priority' => '0.4',
                'name' => '0.4',
            ],
            [
                'id_sitemap_priority' => '0.5',
                'name' => '0.5',
            ],
            [
                'id_sitemap_priority' => '0.6',
                'name' => '0.6',
            ],
            [
                'id_sitemap_priority' => '0.7',
                'name' => '0.7',
            ],
            [
                'id_sitemap_priority' => '0.8',
                'name' => '0.8',
            ],
            [
                'id_sitemap_priority' => '0.9',
                'name' => '0.9',
            ],
            [
                'id_sitemap_priority' => '1',
                'name' => '1',
            ],
        ];

        if (file_exists(_PS_MODULE_DIR_ . 'everpsseo/views/img/everpsseo.jpg')) {
            $defaultUrlImage = $this->_path . 'views/img/everpsseo.jpg';
        } else {
            $defaultUrlImage = Tools::getHttpHost(true) . __PS_BASE_URI__ . 'img/' . Configuration::get(
                'PS_LOGO'
            );
        }
        $defaultImage = '<image src="' . pSQL($defaultUrlImage) . '" style="max-width:80px;" />';

        $knowledgegraph_type = [
            [
                'id_knowledgegraph' => 'Organization',
                'name' => 'Organization',
            ],
            [
                'id_knowledgegraph' => 'Person',
                'name' => 'Person',
            ],
        ];

        $form_fields = [];

        //General SEO parameters
        $form_fields[] = array(
            'form' => array(
                'legend' => [
                    'title' => $this->l('Global SEO settings'),
                    'icon' => 'icon-smile',
                ],
                'input' => array(
                    [
                        'type' => 'select',
                        'label' => $this->l('Person or company'),
                        'desc' => $this->l('For Google knowledgegraph'),
                        'hint' => $this->l('Will work using rich snippets'),
                        'name' => 'EVERSEO_KNOWLEDGE',
                        'required' => true,
                        'options' => [
                            'query' => $knowledgegraph_type,
                            'id' => 'id_knowledgegraph',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Ever Quality level ?'),
                        'desc' => $this->l('Notation system level'),
                        'hint' => $this->l('At what level of difficulty do you want to start?'),
                        'name' => 'EVERSEO_QUALITY_LEVEL',
                        'required' => true,
                        'options' => [
                            'query' => $qualityRiskLevel,
                            'id' => 'id_quality_risk_level',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Theme color'),
                        'desc' => $this->l('This will be used for mobile'),
                        'hint' => $this->l('Please choose a theme color'),
                        'required' => false,
                        'name' => 'EVERSEO_THEME_COLOR',
                        'lang' => false,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Redirect all users except registered IP'),
                        'desc' => $this->l('Will redirect all users based on maintenance IP'),
                        'hint' => $this->l('Enable if you have troubles with maintenance mode'),
                        'name' => 'EVERSEO_MAINTENANCE',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Redirect users to this URL if SEO maintenance is ON'),
                        'desc' => $this->l('Will redirect to this URL only if SEO maintenance is ON'),
                        'hint' => $this->l('Default will be google.com'),
                        'name' => 'EVERSEO_MAINTENANCE_URL',
                        'lang' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Index now request limit per day'),
                        'desc' => $this->l('Set here how many requests can be sent to Index Nox'),
                        'hint' => $this->l('Format is often UA-12345678-1'),
                        'required' => true,
                        'name' => 'EVERSEO_INDEXNOW_LIMIT',
                        'lang' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Analytics tracking code'),
                        'desc' => $this->l('This is your GA_TRACKING_ID'),
                        'hint' => $this->l('Format is often UA-12345678-1'),
                        'required' => false,
                        'name' => 'EVERSEO_ANALYTICS',
                        'lang' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Search Console site verification'),
                        'desc' => $this->l('Please add Search Console meta content'),
                        'hint' => $this->l('Meta content given by Google Search Console'),
                        'required' => false,
                        'name' => 'EVERSEO_SEARCHCONSOLE',
                        'lang' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Google Tag Manager code'),
                        'desc' => $this->l('Please add Google Tag Manager GTM code'),
                        'hint' => $this->l('GTM code is given by Google Tag Manager'),
                        'required' => false,
                        'name' => 'EVERSEO_GTAG',
                        'lang' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Facebook pixel (number)'),
                        'desc' => $this->l('Facebook pixel number'),
                        'hint' => $this->l('Given by Facebook ad manager'),
                        'required' => false,
                        'name' => 'EVERSEO_FBPIXEL',
                        'lang' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Adwords tracking code'),
                        'desc' => $this->l('Adwords meta value'),
                        'hint' => $this->l('Please only set meta value'),
                        'required' => false,
                        'name' => 'EVERSEO_ADWORDS',
                        'lang' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Adwords add to cart label code'),
                        'desc' => $this->l('Adwords add to cart label code'),
                        'hint' => $this->l('Please only add to cart label code'),
                        'required' => false,
                        'name' => 'EVERSEO_ADWORDS_CART_LABEL',
                        'lang' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Adwords event snippet code'),
                        'desc' => $this->l('Adwords meta value for order confirmation'),
                        'hint' => $this->l('Please only set meta value'),
                        'required' => false,
                        'name' => 'EVERSEO_ADWORDS_SENDTO',
                        'lang' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Adwords contact event snippet code'),
                        'desc' => $this->l('Adwords meta value for contact page'),
                        'hint' => $this->l('Please only set meta value'),
                        'required' => false,
                        'name' => 'EVERSEO_ADWORDS_CONTACT',
                        'lang' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Adwords Opart quotation event snippet code'),
                        'desc' => $this->l('Adwords meta value for quotation page'),
                        'hint' => $this->l('Opart quotation module must be installed'),
                        'required' => false,
                        'name' => 'EVERSEO_ADWORDS_OPART',
                        'lang' => false,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Add Facebook Open Graph metas'),
                        'desc' => $this->l('Do you share pages to Facebook ?'),
                        'hint' => $this->l('Only if your theme does not support it'),
                        'name' => 'EVERSEO_USE_OPENGRAPH',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Add twitter metas'),
                        'desc' => $this->l('Do you have Twitter account ?'),
                        'hint' => $this->l('You should !'),
                        'name' => 'EVERSEO_USE_TWITTER',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Twitter account'),
                        'desc' => $this->l('Add your @, no spaces'),
                        'hint' => $this->l('You can add a false @ 🙂'),
                        'required' => true,
                        'name' => 'EVERSEO_TWITTER_NAME',
                        'lang' => false,
                    ],
                    [
                        'type' => 'file',
                        'label' => $this->l('Default image'),
                        'desc' => $this->l('For sharing pages and products'),
                        'hint' => $this->l('Default will be shop logo'),
                        'name' => 'image',
                        'display_image' => true,
                        'image' => $defaultImage,
                        'desc' => sprintf($this->l('
                            maximum image size: %s.'), ini_get('upload_max_filesize')),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Use webp'),
                        'desc' => $this->l('Do you want to use webp img files on your shop ?'),
                        'hint' => $this->l('First you will have to generate webp files using command lines'),
                        'name' => 'EVERSEO_WEBP',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Use lazyload on "loading" attributes'),
                        'desc' => $this->l('Do you want to use laazy load on your shop ?'),
                        'hint' => $this->l('First you will have to add "loading" attribute to each element to lazy load'),
                        'name' => 'EVERSEO_LAZY_LOAD',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Lazy load exceptions'),
                        'desc' => $this->l('You can enter here the HTML classes which will not be lazy loaded by the module'),
                        'hint' => $this->l('For example : "#carousel img, #slider img"'),
                        'name' => 'EVERSEO_LAZY_LOAD_EXCEPTIONS',
                        'lang' => false,
                        'required' => false,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Defer all scripts'),
                        'desc' => $this->l('If enabled, all scripts will have defer attribute'),
                        'hint' => $this->l('Will add to all scripts defer attribute'),
                        'name' => 'EVERSEO_DEFER',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Move all scripts to bottom of HTML page'),
                        'desc' => $this->l('If enabled, all scripts will be moved to bottom of HTML page'),
                        'hint' => $this->l('Will move all scripts to bottom of HTML page'),
                        'name' => 'EVERSEO_BOTTOM_SCRIPTS',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Add missing alt attributes'),
                        'desc' => $this->l('If enabled, all images without alt attributes will have auto generated alt'),
                        'hint' => $this->l('Will add alt to all images without this attribute'),
                        'name' => 'EVERSEO_ADD_ALT',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Add missing title attributes'),
                        'desc' => $this->l('If enabled, all links without title attributes will have auto generated title'),
                        'hint' => $this->l('Will add title to all titles without this attribute'),
                        'name' => 'EVERSEO_ADD_TITLE',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Set all external links as nofollow'),
                        'desc' => $this->l('All external links will open on new target with nofollow attribute'),
                        'hint' => $this->l('Else all external links will have follow attribute'),
                        'name' => 'EVERSEO_EXTERNAL_NOFOLLOW',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Compress HTML page ?'),
                        'desc' => $this->l('Will compress HTML page before rendering'),
                        'hint' => $this->l('Else HTML output won\'t be compressed'),
                        'name' => 'EVERSEO_COMPRESS_HTML',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Use rich snippets'),
                        'desc' => $this->l('Will add prices on search console'),
                        'hint' => $this->l('Will not add notations, please use King Avis'),
                        'name' => 'EVERSEO_RSNIPPETS',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Add canonical URL'),
                        'desc' => $this->l('Only if your theme does not support it'),
                        'hint' => $this->l('Is fully required by Google'),
                        'name' => 'EVERSEO_CANONICAL',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Add hreflangs'),
                        'desc' => $this->l('Is your site multilingual ?'),
                        'hint' => $this->l('Set not if your site is not multilingual'),
                        'name' => 'EVERSEO_HREF_LANG',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Max element quantities in sitemaps'),
                        'desc' => $this->l('Reduce this value to set lighter sitemaps'),
                        'hint' => $this->l('Should not be more than 50 000'),
                        'name' => 'EVERSEO_SITEMAP_QTY_ELEMENTS',
                        'lang' => false,
                        'required' => true,
                    ],
                    [
                        'type' => 'select',
                        'label' =>  $this->l('Allowed languages in sitemap'),
                        'desc' =>  $this->l('Choose allowed langs for sitemaps'),
                        'hint' =>  $this->l('Only allowed langs will have sitemaps'),
                        'name' => 'EVERSEO_SITEMAP_LANGS[]',
                        'class' => 'chosen',
                        'identifier' => 'name',
                        'multiple' => true,
                        'required' => true,
                        'options' => [
                            'query' => Language::getLanguages(false),
                            'id' => 'id_lang',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Regenerate link rewrite ?'),
                        'desc' => $this->l('Will regenerate link rewrite for products & categories for allowed languages'),
                        'hint' => $this->l('Set "No" will leave actual link rewrites'),
                        'name' => 'EVERSEO_REWRITE_LINKS',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Create logs files on commands ?'),
                        'desc' => $this->l('Will create logs files on commands errors'),
                        'hint' => $this->l('Set "No" will only output errors on commands'),
                        'name' => 'EVER_LOG_CMD',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        // XLSX files import
        $form_fields[] = array(
            'form' => array(
                'legend' => [
                    'title' => $this->l('Upload redirection update file'),
                    'icon' => 'icon-download',
                ],
                'input' => array(
                    [
                        'type' => 'file',
                        'label' => $this->l('Upload redirection file'),
                        'desc' => $this->l('Will upload redirection file and wait until update cron is triggered'),
                        'hint' => $this->l('For SEO updates only'),
                        'name' => 'redirection_file',
                        'display_image' => false,
                        'required' => false,
                    ],
                ),
                'buttons' => [
                    'import' => [
                        'name' => 'submitUploadRedirectionFile',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-download',
                        'title' => $this->l('Upload file'),
                    ],
                ],
            )
        );
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Upload products update file'),
                    'icon' => 'icon-download',
                ),
                'input' => array(
                    array(
                        'type' => 'file',
                        'label' => $this->l('Upload products file'),
                        'desc' => $this->l('Will upload products file and wait until update cron is triggered'),
                        'hint' => $this->l('For SEO updates only'),
                        'name' => 'products_file',
                        'display_image' => false,
                        'required' => false
                    ),
                ),
                'buttons' => array(
                    'import' => array(
                        'name' => 'submitUploadProductsFile',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-download',
                        'title' => $this->l('Upload file')
                    ),
                ),
            )
        );
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Upload categories update file'),
                    'icon' => 'icon-download',
                ),
                'input' => array(
                    array(
                        'type' => 'file',
                        'label' => $this->l('Upload categories file'),
                        'desc' => $this->l('Will upload categories file and wait until update cron is triggered'),
                        'hint' => $this->l('For SEO updates only'),
                        'name' => 'categories_file',
                        'display_image' => false,
                        'required' => false
                    ),
                ),
                'buttons' => array(
                    'import' => array(
                        'name' => 'submitUploadCategoriesFile',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-download',
                        'title' => $this->l('Upload file')
                    ),
                ),
            )
        );
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Upload feature values update file'),
                    'icon' => 'icon-download',
                ),
                'input' => array(
                    array(
                        'type' => 'file',
                        'label' => $this->l('Upload feature values file'),
                        'desc' => $this->l('Will upload feature values file and wait until update cron is triggered'),
                        'hint' => $this->l('For SEO updates only'),
                        'name' => 'featurevalues_file',
                        'display_image' => false,
                        'required' => false
                    ),
                ),
                'buttons' => array(
                    'import' => array(
                        'name' => 'submitUploadFeatureValuesFile',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-download',
                        'title' => $this->l('Upload file')
                    ),
                ),
            )
        );

        // Search override fields
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Search settings'),
                    'icon' => 'icon-search',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Auto redirect search if category is found ?'),
                        'desc' => $this->l('Will redirect user to category page if search matches category name'),
                        'hint' => $this->l('Else search won\'t be redirected to category'),
                        'name' => 'EVER_SEARCH_CATEGORIES',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Auto redirect search if manufacturer is found ?'),
                        'desc' => $this->l('Will redirect user to manufacturer page if search matches manufacturer name'),
                        'hint' => $this->l('Else search won\'t be redirected to manufacturer'),
                        'name' => 'EVER_SEARCH_MANUFACTURERS',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Auto redirect search if supplier is found ?'),
                        'desc' => $this->l('Will redirect user to supplier page if search matches supplier name'),
                        'hint' => $this->l('Else search won\'t be redirected to supplier'),
                        'name' => 'EVER_SEARCH_SUPPLIERS',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Auto redirect search if product is found ?'),
                        'desc' => $this->l('Will redirect user to product page if search matches product name'),
                        'hint' => $this->l('Else search won\'t be redirected to product'),
                        'name' => 'EVER_SEARCH_PRODUCTS',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        // Redirect setting
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('404 redirects'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Allow auto redirect'),
                        'desc' => $this->l('Anti 404 pages'),
                        'hint' => $this->l('Awesome function !'),
                        'name' => 'EVERSEO_REWRITE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Redirect to products ?'),
                        'desc' => $this->l('Do you want to redirect 404 to products ?'),
                        'hint' => $this->l('You are on an e-shop... Of course you want !'),
                        'name' => 'EVERSEO_PRODUCT',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Redirect to categories ?'),
                        'desc' => $this->l('Will redirect 404 to categories'),
                        'hint' => $this->l('Perhaps categories are more useful than products ?'),
                        'name' => 'EVERSEO_CATEGORY',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Redirect using tags ?'),
                        'desc' => $this->l('Will redirect to products using tags'),
                        'hint' => $this->l('You should if you use tags on products'),
                        'name' => 'EVERSEO_TAGS',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Redirect priorities'),
                        'desc' => $this->l('What are redirect priorities ?'),
                        'hint' => $this->l('Default should be products - categories - tags'),
                        'name' => 'EVERSEO_PRIORITY',
                        'required' => false,
                        'options' => array(
                            'query' => $priority,
                            'id' => 'id_priority',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Redirection codes'),
                        'desc' => $this->l('Will the redirect be permanent ?'),
                        'hint' => $this->l('Default should be 302'),
                        'name' => 'EVERSEO_REDIRECT',
                        'required' => true,
                        'options' => array(
                            'query' => $redirectCodes,
                            'id' => 'id_redirect',
                            'name' => 'name',
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('What if is not found ?'),
                        'desc' => $this->l('Will redirect to home if not found'),
                        'hint' => $this->l('Do not redirect a lot of 404 to homepage !'),
                        'name' => 'EVERSEO_NOT_FOUND',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('allowed forcing redirect product'),
                        'desc' => $this->l('Allow command for force redirect produit 404 to category parent'),
                        'name' => 'EVERSEO_FORCE_PRODUCT_REDIRECT',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Filter results by'),
                        'desc' => $this->l('Lets show oldest or newest value ?'),
                        'hint' => $this->l('Depends on your own management !'),
                        'name' => 'EVERSEO_ORDER_BY',
                        'options' => array(
                            'query' => $orderby,
                            'id' => 'id_orderby',
                            'name' => 'name',
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable custom 404 page'),
                        'desc' => $this->l('Override 404 page with Ever SEO'),
                        'hint' => $this->l('Add some custom content on 404 page !'),
                        'name' => 'EVERSEO_CUSTOM_404',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Use search bar on module 404 page'),
                        'desc' => $this->l('Will add a search form on your 404 page'),
                        'hint' => $this->l('Only if module\'s custom 404 page is used'),
                        'name' => 'EVERSEO_404_SEARCH',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('404 top content'),
                        'desc' => $this->l('Type 404 top content'),
                        'hint' => $this->l('Will appear on top of 404 page'),
                        'required' => false,
                        'name' => 'EVERSEO_404_TOP',
                        'lang' => true,
                        'autoload_rte' => true,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('404 bottom content'),
                        'desc' => $this->l('Type 404 bottom content'),
                        'hint' => $this->l('Will appear on top of 404 page'),
                        'required' => false,
                        'name' => 'EVERSEO_404_BOTTOM',
                        'lang' => true,
                        'autoload_rte' => true,
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        // Robots.txt rules
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Robots.txt'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Rewrite robots.txt'),
                        'desc' => $this->l('Will erase all native Prestashop rules'),
                        'hint' => $this->l('Set no only to add, instead of rewrite'),
                        'name' => 'EVERSEO_ROBOTS_TXT_REWRITE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'cols' => 36,
                        'rows' => 4,
                        'type' => 'textarea',
                        'label' => $this->l('Add here your own robots.txt rules'),
                        'desc' => $this->l('Dont forget to reset robots.txt on SEO & URL'),
                        'hint' => $this->l('Robots.txt custom rules'),
                        'name' => 'EVERSEO_ROBOTS_TXT',
                        'lang' => false,
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        // Internal Linking
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Internal linking'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-download"></i>',
                        'label' => $this->l('Search for specfic text (case sensitive)'),
                        'desc' => $this->l('Search for'),
                        'hint' => $this->l('Will search for this text on database'),
                        'name' => 'SEARCHED',
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-download"></i>',
                        'label' => $this->l('URL linked to'),
                        'desc' => $this->l('Add link to this URL'),
                        'hint' => $this->l('Will add link on searched text'),
                        'name' => 'LINKEDTO',
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'label' => $this->l('How many links per occurence found'),
                        'desc' => $this->l('How many links per content ?'),
                        'hint' => $this->l('How many links do you want to create ?'),
                        'name' => 'EVERSEO_LINKED_NBR',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Language'),
                        'desc' => $this->l('Specify language'),
                        'hint' => $this->l('Add internal linking only for thoses languages'),
                        'name' => 'EVERSEO_LANG',
                        'required' => true,
                        'options' => array(
                            'query' => Language::getLanguages(false),
                            'id' => 'id_lang',
                            'name' => 'name',
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('CMS'),
                        'desc' => $this->l('Add internal linking on CMS'),
                        'hint' => $this->l('Internal linking only on CMS'),
                        'name' => 'EVERSEO_CMS_LINKED',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Products description'),
                        'desc' => $this->l('Add internal linking on products description'),
                        'hint' => $this->l('Internal linking only on products description'),
                        'name' => 'EVERSEO_LONG_DESC_LINKED',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Products short description'),
                        'desc' => $this->l('Add internal linking on products short description'),
                        'hint' => $this->l('Internal linking only on products short description'),
                        'name' => 'EVERSEO_SHORT_DESC_LINKED',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Categories description'),
                        'desc' => $this->l('Add internal linking on categories descriptions ?'),
                        'hint' => $this->l('Internal linking only on categories short description'),
                        'name' => 'EVERSEO_CATEG_LINKED',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show manufacturers on product page'),
                        'desc' => $this->l('Will show manufacturer image'),
                        'hint' => $this->l('Will add image and link to manufacturer page'),
                        'name' => 'EVERSEO_MANUFACTURER_REASSURANCE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show suppliers on product page'),
                        'desc' => $this->l('Will show supplier image'),
                        'hint' => $this->l('Will add image and link to supplier page'),
                        'name' => 'EVERSEO_SUPPLIER_REASSURANCE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ),
                        ),
                    ),
                ),
                'buttons' => array(
                    'internalLinking' => array(
                        'name' => 'submitInternalLinking',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Replace and add internal linking'),
                    ),
                ),
            ),
        );

        //Product meta automation
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Product meta automation'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'free',
                        'label' =>  $this->l('Product meta shortcodes'),
                        'name' => 'product_shortcodes',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Product title automation'),
                        'desc' => $this->l('Don\'t forget to use shortcodes'),
                        'hint' => $this->l('Will rewrite all product SEO titles'),
                        'required' => false,
                        'name' => 'EVERSEO_PRODUCT_TITLE_AUTO',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Product meta_description automation'),
                        'desc' => $this->l('Don\'t forget to use shortcodes'),
                        'hint' => $this->l('Will rewrite all product SEO meta descriptions'),
                        'required' => false,
                        'name' => 'EVERSEO_PRODUCT_METADESC_AUTO',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Allowed languages for automation'),
                        'desc' =>  $this->l('Choose allowed languages for product meta automation'),
                        'hint' => $this->l('Please choose at least one language'),
                        'name' => 'EVERSEO_AUTO_PRODUCT_LANGS[]',
                        'class' => 'chosen',
                        'identifier' => 'name',
                        'multiple' => true,
                        'options' => array(
                            'query' => Language::getLanguages(false),
                            'id' => 'id_lang',
                            'name' => 'name',
                        ),
                    ),
                ),
                'buttons' => array(
                    'autoAlt' => array(
                        'name' => 'submitAutoProduct',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Replace all meta on products'),
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        //Product image alt automation
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Product image alt automation'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'free',
                        'label' =>  $this->l('Product image meta shortcodes'),
                        'name' => 'image_shortcodes',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Product image alt automation'),
                        'desc' =>  $this->l('Choose allowed languages for product meta automation'),
                        'hint' => $this->l('Please choose at least one language'),
                        'required' => false,
                        'name' => 'EVERSEO_IMAGE_ALT_AUTO',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Allowed languages for automation'),
                        'desc' =>  $this->l('Choose allowed languages for product meta automation'),
                        'hint' => $this->l('Please choose at least one language'),
                        'name' => 'EVERSEO_AUTO_IMAGE_LANGS[]',
                        'class' => 'chosen',
                        'identifier' => 'name',
                        'multiple' => true,
                        'options' => array(
                            'query' => Language::getLanguages(false),
                            'id' => 'id_lang',
                            'name' => 'name',
                        ),
                    ),
                ),
                'buttons' => array(
                    'autoAlt' => array(
                        'name' => 'submitAutoAlt',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Replace all alt on images'),
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        //Category meta automation
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Category meta automation'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'free',
                        'label' =>  $this->l('Category meta shortcodes'),
                        'name' => 'category_shortcodes'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Category title automation'),
                        'required' => false,
                        'name' => 'EVERSEO_CATEGORY_TITLE_AUTO',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Category meta_description automation'),
                        'required' => false,
                        'name' => 'EVERSEO_CATEGORY_METADESC_AUTO',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Allowed languages for automation'),
                        'desc' =>  $this->l('Choose allowed languages for product meta automation'),
                        'hint' => $this->l('Please choose at least one language'),
                        'name' => 'EVERSEO_AUTO_CATEGORY_LANGS[]',
                        'class' => 'chosen',
                        'identifier' => 'name',
                        'multiple' => true,
                        'options' => array(
                            'query' => Language::getLanguages(false),
                            'id' => 'id_lang',
                            'name' => 'name',
                        ),
                    ),
                ),
                'buttons' => array(
                    'autoCategory' => array(
                        'name' => 'submitAutoCategory',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Replace all metas on categories'),
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        //Manufacturer meta automation
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Manufacturer meta automation'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'free',
                        'label' =>  $this->l('Manufacturer meta shortcodes'),
                        'name' => 'manufacturer_shortcodes',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Manufacturer title automation'),
                        'required' => false,
                        'name' => 'EVERSEO_MANUFACTURER_TITLE_AUTO',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Manufacturer meta_description automation'),
                        'required' => false,
                        'name' => 'EVERSEO_MANUFACTURER_METADESC_AUTO',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Allowed languages for automation'),
                        'hint' =>  $this->l('Choose allowed languages for manufacturer meta automation'),
                        'name' => 'EVERSEO_AUTO_MANUFACTURER_LANGS[]',
                        'class' => 'chosen',
                        'identifier' => 'name',
                        'multiple' => true,
                        'options' => array(
                            'query' => Language::getLanguages(false),
                            'id' => 'id_lang',
                            'name' => 'name',
                        ),
                    ),
                ),
                'buttons' => array(
                    'autoManufacturer' => array(
                        'name' => 'submitAutoManufacturer',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Replace all metas on manufacturers'),
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        //Supplier meta automation
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Supplier meta automation'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'free',
                        'label' =>  $this->l('Supplier meta shortcodes'),
                        'name' => 'supplier_shortcodes'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Supplier title automation'),
                        'required' => false,
                        'name' => 'EVERSEO_SUPPLIER_TITLE_AUTO',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Supplier meta_description automation'),
                        'required' => false,
                        'name' => 'EVERSEO_SUPPLIER_METADESC_AUTO',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Allowed languages for automation'),
                        'hint' =>  $this->l('Choose allowed languages for supplier meta automation'),
                        'name' => 'EVERSEO_AUTO_SUPPLIER_LANGS[]',
                        'class' => 'chosen',
                        'identifier' => 'name',
                        'multiple' => true,
                        'options' => array(
                            'query' => Language::getLanguages(false),
                            'id' => 'id_lang',
                            'name' => 'name',
                        ),
                    ),
                ),
                'buttons' => array(
                    'autoSupplier' => array(
                        'name' => 'submitAutoSupplier',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Replace all metas on suppliers')
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        //Cms meta automation
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Cms meta automation'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'free',
                        'label' =>  $this->l('CMS meta shortcodes'),
                        'name' => 'cms_shortcodes'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Cms title automation'),
                        'required' => false,
                        'name' => 'EVERSEO_CMS_TITLE_AUTO',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Cms meta_description automation'),
                        'required' => false,
                        'name' => 'EVERSEO_CMS_METADESC_AUTO',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Allowed languages for automation'),
                        'hint' =>  $this->l('Choose allowed languages for CMS meta automation'),
                        'name' => 'EVERSEO_AUTO_CMS_LANGS[]',
                        'class' => 'chosen',
                        'identifier' => 'name',
                        'multiple' => true,
                        'options' => array(
                            'query' => Language::getLanguages(false),
                            'id' => 'id_lang',
                            'name' => 'name',
                        ),
                    ),
                ),
                'buttons' => array(
                    'autoCms' => array(
                        'name' => 'submitAutoCms',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Replace all metas on CMS')
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        //Page meta meta automation
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Page meta meta automation'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'free',
                        'label' => $this->l('Pagemetas meta shortcodes'),
                        'name' => 'pagemeta_shortcodes'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Page meta title automation'),
                        'required' => false,
                        'name' => 'EVERSEO_PAGEMETA_TITLE_AUTO',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Page meta meta_description automation'),
                        'required' => false,
                        'name' => 'EVERSEO_PAGEMETA_METADESC_AUTO',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Allowed languages for automation'),
                        'hint' =>  $this->l('Choose allowed languages for pages meta automation'),
                        'name' => 'EVERSEO_AUTO_PAGEMETA_LANGS[]',
                        'class' => 'chosen',
                        'identifier' => 'name',
                        'multiple' => true,
                        'options' => array(
                            'query' => Language::getLanguages(false),
                            'id' => 'id_lang',
                            'name' => 'name',
                        ),
                    ),
                ),
                'buttons' => array(
                    'autoPagemeta' => array(
                        'name' => 'submitAutoPagemeta',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Replace all metas on Page metas')
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );
        // Product content generator settings
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Product content generator'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'free',
                        'label' =>  $this->l('Product shortcodes'),
                        'name' => 'product_shortcodes'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Delete content before updating ?'),
                        'desc' => $this->l('Will delete content already set'),
                        'hint' => $this->l('Set "No" to add content after'),
                        'name' => 'EVERSEO_DELETE_PRODUCT_CONTENT',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'select',
                        'label' => 'Allowed languages for product generator',
                        'desc' => 'Choose allowed langs for product content generator',
                        'hint' => 'Only allowed langs will have generated content',
                        'name' => 'EVERSEO_PGENERATOR_LANGS[]',
                        'class' => 'chosen',
                        'identifier' => 'name',
                        'multiple' => true,
                        'required' => true,
                        'options' => array(
                            'query' => Language::getLanguages(false),
                            'id' => 'id_lang',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'EVERSEO_PGENERATOR_CATEGORIES[]',
                        'label' => $this->l('Category'),
                        'required' => false,
                        'hint' => 'Only selected categories will be allowed for content generation',
                        'class' => 'chosen',
                        'identifier' => 'name',
                        'multiple' => true,
                        'required' => true,
                        'options' => array(
                            'query' => $categories,
                            'id' => 'id_category',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Product description generator'),
                        'desc' => $this->l('Type product description using shortcodes'),
                        'hint' => $this->l('Will generate content for each product'),
                        'required' => false,
                        'name' => 'PRODUCT_DESC_GENERATE',
                        'lang' => true,
                        'autoload_rte' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Product short description generator'),
                        'desc' => $this->l('Type product short description using shortcodes'),
                        'hint' => $this->l('Will generate short description for each product'),
                        'required' => false,
                        'name' => 'PRODUCT_SHORT_DESC_GENERATE',
                        'lang' => true,
                        'autoload_rte' => true
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Activate product bottom generation ?'),
                        'desc' => $this->l('Will set content to bottom of each product'),
                        'hint' => $this->l('Set "yes" for add this in generation content'),
                        'name' => 'EVERSEO_BOTTOM_PRODUCT_CONTENT',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Product bottom content generator'),
                        'desc' => $this->l('Type product bottom content using shortcodes'),
                        'hint' => $this->l('Will generate bottom content for each product'),
                        'required' => false,
                        'name' => 'PRODUCT_BOTTOM_GENERATE',
                        'lang' => true,
                        'autoload_rte' => true
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
                'buttons' => array(
                    'generateProductsContent' => array(
                        'name' => 'submitGenerateProductsContent',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Generate products content')
                    ),
                ),
            ),
        );

        // Category content generator settings
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Category content generator'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'free',
                        'label' =>  $this->l('Category shortcodes'),
                        'name' => 'category_shortcodes'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Delete content before updating ?'),
                        'desc' => $this->l('Will delete content already set'),
                        'hint' => $this->l('Set "No" to add content after'),
                        'name' => 'EVERSEO_DELETE_CATEGORY_CONTENT',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Add content to bottom ?'),
                        'desc' => $this->l('Will set content to bottom of each category'),
                        'hint' => $this->l('Set "No" to add default content'),
                        'name' => 'EVERSEO_BOTTOM_CATEGORY_CONTENT',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'select',
                        'label' => 'Allowed languages for category generator',
                        'desc' => 'Choose allowed langs for category content generator',
                        'hint' => 'Only allowed langs will have generated content',
                        'name' => 'EVERSEO_CGENERATOR_LANGS[]',
                        'class' => 'chosen',
                        'identifier' => 'name',
                        'multiple' => true,
                        'required' => true,
                        'options' => array(
                            'query' => Language::getLanguages(false),
                            'id' => 'id_lang',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'EVERSEO_CGENERATOR_CATEGORIES[]',
                        'label' => $this->l('Category'),
                        'required' => false,
                        'hint' => 'Only selected categories will be allowed for content generation',
                        'class' => 'chosen',
                        'identifier' => 'name',
                        'multiple' => true,
                        'required' => true,
                        'options' => array(
                            'query' => $categories,
                            'id' => 'id_category',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Category description generator'),
                        'desc' => $this->l('Type category description using shortcodes'),
                        'hint' => $this->l('Will generate content for each category'),
                        'required' => false,
                        'name' => 'CATEGORY_DESC_GENERATE',
                        'lang' => true,
                        'autoload_rte' => true
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
                'buttons' => array(
                    'generateCategorysContent' => array(
                        'name' => 'submitGenerateCategoriesContent',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Generate categories content')
                    ),
                ),
            ),
        );

        // Manufacturer content generator settings
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Manufacturer content generator'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'free',
                        'label' =>  $this->l('Manufacturer shortcodes'),
                        'name' => 'manufacturer_shortcodes'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Delete content before updating ?'),
                        'desc' => $this->l('Will delete content already set'),
                        'hint' => $this->l('Set "No" to add content after'),
                        'name' => 'EVERSEO_DELETE_MANUFACTURER_CONTENT',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Add content to bottom ?'),
                        'desc' => $this->l('Will set content to bottom of each manufacturer'),
                        'hint' => $this->l('Set "No" to add default content'),
                        'name' => 'EVERSEO_BOTTOM_MANUFACTURER_CONTENT',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Manufacturer description generator'),
                        'desc' => $this->l('Type manufacturer description using shortcodes'),
                        'hint' => $this->l('Will generate content for each manufacturer'),
                        'required' => false,
                        'name' => 'MANUFACTURER_DESC_GENERATE',
                        'lang' => true,
                        'autoload_rte' => true
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
                'buttons' => array(
                    'generateManufacturersContent' => array(
                        'name' => 'submitGeneratemanufacturersContent',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Generate categories content')
                    ),
                ),
            ),
        );

        // Suppliers content generator settings
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Supplier content generator'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'free',
                        'label' =>  $this->l('Supplier shortcodes'),
                        'name' => 'supplier_shortcodes'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Delete content before updating ?'),
                        'desc' => $this->l('Will delete content already set'),
                        'hint' => $this->l('Set "No" to add content after'),
                        'name' => 'EVERSEO_DELETE_SUPPLIER_CONTENT',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Add content to bottom ?'),
                        'desc' => $this->l('Will set content to bottom of each supplier'),
                        'hint' => $this->l('Set "No" to add default content'),
                        'name' => 'EVERSEO_BOTTOM_SUPPLIER_CONTENT',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Supplier description generator'),
                        'desc' => $this->l('Type supplier description using shortcodes'),
                        'hint' => $this->l('Will generate content for each supplier'),
                        'required' => false,
                        'name' => 'SUPPLIER_DESC_GENERATE',
                        'lang' => true,
                        'autoload_rte' => true
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
                'buttons' => array(
                    'generateSuppliersContent' => array(
                        'name' => 'submitGenerateSuppliersContent',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Generate categories content')
                    ),
                ),
            ),
        );

        //Default Category sitemap settings
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Categories sitemaps'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'EVERSEO_SITEMAP_CATEGORY',
                        'is_bool' => true,
                        'desc' => $this->l('Generate a sitemap for categories'),
                        'hint' => $this->l('Will allow categories sitemaps generation'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Categories frequency on sitemap'),
                        'name' => 'EVERSEO_SITEMAP_CATEGORY_FREQUENCY',
                        'options' => array(
                            'query' => $frequency,
                            'id' => 'id_frequency',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Categories priority on sitemap'),
                        'name' => 'EVERSEO_SITEMAP_CATEGORY_PRIORITY',
                        'options' => array(
                            'query' => $sitemapPriority,
                            'id' => 'id_sitemap_priority',
                            'name' => 'name'
                        )
                    ),
                ),
                'buttons' => array(
                    'import' => array(
                        'name' => 'submitSitemapCategory',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Generate categories sitemap')
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        //Default Product sitemap settings
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Products sitemaps'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'EVERSEO_SITEMAP_PRODUCT',
                        'is_bool' => true,
                        'desc' => $this->l('Generate a sitemap for products'),
                        'hint' => $this->l('Will allow products sitemaps generation'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Products frequency on sitemap'),
                        'name' => 'EVERSEO_SITEMAP_PRODUCT_FREQUENCY',
                        'options' => array(
                            'query' => $frequency,
                            'id' => 'id_frequency',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Products priority on sitemap'),
                        'name' => 'EVERSEO_SITEMAP_PRODUCT_PRIORITY',
                        'options' => array(
                            'query' => $sitemapPriority,
                            'id' => 'id_sitemap_priority',
                            'name' => 'name'
                        )
                    ),
                ),
                'buttons' => array(
                    'import' => array(
                        'name' => 'submitSitemapProduct',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Generate Products sitemap')
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        //Default Images sitemap settings
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Images sitemaps'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'EVERSEO_SITEMAP_IMAGE',
                        'is_bool' => true,
                        'desc' => $this->l('Generate a sitemap for images'),
                        'hint' => $this->l('Will allow images sitemaps generation'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Images frequency on sitemap'),
                        'name' => 'EVERSEO_SITEMAP_IMAGE_FREQUENCY',
                        'options' => array(
                            'query' => $frequency,
                            'id' => 'id_frequency',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Images priority on sitemap'),
                        'name' => 'EVERSEO_SITEMAP_IMAGE_PRIORITY',
                        'options' => array(
                            'query' => $sitemapPriority,
                            'id' => 'id_sitemap_priority',
                            'name' => 'name'
                        )
                    ),
                ),
                'buttons' => array(
                    'import' => array(
                        'name' => 'submitSitemapImage',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Generate Products sitemap')
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        //Default Cms sitemap settings
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Cms sitemaps'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'EVERSEO_SITEMAP_CMS',
                        'is_bool' => true,
                        'desc' => $this->l('Generate a sitemap for cms'),
                        'hint' => $this->l('Will allow CMS sitemaps generation'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Cms frequency on sitemap'),
                        'name' => 'EVERSEO_SITEMAP_CMS_FREQUENCY',
                        'options' => array(
                            'query' => $frequency,
                            'id' => 'id_frequency',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Cms priority on sitemap'),
                        'name' => 'EVERSEO_SITEMAP_CMS_PRIORITY',
                        'options' => array(
                            'query' => $sitemapPriority,
                            'id' => 'id_sitemap_priority',
                            'name' => 'name'
                        )
                    ),
                ),
                'buttons' => array(
                    'import' => array(
                        'name' => 'submitSitemapCms',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Generate Cms sitemap')
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        //Default Page Meta sitemap settings
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Page Meta sitemaps'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'EVERSEO_SITEMAP_PAGE_META',
                        'is_bool' => true,
                        'desc' => $this->l('Generate a sitemap for page meta'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Page Meta frequency on sitemap'),
                        'name' => 'EVERSEO_SITEMAP_PAGE_META_FREQUENCY',
                        'options' => array(
                            'query' => $frequency,
                            'id' => 'id_frequency',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Page Meta priority on sitemap'),
                        'name' => 'EVERSEO_SITEMAP_PAGE_META_PRIORITY',
                        'options' => array(
                            'query' => $sitemapPriority,
                            'id' => 'id_sitemap_priority',
                            'name' => 'name'
                        )
                    ),
                ),
                'buttons' => array(
                    'import' => array(
                        'name' => 'submitSitemapPageMeta',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Generate Page Meta sitemap')
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        //Default Manufacturer sitemap settings
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Manufacturer sitemaps'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'EVERSEO_SITEMAP_MANUFACTURER',
                        'is_bool' => true,
                        'desc' => $this->l('Generate a sitemap for manufaturers'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Manufacturers frequency on sitemap'),
                        'name' => 'EVERSEO_SITEMAP_MANUFACTURER_FREQUENCY',
                        'options' => array(
                            'query' => $frequency,
                            'id' => 'id_frequency',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Manufacturers priority on sitemap'),
                        'name' => 'EVERSEO_SITEMAP_MANUFACTURER_PRIORITY',
                        'options' => array(
                            'query' => $sitemapPriority,
                            'id' => 'id_sitemap_priority',
                            'name' => 'name'
                        )
                    ),
                ),
                'buttons' => array(
                    'import' => array(
                        'name' => 'submitSitemapManufacturer',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Generate Manufacturers sitemap')
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        // Default Supplier sitemap settings
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Suppliers sitemaps'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'EVERSEO_SITEMAP_SUPPLIER',
                        'is_bool' => true,
                        'desc' => $this->l('Generate a sitemap for suppliers'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Suppliers frequency on sitemap'),
                        'name' => 'EVERSEO_SITEMAP_SUPPLIER_FREQUENCY',
                        'options' => array(
                            'query' => $frequency,
                            'id' => 'id_frequency',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Suppliers priority on sitemap'),
                        'name' => 'EVERSEO_SITEMAP_SUPPLIER_PRIORITY',
                        'options' => array(
                            'query' => $sitemapPriority,
                            'id' => 'id_sitemap_priority',
                            'name' => 'name'
                        )
                    ),
                ),
                'buttons' => array(
                    'import' => array(
                        'name' => 'submitSitemapSupplier',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Generate Suppliers sitemap')
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        //Default noindex parameters
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Default indexability'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Index categories'),
                        'name' => 'EVERSEO_INDEX_CATEGORY',
                        'is_bool' => true,
                        'desc' => $this->l('Default index categories'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Index products'),
                        'name' => 'EVERSEO_INDEX_PRODUCT',
                        'is_bool' => true,
                        'desc' => $this->l('Default index products'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Index CMS'),
                        'name' => 'EVERSEO_INDEX_CMS',
                        'is_bool' => true,
                        'desc' => $this->l('Default index CMS'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Index page metas'),
                        'name' => 'EVERSEO_INDEX_PAGE_META',
                        'is_bool' => true,
                        'desc' => $this->l('Default index pages meta'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Index manufacturers'),
                        'name' => 'EVERSEO_INDEX_MANUFACTURER',
                        'is_bool' => true,
                        'desc' => $this->l('Default index manufacturers'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Index suppliers'),
                        'name' => 'EVERSEO_INDEX_SUPPLIER',
                        'is_bool' => true,
                        'desc' => $this->l('Default index suppliers'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Index on pages with args'),
                        'name' => 'EVERSEO_INDEX_ARGS',
                        'is_bool' => true,
                        'desc' => $this->l('Will set index on pages with args'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        //Default follow parameters
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Default follow'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Follow categories'),
                        'name' => 'EVERSEO_FOLLOW_CATEGORY',
                        'is_bool' => true,
                        'desc' => $this->l('Default follow categories'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Follow products'),
                        'name' => 'EVERSEO_FOLLOW_PRODUCT',
                        'is_bool' => true,
                        'desc' => $this->l('Default follow products'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Follow CMS'),
                        'name' => 'EVERSEO_FOLLOW_CMS',
                        'is_bool' => true,
                        'desc' => $this->l('Default follow CMS'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Follow page metas'),
                        'name' => 'EVERSEO_FOLLOW_PAGE_META',
                        'is_bool' => true,
                        'desc' => $this->l('Default follow pages meta'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Follow manufacturers'),
                        'name' => 'EVERSEO_FOLLOW_MANUFACTURER',
                        'is_bool' => true,
                        'desc' => $this->l('Default follow manufacturers'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Follow suppliers'),
                        'name' => 'EVERSEO_FOLLOW_SUPPLIER',
                        'is_bool' => true,
                        'desc' => $this->l('Default follow suppliers'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Follow on pages with args'),
                        'desc' => $this->l('Will set follow on pages with args'),
                        'hint' => $this->l('Args can be pages parameters'),
                        'name' => 'EVERSEO_FOLLOW_ARGS',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        // Header tags
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Header custom tags'),
                'desc' => $this->l('Add your own head rules'),
                'hint' => $this->l('HTML or text only'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Header custom tags'),
                        'name' => 'EVERSEO_HEADER_TAGS',
                        'desc' => $this->l('Add here your own custom SEO tags, will be set before head ending tag'),
                        'cols' => 36,
                        'rows' => 4,
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        // Default author and publisher
        $form_fields[] = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Default author and publisher'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Use author and publisher meta'),
                        'desc' => $this->l('Will add publisher and author meta'),
                        'hint' => 'Will add author meta',
                        'name' => 'EVERSEO_USE_AUTHOR',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Please choose default author'),
                        'desc' => $this->l('Default author'),
                        'hint' => 'Will add author meta',
                        'name' => 'EVERSEO_AUTHOR',
                        'options' => array(
                            'query' => $employees,
                            'id' => 'id_employee',
                            'name' => 'firstname'
                        )
                    ),
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );

        // Store commander check
        $form_fields[] = array(
            'form' => array(
                'legend' => [
                    'title' => $this->l('Useful buttons :-)'),
                    'icon' => 'icon-cogs',
                ],
                'buttons' => [
                    'multishopSitemapIndex' => [
                        'name' => 'submitMultishopSitemapIndex',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Check multishop index and sitemaps'),
                    ],
                    'deleteDuplicate' => [
                        'name' => 'submitDeleteDuplicate',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Delete duplicate entries'),
                    ],
                    'updateSeoProducts' => [
                        'name' => 'submitUpdateSeoProducts',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Check for products updates'),
                    ],
                    'updateSeoCategories' => [
                        'name' => 'submitUpdateSeoCategories',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Check for categories updates'),
                    ],
                    'updateSeoManufacturers' => [
                        'name' => 'submitUpdateSeoManufacturers',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Check for manufacturers updates'),
                    ],
                    'updateSeoSuppliers' => [
                        'name' => 'submitUpdateSeoSuppliers',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Check for suppliers updates'),
                    ],
                    'updateSeoCms' => [
                        'name' => 'submitUpdateSeoCms',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Check for CMS updates'),
                    ],
                    'updateSeoImages' => [
                        'name' => 'submitUpdateSeoImages',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Check for images updates'),
                    ],
                    'updateSeoPageMetas' => [
                        'name' => 'submitUpdateSeoPageMetas',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Check for page metas updates'),
                    ],
                    'truncateStatsData' => [
                        'name' => 'submitTruncateStatsData',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-uninstall',
                        'title' => $this->l('Truncate Prestashop Stats Data'),
                    ],
                    'truncateSeo404' => [
                        'name' => 'submitTruncateSeo404',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-uninstall',
                        'title' => $this->l('Truncate Ever SEO registered 404'),
                    ],
                ],
            ),
        );

        // Lang noindex, nofollow, no sitemap
        $form_fields[] = array(
            'form' => array(
                'legend' => [
                    'title' => $this->l('Bulk per language'),
                    'icon' => 'icon-cogs',
                ],
                'input' => array([
                        'type' => 'select',
                        'label' => $this->l('Selected languages'),
                        'desc' => $this->l('Choose allowed langs for bulk actions'),
                        'hint' => $this->l('This will bulk actions on your whole shop'),
                        'name' => 'EVERSEO_BULK_LANGS[]',
                        'class' => 'chosen',
                        'identifier' => 'name',
                        'multiple' => true,
                        'required' => true,
                        'options' => [
                            'query' => Language::getLanguages(false),
                            'id' => 'id_lang',
                            'name' => 'name',
                        ],
                ],
                ),
                'buttons' => array(
                    'unused_langs' => [
                        'name' => 'submitDeleteUnusedObjects',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-delete',
                        'title' => $this->l('Delete unused langs on database'),
                    ],
                    'noindexLang' => [
                        'name' => 'submitNoindexLang',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Noindex all selected langs'),
                    ],
                    'nofollowLang' => array(
                        'name' => 'submitNofollowLang',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Nofollow all selected langs'),
                    ),
                    'nositemapLang' => [
                        'name' => 'submitNositemapLang',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('No sitemap all selected langs'),
                    ],
                    'indexLang' => [
                        'name' => 'submitIndexLang',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Index all selected langs'),
                    ],
                    'followLang' => [
                        'name' => 'submitFollowLang',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Follow all selected langs'),
                    ],
                    'sitemapLang' => [
                        'name' => 'submitSitemapLang',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Sitemap all selected langs'),
                    ],
                ),
            ),
        );
        // Security options
        $form_fields[] = array(
            'form' => array(
                'legend' => [
                    'title' => $this->l('Security options'),
                    'icon' => 'icon-cogs',
                ],
                'input' => array(
                    [
                        'type' => 'switch',
                        'label' => $this->l('Block right click and shortcuts'),
                        'desc' => $this->l('Will block right click and some shortcuts'),
                        'hint' => $this->l('Will secure your shop'),
                        'name' => 'EVERSEO_BLOCK_RIGHT_CLICK',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Prevent hotlinking using htaccess'),
                        'desc' => $this->l('You must regenerate htaccess file after this'),
                        'hint' => $this->l('Will prevent hotlinking on your shop'),
                        'name' => 'EVERSEO_HOTLINKING',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Prevent clickjacking'),
                        'desc' => $this->l('Will add rules on your htaccess file'),
                        'hint' => $this->l('Will secure your shop'),
                        'name' => 'EVERSEO_CLICKJACKING',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Noindex PDF files'),
                        'desc' => $this->l('Will add rule on your htaccess file to noindex PDF files'),
                        'hint' => $this->l('Will noindex your PDF files'),
                        'name' => 'EVERSEO_LOCK_PDF',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Noindex XLSX files'),
                        'desc' => $this->l('Will add rule on your htaccess file to noindex XLSX files'),
                        'hint' => $this->l('Will noindex your XLSX files'),
                        'name' => 'EVERSEO_LOCK_XLSX',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Noindex CSV files'),
                        'desc' => $this->l('Will add rule on your htaccess file to noindex CSV files'),
                        'hint' => $this->l('Will noindex your CSV files'),
                        'name' => 'EVERSEO_LOCK_CSV',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Noindex WORD files'),
                        'desc' => $this->l('Will add rule on your htaccess file to noindex WORD files'),
                        'hint' => $this->l('Will noindex your WORD files'),
                        'name' => 'EVERSEO_LOCK_WORD',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Google Captcha V3 Site Key'),
                        'desc' => $this->l('Will add Google Recaptcha V3'),
                        'hint' => $this->l('Will secure your shop'),
                        'name' => 'EVERPSCAPTCHA_SITE_KEY',
                        'required' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Google Captcha V3 Secret Key'),
                        'desc' => $this->l('Will add Google Recaptcha V3'),
                        'hint' => $this->l('Will secure your shop'),
                        'name' => 'EVERPSCAPTCHA_SECRET_KEY',
                        'required' => false,
                    ],
                ),
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ),
        );
        // Htacccess custom rules
        $form_fields[] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Custom htaccess rules'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Appended custom htaccess rules'),
                        'desc' => $this->l('Don\'t forget to regenerate htaccess file'),
                        'hint' => $this->l('Htaccess rules MUST be made by professionnals'),
                        'name' => 'EVERHTACCESS',
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Prepended custom htaccess rules'),
                        'desc' => $this->l('Don\'t forget to regenerate htaccess file'),
                        'hint' => $this->l('Htaccess rules MUST be made by professionnals'),
                        'name' => 'EVERHTACCESS_PREPEND',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Add Ever SEO 404 redirections to htaccess file'),
                        'desc' => $this->l('Will add your registered redirects to on htaccess file beginning'),
                        'hint' => $this->l('Else PHP will only do redirections'),
                        'name' => 'EVERHTACCESS_404',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
        // Google translate API
        $form_fields[] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Google Translate widget'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Show Google Translate on top'),
                        'desc' => $this->l('Will show Google Translate widget on top'),
                        'hint' => $this->l('Set "No" to hide Google Translate widget'),
                        'name' => 'EVERSEO_GTOP',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Show Google Translate on columns'),
                        'desc' => $this->l('Will show Google Translate widget on columns'),
                        'hint' => $this->l('Set "No" to hide Google Translate widget'),
                        'name' => 'EVERSEO_GCOLUMN',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
        return $form_fields;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $everseo_404_top = [];
        $everseo_404_bottom = [];
        $product_shortdesc = [];
        $product_desc = [];
        $product_bttm = [];
        $category_desc = [];
        $manufacturer_desc = [];
        $supplier_desc = [];
        // Title and meta generation
        $pagemeta_metadesc = [];
        $pagemeta_title = [];
        $cms_metadesc = [];
        $cms_title = [];
        $supplier_metadesc = [];
        $supplier_title = [];
        $m_metadesc = [];
        $m_title = [];
        $category_metadesc = [];
        $category_title = [];
        $image_alt = [];
        $product_metadesc = [];
        $product_title = [];
        foreach (Language::getLanguages(false) as $lang) {
            $everseo_404_top[$lang['id_lang']] = (Tools::getValue('EVERSEO_404_TOP_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_404_TOP_' . $lang['id_lang']) : '';

            $everseo_404_bottom[$lang['id_lang']] = (Tools::getValue('EVERSEO_404_BOTTOM_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_404_BOTTOM_' . $lang['id_lang']) : '';

            $product_shortdesc[$lang['id_lang']] = (Tools::getValue('PRODUCT_SHORT_DESC_GENERATE_' . $lang['id_lang']))
            ? Tools::getValue('PRODUCT_SHORT_DESC_GENERATE_' . $lang['id_lang']) : '';

            $product_desc[$lang['id_lang']] = (Tools::getValue('PRODUCT_DESC_GENERATE_' . $lang['id_lang']))
            ? Tools::getValue('PRODUCT_DESC_GENERATE_' . $lang['id_lang']) : '';

            $product_bttm[$lang['id_lang']] = (Tools::getValue('PRODUCT_BOTTOM_GENERATE_' . $lang['id_lang']))
            ? Tools::getValue('PRODUCT_BOTTOM_GENERATE_' . $lang['id_lang']) : '';

            $category_desc[$lang['id_lang']] = (Tools::getValue('CATEGORY_DESC_GENERATE_' . $lang['id_lang']))
            ? Tools::getValue('CATEGORY_DESC_GENERATE_' . $lang['id_lang']) : '';

            $manufacturer_desc[$lang['id_lang']] = (Tools::getValue('MANUFACTURER_DESC_GENERATE_' . $lang['id_lang']))
            ? Tools::getValue('MANUFACTURER_DESC_GENERATE_' . $lang['id_lang']) : '';

            $supplier_desc[$lang['id_lang']] = (Tools::getValue('SUPPLIER_DESC_GENERATE_' . $lang['id_lang']))
            ? Tools::getValue('SUPPLIER_DESC_GENERATE_' . $lang['id_lang']) : '';

            $pagemeta_metadesc[$lang['id_lang']] = (Tools::getValue('EVERSEO_PAGEMETA_METADESC_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_PAGEMETA_METADESC_AUTO_' . $lang['id_lang']) : '';

            $pagemeta_title[$lang['id_lang']] = (Tools::getValue('EVERSEO_PAGEMETA_TITLE_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_PAGEMETA_TITLE_AUTO_' . $lang['id_lang']) : '';

            $cms_metadesc[$lang['id_lang']] = (Tools::getValue('EVERSEO_CMS_METADESC_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_CMS_METADESC_AUTO_' . $lang['id_lang']) : '';

            $cms_title[$lang['id_lang']] = (Tools::getValue('EVERSEO_CMS_TITLE_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_CMS_TITLE_AUTO_' . $lang['id_lang']) : '';

            $supplier_metadesc[$lang['id_lang']] = (Tools::getValue('EVERSEO_SUPPLIER_METADESC_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_SUPPLIER_METADESC_AUTO_' . $lang['id_lang']) : '';

            $supplier_title[$lang['id_lang']] = (Tools::getValue('EVERSEO_SUPPLIER_TITLE_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_SUPPLIER_TITLE_AUTO_' . $lang['id_lang']) : '';

            $m_metadesc[$lang['id_lang']] = (Tools::getValue('EVERSEO_MANUFACTURER_METADESC_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_MANUFACTURER_METADESC_AUTO_' . $lang['id_lang']) : '';

            $m_title[$lang['id_lang']] = (Tools::getValue('EVERSEO_MANUFACTURER_TITLE_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_MANUFACTURER_TITLE_AUTO_' . $lang['id_lang']) : '';

            $category_metadesc[$lang['id_lang']] = (Tools::getValue('EVERSEO_CATEGORY_METADESC_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_CATEGORY_METADESC_AUTO_' . $lang['id_lang']) : '';

            $category_title[$lang['id_lang']] = (Tools::getValue('EVERSEO_CATEGORY_TITLE_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_CATEGORY_TITLE_AUTO_' . $lang['id_lang']) : '';

            $image_alt[$lang['id_lang']] = (Tools::getValue('EVERSEO_IMAGE_ALT_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_IMAGE_ALT_AUTO_' . $lang['id_lang']) : '';

            $product_metadesc[$lang['id_lang']] = (Tools::getValue('EVERSEO_PRODUCT_METADESC_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_PRODUCT_METADESC_AUTO_' . $lang['id_lang']) : '';

            $product_title[$lang['id_lang']] = (Tools::getValue('EVERSEO_PRODUCT_TITLE_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_PRODUCT_TITLE_AUTO_' . $lang['id_lang']) : '';
        }
        return [
            'product_shortcodes' => '<p class="form-control-static">Products shortcodes</p>
            <p><code>[product_title]</code> for product name</p>
            <p><code>[product_combinations]</code> for product combination names</p>
            <p><code>[product_features]</code> for product features and values names</p>
            <p><code>[product_reference]</code> for product reference</p>
            <p><code>[product_short_desc]</code> for product short description</p>
            <p><code>[product_desc]</code> for product description</p>
            <p><code>[product_manufacturer]</code> for product manufacturer</p>
            <p><code>[product_supplier]</code> for product supplier</p>
            <p><code>[shop_name]</code> for shop name</p>
            <p><code>[product_default_category]</code> for product default category</p>
            <p><code>[category_desc]</code> for category description</p>
            <p><code>[category_meta_desc]</code> for category meta description</p>',
            'image_shortcodes' => '<p class="form-control-static">Images shortcodes<p>
            <p><code>[product_title]</code> for product name</p>
            <p><code>[product_reference]</code> for product reference</p>
            <p><code>[product_short_desc]</code> for product short description</p>
            <p><code>[product_desc]</code> for product description</p>
            <p><code>[product_manufacturer]</code> for product manufacturer</p>
            <p><code>[product_supplier]</code> for product supplier</p>
            <p><code>[shop_name]</code> for shop name</p>
            <p><code>[product_default_category]</code> for product default category</p>',
            'category_shortcodes' => '<p class="form-control-static">Categories shortcodes<p>
            <p><code>[category_title]</code> for category name</p>
            <p><code>[category_desc]</code> for category description</p>
            <p><code>[children]</code> for category childs name</p>
            <p><code>[parent]</code> for category parent name</p>
            <p><code>[shop_name]</code> for shop name</p>',
            'manufacturer_shortcodes' => '<p class="form-control-static">Manufacturers shortcodes<p>
            <p><code>[manufacturer_title]</code> for manufacturer name</p>
            <p><code>[manufacturer_desc]</code> for manufacturer description</p>
            <p><code>[shop_name]</code> for shop name</p>',
            'supplier_shortcodes' => '<p class="form-control-static">Suppliers shortcodes<p>
            <p><code>[supplier_title]</code> for supplier name</p>
            <p><code>[supplier_desc]</code> for supplier description</p>
            <p><code>[shop_name]</code> for shop name</p>',
            'cms_shortcodes' => '<p class="form-control-static">CMS shortcodes<p>
            <p><code>[cms_title]</code> for cms name</p>
            <p><code>[cms_desc]</code> for cms description</p>
            <p><code>[shop_name]</code> for shop name</p>',
            'cmscategories_shortcodes' => '<p class="form-control-static">CMS categories shortcodes<p>
            <p><code>[cmscategories_title]</code> for cms categories name</p>
            <p><code>[cmscategories_desc]</code> for cms categories description</p>
            <p><code>[shop_name]</code> for shop name</p>',
            'pagemeta_shortcodes' => '<p class="form-control-static">Pages shortcodes<p>
            <p><code>[pagemeta_title]</code> for pagemeta name</p>
            <p><code>[pagemeta_desc]</code> for pagemeta description</p>
            <p><code>[shop_name]</code> for shop name</p>',
            //Internal linking
            'EVERSEO_LANG' => Configuration::get(
                'EVERSEO_LANG'
            ),
            'EVERSEO_LINKED_NBR' => Configuration::get(
                'EVERSEO_LINKED_NBR'
            ),
            'EVERSEO_CMS_LINKED' => Configuration::get(
                'EVERSEO_CMS_LINKED'
            ),
            'EVERSEO_LONG_DESC_LINKED' => Configuration::get(
                'EVERSEO_LONG_DESC_LINKED'
            ),
            'EVERSEO_SHORT_DESC_LINKED' => Configuration::get(
                'EVERSEO_SHORT_DESC_LINKED'
            ),
            'EVERSEO_CATEG_LINKED' => Configuration::get(
                'EVERSEO_CATEG_LINKED'
            ),
            'EVERSEO_MANUFACTURER_REASSURANCE' => Configuration::get(
                'EVERSEO_MANUFACTURER_REASSURANCE'
            ),
            'EVERSEO_SUPPLIER_REASSURANCE' => Configuration::get(
                'EVERSEO_SUPPLIER_REASSURANCE'
            ),
            'SEARCHED' => '',
            'LINKEDTO' => '',
            //404 redirects
            'EVERSEO_REWRITE' => Configuration::get(
                'EVERSEO_REWRITE'
            ),
            'EVERSEO_PRODUCT' => Configuration::get(
                'EVERSEO_PRODUCT'
            ),
            'EVERSEO_CATEGORY' => Configuration::get(
                'EVERSEO_CATEGORY'
            ),
            'EVERSEO_TAGS' => Configuration::get(
                'EVERSEO_TAGS'
            ),
            'EVERSEO_PRIORITY' => Configuration::get(
                'EVERSEO_PRIORITY'
            ),
            'EVERSEO_ORDER_BY' => Configuration::get(
                'EVERSEO_ORDER_BY'
            ),
            'EVERSEO_CUSTOM_404' => Configuration::get(
                'EVERSEO_CUSTOM_404'
            ),
            'EVERSEO_404_SEARCH' => Configuration::get(
                'EVERSEO_404_SEARCH'
            ),
            'EVERSEO_REDIRECT' => Configuration::get(
                'EVERSEO_REDIRECT'
            ),
            'EVERSEO_NOT_FOUND' => Configuration::get(
                'EVERSEO_NOT_FOUND'
            ),
            'EVERSEO_FORCE_PRODUCT_REDIRECT' => Configuration::get(
                'EVERSEO_FORCE_PRODUCT_REDIRECT'
            ),
            'EVERSEO_QUALITY_LEVEL' => Configuration::get(
                'EVERSEO_QUALITY_LEVEL'
            ),
            //Social networks & Google tags
            'EVERSEO_RSNIPPETS' => Configuration::get(
                'EVERSEO_RSNIPPETS'
            ),
            'EVERSEO_KNOWLEDGE' => Configuration::get(
                'EVERSEO_KNOWLEDGE'
            ),
            //Social networks & Google tags
            'EVERSEO_SITEMAP_QTY_ELEMENTS' => Configuration::get(
                'EVERSEO_SITEMAP_QTY_ELEMENTS'
            ),
            'EVERSEO_AUTO_PAGEMETA_LANGS[]' => Tools::getValue(
                'EVERSEO_AUTO_PAGEMETA_LANGS',
                json_decode(
                    Configuration::get(
                        'EVERSEO_AUTO_PAGEMETA_LANGS'
                    )
                )
            ),
            'EVERSEO_AUTO_CMS_LANGS[]' => Tools::getValue(
                'EVERSEO_AUTO_CMS_LANGS',
                json_decode(
                    Configuration::get(
                        'EVERSEO_AUTO_CMS_LANGS'
                    )
                )
            ),
            'EVERSEO_AUTO_SUPPLIER_LANGS[]' => Tools::getValue(
                'EVERSEO_AUTO_SUPPLIER_LANGS',
                json_decode(
                    Configuration::get(
                        'EVERSEO_AUTO_SUPPLIER_LANGS'
                    )
                )
            ),
            'EVERSEO_AUTO_MANUFACTURER_LANGS[]' => Tools::getValue(
                'EVERSEO_AUTO_MANUFACTURER_LANGS',
                json_decode(
                    Configuration::get(
                        'EVERSEO_AUTO_MANUFACTURER_LANGS'
                    )
                )
            ),
            'EVERSEO_AUTO_CATEGORY_LANGS[]' => Tools::getValue(
                'EVERSEO_AUTO_CATEGORY_LANGS',
                json_decode(
                    Configuration::get(
                        'EVERSEO_AUTO_CATEGORY_LANGS'
                    )
                )
            ),
            'EVERSEO_AUTO_IMAGE_LANGS[]' => Tools::getValue(
                'EVERSEO_AUTO_IMAGE_LANGS',
                json_decode(
                    Configuration::get(
                        'EVERSEO_AUTO_IMAGE_LANGS'
                    )
                )
            ),
            'EVERSEO_AUTO_PRODUCT_LANGS[]' => Tools::getValue(
                'EVERSEO_AUTO_PRODUCT_LANGS',
                json_decode(
                    Configuration::get(
                        'EVERSEO_AUTO_PRODUCT_LANGS'
                    )
                )
            ),
            'EVERSEO_REWRITE_LINKS' => Configuration::get('EVERSEO_REWRITE_LINKS'),
            'EVER_LOG_CMD' => Configuration::get('EVER_LOG_CMD'),
            'EVER_SEARCH_CATEGORIES' => Configuration::get('EVER_SEARCH_CATEGORIES'),
            'EVER_SEARCH_MANUFACTURERS' => Configuration::get('EVER_SEARCH_MANUFACTURERS'),
            'EVER_SEARCH_SUPPLIERS' => Configuration::get('EVER_SEARCH_SUPPLIERS'),
            'EVER_SEARCH_PRODUCTS' => Configuration::get('EVER_SEARCH_PRODUCTS'),
            'EVERSEO_AUTO_PAGEMETA_LANGS[]' => Tools::getValue(
                'EVERSEO_AUTO_PAGEMETA_LANGS',
                json_decode(
                    Configuration::get(
                        'EVERSEO_AUTO_PAGEMETA_LANGS'
                    )
                )
            ),
            'EVERSEO_SITEMAP_LANGS[]' => Tools::getValue(
                'EVERSEO_SITEMAP_LANGS',
                json_decode(
                    Configuration::get(
                        'EVERSEO_SITEMAP_LANGS'
                    )
                )
            ),
            'EVERSEO_CANONICAL' => Configuration::get(
                'EVERSEO_CANONICAL'
            ),
            'EVERSEO_HREF_LANG' => Configuration::get(
                'EVERSEO_HREF_LANG'
            ),
            'EVERSEO_USE_TWITTER' => Configuration::get(
                'EVERSEO_USE_TWITTER'
            ),
            'EVERSEO_TWITTER_NAME' => Configuration::get(
                'EVERSEO_TWITTER_NAME'
            ),
            'EVERSEO_USE_OPENGRAPH' => Configuration::get(
                'EVERSEO_USE_OPENGRAPH'
            ),
            'EVERSEO_THEME_COLOR' => Configuration::get(
                'EVERSEO_THEME_COLOR'
            ),
            'EVERSEO_WEBP' => Configuration::get(
                'EVERSEO_WEBP'
            ),
            'EVERSEO_BOTTOM_SCRIPTS' => Configuration::get(
                'EVERSEO_BOTTOM_SCRIPTS'
            ),
            'EVERSEO_DEFER' => Configuration::get(
                'EVERSEO_DEFER'
            ),
            'EVERSEO_ADD_ALT' => Configuration::get(
                'EVERSEO_ADD_ALT'
            ),
            'EVERSEO_ADD_TITLE' => Configuration::get(
                'EVERSEO_ADD_TITLE'
            ),
            'EVERSEO_EXTERNAL_NOFOLLOW' => Configuration::get(
                'EVERSEO_EXTERNAL_NOFOLLOW'
            ),
            'EVERSEO_COMPRESS_HTML' => Configuration::get(
                'EVERSEO_COMPRESS_HTML'
            ),
            'EVERSEO_MAINTENANCE' => Configuration::get(
                'EVERSEO_MAINTENANCE'
            ),
            'EVERSEO_MAINTENANCE_URL' => Configuration::get(
                'EVERSEO_MAINTENANCE_URL'
            ),
            'EVERSEO_INDEXNOW_LIMIT' => Configuration::get(
                'EVERSEO_INDEXNOW_LIMIT'
            ),
            'EVERSEO_ANALYTICS' => Configuration::get(
                'EVERSEO_ANALYTICS'
            ),
            'EVERSEO_SEARCHCONSOLE' => Configuration::get(
                'EVERSEO_SEARCHCONSOLE'
            ),
            'EVERSEO_GTAG' => Configuration::get(
                'EVERSEO_GTAG'
            ),
            'EVERSEO_FBPIXEL' => Configuration::get(
                'EVERSEO_FBPIXEL'
            ),
            'EVERSEO_ADWORDS' => Configuration::get(
                'EVERSEO_ADWORDS'
            ),
            'EVERSEO_ADWORDS_CART_LABEL' => Configuration::get(
                'EVERSEO_ADWORDS_CART_LABEL'
            ),
            'EVERSEO_ADWORDS_SENDTO' => Configuration::get(
                'EVERSEO_ADWORDS_SENDTO'
            ),
            'EVERSEO_ADWORDS_CONTACT' => Configuration::get(
                'EVERSEO_ADWORDS_CONTACT'
            ),
            'EVERSEO_ADWORDS_OPART' => Configuration::get(
                'EVERSEO_ADWORDS_OPART'
            ),
            //Noindex
            'EVERSEO_INDEX_CATEGORY' => Configuration::get(
                'EVERSEO_INDEX_CATEGORY'
            ),
            'EVERSEO_INDEX_PRODUCT' => Configuration::get(
                'EVERSEO_INDEX_PRODUCT'
            ),
            'EVERSEO_INDEX_CMS' => Configuration::get(
                'EVERSEO_INDEX_CMS'
            ),
            'EVERSEO_INDEX_PAGE_META' => Configuration::get(
                'EVERSEO_INDEX_PAGE_META'
            ),
            'EVERSEO_INDEX_MANUFACTURER' => Configuration::get(
                'EVERSEO_INDEX_MANUFACTURER'
            ),
            'EVERSEO_INDEX_SUPPLIER' => Configuration::get(
                'EVERSEO_INDEX_SUPPLIER'
            ),
            'EVERSEO_INDEX_ARGS' => Configuration::get(
                'EVERSEO_INDEX_ARGS'
            ),
            //Follow
            'EVERSEO_FOLLOW_CATEGORY' => Configuration::get(
                'EVERSEO_FOLLOW_CATEGORY'
            ),
            'EVERSEO_FOLLOW_PRODUCT' => Configuration::get(
                'EVERSEO_FOLLOW_PRODUCT'
            ),
            'EVERSEO_FOLLOW_CMS' => Configuration::get(
                'EVERSEO_FOLLOW_CMS'
            ),
            'EVERSEO_FOLLOW_PAGE_META' => Configuration::get(
                'EVERSEO_FOLLOW_PAGE_META'
            ),
            'EVERSEO_FOLLOW_MANUFACTURER' => Configuration::get(
                'EVERSEO_FOLLOW_MANUFACTURER'
            ),
            'EVERSEO_FOLLOW_SUPPLIER' => Configuration::get(
                'EVERSEO_FOLLOW_SUPPLIER'
            ),
            'EVERSEO_FOLLOW_ARGS' => Configuration::get(
                'EVERSEO_FOLLOW_ARGS'
            ),

            //Sitemap
            'EVERSEO_SITEMAP_PRODUCT' => Configuration::get(
                'EVERSEO_SITEMAP_PRODUCT'
            ),
            'EVERSEO_SITEMAP_IMAGE' => Configuration::get(
                'EVERSEO_SITEMAP_IMAGE'
            ),
            'EVERSEO_SITEMAP_CATEGORY' => Configuration::get(
                'EVERSEO_SITEMAP_CATEGORY'
            ),
            'EVERSEO_SITEMAP_CMS' => Configuration::get(
                'EVERSEO_SITEMAP_CMS'
            ),
            'EVERSEO_SITEMAP_PAGE_META' => Configuration::get(
                'EVERSEO_SITEMAP_PAGE_META'
            ),
            'EVERSEO_SITEMAP_MANUFACTURER' => Configuration::get(
                'EVERSEO_SITEMAP_MANUFACTURER'
            ),
            'EVERSEO_SITEMAP_SUPPLIER' => Configuration::get(
                'EVERSEO_SITEMAP_SUPPLIER'
            ),

            //Sitemap frequencies
            'EVERSEO_SITEMAP_PRODUCT_FREQUENCY' => Configuration::get(
                'EVERSEO_SITEMAP_PRODUCT_FREQUENCY'
            ),
            'EVERSEO_SITEMAP_IMAGE_FREQUENCY' => Configuration::get(
                'EVERSEO_SITEMAP_IMAGE_FREQUENCY'
            ),
            'EVERSEO_SITEMAP_CATEGORY_FREQUENCY' => Configuration::get(
                'EVERSEO_SITEMAP_CATEGORY_FREQUENCY'
            ),
            'EVERSEO_SITEMAP_CMS_FREQUENCY' => Configuration::get(
                'EVERSEO_SITEMAP_CMS_FREQUENCY'
            ),
            'EVERSEO_SITEMAP_MANUFACTURER_FREQUENCY' => Configuration::get(
                'EVERSEO_SITEMAP_MANUFACTURER_FREQUENCY'
            ),
            'EVERSEO_SITEMAP_SUPPLIER_FREQUENCY' => Configuration::get(
                'EVERSEO_SITEMAP_SUPPLIER_FREQUENCY'
            ),
            'EVERSEO_SITEMAP_PAGE_META_FREQUENCY' => Configuration::get(
                'EVERSEO_SITEMAP_PAGE_META_FREQUENCY'
            ),
            //Sitemap priorities
            'EVERSEO_SITEMAP_PRODUCT_PRIORITY' => Configuration::get(
                'EVERSEO_SITEMAP_PRODUCT_PRIORITY'
            ),
            'EVERSEO_SITEMAP_IMAGE_PRIORITY' => Configuration::get(
                'EVERSEO_SITEMAP_IMAGE_PRIORITY'
            ),
            'EVERSEO_SITEMAP_CATEGORY_PRIORITY' => Configuration::get(
                'EVERSEO_SITEMAP_CATEGORY_PRIORITY'
            ),
            'EVERSEO_SITEMAP_CMS_PRIORITY' => Configuration::get(
                'EVERSEO_SITEMAP_CMS_PRIORITY'
            ),
            'EVERSEO_SITEMAP_MANUFACTURER_PRIORITY' => Configuration::get(
                'EVERSEO_SITEMAP_MANUFACTURER_PRIORITY'
            ),
            'EVERSEO_SITEMAP_SUPPLIER_PRIORITY' => Configuration::get(
                'EVERSEO_SITEMAP_SUPPLIER_PRIORITY'
            ),
            'EVERSEO_SITEMAP_PAGE_META_PRIORITY' => Configuration::get(
                'EVERSEO_SITEMAP_PAGE_META_PRIORITY'
            ),
            //Keywords strategy
            'EVERSEO_HEADER_TAGS' => Configuration::get(
                'EVERSEO_HEADER_TAGS'
            ),
            // Bottom content
            'EVERSEO_BOTTOM_MANUFACTURER_CONTENT' => Configuration::get(
                'EVERSEO_BOTTOM_MANUFACTURER_CONTENT'
            ),
            'EVERSEO_BOTTOM_CATEGORY_CONTENT' => Configuration::get(
                'EVERSEO_BOTTOM_CATEGORY_CONTENT'
            ),
            'EVERSEO_BOTTOM_SUPPLIER_CONTENT' => Configuration::get(
                'EVERSEO_BOTTOM_SUPPLIER_CONTENT'
            ),
            // Delete elements
            'EVERSEO_DELETE_CATEGORY' => Configuration::get(
                'EVERSEO_DELETE_CATEGORY'
            ),
            'EVERSEO_DELETE_PRODUCT' => Configuration::get(
                'EVERSEO_DELETE_PRODUCT'
            ),
            'EVERSEO_DELETE_CMS' => Configuration::get(
                'EVERSEO_DELETE_CMS'
            ),
            'EVERSEO_DELETE_PAGE_META' => Configuration::get(
                'EVERSEO_DELETE_PAGE_META'
            ),
            'EVERSEO_DELETE_MANUFACTURER' => Configuration::get(
                'EVERSEO_DELETE_MANUFACTURER'
            ),
            'EVERSEO_DELETE_SUPPLIER' => Configuration::get(
                'EVERSEO_DELETE_SUPPLIER'
            ),
            'EVERSEO_DELETE_INFO' => Configuration::get(
                'EVERSEO_DELETE_INFO'
            ),
            'EVERSEO_DELETE_GROUP' => Configuration::get(
                'EVERSEO_DELETE_GROUP'
            ),
            'EVERSEO_DELETE_GENDER' => Configuration::get(
                'EVERSEO_DELETE_GENDER'
            ),
            'EVERSEO_DELETE_FEATURE' => Configuration::get(
                'EVERSEO_DELETE_FEATURE'
            ),
            'EVERSEO_DELETE_FEATURE_VALUE' => Configuration::get(
                'EVERSEO_DELETE_FEATURE_VALUE'
            ),
            'EVERSEO_DELETE_CUST_FIELD' => Configuration::get(
                'EVERSEO_DELETE_CUST_FIELD'
            ),
            'EVERSEO_DELETE_CONTACT' => Configuration::get(
                'EVERSEO_DELETE_CONTACT'
            ),
            'EVERSEO_DELETE_COUNTRY' => Configuration::get(
                'EVERSEO_DELETE_COUNTRY'
            ),
            'EVERSEO_DELETE_CART_RULE' => Configuration::get(
                'EVERSEO_DELETE_CART_RULE'
            ),
            'EVERSEO_DELETE_CARRIER' => Configuration::get(
                'EVERSEO_DELETE_CARRIER'
            ),
            'EVERSEO_DELETE_ATTACHMENT' => Configuration::get(
                'EVERSEO_DELETE_ATTACHMENT'
            ),
            'EVERSEO_DELETE_ATTRIBUTE' => Configuration::get(
                'EVERSEO_DELETE_ATTRIBUTE'
            ),
            'EVERSEO_DELETE_ATTRIBUTE_GROUP' => Configuration::get(
                'EVERSEO_DELETE_ATTRIBUTE_GROUP'
            ),
            'EVERSEO_404_TOP' => static::getConfigInMultipleLangs('EVERSEO_404_TOP'),
            'EVERSEO_404_BOTTOM' => static::getConfigInMultipleLangs('EVERSEO_404_BOTTOM'),
            // Content generator
            'CATEGORY_DESC_GENERATE' => static::getConfigInMultipleLangs('CATEGORY_DESC_GENERATE'),
            // Pagemeta meta desc
            'EVERSEO_PAGEMETA_METADESC_AUTO' => static::getConfigInMultipleLangs(
                'EVERSEO_PAGEMETA_METADESC_AUTO'
            ),
            // Pagemeta title
            'EVERSEO_PAGEMETA_TITLE_AUTO' => static::getConfigInMultipleLangs(
                'EVERSEO_PAGEMETA_TITLE_AUTO'
            ),
            // CMS meta desc
            'EVERSEO_CMS_METADESC_AUTO' => static::getConfigInMultipleLangs(
                'EVERSEO_CMS_METADESC_AUTO'
            ),
            // CMS title
            'EVERSEO_CMS_TITLE_AUTO' => static::getConfigInMultipleLangs(
                'EVERSEO_CMS_TITLE_AUTO'
            ),
            // Supplier meta desc
            'EVERSEO_SUPPLIER_METADESC_AUTO' => static::getConfigInMultipleLangs(
                'EVERSEO_SUPPLIER_METADESC_AUTO'
            ),
            // Supplier title
            'EVERSEO_SUPPLIER_TITLE_AUTO' => static::getConfigInMultipleLangs(
                'EVERSEO_SUPPLIER_TITLE_AUTO'
            ),
            // Manufacturer meta desc
            'EVERSEO_MANUFACTURER_METADESC_AUTO' => static::getConfigInMultipleLangs(
                'EVERSEO_MANUFACTURER_METADESC_AUTO'
            ),
            // Manufacturer title
            'EVERSEO_MANUFACTURER_TITLE_AUTO' => static::getConfigInMultipleLangs(
                'EVERSEO_MANUFACTURER_TITLE_AUTO'
            ),
            // Category meta desc
            'EVERSEO_CATEGORY_METADESC_AUTO' => static::getConfigInMultipleLangs(
                'EVERSEO_CATEGORY_METADESC_AUTO'
            ),
            // Category title
            'EVERSEO_CATEGORY_TITLE_AUTO' => static::getConfigInMultipleLangs(
                'EVERSEO_CATEGORY_TITLE_AUTO'
            ),
            // Image alt
            'EVERSEO_IMAGE_ALT_AUTO' => static::getConfigInMultipleLangs(
                'EVERSEO_IMAGE_ALT_AUTO'
            ),
            // Product meta desc
            'EVERSEO_PRODUCT_METADESC_AUTO' => static::getConfigInMultipleLangs(
                'EVERSEO_PRODUCT_METADESC_AUTO'
            ),
            // Product title
            'EVERSEO_PRODUCT_TITLE_AUTO' => static::getConfigInMultipleLangs(
                'EVERSEO_PRODUCT_TITLE_AUTO'
            ),
            // Generator langs
            'EVERSEO_CGENERATOR_LANGS[]' => Tools::getValue(
                'EVERSEO_CGENERATOR_LANGS',
                json_decode(
                    Configuration::get(
                        'EVERSEO_CGENERATOR_LANGS'
                    )
                )
            ),
            'EVERSEO_CGENERATOR_CATEGORIES[]' => Tools::getValue(
                'EVERSEO_CGENERATOR_CATEGORIES',
                json_decode(
                    Configuration::get(
                        'EVERSEO_CGENERATOR_CATEGORIES'
                    )
                )
            ),
            'PRODUCT_SHORT_DESC_GENERATE' => static::getConfigInMultipleLangs('PRODUCT_SHORT_DESC_GENERATE'),
            'PRODUCT_DESC_GENERATE' => static::getConfigInMultipleLangs('PRODUCT_DESC_GENERATE'),
            'PRODUCT_BOTTOM_GENERATE' => static::getConfigInMultipleLangs('PRODUCT_BOTTOM_GENERATE'),
            'EVERSEO_DELETE_PRODUCT_CONTENT' => Configuration::get(
                'EVERSEO_DELETE_PRODUCT_CONTENT'
            ),
            'EVERSEO_BOTTOM_PRODUCT_CONTENT' => Configuration::get(
                'EVERSEO_BOTTOM_PRODUCT_CONTENT'
            ),
            'EVERSEO_PGENERATOR_LANGS[]' => Tools::getValue(
                'EVERSEO_PGENERATOR_LANGS',
                json_decode(
                    Configuration::get(
                        'EVERSEO_PGENERATOR_LANGS'
                    )
                )
            ),
            'EVERSEO_PGENERATOR_CATEGORIES[]' => Tools::getValue(
                'EVERSEO_PGENERATOR_CATEGORIES',
                json_decode(
                    Configuration::get(
                        'EVERSEO_PGENERATOR_CATEGORIES'
                    )
                )
            ),
            'MANUFACTURER_DESC_GENERATE' => static::getConfigInMultipleLangs('MANUFACTURER_DESC_GENERATE'),
            'SUPPLIER_DESC_GENERATE' => static::getConfigInMultipleLangs('SUPPLIER_DESC_GENERATE'),
            'EVERSEO_DELETE_MANUFACTURER_CONTENT' => Configuration::get(
                'EVERSEO_DELETE_MANUFACTURER_CONTENT'
            ),
            'EVERSEO_DELETE_SUPPLIER_CONTENT' => Configuration::get(
                'EVERSEO_DELETE_SUPPLIER_CONTENT'
            ),
            'EVERSEO_USE_AUTHOR' => Configuration::get(
                'EVERSEO_USE_AUTHOR'
            ),
            'EVERSEO_AUTHOR' => Configuration::get(
                'EVERSEO_AUTHOR'
            ),
            'EVERSEO_SUBJECT' => Configuration::get(
                'EVERSEO_SUBJECT'
            ),
            'EVERSEO_MESSAGE' => Configuration::get(
                'EVERSEO_MESSAGE'
            ),
            'EVERSEO_ROBOTS_TXT_REWRITE' => Configuration::get(
                'EVERSEO_ROBOTS_TXT_REWRITE'
            ),
            'EVERSEO_ROBOTS_TXT' => Configuration::get(
                'EVERSEO_ROBOTS_TXT'
            ),
            'EVERSEO_BULK_LANGS[]' => Tools::getValue(
                'EVERSEO_BULK_LANGS',
                json_decode(
                    Configuration::get(
                        'EVERSEO_BULK_LANGS'
                    )
                )
            ),
            'EVERSEO_LOCK_PDF' => Configuration::get(
                'EVERSEO_LOCK_PDF'
            ),
            'EVERSEO_LOCK_XLSX' => Configuration::get(
                'EVERSEO_LOCK_XLSX'
            ),
            'EVERSEO_LOCK_CSV' => Configuration::get(
                'EVERSEO_LOCK_CSV'
            ),
            'EVERSEO_LOCK_WORD' => Configuration::get(
                'EVERSEO_LOCK_WORD'
            ),
            'EVERSEO_CLICKJACKING' => Configuration::get(
                'EVERSEO_CLICKJACKING'
            ),
            'EVERSEO_BLOCK_RIGHT_CLICK' => Configuration::get(
                'EVERSEO_BLOCK_RIGHT_CLICK'
            ),
            'EVERSEO_HOTLINKING' => Configuration::get(
                'EVERSEO_HOTLINKING'
            ),
            'EVERSEO_DEFLATE' => Configuration::get(
                'EVERSEO_DEFLATE'
            ),
            'EVERPSCAPTCHA_SITE_KEY' => Tools::getValue(
                'EVERPSCAPTCHA_SITE_KEY',
                Configuration::get(
                    'EVERPSCAPTCHA_SITE_KEY'
                )
            ),
            'EVERPSCAPTCHA_SECRET_KEY' => Tools::getValue(
                'EVERPSCAPTCHA_SECRET_KEY',
                Configuration::get(
                    'EVERPSCAPTCHA_SECRET_KEY'
                )
            ),
            'EVERHTACCESS' => Configuration::get(
                'EVERHTACCESS'
            ),
            'EVERHTACCESS_PREPEND' => Configuration::get(
                'EVERHTACCESS_PREPEND'
            ),
            'EVERHTACCESS_404' => Configuration::get(
                'EVERHTACCESS_404'
            ),
            'EVERSEO_GTOP' => Configuration::get(
                'EVERSEO_GTOP'
            ),
            'EVERSEO_GCOLUMN' => Configuration::get(
                'EVERSEO_GCOLUMN'
            ),
            'EVERSEO_LAZY_LOAD' => Configuration::get(
                'EVERSEO_LAZY_LOAD'
            ),
            'EVERSEO_LAZY_LOAD_EXCEPTIONS' => Configuration::get(
                'EVERSEO_LAZY_LOAD_EXCEPTIONS'
            ),
            'EVERSEO_DELETE_CATEGORY_CONTENT' => Configuration::get(
                'EVERSEO_DELETE_CATEGORY_CONTENT'
            )
        ];
    }

    public function postValidation()
    {
        if (Tools::isSubmit('submiteverpsseoModule')) {
            if (!Tools::getValue('EVERSEO_LANG')
                || !Validate::isInt(Tools::getValue('EVERSEO_LANG'))
            ) {
                $this->posterrors[] = $this->l('error : [Language] is not valid');
            }

            if (!Tools::getValue('EVERSEO_LINKED_NBR')
                || !Validate::isInt(Tools::getValue('EVERSEO_LINKED_NBR'))
            ) {
                $this->posterrors[] = $this->l('error : Link number is not valid');
            }

            if (!Tools::getValue('EVERSEO_CMS_LINKED')
                || !Validate::isInt(Tools::getValue('EVERSEO_CMS_LINKED'))
            ) {
                $this->posterrors[] = $this->l('error : [CMS] is not valid');
            }

            if (!Tools::getValue('EVERSEO_LONG_DESC_LINKED')
                || !Validate::isInt(Tools::getValue('EVERSEO_LONG_DESC_LINKED'))
            ) {
                $this->posterrors[] = $this->l('error : [Products Description] is not valid');
            }

            if (!Tools::getValue('EVERSEO_SHORT_DESC_LINKED')
                || !Validate::isInt(Tools::getValue('EVERSEO_SHORT_DESC_LINKED'))
            ) {
                $this->posterrors[] = $this->l('error : [Products Short Description] is not valid');
            }

            if (!Tools::getValue('EVERSEO_CATEG_LINKED')
                || !Validate::isInt(Tools::getValue('EVERSEO_CATEG_LINKED'))
            ) {
                $this->posterrors[] = $this->l('error : [Categories Description] is not valid');
            }

            if (!Validate::isUrl(Tools::getValue('LINKEDTO'))
                && !Validate::isGenericName(Tools::getValue('SEARCHED'))
            ) {
                $this->postErrors[] = $this->l('Error : [URL linked to] is not valid.');
            }

            if (Tools::getValue('EVERSEO_MANUFACTURER_REASSURANCE')
                && !Validate::isBool(Tools::getValue('EVERSEO_MANUFACTURER_REASSURANCE'))
            ) {
                $this->posterrors[] = $this->l('error : [Manufacturer link] is not valid');
            }

            if (Tools::getValue('EVERSEO_SUPPLIER_REASSURANCE')
                && !Validate::isBool(Tools::getValue('EVERSEO_SUPPLIER_REASSURANCE'))
            ) {
                $this->posterrors[] = $this->l('error : [Supplier link] is not valid');
            }

            if (!Tools::getValue('EVERSEO_PRODUCT')
                || !Validate::isBool(Tools::getValue('EVERSEO_PRODUCT'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Redirect to products" is not valid');
            }

            if (!Tools::getValue('EVERSEO_CATEGORY')
                || !Validate::isBool(Tools::getValue('EVERSEO_CATEGORY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Redirect to categories" is not valid');
            }

            if (!Tools::getValue('EVERSEO_TAGS')
                || !Validate::isBool(Tools::getValue('EVERSEO_TAGS'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Redirect using tags" is not valid');
            }

            if (!Tools::getValue('EVERSEO_PRIORITY')
                || !Validate::isInt(Tools::getValue('EVERSEO_PRIORITY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Redirect priorities" is not valid');
            }

            if (!Tools::getValue('EVERSEO_REDIRECT')
                || !Validate::isInt(Tools::getValue('EVERSEO_REDIRECT'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Use 301 redirect" is not valid');
            }

            if (Tools::getValue('EVERSEO_NOT_FOUND')
                && !Validate::isBool(Tools::getValue('EVERSEO_NOT_FOUND'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "What if is not found" is not valid');
            }

            if (Tools::getValue('EVERSEO_FORCE_PRODUCT_REDIRECT')
                && !Validate::isBool(Tools::getValue('EVERSEO_FORCE_PRODUCT_REDIRECT'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "forcing redrect product" is not valid');
            }

            if (Tools::getValue('EVERSEO_ORDER_BY')
                && !Validate::isBool(Tools::getValue('EVERSEO_ORDER_BY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Filter results by" is not valid');
            }

            if (Tools::getValue('EVERSEO_CUSTOM_404')
                && !Validate::isBool(Tools::getValue('EVERSEO_CUSTOM_404'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Custom 404" is not valid');
            }

            if (Tools::getValue('EVERSEO_404_SEARCH')
                && !Validate::isBool(Tools::getValue('EVERSEO_404_SEARCH'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Search bar on 404" is not valid');
            }

            if (Tools::getValue('EVERSEO_QUALITY_LEVEL')
                && !Validate::isInt(Tools::getValue('EVERSEO_QUALITY_LEVEL'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Ever Quality level" is not valid');
            }

            if (!Tools::getValue('EVERSEO_KNOWLEDGE')
                || !Validate::isString(Tools::getValue('EVERSEO_KNOWLEDGE'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "knowledgegraph" is not valid');
            }

            if (!Tools::getValue('EVERSEO_INDEXNOW_LIMIT')
                || !Validate::isInt(Tools::getValue('EVERSEO_INDEXNOW_LIMIT'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Index now day limit" is not valid');
            }

            if (Tools::getValue('EVERSEO_ANALYTICS')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_ANALYTICS'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Analytics tracking code" is not valid');
            }

            if (Tools::getValue('EVERSEO_THEME_COLOR')
                && !Validate::isColor(Tools::getValue('EVERSEO_THEME_COLOR'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Theme color" is not valid');
            }

            if (Tools::getValue('EVERSEO_WEBP')
                && !Validate::isBool(Tools::getValue('EVERSEO_WEBP'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Use webp" is not valid');
            }

            if (Tools::getValue('EVERSEO_LAZY_LOAD')
                && !Validate::isBool(Tools::getValue('EVERSEO_LAZY_LOAD'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Use lazyload" is not valid');
            }
        
            if (!function_exists('imagewebp') && (bool)Tools::getValue('EVERSEO_WEBP') === true) {
                $this->postErrors[] = $this->l('Error : You must have imagewebp extension enabled on your server');
            }

            if (Tools::getValue('EVERSEO_MAINTENANCE')
                && !Validate::isBool(Tools::getValue('EVERSEO_MAINTENANCE'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "SEO maintenance" is not valid');
            }

            if (Tools::getValue('EVERSEO_MAINTENANCE_URL')
                && !Validate::isUrl(Tools::getValue('EVERSEO_MAINTENANCE_URL'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "SEO maintenance URL" is not valid');
            }

            if (Tools::getValue('EVERSEO_SEARCHCONSOLE')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_SEARCHCONSOLE'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Search console" is not valid');
            }

            if (Tools::getValue('EVERSEO_GTAG')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_GTAG'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Google GTM code" is not valid');
            }

            if (Tools::getValue('EVERSEO_FBPIXEL')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_FBPIXEL'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Facebook pixel" is not valid');
            }

            if (Tools::getValue('EVERSEO_ADWORDS')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_ADWORDS'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Adwords tracking code" is not valid');
            }

            if (Tools::getValue('EVERSEO_ADWORDS_CART_LABEL')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_ADWORDS_CART_LABEL'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Adwords add to cart label" is not valid');
            }

            if (Tools::getValue('EVERSEO_ADWORDS_SENDTO')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_ADWORDS_SENDTO'))) {
                $this->postErrors[] = $this->l('Error : The field "Adwords event snippet code" is not valid');
            }

            if (Tools::getValue('EVERSEO_ADWORDS_CONTACT')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_ADWORDS_CONTACT'))) {
                $this->postErrors[] = $this->l('Error : The field "Adwords contact event snippet code" is not valid');
            }

            if (Tools::getValue('EVERSEO_ADWORDS_OPART')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_ADWORDS_OPART'))) {
                $this->postErrors[] = $this->l('Error : The field "Adwords Opart event snippet code" is not valid');
            }

            if (Tools::getValue('EVERSEO_USE_OPENGRAPH')
                && !Validate::isBool(Tools::getValue('EVERSEO_USE_OPENGRAPH'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Add Facebook Open Graph metas" is not valid');
            }

            if (Tools::getValue('EVERSEO_USE_TWITTER')
                && !Validate::isBool(Tools::getValue('EVERSEO_USE_TWITTER'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Add twitter metas" is not valid');
            }

            if (Tools::getValue('EVERSEO_TWITTER_NAME')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_TWITTER_NAME'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Twitter account" is not valid');
            }

            if (Tools::getValue('EVERSEO_CANONICAL')
                && !Validate::isBool(Tools::getValue('EVERSEO_CANONICAL'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Canonical URL" is not valid');
            }

            if (Tools::getValue('EVERSEO_HREF_LANG')
                && !Validate::isBool(Tools::getValue('EVERSEO_HREF_LANG'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Add hreflangs" is not valid');
            }

            if (Tools::getValue('EVERSEO_RSNIPPETS')
                && !Validate::isBool(Tools::getValue('EVERSEO_RSNIPPETS'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Google Rich Snippets" is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_QTY_ELEMENTS')
                && !Validate::isInt(Tools::getValue('EVERSEO_SITEMAP_QTY_ELEMENTS'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Sitemap number" is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_LANGS')
                && !Validate::isArrayWithIds(Tools::getValue('EVERSEO_SITEMAP_LANGS'))
            ) {
                $this->posterrors[] = $this->l('error : [Sitemap langs] is not valid');
            }

            if (Tools::getValue('EVERSEO_BULK_LANGS')
                && !Validate::isArrayWithIds(Tools::getValue('EVERSEO_BULK_LANGS'))
            ) {
                $this->posterrors[] = $this->l('error : [Sitemap langs] is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_CATEGORY')
                && !Validate::isBool(Tools::getValue('EVERSEO_SITEMAP_CATEGORY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Active" for Category Sitemap is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_CATEGORY_FREQUENCY')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_SITEMAP_CATEGORY_FREQUENCY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Categories frequency on sitemap" is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_CATEGORY_PRIORITY')
                && !Validate::isFloat(Tools::getValue('EVERSEO_SITEMAP_CATEGORY_PRIORITY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Categories priority on sitemap" is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_PRODUCT')
                && !Validate::isBool(Tools::getValue('EVERSEO_SITEMAP_PRODUCT'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Active" for Product Sitemap is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_PRODUCT_FREQUENCY')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_SITEMAP_PRODUCT_FREQUENCY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Products frequency on sitemap" is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_PRODUCT_PRIORITY')
                && !Validate::isFloat(Tools::getValue('EVERSEO_SITEMAP_PRODUCT_PRIORITY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Products priority on sitemap" is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_IMAGE')
                && !Validate::isBool(Tools::getValue('EVERSEO_SITEMAP_IMAGE'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Active" for Image Sitemap is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_IMAGE_FREQUENCY')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_SITEMAP_IMAGE_FREQUENCY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Images frequency on sitemap" is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_IMAGE_PRIORITY')
                && !Validate::isFloat(Tools::getValue('EVERSEO_SITEMAP_IMAGE_PRIORITY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Images priority on sitemap" is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_CMS')
                && !Validate::isBool(Tools::getValue('EVERSEO_SITEMAP_CMS'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Active" for CMS Sitemap is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_CMS_FREQUENCY')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_SITEMAP_CMS_FREQUENCY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Cms frequency on sitemap" is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_CMS_PRIORITY')
                && !Validate::isFloat(Tools::getValue('EVERSEO_SITEMAP_CMS_PRIORITY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Cms priority on sitemap" is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_PAGE_META')
                && !Validate::isBool(Tools::getValue('EVERSEO_SITEMAP_PAGE_META'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Active" for Page Meta Sitemap is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_PAGE_META_FREQUENCY')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_SITEMAP_PAGE_META_FREQUENCY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Page Meta frequency on sitemap" is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_PAGE_META_PRIORITY')
                && !Validate::isFloat(Tools::getValue('EVERSEO_SITEMAP_PAGE_META_PRIORITY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Page Meta priority on sitemap" is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_MANUFACTURER')
                && !Validate::isBool(Tools::getValue('EVERSEO_SITEMAP_MANUFACTURER'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Active" for Manufacturer Sitemap is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_MANUFACTURER_FREQUENCY')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_SITEMAP_MANUFACTURER_FREQUENCY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Manufacturers frequency on sitemap" is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_MANUFACTURER_PRIORITY')
                && !Validate::isFloat(Tools::getValue('EVERSEO_SITEMAP_MANUFACTURER_PRIORITY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Manufacturers priority on sitemap" is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_SUPPLIER')
                && !Validate::isBool(Tools::getValue('EVERSEO_SITEMAP_SUPPLIER'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Active" for Supplier Sitemap is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_SUPPLIER_FREQUENCY')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_SITEMAP_SUPPLIER_FREQUENCY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Suppliers frequency on sitemap" is not valid');
            }

            if (Tools::getValue('EVERSEO_SITEMAP_SUPPLIER_PRIORITY')
                && !Validate::isFloat(Tools::getValue('EVERSEO_SITEMAP_SUPPLIER_PRIORITY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Suppliers priority on sitemap" is not valid');
            }

            if (Tools::getValue('EVERSEO_INDEX_CATEGORY')
                && !Validate::isBool(Tools::getValue('EVERSEO_INDEX_CATEGORY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Index categories" is not valid');
            }

            if (Tools::getValue('EVERSEO_INDEX_PRODUCT')
                && !Validate::isBool(Tools::getValue('EVERSEO_INDEX_PRODUCT'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Index products" is not valid');
            }

            if (Tools::getValue('EVERSEO_INDEX_CMS')
                && !Validate::isBool(Tools::getValue('EVERSEO_INDEX_CMS'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Index CMS" is not valid');
            }

            if (Tools::getValue('EVERSEO_INDEX_PAGE_META')
                && !Validate::isBool(Tools::getValue('EVERSEO_INDEX_PAGE_META'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Index page meta" is not valid');
            }

            if (Tools::getValue('EVERSEO_INDEX_MANUFACTURER')
                && !Validate::isBool(Tools::getValue('EVERSEO_INDEX_MANUFACTURER'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Index Manufacturers" is not valid');
            }

            if (Tools::getValue('EVERSEO_INDEX_SUPPLIER')
                && !Validate::isBool(Tools::getValue('EVERSEO_INDEX_SUPPLIER'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Index suppliers" is not valid');
            }

            if (Tools::getValue('EVERSEO_INDEX_ARGS')
                && !Validate::isBool(Tools::getValue('EVERSEO_INDEX_ARGS'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Index on pages with args" is not valid');
            }

            if (Tools::getValue('EVERSEO_FOLLOW_CATEGORY')
                && !Validate::isBool(Tools::getValue('EVERSEO_FOLLOW_CATEGORY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Follow categories" is not valid');
            }

            if (Tools::getValue('EVERSEO_FOLLOW_PRODUCT')
                && !Validate::isBool(Tools::getValue('EVERSEO_FOLLOW_PRODUCT'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Follow products" is not valid');
            }

            if (Tools::getValue('EVERSEO_FOLLOW_CMS')
                && !Validate::isBool(Tools::getValue('EVERSEO_FOLLOW_CMS'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Follow CMS" is not valid');
            }

            if (Tools::getValue('EVERSEO_FOLLOW_PAGE_META')
                && !Validate::isBool(Tools::getValue('EVERSEO_FOLLOW_PAGE_META'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Follow page meta" is not valid');
            }

            if (Tools::getValue('EVERSEO_FOLLOW_MANUFACTURER')
                && !Validate::isBool(Tools::getValue('EVERSEO_FOLLOW_MANUFACTURER'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Follow manufacturers" is not valid');
            }

            if (Tools::getValue('EVERSEO_FOLLOW_SUPPLIER')
                && !Validate::isBool(Tools::getValue('EVERSEO_FOLLOW_SUPPLIER'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Follow suppliers" is not valid');
            }

            if (Tools::getValue('EVERSEO_FOLLOW_ARGS')
                && !Validate::isBool(Tools::getValue('EVERSEO_FOLLOW_ARGS'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Follow on pages with args" is not valid');
            }

            if (Tools::getValue('EVERSEO_ROBOTS_TXT_REWRITE')
                && !Validate::isBool(Tools::getValue('EVERSEO_ROBOTS_TXT_REWRITE'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Rewrite robots.txt" is not valid');
            }

            if (Tools::getValue('EVERSEO_ROBOTS_TXT')
                && !Validate::isCleanHtml(Tools::getValue('EVERSEO_ROBOTS_TXT'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Robots.txt custom rules" is not valid');
            }

            if (Tools::getValue('EVERSEO_PGENERATOR_CATEGORIES')
                && !Validate::isArrayWithIds(Tools::getValue('EVERSEO_PGENERATOR_CATEGORIES'))
            ) {
                $this->postErrors[] = $this->l('Error: allowed categories is not valid');
            }

            if (Tools::getValue('EVERSEO_CGENERATOR_CATEGORIES')
                && !Validate::isArrayWithIds(Tools::getValue('EVERSEO_CGENERATOR_CATEGORIES'))
            ) {
                die(var_dump(Tools::getValue('EVERSEO_CGENERATOR_CATEGORIES')));
                $this->postErrors[] = $this->l('Error: allowed categories is not valid');
            }

            if (Tools::getValue('EVERSEO_DELETE_PRODUCT_CONTENT')
                && !Validate::isBool(Tools::getValue('EVERSEO_DELETE_PRODUCT_CONTENT'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "delete default product content" is not valid');
            }

            if (Tools::getValue('EVERSEO_BOTTOM_PRODUCT_CONTENT')
                && !Validate::isBool(Tools::getValue('EVERSEO_BOTTOM_PRODUCT_CONTENT'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Set content to product bottom" is not valid');
            }

            if (Tools::getValue('EVERSEO_DELETE_CATEGORY_CONTENT')
                && !Validate::isBool(Tools::getValue('EVERSEO_DELETE_CATEGORY_CONTENT'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "delete default category content" is not valid');
            }

            if (Tools::getValue('EVERSEO_DELETE_MANUFACTURER_CONTENT')
                && !Validate::isBool(Tools::getValue('EVERSEO_DELETE_MANUFACTURER_CONTENT'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "delete default manufacturer content" is not valid');
            }

            if (Tools::getValue('EVERSEO_DELETE_SUPPLIER_CONTENT')
                && !Validate::isBool(Tools::getValue('EVERSEO_DELETE_SUPPLIER_CONTENT'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "delete default supplier content" is not valid');
            }

            if (Tools::getValue('EVERSEO_BLOCK_RIGHT_CLICK')
                && !Validate::isBool(Tools::getValue('EVERSEO_BLOCK_RIGHT_CLICK'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "block right click" is not valid');
            }

            if (Tools::getValue('EVERSEO_CLICKJACKING')
                && !Validate::isBool(Tools::getValue('EVERSEO_CLICKJACKING'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "prevent clickjacking" is not valid');
            }

            if (Tools::getValue('EVERSEO_HOTLINKING')
                && !Validate::isBool(Tools::getValue('EVERSEO_HOTLINKING'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "prevent hotlinking" is not valid');
            }

            if (Tools::getValue('EVERSEO_DEFLATE')
                && !Validate::isBool(Tools::getValue('EVERSEO_DEFLATE'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "mod_deflate" is not valid');
            }

            if (Tools::getValue('EVERHTACCESS_404')
                && !Validate::isBool(Tools::getValue('EVERHTACCESS_404'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Add 404 redirects to htaccess" is not valid');
            }

            if (Tools::getValue('EVERPSCAPTCHA_SITE_KEY')
                && !Validate::isString(Tools::getValue('EVERPSCAPTCHA_SITE_KEY'))
            ) {
                $this->postErrors[] = $this->l('The field "Google Site Key" is not valid.');
            }

            if (Tools::getValue('EVERPSCAPTCHA_SECRET_KEY')
                && !Validate::isString(Tools::getValue('EVERPSCAPTCHA_SECRET_KEY'))
            ) {
                $this->postErrors[] = $this->l('The field "Google Secret Key" is not valid.');
            }

            if (Tools::getValue('EVERSEO_GTOP')
                && !Validate::isBool(Tools::getValue('EVERSEO_GTOP'))
            ) {
                $this->postErrors[] = $this->l('The field "Google Translate on top" is not valid.');
            }

            if (Tools::getValue('EVERSEO_GCOLUMN')
                && !Validate::isBool(Tools::getValue('EVERSEO_GCOLUMN'))
            ) {
                $this->postErrors[] = $this->l('The field "Google Translate on columns" is not valid.');
            }

            foreach (Language::getLanguages(false) as $language) {
                if (Tools::getValue('EVERSEO_404_TOP_' . $language['id_lang'])
                    && !Validate::isCleanHtml(Tools::getValue('EVERSEO_404_TOP_' . $language['id_lang']))
                ) {
                    $this->postErrors[] = $this->l('Error : The top content on 404 page is not valid');
                }
                if (Tools::getValue('EVERSEO_404_BOTTOM_' . $language['id_lang'])
                    && !Validate::isCleanHtml(Tools::getValue('EVERSEO_404_BOTTOM_' . $language['id_lang']))
                ) {
                    $this->postErrors[] = $this->l('Error : The top content on 404 page is not valid');
                }

                if (Tools::getValue('PRODUCT_SHORT_DESC_GENERATE_' . $language['id_lang'])
                    && !Validate::isCleanHtml(Tools::getValue('PRODUCT_SHORT_DESC_GENERATE_' . $language['id_lang']))
                ) {
                    $this->postErrors[] = $this->l('Error : The product short description is not valid');
                }

                if (Tools::getValue('PRODUCT_DESC_GENERATE_' . $language['id_lang'])
                    && !Validate::isCleanHtml(Tools::getValue('PRODUCT_DESC_GENERATE_' . $language['id_lang']))
                ) {
                    $this->postErrors[] = $this->l('Error : The product description is not valid');
                }

                if (Tools::getValue('CATEGORY_DESC_GENERATE_' . $language['id_lang'])
                    && !Validate::isCleanHtml(Tools::getValue('CATEGORY_DESC_GENERATE_' . $language['id_lang']))
                ) {
                    $this->postErrors[] = $this->l('Error : The category content is not valid');
                }

                if (Tools::getValue('SUPPLIER_DESC_GENERATE_' . $language['id_lang'])
                    && !Validate::isCleanHtml(Tools::getValue('SUPPLIER_DESC_GENERATE_' . $language['id_lang']))
                ) {
                    $this->postErrors[] = $this->l('Error : The supplier content is not valid');
                }

                if (Tools::getValue('MANUFACTURER_DESC_GENERATE_' . $language['id_lang'])
                    && !Validate::isCleanHtml(Tools::getValue('MANUFACTURER_DESC_GENERATE_' . $language['id_lang']))
                ) {
                    $this->postErrors[] = $this->l('Error : The manufacturer content is not valid');
                }
            }
        }
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        if ((bool) Tools::getValue('EVERSEO_WEBP') === false) {
            // Logo
            $psLogo = \Configuration::get(
                'PS_LOGO'
            );
            $psLogo = str_replace('.webp', '', $psLogo);
            // \Configuration::updateValue(
            //     'PS_LOGO',
            //     $psLogo
            // );
            Hook::exec('actionHtaccessCreate');
        }
        // Save configuration
        $form_values = $this->getConfigFormValues();
        $everseo_404_top = [];
        $everseo_404_bottom = [];
        $product_shortdesc = [];
        $product_desc = [];
        $product_bttm = [];
        $category_desc = [];
        $manufacturer_desc = [];
        $supplier_desc = [];
        // Title and meta generation
        $pagemeta_metadesc = [];
        $pagemeta_title = [];
        $cms_metadesc = [];
        $cms_title = [];
        $supplier_metadesc = [];
        $supplier_title = [];
        $m_metadesc = [];
        $m_title = [];
        $category_metadesc = [];
        $category_title = [];
        $image_alt = [];
        $product_metadesc = [];
        $product_title = [];
        foreach (Language::getLanguages(false) as $lang) {
            $everseo_404_top[$lang['id_lang']] = (Tools::getValue('EVERSEO_404_TOP_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_404_TOP_' . $lang['id_lang']) : '';

            $everseo_404_bottom[$lang['id_lang']] = (Tools::getValue('EVERSEO_404_BOTTOM_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_404_BOTTOM_' . $lang['id_lang']) : '';

            $product_shortdesc[$lang['id_lang']] = (Tools::getValue('PRODUCT_SHORT_DESC_GENERATE_' . $lang['id_lang']))
            ? Tools::getValue('PRODUCT_SHORT_DESC_GENERATE_' . $lang['id_lang']) : '';

            $product_desc[$lang['id_lang']] = (Tools::getValue('PRODUCT_DESC_GENERATE_' . $lang['id_lang']))
            ? Tools::getValue('PRODUCT_DESC_GENERATE_' . $lang['id_lang']) : '';

            $product_bttm[$lang['id_lang']] = (Tools::getValue('PRODUCT_BOTTOM_GENERATE_' . $lang['id_lang']))
            ? Tools::getValue('PRODUCT_BOTTOM_GENERATE_' . $lang['id_lang']) : '';

            $category_desc[$lang['id_lang']] = (Tools::getValue('CATEGORY_DESC_GENERATE_' . $lang['id_lang']))
            ? Tools::getValue('CATEGORY_DESC_GENERATE_' . $lang['id_lang']) : '';

            $manufacturer_desc[$lang['id_lang']] = (Tools::getValue('MANUFACTURER_DESC_GENERATE_' . $lang['id_lang']))
            ? Tools::getValue('MANUFACTURER_DESC_GENERATE_' . $lang['id_lang']) : '';

            $supplier_desc[$lang['id_lang']] = (Tools::getValue('SUPPLIER_DESC_GENERATE_' . $lang['id_lang']))
            ? Tools::getValue('SUPPLIER_DESC_GENERATE_' . $lang['id_lang']) : '';

            $pagemeta_metadesc[$lang['id_lang']] = (Tools::getValue('EVERSEO_PAGEMETA_METADESC_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_PAGEMETA_METADESC_AUTO_' . $lang['id_lang']) : '';

            $pagemeta_title[$lang['id_lang']] = (Tools::getValue('EVERSEO_PAGEMETA_TITLE_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_PAGEMETA_TITLE_AUTO_' . $lang['id_lang']) : '';

            $cms_metadesc[$lang['id_lang']] = (Tools::getValue('EVERSEO_CMS_METADESC_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_CMS_METADESC_AUTO_' . $lang['id_lang']) : '';

            $cms_title[$lang['id_lang']] = (Tools::getValue('EVERSEO_CMS_TITLE_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_CMS_TITLE_AUTO_' . $lang['id_lang']) : '';

            $supplier_metadesc[$lang['id_lang']] = (Tools::getValue('EVERSEO_SUPPLIER_METADESC_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_SUPPLIER_METADESC_AUTO_' . $lang['id_lang']) : '';

            $supplier_title[$lang['id_lang']] = (Tools::getValue('EVERSEO_SUPPLIER_TITLE_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_SUPPLIER_TITLE_AUTO_' . $lang['id_lang']) : '';

            $m_metadesc[$lang['id_lang']] = (Tools::getValue('EVERSEO_MANUFACTURER_METADESC_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_MANUFACTURER_METADESC_AUTO_' . $lang['id_lang']) : '';

            $m_title[$lang['id_lang']] = (Tools::getValue('EVERSEO_MANUFACTURER_TITLE_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_MANUFACTURER_TITLE_AUTO_' . $lang['id_lang']) : '';

            $category_metadesc[$lang['id_lang']] = (Tools::getValue('EVERSEO_CATEGORY_METADESC_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_CATEGORY_METADESC_AUTO_' . $lang['id_lang']) : '';

            $category_title[$lang['id_lang']] = (Tools::getValue('EVERSEO_CATEGORY_TITLE_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_CATEGORY_TITLE_AUTO_' . $lang['id_lang']) : '';

            $image_alt[$lang['id_lang']] = (Tools::getValue('EVERSEO_IMAGE_ALT_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_IMAGE_ALT_AUTO_' . $lang['id_lang']) : '';

            $product_metadesc[$lang['id_lang']] = (Tools::getValue('EVERSEO_PRODUCT_METADESC_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_PRODUCT_METADESC_AUTO_' . $lang['id_lang']) : '';

            $product_title[$lang['id_lang']] = (Tools::getValue('EVERSEO_PRODUCT_TITLE_AUTO_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_PRODUCT_TITLE_AUTO_' . $lang['id_lang']) : '';
        }
        foreach (array_keys($form_values) as $key) {
            if ($key == 'EVERSEO_SITEMAP_LANGS[]') {
                Configuration::updateValue(
                    'EVERSEO_SITEMAP_LANGS',
                    json_encode(Tools::getValue('EVERSEO_SITEMAP_LANGS')),
                    true
                );
            } elseif ($key == 'EVERSEO_AUTO_CMS_LANGS[]') {
                Configuration::updateValue(
                    'EVERSEO_AUTO_CMS_LANGS',
                    json_encode(Tools::getValue('EVERSEO_AUTO_CMS_LANGS')),
                    true
                );
            } elseif ($key == 'EVERSEO_AUTO_SUPPLIER_LANGS[]') {
                Configuration::updateValue(
                    'EVERSEO_AUTO_SUPPLIER_LANGS',
                    json_encode(Tools::getValue('EVERSEO_AUTO_SUPPLIER_LANGS')),
                    true
                );
            } elseif ($key == 'EVERSEO_AUTO_MANUFACTURER_LANGS[]') {
                Configuration::updateValue(
                    'EVERSEO_AUTO_MANUFACTURER_LANGS',
                    json_encode(Tools::getValue('EVERSEO_AUTO_MANUFACTURER_LANGS')),
                    true
                );
            } elseif ($key == 'EVERSEO_AUTO_CATEGORY_LANGS[]') {
                Configuration::updateValue(
                    'EVERSEO_AUTO_CATEGORY_LANGS',
                    json_encode(Tools::getValue('EVERSEO_AUTO_CATEGORY_LANGS')),
                    true
                );
            } elseif ($key == 'EVERSEO_AUTO_IMAGE_LANGS[]') {
                Configuration::updateValue(
                    'EVERSEO_AUTO_IMAGE_LANGS',
                    json_encode(Tools::getValue('EVERSEO_AUTO_IMAGE_LANGS')),
                    true
                );
            } elseif ($key == 'EVERSEO_AUTO_PRODUCT_LANGS[]') {
                Configuration::updateValue(
                    'EVERSEO_AUTO_PRODUCT_LANGS',
                    json_encode(Tools::getValue('EVERSEO_AUTO_PRODUCT_LANGS')),
                    true
                );
            } elseif ($key == 'EVERSEO_AUTO_PAGEMETA_LANGS[]') {
                Configuration::updateValue(
                    'EVERSEO_AUTO_PAGEMETA_LANGS',
                    json_encode(Tools::getValue('EVERSEO_AUTO_PAGEMETA_LANGS')),
                    true
                );
            } elseif ($key == 'EVERSEO_BULK_LANGS[]') {
                Configuration::updateValue(
                    'EVERSEO_BULK_LANGS',
                    json_encode(Tools::getValue('EVERSEO_BULK_LANGS')),
                    true
                );
            } elseif ($key == 'EVERSEO_PGENERATOR_CATEGORIES[]') {
                Configuration::updateValue(
                    'EVERSEO_PGENERATOR_CATEGORIES',
                    json_encode(Tools::getValue('EVERSEO_PGENERATOR_CATEGORIES')),
                    true
                );
            } elseif ($key == 'EVERSEO_CGENERATOR_CATEGORIES[]') {
                Configuration::updateValue(
                    'EVERSEO_CGENERATOR_CATEGORIES',
                    json_encode(Tools::getValue('EVERSEO_CGENERATOR_CATEGORIES')),
                    true
                );
            } elseif ($key == 'EVERSEO_404_TOP') {
                Configuration::updateValue('EVERSEO_404_TOP', $everseo_404_top, true);
            } elseif ($key == 'EVERSEO_404_BOTTOM') {
                Configuration::updateValue('EVERSEO_404_BOTTOM', $everseo_404_bottom, true);
            } elseif ($key == 'PRODUCT_DESC_GENERATE') {
                Configuration::updateValue('PRODUCT_DESC_GENERATE', $product_desc, true);
            } elseif ($key == 'PRODUCT_SHORT_DESC_GENERATE') {
                Configuration::updateValue('PRODUCT_SHORT_DESC_GENERATE', $product_shortdesc, true);
            } elseif ($key == 'PRODUCT_BOTTOM_GENERATE') {
                Configuration::updateValue('PRODUCT_BOTTOM_GENERATE', $product_bttm, true);
            } elseif ($key == 'CATEGORY_DESC_GENERATE') {
                Configuration::updateValue('CATEGORY_DESC_GENERATE', $category_desc, true);
            } elseif ($key == 'MANUFACTURER_DESC_GENERATE') {
                Configuration::updateValue('MANUFACTURER_DESC_GENERATE', $manufacturer_desc, true);
            } elseif ($key == 'SUPPLIER_DESC_GENERATE') {
                Configuration::updateValue('SUPPLIER_DESC_GENERATE', $supplier_desc, true);
            } elseif ($key == 'EVERSEO_PAGEMETA_METADESC_AUTO') {
                Configuration::updateValue('EVERSEO_PAGEMETA_METADESC_AUTO', $pagemeta_metadesc, true);
            } elseif ($key == 'EVERSEO_PAGEMETA_TITLE_AUTO') {
                Configuration::updateValue('EVERSEO_PAGEMETA_TITLE_AUTO', $pagemeta_title, true);
            } elseif ($key == 'EVERSEO_CMS_METADESC_AUTO') {
                Configuration::updateValue('EVERSEO_CMS_METADESC_AUTO', $cms_metadesc, true);
            } elseif ($key == 'EVERSEO_CMS_TITLE_AUTO') {
                Configuration::updateValue('EVERSEO_CMS_TITLE_AUTO', $cms_title, true);
            } elseif ($key == 'EVERSEO_SUPPLIER_METADESC_AUTO') {
                Configuration::updateValue('EVERSEO_SUPPLIER_METADESC_AUTO', $supplier_metadesc, true);
            } elseif ($key == 'EVERSEO_SUPPLIER_TITLE_AUTO') {
                Configuration::updateValue('EVERSEO_SUPPLIER_TITLE_AUTO', $supplier_title, true);
            } elseif ($key == 'EVERSEO_MANUFACTURER_METADESC_AUTO') {
                Configuration::updateValue('EVERSEO_MANUFACTURER_METADESC_AUTO', $m_metadesc, true);
            } elseif ($key == 'EVERSEO_MANUFACTURER_TITLE_AUTO') {
                Configuration::updateValue('EVERSEO_MANUFACTURER_TITLE_AUTO', $m_title, true);
            } elseif ($key == 'EVERSEO_CATEGORY_METADESC_AUTO') {
                Configuration::updateValue('EVERSEO_CATEGORY_METADESC_AUTO', $category_metadesc, true);
            } elseif ($key == 'EVERSEO_CATEGORY_TITLE_AUTO') {
                Configuration::updateValue('EVERSEO_CATEGORY_TITLE_AUTO', $category_title, true);
            } elseif ($key == 'EVERSEO_IMAGE_ALT_AUTO') {
                Configuration::updateValue('EVERSEO_IMAGE_ALT_AUTO', $image_alt, true);
            } elseif ($key == 'EVERSEO_PRODUCT_METADESC_AUTO') {
                Configuration::updateValue('EVERSEO_PRODUCT_METADESC_AUTO', $product_metadesc, true);
            } elseif ($key == 'EVERSEO_PRODUCT_TITLE_AUTO') {
                Configuration::updateValue('EVERSEO_PRODUCT_TITLE_AUTO', $product_title, true);
            } elseif ($key == 'EVERSEO_PGENERATOR_LANGS[]') {
                Configuration::updateValue(
                    'EVERSEO_PGENERATOR_LANGS',
                    json_encode(Tools::getValue('EVERSEO_PGENERATOR_LANGS')),
                    true
                );
            } elseif ($key == 'EVERSEO_CGENERATOR_LANGS[]') {
                Configuration::updateValue(
                    'EVERSEO_CGENERATOR_LANGS',
                    json_encode(Tools::getValue('EVERSEO_CGENERATOR_LANGS')),
                    true
                );
            } elseif ($key == 'EVERSEO_PGENERATOR_CATEGORIES') {
                Configuration::updateValue(
                    'EVERSEO_PGENERATOR_CATEGORIES',
                    json_encode(Tools::getValue($key)),
                    true
                );
            } elseif ($key == 'EVERSEO_CGENERATOR_CATEGORIES') {
                Configuration::updateValue(
                    'EVERSEO_CGENERATOR_CATEGORIES',
                    json_encode(Tools::getValue($key)),
                    true
                );
            } else {
                Configuration::updateValue($key, Tools::getValue($key));
            }
        }
        /* Uploads image */
        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['image']['name'], '.'), 1));
        $imagesize = @getimagesize($_FILES['image']['tmp_name']);
        if (isset($_FILES['image']) &&
            isset($_FILES['image']['tmp_name']) &&
            !empty($_FILES['image']['tmp_name']) &&
            !empty($imagesize) &&
            in_array(
                Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)),
                [
                    'jpg',
                    'gif',
                    'jpeg',
                    'png',
                ]
            ) &&
            in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
        ) {
            $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');

            if ($error = ImageManager::validateUpload($_FILES['image'])) {
                $this->postErrors[] = $error;
            } elseif (!$temp_name
                || !move_uploaded_file($_FILES['image']['tmp_name'], $temp_name)
            ) {
                $this->postErrors[] = $this->l('An error occurred during the image upload process.');
            } elseif (!ImageManager::resize(
                $temp_name,
                dirname(__FILE__) . '/views/img/everpsseo.jpg',
                null,
                null,
                $type
            )) {
                $this->postErrors[] = $this->l('An error occurred during the image upload process.');
            }

            if (isset($temp_name)) {
                @unlink($temp_name);
            }
        }
        // Set default category index
        $this->getColumnStructure(
            'ever_seo_category',
            'indexable',
            (int) Tools::getValue('EVERSEO_INDEX_CATEGORY')
        );
        // Set default product index
        $this->getColumnStructure(
            'ever_seo_product',
            'indexable',
            (int) Tools::getValue('EVERSEO_INDEX_PRODUCT')
        );
        // Set default page meta index
        $this->getColumnStructure(
            'ever_seo_pagemeta',
            'indexable',
            (int) Tools::getValue('EVERSEO_INDEX_PAGE_META')
        );
        // Set default manufacturer index
        $this->getColumnStructure(
            'ever_seo_manufacturer',
            'indexable',
            (int) Tools::getValue('EVERSEO_INDEX_MANUFACTURER')
        );
        // Set default supplier index
        $this->getColumnStructure(
            'ever_seo_supplier',
            'indexable',
            (int) Tools::getValue('EVERSEO_INDEX_SUPPLIER')
        );
        // Set default category follow
        $this->getColumnStructure(
            'ever_seo_category',
            'follow',
            (int) Tools::getValue('EVERSEO_FOLLOW_CATEGORY')
        );
        // Set default product follow
        $this->getColumnStructure(
            'ever_seo_product',
            'follow',
            (int) Tools::getValue('EVERSEO_FOLLOW_PRODUCT')
        );
        // Set default page meta follow
        $this->getColumnStructure(
            'ever_seo_pagemeta',
            'follow',
            (int) Tools::getValue('EVERSEO_FOLLOW_PAGE_META')
        );
        // Set default manufacturer follow
        $this->getColumnStructure(
            'ever_seo_manufacturer',
            'follow',
            (int) Tools::getValue('EVERSEO_FOLLOW_MANUFACTURER')
        );
        // Set default supplier follow
        $this->getColumnStructure(
            'ever_seo_supplier',
            'follow',
            (int) Tools::getValue('EVERSEO_FOLLOW_SUPPLIER')
        );
        // Set default category sitemap
        $this->getColumnStructure(
            'ever_seo_category',
            'allowed_sitemap',
            (int) Tools::getValue('EVERSEO_SITEMAP_CATEGORY')
        );
        // Set default product sitemap
        $this->getColumnStructure(
            'ever_seo_product',
            'allowed_sitemap',
            (int) Tools::getValue('EVERSEO_SITEMAP_PRODUCT')
        );
        // Set default page meta sitemap
        $this->getColumnStructure(
            'ever_seo_pagemeta',
            'allowed_sitemap',
            (int) Tools::getValue('EVERSEO_SITEMAP_PAGE_META')
        );
        // Set default manufacturer sitemap
        $this->getColumnStructure(
            'ever_seo_manufacturer',
            'allowed_sitemap',
            (int) Tools::getValue('EVERSEO_SITEMAP_MANUFACTURER')
        );
        // Set default supplier sitemap
        $this->getColumnStructure(
            'ever_seo_supplier',
            'allowed_sitemap',
            (int) Tools::getValue('EVERSEO_SITEMAP_SUPPLIER')
        );
        // Generate robots.txt file if rewrite file in ON
        if ((bool) Configuration::get('EVERSEO_ROBOTS_TXT_REWRITE')) {
            $this->generateRobots();
        }
        if (!Configuration::get('EVERSEO_INDEXNOW_KEY')) {
            EverPsSeoTools::generateIndexNowKey();
        }
        $this->postSuccess[] = $this->l('All settings have been saved');
    }

#################### END CONFIG FORM ####################
#################### START ACTION HOOKS ####################

    public function hookActionProductUpdateFromMatriceAfter($params)
    {
        $product = $params['product'];
        if (!Validate::isLoadedObject($product)) {
            return;
        }

        if ((bool) Configuration::get('EVERSEO_WEBP') === true) {
            $allowedFormats = [
                'jpg',
                'jpeg',
                'png'
            ];
            $images = EverPsSeoImage::getAllProductImages($product->id);
            foreach ($images as $i) {
                $image = new Image(
                    (int) $i['id_image']
                );
                $productImages = glob(_PS_PRODUCT_IMG_DIR_ . $image->getImgFolder() . '*');
                foreach ($productImages as $img) {
                    $info = new SplFileInfo(basename($img));
                    if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                        EverPsSeoImage::webpConvert2($img);
                    }
                }
            }
        }
    }

    public function hookActionOutputHTMLBefore($params)
    {
        $shop_url = Configuration::get(
            'PS_SHOP_DOMAIN_SSL',
            null,
            null,
            (int) $this->context->shop->id
        );
        $txt = $params['html'];
        preg_match('/<meta\s+name=["\']description["\']\s+content=["\']([^"\']+)["\']\s*\/?>/i', $txt, $matches);

        if (isset($matches[1])) {
            $defaultValue = $matches[1];
            // Retirer les guillemets du texte
            $defaultValue = str_replace('"', '', $defaultValue);
            $defaultValue = str_replace("'", '', $defaultValue);
            // Retirer le code HTML du texte
            $defaultValue = strip_tags($defaultValue);
        } else {
            $defaultValue = Configuration::get('PS_SHOP_NAME');
        }

        // Utiliser la valeur par défaut si le texte est vide
        if (empty($defaultValue)) {
            $defaultValue = Configuration::get('PS_SHOP_NAME');
        }
        // Replace all shortcodes, everywhere
        if ((bool) $this->context->customer->isLogged()) {
            $txt = EverPsSeoTools::changeFrontShortcodes(
                $txt,
                (int) $this->context->customer->id
            );
        } else {
            $txt = EverPsSeoTools::changeFrontShortcodes(
                $txt
            );
        }
        // Move all scripts to footer
        if ((bool) Configuration::get('EVERSEO_BOTTOM_SCRIPTS') && isset($txt)) {
            $js = '';
            preg_match_all('#<script(.*?)</script>#is', $txt, $matches);
            foreach ($matches[0] as $value) {
                $js .= $value;
            }
            $txt = preg_replace('#<script(.*?)</script>#is', '', $txt);
            $txt = preg_replace('#<body(.*?)</body>#is', '<body$1' . $js . '</body>', $txt);
        }
        // Defer javascript
        if ((bool) Configuration::get('EVERSEO_DEFER')) {
            $txt = str_replace(
                '<script type="text/javascript">',
                '<script type="text/javascript" defer>',
                $txt
            );
            $txt = str_replace(
                '<script src="',
                '<script defer src="',
                $txt
            );
        }
        if ((bool) Configuration::get('EVERSEO_COMPRESS_HTML') === true) {
            $txt = preg_replace(
                [
                    '/ {2,}/',
                    '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s',
                ],
                [
                    ' ',
                    '',
                ],
                $txt
            );
        }
        if ((bool) Configuration::get('EVERSEO_ADD_TITLE') === true) {
            $txt = preg_replace_callback(
                '/<a(?![^>]*\btitle=["\'])(.*?)>(.*?)<\/a>/i',
                function($matches) use ($defaultValue) {
                    $attributes = $matches[1];
                    $linkText = trim(preg_replace('/\s+/', ' ', $matches[2]));
                    if (preg_match('/<img.*>/', $matches[0])) {
                        $attributes .= ' title="' . htmlspecialchars($defaultValue, ENT_QUOTES) . '"';
                    } elseif (!preg_match('/\btitle=["\']/', $attributes)) {
                        $altValue = htmlspecialchars(strip_tags($linkText) . ' | ' . Configuration::get('PS_SHOP_NAME'), ENT_QUOTES);
                        $attributes .= ' title="' . trim($altValue) . '"';
                    }
                    return '<a' . $attributes . ' data-everseo="1">' . $matches[2] . '</a>';
                },
                $txt
            );
        }

        if ((bool) Configuration::get('EVERSEO_ADD_ALT') === true) {
            $txt = preg_replace_callback(
                '/<img(?:\s+[^>]*)?>/i',
                function($matches) use ($defaultValue) {
                    $imgTag = $matches[0];
                    $altValue = '';
                    preg_match('/\balt\s*=\s*(["\'])(.*?)\1/i', $imgTag, $altMatches);
                    if (!empty($altMatches[2])) {
                        $altValue = ' alt="' . htmlspecialchars($altMatches[2], ENT_QUOTES) . '"';
                        $imgTag = preg_replace('/\balt\s*=\s*(["\'])(.*?)\1/i', $altValue, $imgTag);
                    } else {
                        $altValue = ' alt="' . htmlspecialchars($defaultValue, ENT_QUOTES) . '" title="' . htmlspecialchars($defaultValue, ENT_QUOTES) . '"';
                        $imgTag = str_replace('<img', '<img' . $altValue . ' data-everseo="1"', $imgTag);
                    }
                    return $imgTag;
                },
                $txt
            );
        }

        if ((bool) Configuration::get('EVERSEO_EXTERNAL_NOFOLLOW') === true) {
            // Utilisation de DOMDocument pour modifier tous les liens dans la variable $txt
            $doc = new \DOMDocument();
            @$doc->loadHTML(mb_convert_encoding($txt, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $links = $doc->getElementsByTagName('a');
            foreach ($links as $link) {
                $href = $link->getAttribute('href');
                // Vérifier si l'URL pointe vers un autre domaine
                if ($href && (strpos($href, 'http://') === 0 || strpos($href, 'https://') === 0)) {
                    $parts = parse_url($href);
                    if ($parts && isset($parts['host']) && $parts['host'] !== Tools::getHttpHost(false, true)) {
                        // Ajouter les attributs rel="nofollow" et target="_blank" à la balise <a>
                        $link->setAttribute('rel', 'nofollow');
                        $link->setAttribute('target', '_blank');
                        $link->setAttribute('data-everseo', '1');
                    }
                }
            }
            // Récupérer le contenu HTML modifié à partir de DOMDocument
            $txt = $doc->saveHTML();
        }
        if ((bool) Configuration::get('EVERSEO_WEBP') === true) {
            preg_match_all('/<img[^>]+?(?:src|data-src)=["\'](?P<src>.+?)["\'][^>]*?>/i', $txt, $matches);
            $image_urls = $matches['src'];

            foreach ($image_urls as $src) {
                $extension = pathinfo($src, PATHINFO_EXTENSION);

                if ($extension != 'webp' && $extension != 'svg' && $extension != 'gif') {
                    // Remplacer l'extension de l'image par .webp
                    $webp_url = preg_replace('/\.[^.]+$/', '.webp', $src);

                    // Remplacer l'URL d'image par l'URL en format webp
                    if (strpos($webp_url, Tools::getHttpHost(true) . __PS_BASE_URI__) !== 0) {
                        $webp_url_rel = str_replace(Tools::getHttpHost(true) . __PS_BASE_URI__, '', $webp_url);
                    } else {
                        $webp_url_rel = $webp_url;
                    }
                    if (strpos($webp_url, 'http://') !== 0 && strpos($webp_url, 'https://') !== 0) {
                        $webp_url = 'https://' . $webp_url;
                    }

                    $txt = str_replace($src, $webp_url_rel, $txt);
                }
            }
        }
        $txt = '<!-- optimized by Ever SEO -->'
        . trim($txt)
        . '<!-- optimized by Ever SEO -->';
        $params['html'] = $txt;
    }

    public function hookActionObjectProductAddAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        $allowedLangs = $this->getAllowedShortcodesLangs(
            'EVERSEO_AUTO_PRODUCT_LANGS'
        );
        foreach (Shop::getShops(false, null, true) as $id_shop) {
            foreach (Language::getLanguages(false) as $language) {
                $this->addElementInTable(
                    'ever_seo_product',
                    'id_seo_product',
                    (int) $params['object']->id,
                    (int) $id_shop,
                    (int) $language['id_lang']
                );
                if (isset($params['object']->name[$language['id_lang']])
                    && !empty($params['object']->name[$language['id_lang']])
                ) {
                    if (in_array((int) $language['id_lang'], $allowedLangs)) {
                        $this->autoSetTitle(
                            'id_seo_product',
                            (int) $params['object']->id,
                            (int) $id_shop,
                            (int) $language['id_lang']
                        );
                        $this->autoSetDescription(
                            'id_seo_product',
                            (int) $params['object']->id,
                            (int) $id_shop,
                            (int) $language['id_lang']
                        );
                    }
                }
            }
        }
    }

    public function hookActionAdminMetaAfterWriteRobotsFile($params)
    {
        $indexes = EverPsSeoSitemap::getSitemapIndexes();
        $allowSitemap = 'Disallow: /order-follow'
                        ."\r\n".
                        'Disallow: /guest-tracking'
                        ."\r\n".
                        'Disallow: /recherche';
        // Panda theme uses random int on css file parameter
        $allowSitemap = 'Disallow: /modules/stthemeeditor/views/css'
                            ."\r\n";
        $allowSitemap .= "\n";
        $allowSitemap .= Configuration::get('EVERSEO_ROBOTS_TXT');
        if ($indexes) {
            foreach ($indexes as $index) {
                $allowSitemap .= 'Sitemap: '
                .$index
                ."\r\n";
            }
        }
        fwrite($params['write_fd'], "#Rules from everpsseo\n");
        fwrite($params['write_fd'], $allowSitemap);
    }

    public function hookActionHtaccessCreate()
    {
        $baseDomain = Tools::getHttpHost();
        $rules = Configuration::get('EVERHTACCESS'). "\n\n";
        $prepend_rules = Configuration::get('EVERHTACCESS_PREPEND'). "\n\n";
        if ((bool) Configuration::get('EVERHTACCESS_404') === true) {
            $everseo_redirects = EverPsSeoRedirect::getRedirects(
                (int) Context::getContext()->shop->id
            );
            foreach ($everseo_redirects as $redirect) {
                switch ((int) $redirect->code) {
                    case 301:
                        $prepend_rules .= 'Redirect permanent ' . $redirect->not_found . ' ' . $redirect->redirection . "\n\n";
                        break;

                    case 302:
                        $prepend_rules .= 'Redirect ' . $redirect->not_found . ' ' . $redirect->redirection . "\n\n";
                        break;

                    case 303:
                        # code...
                        break;

                    default:
                        # code...
                        break;
                }
            }
        }
        if ((bool) Configuration::get('EVERSEO_LOCK_PDF') === true) {
            $rules .= '#Noindex PDF
<Files ~ "\.pdf$">
  Header set X-Robots-Tag "noindex"
</Files>
' . "\n\n";
        }
        if ((bool) Configuration::get('EVERSEO_LOCK_XLSX') === true) {
            $rules .= '#Noindex XLSX
<Files ~ "\.xslx$">
  Header set X-Robots-Tag "noindex"
</Files>
' . "\n\n";
        }
        if ((bool) Configuration::get('EVERSEO_LOCK_CSV') === true) {
            $rules .= '#Noindex XLSX
<Files ~ "\.csv$">
  Header set X-Robots-Tag "noindex"
</Files>
' . "\n\n";
        }
        if ((bool) Configuration::get('EVERSEO_LOCK_WORD') === true) {
            $rules .= '#Noindex XLSX
<Files ~ "\.word$">
  Header set X-Robots-Tag "noindex"
</Files>
' . "\n\n";
        }
        if ((bool) Configuration::get('EVERSEO_CLICKJACKING') === true) {
            $rules .= '<IfModule mod_headers.c>
Header always append X-Frame-Options SAMEORIGIN
</IfModule>' . "\n\n";
        }
        if ((bool) Configuration::get('EVERSEO_HOTLINKING')) {
            $rules .= '# Team-Ever hotlinks protection
RewriteCond %{HTTP_REFERER} !^$
RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?' . $baseDomain . ' [NC]
RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?google.com [NC]
RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?facebook.com [NC]
RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?twitter.com [NC]
RewriteRule \.(jpg|jpeg|png|gif)$ - [F,NC]' . "\n\n";
        }
        if ((bool) Configuration::get('EVERSEO_DEFLATE')) {
            $rules .= '<IfModule mod_deflate.c>
  # Compress HTML, CSS, JavaScript, Text, XML and fonts
  AddOutputFilterByType DEFLATE application/javascript
  AddOutputFilterByType DEFLATE application/rss+xml
  AddOutputFilterByType DEFLATE application/vnd.ms-fontobject
  AddOutputFilterByType DEFLATE application/x-font
  AddOutputFilterByType DEFLATE application/x-font-opentype
  AddOutputFilterByType DEFLATE application/x-font-otf
  AddOutputFilterByType DEFLATE application/x-font-truetype
  AddOutputFilterByType DEFLATE application/x-font-ttf
  AddOutputFilterByType DEFLATE application/x-javascript
  AddOutputFilterByType DEFLATE application/xhtml+xml
  AddOutputFilterByType DEFLATE application/xml
  AddOutputFilterByType DEFLATE application/json
  AddOutputFilterByType DEFLATE font/opentype
  AddOutputFilterByType DEFLATE font/otf
  AddOutputFilterByType DEFLATE font/ttf
  AddOutputFilterByType DEFLATE image/svg+xml
  AddOutputFilterByType DEFLATE image/x-icon
  AddOutputFilterByType DEFLATE text/css
  AddOutputFilterByType DEFLATE text/html
  AddOutputFilterByType DEFLATE text/javascript
  AddOutputFilterByType DEFLATE text/plain
  AddOutputFilterByType DEFLATE text/xml

  # Remove browser bugs (only needed for really old browsers)
  BrowserMatch ^Mozilla/4 gzip-only-text/html
  BrowserMatch ^Mozilla/4\.0[678] no-gzip
  BrowserMatch \bMSIE !no-gzip !gzip-only-text/html
  Header append Vary User-Agent
</IfModule>' . "\n\n";
            $rules .= '# BEGIN Expire headers
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresDefault "access plus 7200 seconds"
  ExpiresByType image/jpg "access plus 2592000 seconds"
  ExpiresByType image/jpeg "access plus 2592000 seconds"
  ExpiresByType image/png "access plus 2592000 seconds"
  ExpiresByType image/gif "access plus 2592000 seconds"
  AddType image/x-icon .ico
  ExpiresByType image/ico "access plus 2592000 seconds"
  ExpiresByType image/icon "access plus 2592000 seconds"
  ExpiresByType image/x-icon "access plus 2592000 seconds"
  ExpiresByType text/css "access plus 2592000 seconds"
  ExpiresByType text/javascript "access plus 2592000 seconds"
  ExpiresByType text/html "access plus 7200 seconds"
  ExpiresByType application/xhtml+xml "access plus 7200 seconds"
  ExpiresByType application/javascript A2592000
  ExpiresByType application/x-javascript "access plus 2592000 seconds"
  ExpiresByType application/x-shockwave-flash "access plus 2592000 seconds"
</IfModule>
# END Expire headers' . "\n\n";
        }
        if ((bool) Configuration::get('EVERSEO_WEBP') === true) {
            $rules .= '# Images
RewriteCond %{HTTP_HOST} ^' . $baseDomain . '$
RewriteRule ^([0-9])(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/p/$1/$1$2$3.webp [L]
RewriteCond %{HTTP_HOST} ^' . $baseDomain . '$
RewriteRule ^([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/p/$1/$2/$1$2$3$4.webp [L]
RewriteCond %{HTTP_HOST} ^' . $baseDomain . '$
RewriteRule ^([0-9])([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/p/$1/$2/$3/$1$2$3$4$5.webp [L]
RewriteCond %{HTTP_HOST} ^' . $baseDomain . '$
RewriteRule ^([0-9])([0-9])([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/p/$1/$2/$3/$4/$1$2$3$4$5$6.webp [L]
RewriteCond %{HTTP_HOST} ^' . $baseDomain . '$
RewriteRule ^([0-9])([0-9])([0-9])([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/p/$1/$2/$3/$4/$5/$1$2$3$4$5$6$7.webp [L]
RewriteCond %{HTTP_HOST} ^' . $baseDomain . '$
RewriteRule ^([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/p/$1/$2/$3/$4/$5/$6/$1$2$3$4$5$6$7$8.webp [L]
RewriteCond %{HTTP_HOST} ^' . $baseDomain . '$
RewriteRule ^([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/p/$1/$2/$3/$4/$5/$6/$7/$1$2$3$4$5$6$7$8$9.webp [L]
RewriteCond %{HTTP_HOST} ^' . $baseDomain . '$
RewriteRule ^c/([0-9]+)(\-[\.*_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/c/$1$2$3.webp [L]
RewriteCond %{HTTP_HOST} ^' . $baseDomain . '$
RewriteRule ^c/([a-zA-Z_-]+)(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/c/$1$2.webp [L]' . "\n\n";
        }
        $path = _PS_ROOT_DIR_ . '/.htaccess';
        $specific_before = $specific_after = '';
        if (file_exists($path)) {
            $content = Tools::file_get_contents($path);
            if (preg_match('#^(.*)\# ~~everstart~~.*\# ~~everend~~[^\n]*(.*)$#s', $content, $m)) {
                $specific_before = $m[1];
                $specific_after = $m[2];
            } else {
                $specific_before = $content;
            }
        }
        if (preg_match('#^(.*)\# ~~everprependstart~~.*\# ~~everprependend~~[^\n]*(.*)$#s', $specific_after, $m)) {
            $specific_after = $m[1];
        } else {
            $specific_after = $content;
        }
        if (!$write_fd = @fopen($path, 'w')) {
            return false;
        }
        fwrite($write_fd, "# ~~everstart~~ Do not remove this comment, Ever SEO uses it\n");
        fwrite($write_fd, trim($rules));
        fwrite($write_fd, "\n# ~~everend~~ Do not remove this comment, Ever SEO uses it\n");
        // prepend Ever SEO rules
        if ($specific_before) {
            fwrite($write_fd, trim($specific_before) . "\n\n");
        }
        if ($specific_after) {
            fwrite($write_fd, trim($specific_after) . "\n\n");
        }
        fwrite($write_fd, "# ~~everprependstart~~ Do not remove this comment, Ever SEO uses it\n");
        fwrite($write_fd, trim($prepend_rules));
        fwrite($write_fd, "\n# ~~everprependend~~ Do not remove this comment, Ever SEO uses it\n");
        fclose($write_fd);
    }

    public function hookActionObjectLanguageAddAfter($params)
    {
        $id_shop = (int) $this->context->shop->id;
        $id_seo_lang = (int) $params['object']->id;
        try {
            Db::getInstance()->insert(
                'ever_seo_lang',
                [
                    'id_seo_lang' => (int) $id_seo_lang,
                    'id_shop' => (int) $id_shop,
                    'iso_code' => pSQL($params['object']->iso_code),
                    'language_code' => pSQL($params['object']->language_code),
                ]
            );
            $this->updateSeoTables((int) $id_shop, (int) $id_seo_lang);
        } catch (Exception $e) {
            PrestaShopLogger::addLog('can\'t add SEO lang ' . (int) $params['object']->id);
        }
    }

    public function hookActionObjectCategoryAddAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        $allowedLangs = $this->getAllowedShortcodesLangs(
            'EVERSEO_AUTO_CATEGORY_LANGS'
        );
        foreach (Shop::getShops(false, null, true) as $id_shop) {
            foreach (Language::getLanguages(false) as $language) {
                $this->addElementInTable(
                    'ever_seo_category',
                    'id_seo_category',
                    (int) $params['object']->id,
                    (int) $id_shop,
                    (int) $language['id_lang']
                );
                if (in_array((int) $language['id_lang'], $allowedLangs)) {
                    $this->autoSetTitle(
                        'id_seo_category',
                        (int) $params['object']->id,
                        (int) $id_shop,
                        (int) $language['id_lang']
                    );
                    $this->autoSetDescription(
                        'id_seo_category',
                        (int) $params['object']->id,
                        (int) $id_shop,
                        (int) $language['id_lang']
                    );
                }
            }
        }
    }

    public function hookActionObjectCmsAddAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        $allowedLangs = $this->getAllowedShortcodesLangs(
            'EVERSEO_AUTO_CMS_LANGS'
        );
        foreach (Shop::getShops(false, null, true) as $id_shop) {
            foreach (Language::getLanguages(false) as $language) {
                $this->addElementInTable(
                    'ever_seo_cms',
                    'id_seo_cms',
                    (int) $params['object']->id,
                    (int) $id_shop,
                    (int) $language['id_lang']
                );
                if (in_array((int) $language['id_lang'], $allowedLangs)) {
                    $this->autoSetTitle(
                        'id_seo_cms',
                        (int) $params['object']->id,
                        (int) $id_shop,
                        (int) $language['id_lang']
                    );
                    $this->autoSetDescription(
                        'id_seo_cms',
                        (int) $params['object']->id,
                        (int) $id_shop,
                        (int) $language['id_lang']
                    );
                }
            }
        }
    }

    public function hookActionObjectManufacturerAddAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        $allowedLangs = $this->getAllowedShortcodesLangs(
            'EVERSEO_AUTO_MANUFACTURER_LANGS'
        );
        foreach (Shop::getShops(false, null, true) as $id_shop) {
            foreach (Language::getLanguages(false) as $language) {
                $this->addElementInTable(
                    'ever_seo_manufacturer',
                    'id_seo_manufacturer',
                    (int) $params['object']->id,
                    (int) $id_shop,
                    (int) $language['id_lang']
                );
                if (in_array((int) $language['id_lang'], $allowedLangs)) {
                    $this->autoSetTitle(
                        'id_seo_manufacturer',
                        (int) $params['object']->id,
                        (int) $id_shop,
                        (int) $language['id_lang']
                    );
                    $this->autoSetDescription(
                        'id_seo_manufacturer',
                        (int) $params['object']->id,
                        (int) $id_shop,
                        (int) $language['id_lang']
                    );
                }
            }
        }
    }

    public function hookActionObjectSupplierAddAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        $allowedLangs = $this->getAllowedShortcodesLangs(
            'EVERSEO_AUTO_SUPPLIER_LANGS'
        );
        foreach (Shop::getShops(false, null, true) as $id_shop) {
            foreach (Language::getLanguages(false) as $language) {
                $this->addElementInTable(
                    'ever_seo_supplier',
                    'id_seo_supplier',
                    (int) $params['object']->id,
                    (int) $id_shop,
                    (int) $language['id_lang']
                );
                if (in_array((int) $language['id_lang'], $allowedLangs)) {
                    $this->autoSetTitle(
                        'id_seo_supplier',
                        (int) $params['object']->id,
                        (int) $id_shop,
                        (int) $language['id_lang']
                    );
                    $this->autoSetDescription(
                        'id_seo_supplier',
                        (int) $params['object']->id,
                        (int) $id_shop,
                        (int) $language['id_lang']
                    );
                }
            }
        }
    }

    public function hookActionObjectImageAddAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        $image = $params['object'];

        if ((bool) Configuration::get('EVERSEO_WEBP') === true) {
            $allowedFormats = [
                'jpg',
                'jpeg',
                'png'
            ];
            $productImages = glob(_PS_PRODUCT_IMG_DIR_ . $image->getImgFolder() . '*');
            foreach ($productImages as $img) {
                $info = new SplFileInfo(basename($img));
                if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                    EverPsSeoImage::webpConvert2($img);
                }
            }
        }
        foreach (Language::getLanguages(false) as $lang) {
            $seoImage = EverPsSeoImage::getSeoImage(
                (int) $image->id,
                (int) $this->context->shop->id,
                (int) $lang['id_lang']
            );

            if (!$seoImage) {
                $seoImage = new EverPsSeoImage();
                $alt =  EverPsSeoImage::changeImageAltShortcodes(
                    (int) $image->id,
                    (int) $lang['id_lang'],
                    (int) Context::getContext()->shop->id
                );
                if (empty($alt)) {
                    continue;
                }
                $legend = Tools::substr(
                    strip_tags($alt),
                    0,
                    128
                );
                $seoImage->alt = $legend;
                $seoImage->id_shop = (int) Context::getContext()->shop->id;
                $seoImage->id_seo_lang = (int) $lang['id_lang'];
                $seoImage->id_seo_product = (int) $image->id_product;
                $seoImage->id_seo_img = (int) $image->id;
                $seoImage->allowed_sitemap = true;
                $seoImage->save();
            }
        }
    }

    public function hookActionObjectLanguageDeleteAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        $this->deleteElementFromTable(
            'ever_seo_lang',
            'id_seo_lang',
            (int) $params['object']->id
        );
    }

    public function hookActionObjectProductDeleteAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        $this->deleteElementFromTable(
            'ever_seo_product',
            'id_seo_product',
            (int) $params['object']->id
        );
    }

    public function hookActionObjectCategoryDeleteAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        $this->deleteElementFromTable(
            'ever_seo_category',
            'id_seo_category',
            (int) $params['object']->id
        );
    }

    public function hookActionObjectCmsDeleteAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        $this->deleteElementFromTable(
            'ever_seo_cms',
            'id_seo_cms',
            (int) $params['object']->id
        );
    }

    public function hookActionObjectManufacturerDeleteAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        $this->deleteElementFromTable(
            'ever_seo_manufacturer',
            'id_seo_manufacturer',
            (int) $params['object']->id
        );
    }

    public function hookActionObjectSupplierDeleteAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        $this->deleteElementFromTable(
            'ever_seo_supplier',
            'id_seo_supplier',
            (int) $params['object']->id
        );
    }

    public function hookActionObjectImageDeleteAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        $image = $params['object'];
        $productImages = glob(_PS_PRODUCT_IMG_DIR_ . $image->getImgFolder() . '*');
        foreach ($productImages as $img) {
            $info = new SplFileInfo(basename($img));
            if (is_file($img) && $info->getExtension() == 'webp') {
                unlink($img);
            }
        }
        $this->deleteElementFromTable(
            'ever_seo_image',
            'id_seo_img',
            (int) $params['object']->id
        );
    }

    public function hookActionChangeSeoShortcodes($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        $shortcodes = EverPsSeoShortcode::getAllSeoShortcodes(
            (int) Context::getContext()->shop->id,
            (int) Context::getContext()->language->id
        );
        if (count($shortcodes) > 0) {
            foreach ($shortcodes as $shortcode) {
                $params['content'] = str_replace(
                    pSQL($shortcode->shortcode),
                    pSQL($shortcode->content),
                    pSQL($params['content'], true)
                );
            }
        }
        return $params['content'];
    }

#################### END ACTION HOOKS ####################
#################### START UPDATE HOOKS ####################

    public function hookActionObjectProductUpdateAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        foreach (Language::getLanguages(false) as $lang) {
            $seo_product = EverPsSeoProduct::getSeoProduct(
                (int) $params['object']->id,
                (int) $this->context->shop->id,
                (int) $lang['id_lang']
            );
            if (!Validate::isLoadedObject($seo_product)) {
                $seo_product = new EverPsSeoProduct();
            }
            $product = new Product(
                (int) $params['object']->id,
                false,
                (int) $lang['id_lang'],
                (int) $this->context->shop->id
            );
            EverPsSeoTools::updateDatabaseMediasToWebP(
                'product',
                $product->id,
                $product->description,
                (int) $lang['id_lang'],
                'description'
            );
            EverPsSeoTools::updateDatabaseMediasToWebP(
                'product',
                $product->id,
                $product->description_short,
                (int) $lang['id_lang'],
                'description_short'
            );
            $seo_product->id_seo_product = (int) $params['object']->id;
            $seo_product->id_shop = (int) $this->context->shop->id;
            $seo_product->id_seo_lang = (int) $lang['id_lang'];
            if (empty($product->meta_title)) {
                $this->autoSetTitle(
                    'id_seo_product',
                    (int) $params['object']->id,
                    (int) $this->context->shop->id,
                    (int) $lang['id_lang']
                );
            } else {
                $seo_product->meta_title = $product->meta_title;
            }
            if (empty($product->meta_description)) {
                $this->autoSetDescription(
                    'id_seo_product',
                    (int) $params['object']->id,
                    (int) $this->context->shop->id,
                    (int) $lang['id_lang']
                );
            } else {
                $seo_product->meta_description = $product->meta_description;
            }
            $seo_product->link_rewrite = $product->link_rewrite;
            if ((bool) $product->active === false
                && (empty($product->redirect_type) || $product->redirect_type == '404')
            ) {
                EverPsSeoProduct::inactiveRedirect(
                    (int) $seo_product->id_seo_product,
                    (int) $this->context->shop->id
                );
            }
            $seo_product->save();
        }
    }

    public function hookActionObjectCategoryUpdateAfter($params)
    {
        // If fucking Elementor is rewriting object from FO
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        foreach (Language::getLanguages(false) as $lang) {
            $seoCategory = EverPsSeoCategory::getSeoCategory(
                (int) $params['object']->id,
                (int) $this->context->shop->id,
                (int) $lang['id_lang']
            );
            if (!$seoCategory) {
                $seoCategory = new EverPsSeoCategory();
            }
            $category = new Category(
                (int) $params['object']->id,
                (int) $lang['id_lang'],
                (int) $this->context->shop->id
            );
            EverPsSeoTools::updateDatabaseMediasToWebP(
                'category',
                $category->id,
                $category->description,
                (int) $lang['id_lang']
            );
            $seoCategory->id_seo_category = (int) $params['object']->id;
            $seoCategory->id_shop = (int) $this->context->shop->id;
            $seoCategory->id_seo_lang = (int) $lang['id_lang'];
            $seoCategory->meta_title = $category->meta_title;
            $seoCategory->meta_description = $category->meta_description;
            $seoCategory->link_rewrite = $category->link_rewrite;
            $seoCategory->save();
        }
    }

    public function hookActionObjectImageUpdateAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        $image = $params['object'];
        $allowedFormats = [
            'jpg',
            'jpeg',
            'png'
        ];
        if ((bool) Configuration::get('EVERSEO_WEBP') === true) {
            $productImages = glob(_PS_PRODUCT_IMG_DIR_ . $image->getImgFolder() . '*');
            foreach ($productImages as $img) {
                $info = new SplFileInfo(basename($img));
                if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                    EverPsSeoImage::webpConvert2($img);
                }
            }
        }
        foreach (Language::getLanguages(false) as $lang) {
            $seoImage = EverPsSeoImage::getSeoImage(
                (int) $image->id,
                (int) $this->context->shop->id,
                (int) $lang['id_lang']
            );

            if (!$seoImage) {
                $seoImage = new EverPsSeoImage();
                $alt =  EverPsSeoImage::changeImageAltShortcodes(
                    (int) $image->id,
                    (int) $lang['id_lang'],
                    (int) Context::getContext()->shop->id
                );
                if (empty($alt)) {
                    continue;
                }
                $legend = Tools::substr(
                    strip_tags($alt),
                    0,
                    128
                );
                $seoImage->alt = $legend;
                $seoImage->id_shop = (int) $this->context->shop->id;
                $seoImage->id_seo_lang = (int) $lang['id_lang'];
                $seoImage->id_seo_product = (int) $image->id_product;
                $seoImage->id_seo_img = (int) $image->id;
                $seoImage->allowed_sitemap = true;
                $seoImage->save();
            }
        }
    }

    public function hookActionObjectCmsUpdateAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        foreach (Language::getLanguages(false) as $lang) {
            $seoCms = EverPsSeoCms::getSeoCms(
                (int) $params['object']->id,
                (int) $this->context->shop->id,
                (int) $lang['id_lang']
            );
            if (!$seoCms) {
                $seoCms = new EverPsSeoCms();
            }
            $cms = new CMS(
                (int) $params['object']->id,
                (int) $this->context->shop->id,
                (int) $lang['id_lang']
            );
            EverPsSeoTools::updateDatabaseMediasToWebP(
                'cms',
                $cms->id,
                $cms->content,
                (int) $lang['id_lang']
            );
            $seoCms->id_seo_cms = (int) $params['object']->id;
            $seoCms->id_shop = (int) $this->context->shop->id;
            $seoCms->id_seo_lang = (int) $lang['id_lang'];
            $seoCms->meta_title = $cms->meta_title;
            $seoCms->meta_description = $cms->meta_description;
            $seoCms->link_rewrite = $cms->link_rewrite;
            $seoCms->allowed_sitemap = $cms->indexation;
            $seoCms->save();
        }
    }

    public function hookActionObjectCmsCategoryUpdateAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        foreach (Language::getLanguages(false) as $lang) {
            $seoCmsCategory = EverPsSeoCmsCategory::getSeoCmsCategory(
                (int) $params['object']->id,
                (int) $this->context->shop->id,
                (int) $lang['id_lang']
            );
            if (!$seoCmsCategory) {
                $seoCmsCategory = new getSeoCmsCategory();
            }
            $cmsCategory = new CMSCategory(
                (int) $params['object']->id,
                (int) $this->context->shop->id,
                (int) $lang['id_lang']
            );
            $seoCmsCategory->id_seo_cms_category = (int) $params['object']->id;
            $seoCmsCategory->id_shop = (int) $this->context->shop->id;
            $seoCmsCategory->id_seo_lang = (int) $lang['id_lang'];
            $seoCmsCategory->meta_title = $cmsCategory->meta_title;
            $seoCmsCategory->meta_description = $cmsCategory->meta_description;
            $seoCmsCategory->link_rewrite = $cmsCategory->link_rewrite;
            $seoCmsCategory->allowed_sitemap = $cmsCategory->indexation;
            $seoCmsCategory->save();
        }
    }

    public function hookActionObjectManufacturerUpdateAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        foreach (Language::getLanguages(false) as $lang) {
            $seoManufacturer = EverPsSeoManufacturer::getSeoManufacturer(
                (int) $params['object']->id,
                (int) $this->context->shop->id,
                (int) $lang['id_lang']
            );
            if (!$seoManufacturer) {
                $seoManufacturer = new EverPsSeoManufacturer();
            }
            $manufacturer = new Manufacturer(
                (int) $params['object']->id,
                (int) $lang['id_lang']
            );
            EverPsSeoTools::updateDatabaseMediasToWebP(
                'manufacturer',
                $manufacturer->id,
                $manufacturer->description,
                (int) $lang['id_lang']
            );
            $seoManufacturer->id_seo_manufacturer = (int) $params['object']->id;
            $seoManufacturer->id_shop = (int) $this->context->shop->id;
            $seoManufacturer->id_seo_lang = (int) $lang['id_lang'];
            $seoManufacturer->meta_title = $manufacturer->meta_title;
            $seoManufacturer->meta_description != $manufacturer->meta_description;
            $seoManufacturer->link_rewrite = $manufacturer->link_rewrite;
            $seoManufacturer->save();
        }
    }

    public function hookActionObjectSupplierUpdateAfter($params)
    {
        if ((bool)EverPsSeoTools::isAdminController() === false) {
            return;
        }
        foreach (Language::getLanguages(false) as $lang) {
            $seoSupplier = EverPsSeoSupplier::getSeoSupplier(
                (int) $params['object']->id,
                (int) $this->context->shop->id,
                (int) $lang['id_lang']
            );
            if (!$seoSupplier) {
                $seoSupplier = new EverPsSeoSupplier();
            }
            $supplier = new Supplier(
                (int) $params['object']->id,
                (int) $lang['id_lang']
            );
            EverPsSeoTools::updateDatabaseMediasToWebP(
                'supplier',
                $supplier->id,
                $supplier->description,
                (int) $lang['id_lang']
            );
            $seoSupplier->id_seo_supplier = (int) $params['object']->id;
            $seoSupplier->id_shop = (int) $this->context->shop->id;
            $seoSupplier->id_seo_lang = (int) $lang['id_lang'];
            $seoSupplier->meta_title = $supplier->meta_title;
            $seoSupplier->meta_description = $supplier->meta_description;
            $seoSupplier->link_rewrite = $supplier->link_rewrite;
            // $seoSupplier->indexable = $supplier->active;
            // $seoSupplier->allowed_sitemap = $supplier->active;
            $seoSupplier->save();
        }
    }

#################### END UPDATE HOOKS ####################
#################### START OBJECT ADD/DELETE ####################

    protected function updateSeoTables($id_shop, $id_seo_lang)
    {
        $seotable = $this->getEverSeoTables();
        foreach ($seotable as $table) {
            $seoObj = $this->getSeoObjByPsTable($table);
            $update = '
                INSERT INTO '. _DB_PREFIX_ . pSQL($table) . ' (
                    ' . pSQL($seoObj) . ',
                    id_shop,
                    id_seo_lang
                )
                SELECT
                    ' . pSQL($seoObj) . ',
                    ' . (int) $id_shop . ',
                    ' . (int) $id_seo_lang . '
                FROM '. _DB_PREFIX_ . pSQL($table) . '
            ';
            try {
                Db::getInstance()->Execute($update);
            } catch (Exception $e) {
                PrestaShopLogger::addLog(
                    'can\'t update SEO tables'
                );
            }
        }
    }

    protected function addElementInTable($table, $object, $id_element, $id_shop, $id_lang)
    {
        return Db::getInstance()->insert(
            $table,
            [
                $object => (int) $id_element,
                'id_shop' => (int) $id_shop,
                'id_seo_lang' => (int) $id_lang,
            ]
        );
    }

    protected function autoSetTitle($object, $id_element, $id_shop, $id_lang)
    {
        switch ($object) {
            case 'id_seo_product':
                $meta_title = EverPsSeoProduct::changeProductTitleShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                $meta_title = Tools::substr($meta_title, 0, 128);

                $sql = 'UPDATE `' . _DB_PREFIX_ . 'product_lang`
                SET meta_title = "' . pSQL($meta_title) . '"
                WHERE id_lang = ' . (int) $id_lang . '
                AND id_shop = ' . (int) $id_shop . '
                AND id_product = ' . (int) $id_element;

                $sql2 = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_product`
                SET meta_title = "' . pSQL($meta_title) . '"
                WHERE id_seo_lang = ' . (int) $id_lang . '
                AND id_shop = ' . (int) $this->context->shop->id . '
                AND id_seo_product = ' . (int) $id_element;
                if (!Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_category':
                $meta_title = EverPsSeoCategory::changeCategoryTitleShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                $meta_title = Tools::substr($meta_title, 0, 128);

                $sql = 'UPDATE `' . _DB_PREFIX_ . 'category_lang`
                SET meta_title = "' . pSQL($meta_title) . '"
                WHERE id_lang = ' . (int) $id_lang . '
                AND id_shop = ' . (int) $id_shop . '
                AND id_category = ' . (int) $id_element;

                $sql2 = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_category`
                SET meta_title = "' . pSQL($meta_title) . '"
                WHERE id_seo_lang = ' . (int) $id_lang . '
                AND id_shop = ' . (int) $id_shop . '
                AND id_seo_category = ' . (int) $id_element;
                if (!Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_cms':
                $meta_title = EverPsSeoCms::changeCmsTitleShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                $meta_title = Tools::substr(pSQL($meta_title), 0, 128);

                $sql = 'UPDATE `' . _DB_PREFIX_ . 'cms_lang`
                SET meta_title = "' . pSQL($meta_title) . '"
                WHERE id_lang = ' . (int) $id_lang . '
                AND id_shop = ' . (int) $id_shop . '
                AND id_cms = ' . (int) $id_element;

                $sql2 = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_cms`
                SET meta_title = "' . pSQL($meta_title) . '"
                WHERE id_seo_lang = ' . (int) $id_lang . '
                AND id_shop = ' . (int) $id_shop . '
                AND id_seo_cms = ' . (int) $id_element;
                if (!Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_manufacturer':
                $meta_title = EverPsSeoManufacturer::changeManufacturerTitleShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                $meta_title = Tools::substr(pSQL($meta_title), 0, 128);

                $sql = 'UPDATE `' . _DB_PREFIX_ . 'manufacturer_lang`
                SET meta_title = "' . pSQL($meta_title) . '"
                WHERE id_lang = ' . (int) $id_lang . '
                AND id_manufacturer = ' . (int) $id_element;

                $sql2 = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_manufacturer`
                SET meta_title = "' . pSQL($meta_title) . '"
                WHERE id_seo_lang = ' . (int) $id_lang . '
                AND id_shop = ' . (int) $id_shop . '
                AND id_seo_manufacturer = ' . (int) $id_element;
                if (!Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_supplier':
                $meta_title = EverPsSeoSupplier::changeSupplierTitleShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                $meta_title = Tools::substr($meta_title, 0, 128);

                $sql = 'UPDATE `' . _DB_PREFIX_ . 'supplier_lang`
                SET meta_title = "' . pSQL($meta_title) . '"
                WHERE id_lang = ' . (int) $id_lang . '
                AND id_supplier = ' . (int) $id_element;

                $sql2 = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_supplier`
                SET meta_title = "' . pSQL($meta_title) . '"
                WHERE id_seo_lang = ' . (int) $id_lang . '
                AND id_shop = ' . (int) $id_shop . '
                AND id_seo_supplier = ' . (int) $id_element;
                if (!Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_pagemeta':
                $meta_title = EverPsSeoPageMeta::changePagemetaTitleShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                $pageMeta = new Meta(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                $pageMeta->title = Tools::substr(pSQL($meta_title), 0, 128);
                // TODO : use SQL query
                // if ($pageMeta->save()) {
                //     return true;
                // }
                break;
        }
    }

    protected function autoSetDescription($object, $id_element, $id_shop, $id_lang)
    {
        switch ($object) {
            case 'id_seo_product':
                $meta_description = EverPsSeoProduct::changeProductMetadescShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                $meta_description = Tools::substr($meta_description, 0, 250);

                $sql = 'UPDATE `' . _DB_PREFIX_ . 'product_lang`
                SET meta_description = "' . pSQL($meta_description) . '"
                WHERE id_lang = ' . (int) $id_lang . '
                AND id_shop = ' . (int) $id_shop . '
                AND id_product = ' . (int) $id_element;

                $sql2 = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_product`
                SET meta_description = "' . pSQL($meta_description) . '"
                WHERE id_seo_lang = ' . (int) $id_lang . '
                AND id_shop = ' . (int) $id_shop . '
                AND id_seo_product = ' . (int) $id_element;
                if (!Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_category':
                $meta_description = EverPsSeoCategory::changeCategoryMetadescShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                $meta_description = Tools::substr($meta_description, 0, 250);

                $sql = 'UPDATE `' . _DB_PREFIX_ . 'category_lang`
                SET meta_description = "' . pSQL($meta_description) . '"
                WHERE id_lang = ' . (int) $id_lang . '
                AND id_shop = ' . (int) $id_shop . '
                AND id_category = ' . (int) $id_element;

                $sql2 = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_category`
                SET meta_description = "' . pSQL($meta_description) . '"
                WHERE id_seo_lang = ' . (int) $id_lang . '
                AND id_shop = ' . (int) $id_shop . '
                AND id_seo_category = ' . (int) $id_element;
                if (!Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_cms':
                $meta_description = EverPsSeoCms::changeCmsMetadescShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                $meta_description = Tools::substr($meta_description, 0, 250);

                $sql = 'UPDATE `' . _DB_PREFIX_ . 'cms_lang`
                SET meta_description = "' . pSQL($meta_description) . '"
                WHERE id_lang = ' . (int) $id_lang . '
                AND id_shop = ' . (int) $id_shop . '
                AND id_cms = ' . (int) $id_element;

                $sql2 = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_cms`
                SET meta_description = "' . pSQL($meta_description) . '"
                WHERE id_seo_lang = ' . (int) $id_lang . '
                AND id_shop = ' . (int) $id_shop . '
                AND id_seo_cms = ' . (int) $id_element;
                if (!Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_manufacturer':
                $meta_description = EverPsSeoManufacturer::changeManufacturerMetadescShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                $meta_description = Tools::substr($meta_description, 0, 250);

                $sql = 'UPDATE `' . _DB_PREFIX_ . 'manufacturer_lang`
                SET meta_description = "' . pSQL($meta_description) . '"
                WHERE id_lang = ' . (int) $id_lang . '
                AND id_manufacturer = ' . (int) $id_element;

                $sql2 = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_manufacturer`
                SET meta_description = "' . pSQL($meta_description) . '"
                WHERE id_seo_lang = ' . (int) $id_lang . '
                AND id_shop = ' . (int) $id_shop . '
                AND id_seo_manufacturer = ' . (int) $id_element;
                if (!Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_supplier':
                $meta_description = EverPsSeoSupplier::changeSupplierMetadescShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                $meta_description = Tools::substr(pSQL($meta_description), 0, 250);

                $sql = 'UPDATE `' . _DB_PREFIX_ . 'supplier_lang`
                SET meta_description = "' . pSQL($meta_description) . '"
                WHERE id_lang = ' . (int) $id_lang . '
                AND id_supplier = ' . (int) $id_element;

                $sql2 = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_supplier`
                SET meta_description = "' . pSQL($meta_description) . '"
                WHERE id_seo_lang = ' . (int) $id_lang . '
                AND id_shop = ' . (int) $id_shop . '
                AND id_seo_supplier = ' . (int) $id_element;
                if (!Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_pagemeta':
                $meta_description = EverPsSeoPageMeta::changePagemetaMetadescShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                $pageMeta = new Meta(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                $pageMeta->meta_description = Tools::substr(pSQL($meta_description), 0, 250);
                // TODO : use SQL query
                // if ($pageMeta->save()) {
                //     return true;
                // }
                break;
        }
    }

    protected function autoSetAlt($id_element, $id_shop, $id_lang)
    {
        $alt = EverPsSeoImage::changeImageAltShortcodes(
            $id_element,
            $id_lang,
            $id_shop
        );
        $image = new Image(
            (int) $id_element,
            (int) $id_lang,
            (int) $id_shop
        );
        $legend = Tools::substr(
            strip_tags($alt),
            0,
            128
        );
        if (Validate::isGenericName($legend)) {
            $image->legend = $legend;
            // Hooked
            if ($image->id_product && $image->save()) {
                return true;
            }
        }
    }

    protected function autoSetAltSeoImage($id_ever_seo_image, $id_element, $id_shop, $id_lang)
    {
        $alt = EverPsSeoImage::changeImageAltShortcodes(
            $id_element,
            $id_lang,
            $id_shop
        );
        $everImg = new EverPsSeoImage(
            (int) $id_ever_seo_image
        );
        $legend = Tools::substr(
            strip_tags($alt),
            0,
            128
        );
        if (Validate::isGenericName($legend)) {
            $everImg->legend = $legend;
            // Hooked
            if ($everImg->id_seo_product && $everImg->save()) {
                return true;
            }
        }
    }


    protected function autoSetContentDesc($object, $id_element, $id_shop, $id_lang)
    {
        switch ($object) {
            case 'id_seo_product':
                $description = EverPsSeoProduct::changeProductDescShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );

                if (empty($description)) {
                    return;
                }
                $product = new Product(
                    (int) $id_element,
                    false,
                    (int) $id_lang,
                    (int) $id_shop
                );
                if (!in_array($product->id_category_default, $this->getAllowedGeneratorCategories(true))) {
                    return;
                }

                if ((bool) Configuration::get('EVERSEO_DELETE_PRODUCT_CONTENT') === true) {
                    $product->description = $description;
                } else {
                    $product->description .= $description;
                }
                $meta_title = Tools::substr($meta_title, 0, 128);

                $sql_desc = 'UPDATE `' . _DB_PREFIX_ . 'product_lang`
                    SET description = "' . pSQL($product->description, true) . '"
                    WHERE id_lang = ' . (int) $id_lang . '
                    AND id_shop = ' . (int) $id_shop . '
                    AND id_product = ' . (int) $id_element;

                if (!Db::getInstance()->execute($sql_desc)) {
                    return false;
                }

                if ((bool) Configuration::get('EVERSEO_BOTTOM_PRODUCT_CONTENT') === true) {
                    $obj = EverPsSeoProduct::getSeoProduct(
                        (int) $id_element,
                        (int) $id_lang,
                        (int) $id_shop
                    );

                    $descriptionBottom = EverPsSeoProduct::changeProductBottomShortcodes(
                        (int) $id_element,
                        (int) $id_lang,
                        (int) $id_shop
                    );
                    if (empty($descriptionBottom)) {
                        return;
                    }
                    if ((bool) Configuration::get('EVERSEO_DELETE_PRODUCT_CONTENT') === false) {
                        $obj->bottom_content = $obj->bottom_content . ' ' . $descriptionBottom;
                    } else {
                        $obj->bottom_content = $descriptionBottom;
                    }
                    $sql_ever_desc = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_product`
                        SET bottom_content = "' . pSQL($obj->bottom_content, true) . '"
                        WHERE id_seo_lang = ' . (int) $id_lang . '
                        AND id_shop = ' . (int) $id_shop . '
                        AND id_seo_product = ' . (int) $id_element;

                    if (!Db::getInstance()->execute($sql_ever_desc)) {
                        return false;
                    }
                }
                break;

            case 'id_seo_category':
                $description = EverPsSeoCategory::changeCategoryDescShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                if (empty($description)) {
                    return;
                }
                $category = new Category(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                if (!in_array($category->id, $this->getAllowedGeneratorCategories())) {
                    return;
                }
                if ((bool) Configuration::get('EVERSEO_BOTTOM_CATEGORY_CONTENT') === false) {
                    if ((bool) Configuration::get('EVERSEO_DELETE_CATEGORY_CONTENT')) {
                        $category->description = $description;
                    } else {
                        $category->description = $category->description . ' ' . $description;
                    }
                    if (!$category->isParentCategoryAvailable()) {
                        $category->id_parent = 2;
                    }
                    if ($category->save()) {
                        return true;
                    }
                } else {
                    $obj = EverPsSeoCategory::getSeoCategory(
                        (int) $id_element,
                        (int) $id_lang,
                        (int) $id_shop
                    );
                    if ((bool) Configuration::get('EVERSEO_DELETE_CATEGORY_CONTENT') === false) {
                        $obj->bottom_content = $obj->bottom_content . ' ' . $description;
                    } else {
                        $obj->bottom_content = $description;
                    }
                    return $obj->save();
                }
                break;

            case 'id_seo_manufacturer':
                $description = EverPsSeoManufacturer::changeManufacturerDescShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                if (empty($description)) {
                    return;
                }
                $manufacturer = new Manufacturer(
                    (int) $id_element,
                    (int) $id_lang
                );
                if ((bool) Configuration::get('EVERSEO_BOTTOM_MANUFACTURER_CONTENT') === false) {
                    $manufacturer->description = Tools::substr(pSQL($description), 0, 250);
                    if ($manufacturer->save()) {
                        return true;
                    }
                } else {
                    $obj = EverPsSeoManufacturer::getSeoManufacturer(
                        (int) $id_element,
                        (int) $id_lang,
                        (int) $id_shop
                    );
                    if ((bool) Configuration::get('EVERSEO_DELETE_MANUFACTURER_CONTENT') === false) {
                        $obj->bottom_content = $obj->bottom_content . ' ' . $description;
                    } else {
                        $obj->bottom_content = $description;
                    }
                    return $obj->save();
                }
                break;

            case 'id_seo_supplier':
                $description = EverPsSeoSupplier::changeSupplierDescShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                if (empty($description)) {
                    return;
                }
                $supplier = new Supplier(
                    (int) $id_element,
                    (int) $id_lang
                );
                if ((bool) Configuration::get('EVERSEO_BOTTOM_SUPPLIER_CONTENT') === false) {
                    $supplier->description = Tools::substr(pSQL($description), 0, 250);
                    if ($supplier->save()) {
                        return true;
                    }
                } else {
                    $obj = EverPsSeoSupplier::getSeoSupplier(
                        (int) $id_element,
                        (int) $id_lang,
                        (int) $id_shop
                    );
                    if ((bool) Configuration::get('EVERSEO_DELETE_SUPPLIER_CONTENT') === false) {
                        $obj->bottom_content = $obj->bottom_content . ' ' . $description;
                    } else {
                        $obj->bottom_content = $description;
                    }
                    return $obj->save();
                }
                break;
        }
    }

    protected function autoSetContentShortDesc($object, $id_element, $id_shop, $id_lang)
    {
        switch ($object) {
            case 'id_seo_product':
                $description_short = EverPsSeoProduct::changeProductShortDescShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                if (empty($description_short)) {
                    return;
                }
                $product = new Product(
                    (int) $id_element,
                    false,
                    (int) $id_lang,
                    (int) $id_shop
                );
                if (!in_array($product->id_category_default, $this->getAllowedGeneratorCategories(true))) {
                    return;
                }
                if ((bool) Configuration::get('EVERSEO_DELETE_PRODUCT_CONTENT')) {
                    $product->description_short = $description_short;
                } else {
                    $product->description_short .= $description_short;
                }

                $sql_desc_short = 'UPDATE `' . _DB_PREFIX_ . 'product_lang`
                    SET description_short = "' . pSQL($product->description_short, true) . '"
                    WHERE id_lang = ' . (int) $id_lang . '
                    AND id_shop = ' . (int) $id_shop . '
                    AND id_product = ' . (int) $id_element;

                if (Db::getInstance()->execute($sql_desc_short)) {
                    return true;
                }
                break;
        }
    }

    protected function deleteElementFromTable($table, $object, $id_element)
    {
        return Db::getInstance()->delete(
            $table,
            $object . ' = ' . (int) $id_element
        );
    }
#################### END OBJECT ADD/DELETE ####################
#################### START DISPLAY HOOKS ####################

    public function hookDisplayAdminProductsSeoStepBottom($params)
    {
        $id_product = (int) $params['id_product'];
        $link = new Link();
        $seo_product = EverPsSeoProduct::getSeoProduct(
            (int) $id_product,
            (int) Context::getContext()->shop->id,
            (int) Context::getContext()->language->id
        );
        $this->context->smarty->assign([
            'seo_product' => $seo_product,
            'everlink' => $link->getAdminLink(
                'AdminEverPsSeoProduct',
                true,
                [],
                ['updateever_seo_product' => true, 'id_ever_seo_product' => $seo_product->id]
            ),
        ]);
        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/seo_product.tpl');
    }

    public function hookDisplayContentWrapperBottom()
    {
        $allowed_controllers = [
            'manufacturer',
            'supplier',
            'category',
            'product',
        ];
        if (!in_array(Tools::getValue('controller'), $allowed_controllers)) {
            return;
        }
        if ((int) Tools::getValue('page') > 0) {
            return;
        }
        $controller_name = Tools::getValue('controller');
        switch ($controller_name) {
            case 'manufacturer':
                $obj = EverPsSeoManufacturer::getSeoManufacturer(
                    (int) Tools::getValue('id_manufacturer'),
                    (int) Context::getContext()->shop->id,
                    (int) Context::getContext()->language->id
                );
                break;

            case 'supplier':
                $obj = EverPsSeoSupplier::getSeoSupplier(
                    (int) Tools::getValue('id_supplier'),
                    (int) Context::getContext()->shop->id,
                    (int) Context::getContext()->language->id
                );
                break;

            case 'category':
                $obj = EverPsSeoCategory::getSeoCategory(
                    (int) Tools::getValue('id_category'),
                    (int) Context::getContext()->shop->id,
                    (int) Context::getContext()->language->id
                );
                break;

            case 'product':
                $obj = EverPsSeoProduct::getSeoProduct(
                    (int) Tools::getValue('id_product'),
                    (int) Context::getContext()->shop->id,
                    (int) Context::getContext()->language->id
                );
                break;

            default:
                $obj = false;
                break;
        }
        if (Validate::isLoadedObject($obj)
            && !empty($obj->bottom_content)
        ) {
            $this->context->smarty->assign([
                'everseo_controller' => Tools::getValue('controller'),
                'bottom_content_id' => $obj->id,
                'bottom_content' => $obj->bottom_content,
            ]);
            return $this->display(__FILE__, 'views/templates/hook/bottom_content.tpl');
        }
    }

    public function hookDisplayReassurance()
    {
        if (!(int) Tools::getValue('id_product')) {
            return;
        }
        $id_product = (int) Tools::getValue('id_product');
        $cacheId = $this->getCacheId($this->name . '-reassurance-' . $id_product . '-' . date('Ymd'));
        if (!$this->isCached('reassurance.tpl', $cacheId)) {
            $product = new Product(
                (int) $id_product,
                false,
                (int) $this->context->language->id,
                (int) $this->context->shop->id
            );
            if ((int) $product->id_manufacturer
                && (bool) Configuration::get('EVERSEO_MANUFACTURER_REASSURANCE')
            ) {
                $manufacturer = new Manufacturer(
                    (int) $product->id_manufacturer,
                    (int) $this->context->language->id
                );
                $this->context->smarty->assign([
                    'manufacturer' => $manufacturer,
                ]);
            }
            if ((int) $product->id_supplier
                && (bool) Configuration::get('EVERSEO_SUPPLIER_REASSURANCE')
            ) {
                $supplier = new Supplier(
                    (int) $product->id_supplier,
                    (int) $this->context->language->id
                );
                $this->context->smarty->assign([
                    'supplier' => $supplier,
                ]);
            }
        }
        return $this->display(__FILE__, 'views/templates/hook/reassurance.tpl', $cacheId);
    }

    public function hookActionAdminControllerSetMedia()
    {
        $this->context->controller->addCss($this->_path . 'views/css/ever.css');
        $currentConfigure = Tools::getValue('configure');
        if ($currentConfigure == 'everpsseo') {
            $this->context->controller->addJs($this->_path . 'views/js/ever.js');
        }
    }

    public function hookDisplayOrderConfirmation($params)
    {
        $order = $params['order'];
        $address = new Address((int) $order->id_address_delivery);
        $carrier = new Carrier((int) $order->id_carrier);
        $currency = $this->context->currency;
        $products = [];
        foreach ($order->getProducts() as $prod) {
            $product = new Product(
                (int) $prod['id_product'],
                false,
                (int) $this->context->language->id,
                (int) $this->context->shop->id
            );
            $category = new Category(
                (int) $product->id_category_default,
                (int) $this->context->language->id,
                (int) $this->context->shop->id
            );
            $manufacturer = new Manufacturer(
                (int) $product->id_manufacturer,
                (int) $this->context->language->id,
                (int) $this->context->shop->id
            );
            $product->qty_ordered = (int) $prod['product_quantity'];
            $product->category_name = $category->name;
            $product->manufacturer_name = $manufacturer->name;
            $product->unit_price_tax_incl = $prod['unit_price_tax_incl'];
            $product->unit_price_tax_excl = $prod['unit_price_tax_excl'];
            $products[] = $product;
        }
        $cartRules = $order->getCartRules();
        if ($cartRules) {
            $cart_rule = new CartRule((int) $cartRules[0]['id_cart_rule']);
            $voucherCode = $cart_rule->code;
        } else {
            $voucherCode = false;
        }
        $totalPaidTaxExcl = Tools::ps_round($order->total_paid_tax_excl, 2);
        $totalShipTaxExcl = Tools::ps_round($order->total_shipping_tax_excl, 2);
        $totalShipTaxIncl = Tools::ps_round($order->total_shipping_tax_incl, 2);
        $total_ws = $totalPaidTaxExcl - $totalShipTaxExcl;
        $totalTaxFull = $order->total_paid_tax_incl - $order->total_paid_tax_excl;
        $totalTaxes = Tools::ps_round($totalTaxFull, 2);
        $this->context->smarty->assign([
            'everorder' => $order,
            'controller_name' => Tools::getValue('controller'),
            'deliveryMethod' => $carrier->name,
            'deliveryTown' => $address->city,
            'products' => $products,
            'orderReference' => $order->reference,
            'totalPaid' => $order->total_paid,
            'totalTaxes' => $totalTaxes,
            'totalAmount' => $order->total_paid_tax_excl,
            'totalShipping' => $order->total_shipping_tax_excl,
            'totalShipTaxIncl' => $totalShipTaxIncl,
            'totalWithoutShipping' => $total_ws,
            'totalTaxFull' => $totalTaxFull,
            'voucherCode' => $voucherCode,
            'evercurrency' => $currency->iso_code,
            'adwords' => Configuration::get('EVERSEO_ADWORDS'),
            'adwordssendto' => Configuration::get('EVERSEO_ADWORDS_SENDTO'),
            'adwordscontact' => Configuration::get('EVERSEO_ADWORDS_CONTACT'),
            'analytics' => Configuration::get(
                'EVERSEO_ANALYTICS'
            ),
            'shop_name' => Configuration::get('PS_SHOP_NAME'),
            'currency_iso_code' => $currency->iso_code,
            'transaction_id' => $order->id,
        ]);
        return $this->display(__FILE__, 'views/templates/hook/confirm.tpl');
    }

    public function hookDisplayHeader()
    {
        return $this->hookHeader();
    }

    public function hookHeader()
    {
        if ((bool) Configuration::get('EVERSEO_MAINTENANCE') === true
            && (bool)EverPsSeoTools::isMaintenanceIpAddress() === false
        ) {
            if (!Configuration::get('EVERSEO_MAINTENANCE_URL')) {
                $maintenance_url = 'https:://www.google.com';
            } else {
                $maintenance_url = Configuration::get('EVERSEO_MAINTENANCE_URL');
            }
            Tools::redirect(
                $maintenance_url
            );
        }
        $controller_name = Tools::getValue('controller');
        $link = new Link();
        if ((bool)Context::getContext()->customer->isLogged()) {
            $customer = new Customer(
                (int) Context::getContext()->customer->id
            );
        } else {
            $customer = false;
        }
        if (Configuration::get('EVERSEO_ADWORDS_CART_LABEL')) {
            Media::addJsDef(array(
                'adwords_add_to_cart' => Configuration::get('EVERSEO_ADWORDS_CART_LABEL'),
                'ever_currency_sign' => $this->context->currency->iso_code
            ));
            $this->context->controller->addJs($this->_path . 'views/js/ads.js');
        }
        if ((bool) Configuration::get('EVERSEO_BLOCK_RIGHT_CLICK') === true) {
            $this->context->controller->addJs($this->_path . 'views/js/rightclick.js');
        }
        // Lazy load
        if ((bool) Configuration::get('EVERSEO_LAZY_LOAD')) {
            $lazyExceptions = Configuration::get('EVERSEO_LAZY_LOAD_EXCEPTIONS');
            if ($lazyExceptions) {
                Media::addJsDef(array(
                    'everlazy_exceptions' => Configuration::get('EVERSEO_LAZY_LOAD_EXCEPTIONS')
                ));
            }
            $this->context->controller->addJs($this->_path . 'views/js/unveil.min.js');
            $this->context->controller->addJs($this->_path . 'views/js/lazyload.js');
        }
        // Google Recaptcha V3, only if everpscaptcha not installed
        if (Configuration::get('EVERPSCAPTCHA_SITE_KEY')
            && Configuration::get('EVERPSCAPTCHA_SECRET_KEY')
            && !Module::isInstalled('everpscaptcha')
        ) {
            $secret = Configuration::get('EVERPSCAPTCHA_SECRET_KEY');
            if (Tools::getIsset('g-recaptcha-response')) {
                if (Tools::getValue('g-recaptcha-response')) {
                    $verifyResponse = Tools::file_get_contents(
                        'https://www.google.com/recaptcha/api/siteverify?secret='
                        . $secret
                        . '&response='
                        . Tools::getValue('g-recaptcha-response')
                    );
                    $responseData = json_decode($verifyResponse);
                    if (!$responseData->success) {
                        sleep(50);
                        exit();
                    }
                } else {
                    sleep(50);
                    exit();
                }
            }
            // Set recaptcha
            $captcha_content = 'https://www.google.com/recaptcha/api.js?render=' . Configuration::get('EVERPSCAPTCHA_SITE_KEY');
            $this->context->controller->addJquery();
            $this->context->controller->registerJavascript(
                'remote-google-recaptcha',
                $captcha_content,
                [
                    'server' => 'remote',
                    'position' => 'bottom',
                    'priority' => 20,
                    'defer' => 'defer',
                ]
            );
            $this->context->smarty->assign([
                'ever_ps_captcha_site_key' => Configuration::get('EVERPSCAPTCHA_SITE_KEY'),
            ]);
        }
        // Google tag manager, for Analytics
        if (Configuration::get('EVERSEO_ANALYTICS')) {
            $analytics_tag_url = 'https://www.googletagmanager.com/gtag/js?id=' . Configuration::get('EVERSEO_ANALYTICS');
            $this->context->controller->addJs(
                $analytics_tag_url
            );
        }
        $shop_url = Configuration::get(
            'PS_SHOP_DOMAIN_SSL',
            null,
            null,
            (int) $this->context->shop->id
        );
        // Default image for social networks, case of product
        if ((int) Tools::getValue('id_product')) {
            $product = new Product(
                (int) Tools::getValue('id_product'),
                false,
                (int) $this->context->language->id,
                (int) $this->context->shop->id
            );
            if (Validate::isLoadedObject($product)) {
                $coverId = Product::getCover(
                    (int) $product->id
                );
                if ($coverId) {
                    $defaultImage = $link->getImageLink(
                        $product->link_rewrite,
                        (int) $product->id . '-' . (int) $coverId['id_image'],
                        $this->imageType
                    );
                } else {
                    $defaultImage = false;
                }
                if (!$defaultImage) {
                    $defaultImage = _PS_IMG_ . Configuration::get('PS_LOGO');
                }
            }
        } else {
            $defaultImage = Tools::getHttpHost(true) . __PS_BASE_URI__ . '/modules/everpsseo/views/img/everpsseo.jpg';
            if (!$defaultImage) {
                $defaultImage = _PS_IMG_ . Configuration::get('PS_LOGO');
            }
        }
        $id_shop = (int) $this->context->shop->id;
        $id_lang = (int) $this->context->language->id;
        $id_author = (int) Configuration::get(
            'EVERSEO_AUTHOR'
        );
        $employee = new Employee((int) $id_author);

        // Set backlink counter +1 or add new one
        $from = EverPsSeoTools::getReferrer();
        $to = $this->protocol_link."$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $askedUrl = str_replace($this->protocol_link, '', $to);
        $simplyUrl = str_replace($shop_url, '', $askedUrl);
        if ($from && $to) {
            $id_ever_seo_backlink = EverPsSeoBacklink::ifBacklinkExists(
                pSQL($from),
                pSQL($to),
                (int) $id_shop
            );
            if ($id_ever_seo_backlink) {
                EverPsSeoBacklink::incrementCounter(
                    (int) $id_ever_seo_backlink,
                    (int) $id_shop
                );
            } else {
                if (false !== stripos(pSQL($from), Configuration::get(
                    'PS_SHOP_DOMAIN_SSL'
                ))) {
                    //How about tracking visitors ?
                } else {
                    $backlink = new EverPsSeoBacklink();
                    $backlink->everfrom = Tools::substr(
                        pSQL($from),
                        0,
                        255
                    );
                    $backlink->everto = Tools::substr(
                        pSQL($to),
                        0,
                        255
                    );
                    $backlink->count = 1;
                    $backlink->id_shop = (int) $id_shop;
                    $backlink->save();
                }
            }
        }

        switch ($controller_name) {
            case 'product':
                $id_product = (int) Tools::getValue('id_product');
                if ((int) $id_product <= 0) {
                    return;
                }
                $product = new Product(
                    (int) $id_product,
                    false,
                    (int) $id_lang,
                    (int) $id_shop
                );
                if (!Validate::isLoadedObject($product)) {
                    return;
                }
                $seo_product = EverPsSeoProduct::getSeoProduct(
                    (int) $id_product,
                    (int) $id_shop,
                    (int) $id_lang
                );
                $currentUrl = $link->getProductLink(
                    (int) $product->id,
                    null,
                    null,
                    null,
                    (int) $id_lang,
                    (int) $id_shop
                );
                if (property_exists('EverPsSeoProduct', 'canonical')
                    && isset($seo_product->canonical)
                    && !empty($seo_product->canonical)
                ) {
                    $canonical_url = str_replace(
                        $product->link_rewrite,
                        $seo_product->canonical,
                        $currentUrl
                    );
                }
                $product->description_short = str_replace(
                    '"',
                    '',
                    $product->description_short
                );
                $this->context->smarty->assign([
                    'everproduct' => $product,
                ]);
                $seo = EverPsSeoTools::getSeoIndexFollow(
                    pSQL($controller_name),
                    (int) $id_shop,
                    (int) Tools::getValue('id_product'),
                    (int) $id_lang
                );
                break;

            case 'category':
                $id_category = (int) Tools::getValue('id_category');
                if ((int) $id_category <= 0) {
                    return;
                }
                if ((bool) Configuration::get('EVERSEO_CANONICAL') === true) {
                    if (Tools::getValue('module') || Tools::getValue('fc') === 'module') {
                        return;
                    } else {
                        $category = new Category(
                            (int) $id_category,
                            (int) $id_lang,
                            (int) $id_shop
                        );
                        $seo_category = EverPsSeoCategory::getSeoCategory(
                            (int) $id_category,
                            (int) $id_shop,
                            (int) $id_lang
                        );
                        $currentUrl = $link->getCategoryLink(
                            (object) $category,
                            null,
                            (int) $id_lang,
                            null,
                            (int) $id_shop
                        );
                    }
                    if (property_exists('EverPsSeoCategory', 'canonical')
                        && isset($seo_category->canonical)
                        && !empty($seo_category->canonical)
                    ) {
                        $canonical_url = str_replace(
                            $category->link_rewrite,
                            $seo_category->canonical,
                            $currentUrl
                        );
                    }
                }
                if (!Tools::getValue('module')) {
                    $seo = EverPsSeoTools::getSeoIndexFollow(
                        pSQL($controller_name),
                        (int) $id_shop,
                        (int) $id_category,
                        (int) $id_lang
                    );
                }
                break;

            case 'cms':
                $id_cms = (int) Tools::getValue('id_cms');
                if ((int) $id_cms <= 0) {
                    return;
                }
                if ((bool) Configuration::get('EVERSEO_CANONICAL') === true) {
                    $cms = new CMS(
                        (int) $id_cms,
                        (int) $id_lang,
                        (int) $id_shop
                    );
                    $seo_cms = EverPsSeoCms::getSeoCms(
                        (int) $id_cms,
                        (int) $id_shop,
                        (int) $id_lang
                    );
                    $canonical_url = $link->getCMSLink(
                        (object) $cms,
                        null,
                        true,
                        (int) $id_lang,
                        (int) $id_shop
                    );
                    if (property_exists('EverPsSeoCms', 'canonical')
                        && isset($seo_cms->canonical)
                        && !empty($seo_cms->canonical)
                    ) {
                        $canonical_url = str_replace(
                            $cms->link_rewrite,
                            $seo_cms->canonical,
                            $canonical_url
                        );
                    }
                }
                $seo = EverPsSeoTools::getSeoIndexFollow(
                    pSQL($controller_name),
                    (int) $id_shop,
                    (int) $id_cms,
                    (int) $id_lang
                );
                break;

            case 'manufacturer':
                $id_manufacturer = (int) Tools::getValue('id_manufacturer');
                if ((int) $id_manufacturer <= 0) {
                    return;
                }
                if ((bool) Configuration::get('EVERSEO_CANONICAL') === true) {
                    $manufacturer = new Manufacturer(
                        (int) $id_manufacturer,
                        (int) $id_lang
                    );
                    if ((bool) Configuration::get('EVERSEO_CANONICAL') === true) {
                        $canonical_url = $link->getManufacturerLink(
                            $manufacturer,
                            null,
                            $this->context->language->id,
                            $this->context->shop->id
                        );
                    }
                    $seo_manufacturer = EverPsSeoManufacturer::getSeoManufacturer(
                        (int) $id_manufacturer,
                        (int) $id_shop,
                        (int) $id_lang
                    );
                    $currentUrl = $link->getManufacturerLink(
                        (object) $manufacturer,
                        null,
                        (int) $id_lang,
                        (int) $id_shop
                    );
                    if (property_exists('EverPsSeoManufacturer', 'canonical')
                        && isset($seo_manufacturer->canonical)
                        && !empty($seo_manufacturer->canonical)
                    ) {
                        $canonical_url = str_replace(
                            $manufacturer->link_rewrite,
                            $seo_manufacturer->canonical,
                            $currentUrl
                        );
                    }
                }
                if (Tools::getValue('id_manufacturer')) {
                    $seo = EverPsSeoTools::getSeoIndexFollow(
                        pSQL($controller_name),
                        (int) $id_shop,
                        (int) Tools::getValue('id_manufacturer'),
                        (int) $id_lang
                    );
                } else {
                    $pageMetaId = Db::getInstance()->getValue(
                        'SELECT id_meta
                        FROM ' . _DB_PREFIX_ . 'meta
                        WHERE page = "' . pSQL($controller_name) . '"'
                    );
                    $seo = EverPsSeoTools::getSeoIndexFollow(
                        false,
                        (int) $id_shop,
                        (int) $pageMetaId,
                        (int) $id_lang
                    );
                }
                break;

            case 'supplier':
                $id_supplier = (int) Tools::getValue('id_supplier');
                if ((int) $id_supplier <= 0) {
                    return;
                }
                if ((bool) Configuration::get('EVERSEO_CANONICAL') === true) {
                    $supplier = new Supplier(
                        (int) $id_supplier,
                        (int) $id_lang
                    );
                    if ((bool) Configuration::get('EVERSEO_CANONICAL') === true) {
                        $canonical_url = $link->getSupplierLink(
                            $supplier,
                            null,
                            $this->context->language->id,
                            $this->context->shop->id
                        );
                    }
                    $seo_supplier = EverPsSeoSupplier::getSeoSupplier(
                        (int) $id_supplier,
                        (int) $id_shop,
                        (int) $id_lang
                    );
                    $currentUrl = $link->getSupplierLink(
                        (object) $supplier,
                        null,
                        (int) $id_lang,
                        (int) $id_shop
                    );
                    if (property_exists('EverPsSeoSupplier', 'canonical')
                        && isset($seo_supplier->canonical)
                        && !empty($seo_supplier->canonical)
                    ) {
                        $canonical_url = str_replace(
                            $supplier->link_rewrite,
                            $seo_supplier->canonical,
                            $currentUrl
                        );
                    }
                }
                if (Tools::getValue('id_supplier')) {
                    $seo = EverPsSeoTools::getSeoIndexFollow(
                        pSQL($controller_name),
                        (int) $id_shop,
                        (int) Tools::getValue('id_supplier'),
                        (int) $id_lang
                    );
                } else {
                    $pageMetaId = Db::getInstance()->getValue(
                        'SELECT id_meta
                        FROM ' . _DB_PREFIX_ . 'meta
                        WHERE page = "' . pSQL($controller_name) . '"'
                    );
                    $seo = EverPsSeoTools::getSeoIndexFollow(
                        false,
                        (int) $id_shop,
                        (int) $pageMetaId,
                        (int) $id_lang
                    );
                }
                break;

            default:
                $pageMetaId = Db::getInstance()->getValue(
                    'SELECT id_meta
                    FROM ' . _DB_PREFIX_ . 'meta
                    WHERE page = "' . pSQL($controller_name) . '"'
                );
                if ((bool) Configuration::get('EVERSEO_CANONICAL') === true) {
                    $canonical_url = $link->getPageLink(pSQL($controller_name));
                }
                $seo = EverPsSeoTools::getSeoIndexFollow(
                    pSQL($controller_name),
                    (int) $id_shop,
                    (int) $pageMetaId,
                    (int) $id_lang
                );
                break;
        }

        if (isset($seo) && $seo) {
            if ($seo[0]['indexable'] == 1) {
                $index = 'index';
            } else {
                $index = 'noindex';
            }

            if ($seo[0]['follow'] == 1) {
                $follow = 'follow';
            } else {
                $follow = 'nofollow';
            }

            if (isset($seo[0]['meta_title'])) {
                $meta_title = $seo[0]['meta_title'];
            } else {
                $meta_title = null;
            }

            if (isset($seo[0]['meta_description'])) {
                $meta_description = $seo[0]['meta_description'];
            } else {
                $meta_description = null;
            }

            if (isset($seo[0]['social_title'])) {
                $social_title = $seo[0]['social_title'];
            } else {
                $social_title = $meta_title;
            }

            if (isset($seo[0]['social_description'])) {
                $social_description = $seo[0]['social_description'];
            } else {
                $social_description = $meta_description;
            }

            if (isset($seo[0]['social_img_url'])) {
                $social_img_url = $seo[0]['social_img_url'];
            } else {
                if ($controller_name == 'product') {
                    $cover_array = Product::getCover(
                        (int) Tools::getValue('id_product')
                    );
                    $product = new Product(
                        (int) Tools::getValue('id_product'),
                        false,
                        (int) $this->context->shop->id,
                        (int) $this->context->language->id
                    );
                    if ($cover_array['id_image']) {
                        $social_img_url = $link->getImageLink(
                            $product->link_rewrite,
                            (int) $product->id . '-' . (int) $cover_array['id_image'],
                            $this->imageType
                        );
                    } else {
                        $social_img_url = Tools::getHttpHost(true) . __PS_BASE_URI__ . 'img/' . Configuration::get(
                            'PS_LOGO'
                        );
                    }
                } else {
                    $social_img_url = Tools::getHttpHost(true) . __PS_BASE_URI__ . 'img/' . Configuration::get(
                        'PS_LOGO'
                    );
                }
            }
            $this->context->smarty->assign([
                'social_title' => $social_title,
                'social_description' => $social_description,
                'social_img_url' => $social_img_url,
            ]);
        }

        if ((bool) EverPsSeoTools::pageHasBannedArgs() === true) {
            $index = 'noindex';
        }
        $page = $this->context->controller->getTemplateVarPage();
        if (isset($index) && isset($follow)) {
            $page['meta']['robots'] = $index . ', ' . $follow;
        }
        if (isset($meta_title) && isset($meta_description)) {
            $page['meta']['title'] = $meta_title;
            $page['meta']['description'] = $meta_description;
        }
        if ((bool) Configuration::get('EVERSEO_CANONICAL') === true
            && isset($canonical_url)
            && !empty($canonical_url)
        ) {
            $page['canonical'] = $canonical_url;
            $page['canonical_url'] = $canonical_url;
        }
        $this->context->smarty->assign('page', $page);
        if ((bool) Configuration::get('EVERSEO_CANONICAL') === false) {
            $canonical_url = false;
        }
        $replyto = Configuration::get(
            'PS_SHOP_EMAIL'
        );
        $identifierUrl = Configuration::get(
            'PS_SHOP_DOMAIN_SSL'
        );
        $yearEnd = date('Y-m-d', strtotime('Dec 31'));
        $this->context->smarty->assign([
            'ever_customer' => $customer,
            'controller_name' => pSQL($controller_name),
            'sitename' => Configuration::get('PS_SHOP_NAME'),
            'site_url' => $this->siteUrl,
            'shop_logo' => $this->siteUrl._PS_IMG_.Configuration::get('PS_LOGO'),
            'everyear' => date('Y'),
            'priceValidUntil' => $yearEnd,
            'replyto' => $replyto,
            'identifierUrl' => $identifierUrl,
            'header_tags' => Configuration::get(
                'EVERSEO_HEADER_TAGS'
            ),
            'ever_theme_color' => Configuration::get(
                'EVERSEO_THEME_COLOR'
            ),
            'analytics' => Configuration::get(
                'EVERSEO_ANALYTICS'
            ),
            'searchconsole' => Configuration::get(
                'EVERSEO_SEARCHCONSOLE'
            ),
            'gtag_manager' => Configuration::get(
                'EVERSEO_GTAG'
            ),
            'pixelfacebook' => Configuration::get(
                'EVERSEO_FBPIXEL'
            ),
            'argsUrl' => Configuration::get(
                'EVERSEO_INDEX_ARGS'
            ),
            'siteName' => Configuration::get(
                'PS_SHOP_NAME'
            ),
            'defaultImage' => $defaultImage,
            'usehreflang' => Configuration::get(
                'EVERSEO_HREF_LANG'
            ),
            'currentLanguageIsoCode' => $this->context->language->iso_code,
            'useTwitter' => Configuration::get(
                'EVERSEO_USE_TWITTER'
            ),
            'twitterAccount' => Configuration::get(
                'EVERSEO_TWITTER_NAME'
            ),
            'useOpenGraph' => Configuration::get(
                'EVERSEO_USE_OPENGRAPH'
            ),
            'xdefault' => Configuration::get(
                'PS_LANG_DEFAULT'
            ),
            'hreflangTags' => EverPsSeoTools::getHeaderHreflangTemplate(
                $controller_name,
                $this->context->shop->id,
                $this->context->language->id
            ),
            'everseo_use_author' => Configuration::get(
                'EVERSEO_USE_AUTHOR'
            ),
            'everseo_author' => $employee->firstname . ' ' . $employee->lastname,
            'simplyUrl' => $simplyUrl,
            'currency_iso' => $this->context->currency->iso_code,
            'everweight_unit' => Configuration::get(
                'PS_WEIGHT_UNIT'
            ),
            'adwords' => Configuration::get(
                'EVERSEO_ADWORDS'
            ),
            'adwordssendto' => Configuration::get(
                'EVERSEO_ADWORDS_SENDTO'
            ),
            'adwordscontact' => Configuration::get(
                'EVERSEO_ADWORDS_CONTACT'
            ),
            'adwordsopart' => Configuration::get(
                'EVERSEO_ADWORDS_OPART'
            ),
            'adwords_add_to_cart' => Configuration::get(
                'EVERSEO_ADWORDS_CART_LABEL'
            ),
            'richsnippet' => Configuration::get(
                'EVERSEO_RSNIPPETS'
            ),
        ]);

        return $this->display(__FILE__, 'views/templates/front/header.tpl');
    }

    public function hookDisplayLeftColumn()
    {
        return $this->hookDisplayRightColumn();
    }

    public function hookDisplayRightColumn()
    {
        if ((bool) Configuration::get('EVERSEO_GCOLUMN')
            && !(bool) Configuration::get('EVERSEO_GTOP')
        ) {
            $cacheId = $this->getCacheId($this->name . '-gtranslate-' . date('Ymd'));
            if (!$this->isCached('gtranslate.tpl', $cacheId)) {
                $default_lang = Configuration::get(
                    'PS_LANG_DEFAULT'
                );
                $language = new Language((int) $default_lang);
                $this->context->smarty->assign([
                    'default_iso_code' => $language->iso_code,
                ]);
            }
            return $this->display(__FILE__, 'views/templates/hook/gtranslate.tpl', $cacheId);
        }
    }

    public function hookDisplayAfterBodyOpeningTag()
    {
        return $this->hookDisplayTop();
    }

    public function hookDisplayTopColumn()
    {
        return $this->hookDisplayTop();
    }

    public function hookDisplayTop()
    {
        $cacheId = $this->getCacheId($this->name . '-displaytop-' . date('Ymd'));
        if (!$this->isCached('displaytop.tpl', $cacheId)) {
            $default_lang = Configuration::get(
                'PS_LANG_DEFAULT'
            );
            $language = new Language((int) $default_lang);
            $this->context->smarty->assign([
                'gtag_manager' => Configuration::get(
                    'EVERSEO_GTAG'
                ),
                'default_iso_code' => $language->iso_code,
                'translate_top' => Configuration::get(
                    'EVERSEO_GTOP'
                ),
                'translate_column' => Configuration::get(
                    'EVERSEO_GCOLUMN'
                ),
            ]);
        }
        return $this->display(__FILE__, 'views/templates/hook/displaytop.tpl', $cacheId);
    }

    public function hookDisplayFooterProduct($params)
    {
        if (Tools::getValue('fc') === 'module') {
            return;
        }
        if ((bool) Configuration::get(
            'EVERSEO_RSNIPPETS'
        )) {
            return $this->hookFooter($params);
        }
    }

    public function hookFooter($params)
    {
        if (Tools::getValue('fc') === 'module') {
            return;
        }
        $return = false;
        if ((bool) Configuration::get(
            'EVERSEO_RSNIPPETS'
        )) {
            $controller_name = Tools::getValue('controller');
            $cacheId = $this->getCacheId($this->name . '-richsnippets-' . $controller_name . '-' . date('Ymd'));
            if (!$this->isCached('richsnippets.tpl', $cacheId)) {
                $shop_name = Configuration::get('PS_SHOP_NAME');
                $shop_logo = _PS_IMG_ . Configuration::get('PS_LOGO');
                $homepage = Tools::getHttpHost(true) . __PS_BASE_URI__;
                $id_lang = (int) $this->context->language->id;
                $id_shop = (int) $this->context->shop->id;
                $id_currency = (int) $this->context->currency->id;
                $currency = new Currency((int) $id_currency);

                switch ($controller_name) {
                    case 'product':
                        $id_product = (int) Tools::getValue('id_product');
                        if ((int) $id_product <= 0) {
                            return;
                        }
                        $link = new Link();
                        $product = new Product(
                            (int) $id_product,
                            false,
                            (int) $id_lang,
                            (int) $id_shop
                        );
                        if (!Validate::isLoadedObject($product)) {
                            return;
                        }
                        $richImage = $product->getCover((int) $product->id);
                        $imgUrl = Tools::getShopProtocol().$link->getImageLink(
                            $product->link_rewrite,
                            (int) $product->id . '-' . $richImage['id_image'],
                            $this->imageType
                        );
                        $currentUrl = $link->getProductLink(
                            $product,
                            null,
                            null,
                            null,
                            (int) $id_lang,
                            (int) $id_shop
                        );
                        $yearEnd = date('Y-m-d', strtotime('Dec 31'));
                        $this->context->smarty->assign([
                            'controller' => pSQL($controller_name),
                            'shop_name' => $shop_name,
                            'shop_logo' => $shop_logo,
                            'homepage' => $homepage,
                            'currentUrl' => pSQL($currentUrl),
                            'productId' => (int) $product->id,
                            'productReference' => $product->reference,
                            'productName' => $product->name,
                            'productID' => (int) $product->id,
                            'productCondition' => $product->condition,
                            'productQuantity' => $product->quantity,
                            'descriptionShort' => $product->description_short,
                            'productPrice' => $product->price,
                            'currencyIsocode' => $currency->iso_code,
                            'currencyPrefix' => $currency->prefix,
                            'currencySuffix' => $currency->suffix,
                            'imgUrl' => $imgUrl,
                            'manufacturer' => Manufacturer::getNameById((int) $product->id_manufacturer),
                            'priceValidUntil' => $yearEnd,
                        ]);
                        $return = true;
                        break;

                    case 'category':
                        $id_category = Tools::getValue('id_category');
                        $link = new Link();
                        $category = new Category(
                            (int) $id_category,
                            (int) $id_lang,
                            (int) $id_shop
                        );
                        $currentUrl = $link->getCategoryLink(
                            (object) $category,
                            null,
                            (int) $id_lang,
                            null,
                            (int) $id_shop
                        );
                        $this->context->smarty->assign([
                            'controller' => pSQL($controller_name),
                            'shop_name' => $shop_name,
                            'shop_logo' => $shop_logo,
                            'homepage' => $homepage,
                            'currentUrl' => pSQL($currentUrl),
                        ]);
                        $return = true;
                        break;

                    case 'cms':
                        $id_cms = Tools::getValue('id_cms');
                        $link = new Link();
                        $cms = new CMS(
                            (int) $id_cms,
                            (int) $id_lang,
                            (int) $id_shop
                        );
                        $currentUrl = $link->getCMSLink(
                            (object) $cms,
                            null,
                            true,
                            (int) $id_lang,
                            (int) $id_shop
                        );
                        $this->context->smarty->assign([
                            'controller' => pSQL($controller_name),
                            'shop_name' => $shop_name,
                            'shop_logo' => $shop_logo,
                            'homepage' => $homepage,
                            'currentUrl' => pSQL($currentUrl),
                        ]);
                        $return = true;
                        break;

                    case 'manufacturer':
                        $id_manufacturer = Tools::getValue('id_manufacturer');
                        $link = new Link();
                        $manufacturer = new Manufacturer(
                            (int) $id_manufacturer,
                            (int) $id_lang
                        );
                        $currentUrl = $link->getManufacturerLink(
                            (object) $manufacturer,
                            null,
                            (int) $id_lang,
                            (int) $id_shop
                        );
                        $this->context->smarty->assign([
                            'controller' => pSQL($controller_name),
                            'shop_name' => $shop_name,
                            'shop_logo' => $shop_logo,
                            'homepage' => $homepage,
                            'currentUrl' => pSQL($currentUrl),
                        ]);
                        $return = true;
                        break;

                    case 'supplier':
                        $id_supplier = Tools::getValue('id_supplier');
                        $link = new Link();
                        $supplier = new Supplier(
                            (int) $id_supplier,
                            (int) $id_lang
                        );
                        $currentUrl = $link->getSupplierLink(
                            (object) $supplier,
                            null,
                            (int) $id_lang,
                            (int) $id_shop
                        );
                        $this->context->smarty->assign([
                            'controller' => pSQL($controller_name),
                            'shop_name' => $shop_name,
                            'shop_logo' => $shop_logo,
                            'homepage' => $homepage,
                            'currentUrl' => pSQL($currentUrl),
                        ]);
                        $return = true;
                        break;

                    default:
                        $link = new Link();
                        $currentUrl = $link->getPageLink(pSQL($controller_name));
                        $this->context->smarty->assign([
                            'controller' => pSQL($controller_name),
                            'shop_name' => $shop_name,
                            'shop_logo' => $shop_logo,
                            'homepage' => $homepage,
                            'currentUrl' => pSQL($currentUrl),
                        ]);
                        $return = true;
                        break;
                }
                if ((bool) $return === true) {                    
                    return $this->display(
                        __FILE__,
                        'views/templates/front/richsnippets.tpl',
                        $cacheId
                    );
                }
            }
        }
    }

#################### END DISPLAY HOOKS ####################
#################### START SITEMAPS ####################
    public function processSitemapProduct($id_shop, $id_lang)
    {
        if (!Configuration::get('EVERSEO_SITEMAP_PRODUCT')) {
            return false;
        }

        $iso_lang = Language::getIsoById((int) $id_lang);

        $sitemap = new EverPsSeoSitemap(
            Tools::getHttpHost(true) . __PS_BASE_URI__
        );
        $sitemap->setPath(_PS_ROOT_DIR_ . '/');
        $sitemap->setFilename('product_shop_' . (int) $id_shop . '_lang_' . $iso_lang);

        $sql =
            'SELECT id_seo_product FROM ' . _DB_PREFIX_ . 'ever_seo_product esp
            LEFT JOIN ' . _DB_PREFIX_ . 'product p
            ON (
                p.id_product = esp.id_seo_product
            )
            WHERE esp.allowed_sitemap = 1
                AND p.active = 1
                AND esp.id_shop = ' . (int) $id_shop . '
                AND esp.id_seo_lang = '. (int) $id_lang;

        if ($results = Db::getInstance()->executeS($sql)) {
            foreach ($results as $id_product) {
                $link = new Link();
                $product = new Product(
                    (int) $id_product['id_seo_product'],
                    false,
                    (int) $id_lang,
                    (int) $id_shop
                );
                $productUrl = $link->getProductLink(
                    $product,
                    null,
                    null,
                    null,
                    (int) $id_lang,
                    (int) $id_shop
                );

                if ($product->active && !empty($product->name)) {
                    $sitemap->addItem(
                        $productUrl,
                        Configuration::get('EVERSEO_SITEMAP_PRODUCT_PRIORITY'),
                        Configuration::get('EVERSEO_SITEMAP_PRODUCT_FREQUENCY'),
                        $product->date_upd
                    );
                }
            }
            return $sitemap->createSitemapIndex(
                Tools::getHttpHost(true) . __PS_BASE_URI__,
                'Today'
            );
        }
    }

    /**
     * Process image sitemap creation
     */
    public function processSitemapImage($id_shop, $id_lang)
    {
        set_time_limit(0);
        if (!Configuration::get('EVERSEO_SITEMAP_IMAGE')) {
            return false;
        }

        $iso_lang = Language::getIsoById((int) $id_lang);

        $sitemap = new EverPsSeoSitemap(
            Tools::getHttpHost(true) . __PS_BASE_URI__
        );
        $sitemap->setPath(_PS_ROOT_DIR_ . '/');
        $sitemap->setFilename('img_shop_' . (int) $id_shop . '_lang_' . $iso_lang);

        $sql =
            'SELECT * FROM ' . _DB_PREFIX_ . 'ever_seo_image esi
            LEFT JOIN ' . _DB_PREFIX_ . 'image i
            ON (
                i.id_image = esi.id_seo_img
            )
            LEFT JOIN ' . _DB_PREFIX_ . 'product p
            ON (
                p.id_product = i.id_product
            )
            WHERE esi.allowed_sitemap = 1
                AND p.active = 1
                AND esi.id_shop = ' . (int) $id_shop . '
                AND esi.id_seo_lang = ' . (int) $id_lang;

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $id_img) {
                $useSSL = ((isset($this->ssl)
                    && $this->ssl
                    && Configuration::get('PS_SSL_ENABLED'))
                    || Tools::usingSecureMode()) ? true : false;
                $protocol_content = ($useSSL) ? 'https://' : 'http://';
                $link = new Link($this->protocol_link, $protocol_content);
                $image = new Image((int) $id_img['id_seo_img']);
                $product = new Product(
                    $id_img['id_seo_product'],
                    false,
                    (int) $id_lang,
                    (int) $id_shop
                );
                $imgUrl = $link->getImageLink(
                    $product->link_rewrite,
                    (int) $product->id . '-' . (int) $image->id,
                    $this->imageType
                );

                if ($product->active) {
                    $sitemap->addItem(
                        $imgUrl,
                        Configuration::get('EVERSEO_SITEMAP_IMAGE_PRIORITY'),
                        Configuration::get('EVERSEO_SITEMAP_IMAGE_FREQUENCY'),
                        $product->date_upd
                    );
                }
            }

            return $sitemap->createSitemapIndex(
                Tools::getHttpHost(true) . __PS_BASE_URI__,
                'Today'
            );
        }
    }

    public function processSitemapCategory($id_shop, $id_lang)
    {
        if (!Configuration::get('EVERSEO_SITEMAP_CATEGORY')) {
            return false;
        }

        $iso_lang = Language::getIsoById((int) $id_lang);

        $sitemap = new EverPsSeoSitemap(
            Tools::getHttpHost(true) . __PS_BASE_URI__
        );
        $sitemap->setPath(_PS_ROOT_DIR_ . '/');
        $sitemap->setFilename('category_shop_' . (int) $id_shop . '_lang_' . $iso_lang);

        $sql =
            'SELECT * FROM ' . _DB_PREFIX_ . 'ever_seo_category esc
            LEFT JOIN ' . _DB_PREFIX_ . 'category c
            ON (
                c.id_category = esc.id_seo_category
            )
            WHERE allowed_sitemap = 1
                AND c.active = 1
                AND id_shop = '. (int) $id_shop . '
                AND id_seo_lang = ' . (int) $id_lang;

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $id_category) {
                $link = new Link();
                $category = new Category(
                    $id_category['id_seo_category'],
                    (int) $id_lang,
                    (int) $id_shop
                );

                if ($category->active && !empty($category->name)) {
                    $categoryUrl = $link->getCategoryLink(
                        $category,
                        null,
                        (int) $id_lang,
                        null,
                        (int) $id_shop
                    );

                    $sitemap->addItem(
                        $categoryUrl,
                        Configuration::get('EVERSEO_SITEMAP_CATEGORY_PRIORITY'),
                        Configuration::get('EVERSEO_SITEMAP_CATEGORY_FREQUENCY'),
                        $category->date_upd
                    );
                }
            }

            return $sitemap->createSitemapIndex(
                Tools::getHttpHost(true) . __PS_BASE_URI__,
                'Today'
            );
        }
    }

    public function processSitemapPageMeta($id_shop, $id_lang)
    {
        if (!Configuration::get('EVERSEO_SITEMAP_PAGE_META')) {
            return false;
        }

        $iso_lang = Language::getIsoById((int) $id_lang);
        $sitemap = new EverPsSeoSitemap(
            Tools::getHttpHost(true) . __PS_BASE_URI__
        );
        $sitemap->setPath(_PS_ROOT_DIR_ . '/');
        $sitemap->setFilename('pagemeta_shop_' . (int) $id_shop . '_lang_' . $iso_lang);

        $sql =
            'SELECT * FROM ' . _DB_PREFIX_ . 'ever_seo_pagemeta
            WHERE allowed_sitemap = 1
                AND id_shop = ' . (int) $id_shop . '
                AND id_seo_lang = '. (int) $id_lang;

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $id_page) {
                $link = new Link();
                $pageMeta = new Meta(
                    (int) $id_page['id_seo_pagemeta'],
                    (int) $id_lang,
                    (int) $id_shop
                );
                // Only configurable pages
                if ((bool) $pageMeta->configurable === false) {
                    // continue;
                }
                // Disable module pages on sitemaps, should be set manually
                if (strpos($pageMeta->page, 'module') !== true) {
                    // continue;
                }
                if ($pageMeta->page == 'module-stblog-category'
                    || $pageMeta->page == 'module-stblog-article'
                    || $pageMeta->page == 'module-stblogarchives-default'
                    || $pageMeta->page == 'module-pm_advancedsearch4-seo'
                    || $pageMeta->page == 'module-ph_simpleblog-single'
                    || $pageMeta->page == 'module-ph_simpleblog-category'
                    || $pageMeta->page == 'module-ph_simpleblog-categorypage'
                    || $pageMeta->page == 'module-ph_simpleblog-page'
                    || $pageMeta->page == 'module-ph_simpleblog-author'
                    || $pageMeta->page == 'module-ph_simpleblog-authorpage'
                    || $pageMeta->page == 'module-pm_advancedsearch4-searchresults'
                    || $pageMeta->page == 'module-pm_advancedsearch-searchresults'
                    || $pageMeta->page == 'module-colissimo-tracking'
                ) {
                    continue;
                }
                $pageMetaUrl = $link->getPageLink(
                    $pageMeta->page,
                    true,
                    (int) $id_lang,
                    null,
                    false,
                    (int) $id_shop
                );

                $sitemap->addItem(
                    $pageMetaUrl,
                    Configuration::get('EVERSEO_SITEMAP_PAGE_META_PRIORITY'),
                    Configuration::get('EVERSEO_SITEMAP_PAGE_META_FREQUENCY'),
                    date('Y-m-d')
                );
            }

            return $sitemap->createSitemapIndex(
                Tools::getHttpHost(true) . __PS_BASE_URI__,
                'Today'
            );
        }
    }

    public function processSitemapManufacturer($id_shop, $id_lang)
    {
        set_time_limit(0);
        if (!Configuration::get('EVERSEO_SITEMAP_MANUFACTURER')) {
            return false;
        }

        $iso_lang = Language::getIsoById((int) $id_lang);

        $sitemap = new EverPsSeoSitemap(
            Tools::getHttpHost(true) . __PS_BASE_URI__
        );
        $sitemap->setPath(_PS_ROOT_DIR_ . '/');
        $sitemap->setFilename('manufacturer_shop_' . (int) $id_shop . '_lang_' . $iso_lang);

        $sql =
            'SELECT * FROM ' . _DB_PREFIX_ . 'ever_seo_manufacturer
            WHERE allowed_sitemap = 1
                AND id_shop = ' . (int) $id_shop . '
                AND id_seo_lang = ' . (int) $id_lang;

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $id_page) {
                $link = new Link();
                $manufacturer = new Manufacturer(
                    (int) $id_page['id_seo_manufacturer'],
                    (int) $id_lang
                );

                $manufacturerUrl = $link->getManufacturerLink(
                    (int) $id_page['id_seo_manufacturer'],
                    null,
                    (int) $id_lang,
                    (int) $id_shop
                );

                if ($manufacturer->active) {
                    $sitemap->addItem(
                        $manufacturerUrl,
                        Configuration::get('EVERSEO_SITEMAP_MANUFACTURER_PRIORITY'),
                        Configuration::get('EVERSEO_SITEMAP_MANUFACTURER_FREQUENCY'),
                        $manufacturer->date_upd
                    );
                }
            }

            return $sitemap->createSitemapIndex(
                Tools::getHttpHost(true) . __PS_BASE_URI__,
                'Today'
            );
        }
    }

    public function processSitemapSupplier($id_shop, $id_lang)
    {
        if (!Configuration::get('EVERSEO_SITEMAP_SUPPLIER')) {
            return false;
        }

        $iso_lang = Language::getIsoById((int) $id_lang);

        $sitemap = new EverPsSeoSitemap(
            Tools::getHttpHost(true) . __PS_BASE_URI__
        );
        $sitemap->setPath(_PS_ROOT_DIR_ . '/');
        $sitemap->setFilename('supplier_shop_' . (int) $id_shop . '_lang_' . $iso_lang);

        $sql =
            'SELECT * FROM ' . _DB_PREFIX_ . 'ever_seo_supplier
            WHERE allowed_sitemap = 1
                AND id_shop = ' . (int) $id_shop . '
                AND id_seo_lang = ' . (int) $id_lang;

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $id_page) {
                $link = new Link();
                $supplier = new Supplier(
                    (int) $id_page['id_seo_supplier'],
                    (int) $id_lang
                );

                $supplierUrl = $link->getSupplierLink(
                    (int) $id_page['id_seo_supplier'],
                    null,
                    (int) $id_lang,
                    (int) $id_shop
                );

                if ($supplier->active) {
                    $sitemap->addItem(
                        $supplierUrl,
                        Configuration::get('EVERSEO_SITEMAP_SUPPLIER_PRIORITY'),
                        Configuration::get('EVERSEO_SITEMAP_SUPPLIER_FREQUENCY'),
                        $supplier->date_upd
                    );
                }
            }

            return $sitemap->createSitemapIndex(
                Tools::getHttpHost(true) . __PS_BASE_URI__,
                'Today'
            );
        }
    }

    public function processSitemapCms($id_shop, $id_lang)
    {
        if (!Configuration::get('EVERSEO_SITEMAP_CMS')) {
            return false;
        }

        $iso_lang = Language::getIsoById((int) $id_lang);

        $sitemap = new EverPsSeoSitemap(
            Tools::getHttpHost(true) . __PS_BASE_URI__
        );
        $sitemap->setPath(_PS_ROOT_DIR_ . '/');
        $sitemap->setFilename('cms_shop_' . (int) $id_shop . '_lang_' . $iso_lang);

        $sql =
            'SELECT * FROM ' . _DB_PREFIX_ . 'ever_seo_cms
            WHERE allowed_sitemap = 1
                AND id_shop = ' . (int) $id_shop . '
                AND id_seo_lang = ' . (int) $id_lang;

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $id_page) {
                $link = new Link();
                $cms = new CMS(
                    (int) $id_page['id_seo_cms'],
                    (int) $id_lang,
                    (int) $id_shop
                );

                if (!$cms->active) {
                    continue;
                }

                $cmsUrl = $link->getCMSLink(
                    (object) $cms,
                    null,
                    true,
                    (int) $id_lang,
                    (int) $id_shop
                );

                $sitemap->addItem(
                    $cmsUrl,
                    Configuration::get('EVERSEO_SITEMAP_CMS_PRIORITY'),
                    Configuration::get('EVERSEO_SITEMAP_CMS_FREQUENCY'),
                    date('Y-m-d')
                );
            }

            return $sitemap->createSitemapIndex(
                Tools::getHttpHost(true) . __PS_BASE_URI__,
                'Today'
            );
        }
    }

    public function everGenerateSitemaps($id_shop = null)
    {
        if (!$id_shop) {
            $id_shop = (int) $this->context->shop->id;
        }
        $languages = Language::getIDs(true);

        foreach ($languages as $id_lang) {
            $allowedLangs = $this->getAllowedSitemapLangs();
            if (in_array((int) $id_lang, $allowedLangs)) {
                $this->processSitemapProduct((int) $id_shop, (int) $id_lang);
                $this->processSitemapImage((int) $id_shop, (int) $id_lang);
                $this->processSitemapCategory((int) $id_shop, (int) $id_lang);
                $this->processSitemapManufacturer((int) $id_shop, (int) $id_lang);
                $this->processSitemapSupplier((int) $id_shop, (int) $id_lang);
                $this->processSitemapCms((int) $id_shop, (int) $id_lang);
                $this->processSitemapPageMeta((int) $id_shop, (int) $id_lang);
                $this->pingbots();
            }
        }
    }

    public function pingbots()
    {
        $siteUrl = Tools::getHttpHost(true) . __PS_BASE_URI__;
        $pingbots = [];
        $pingbots[] = 'https://google.com/ping?sitemap=';
        $pingbots[] = 'https://www.bing.com/webmaster/ping.aspx?siteMap=';
        $indexes = glob(_PS_ROOT_DIR_ . '/*');
        foreach ($indexes as $index) {
            $info = new SplFileInfo(basename($index));
            if (is_file($index) && $info->getExtension() == 'xml') {
                foreach ($pingbots as $ping) {
                    $ch = curl_init();
                    curl_setopt(
                        $ch,
                        CURLOPT_URL,
                        $ping.$siteUrl.basename($index)
                    );
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_exec($ch);
                    curl_close($ch);
                }
            }
        }
        return true;
    }

#################### END SITEMAPS ####################
#################### LANG CLEANER ####################

    protected function processDeleteUnusedObjects()
    {
        $pstable = [
            'category_lang',
            'product_lang',
            'image_lang',
            'cms_lang',
            'meta_lang',
            'manufacturer_lang',
            'supplier_lang',
            'info_lang',
            'group_lang',
            'gender_lang',
            'feature_lang',
            'feature_value_lang',
            'customization_field_lang',
            'country_lang',
            'cart_rule_lang',
            'carrier_lang',
            'attachment_lang',
            'attribute_lang',
            'attribute_group_lang',
        ];
        foreach ($pstable as $table) {
            $seotable = $this->getSeoTableByPsTable($table);
            $unused = 'DELETE FROM ' . _DB_PREFIX_ . pSQL($table) . '
            WHERE id_lang NOT IN
            (SELECT id_seo_lang FROM ' . _DB_PREFIX_ . 'ever_seo_lang)';
            if (Db::getInstance()->Execute($unused)) {
                if ($seotable) {
                    $updateSeo = 'DELETE FROM '. _DB_PREFIX_ . pSQL($seotable) . '
                    WHERE id_seo_lang NOT IN
                    (SELECT id_seo_lang FROM ' . _DB_PREFIX_ . 'ever_seo_lang)';
                    try {
                        Db::getInstance()->Execute($updateSeo);
                    } catch (Exception $e) {
                        PrestaShopLogger::addLog(
                            'Can\'t delete object on '. _DB_PREFIX_ . $seotable
                        );
                    }
                } else {
                    return true;
                }
            } else {
                PrestaShopLogger::addLog(
                    'Can\'t delete object on '. _DB_PREFIX_ . $table
                );
            }
        }
    }

#################### END LANG CLEANER ####################
#################### START INTERNAL LINKING ####################

    protected function processInternalLinking($id_shop)
    {
        set_time_limit(0);
        $id_lang = (int) Configuration::get('EVERSEO_LANG');
        $cms = (int) Configuration::get('EVERSEO_CMS_LINKED');
        $product_long_desc = (int) Configuration::get('EVERSEO_LONG_DESC_LINKED');
        $product_short_desc = (int) Configuration::get('EVERSEO_SHORT_DESC_LINKED');
        $categ = (int) Configuration::get('EVERSEO_CATEG_LINKED');
        $maxOccur = (int) Configuration::get('EVERSEO_LINKED_NBR');
        $searchedText = Configuration::get('SEARCHED');
        $replacingText = Configuration::get('LINKEDTO');
        $link = '<a href=\"' . pSQL($replacingText) . '\" title=\"' . pSQL($searchedText) . '\">' . pSQL($searchedText) . '</a>';
        $limit = (int) Configuration::get('EVERSEO_LINKED_NBR');

        //CMS
        if ($cms) {
            $sql =
                'UPDATE ' . _DB_PREFIX_ . 'cms_lang
                SET content =
                REPLACE(
                    content,
                    "' . pSQL($searchedText, true) . '",
                    "' . pSQL($link, true) . '")
                WHERE INSTR(
                    content,
                    "' . pSQL($searchedText, true) . '"
                ) > 0
                AND INSTR(
                    content,
                    "' . pSQL($searchedText, true) . '"
                ) <= ' . (int) $maxOccur . '
                AND id_shop = ' . (int) $id_shop . '
                AND id_lang = ' . (int) $id_lang . '
                LIMIT ' . (int) $limit;

            if (!Db::getInstance()->execute($sql)) {
                $this->postErrors[] = $this->l('Error on CMS content');
            } else {
                $this->querySuccess[] = $this->l('Content of CMS rewrited');
            }
        }

        //Products
        if ($product_long_desc) {
            $sql =
                'UPDATE ' . _DB_PREFIX_ . 'product_lang
                SET description =
                REPLACE(
                    description,
                    "' . pSQL($searchedText, true) . '",
                    "' . $link . '"
                )
                WHERE INSTR(
                    description,
                    "' . pSQL($searchedText, true) . '"
                ) > 0
                AND id_shop = ' . (int) $id_shop . '
                AND id_lang = ' . (int) $id_lang . '
                LIMIT ' . (int) $limit;

            if (!Db::getInstance()->execute($sql)) {
                $this->postErrors[] = $this->l('Error on Products description content');
            } else {
                $this->querySuccess[] = $this->l('Content of Products description rewrited');
            }
        }

        if ($product_short_desc) {
            $sql =
                'UPDATE ' . _DB_PREFIX_ . 'product_lang
                SET description_short =
                REPLACE(
                    description_short,
                    "' . pSQL($searchedText, true) . '",
                    "' . $link . '"
                )
                WHERE INSTR(
                    description_short,
                    "' . pSQL($searchedText, true) . '"
                ) > 0
                AND id_shop = ' . (int) $id_shop . '
                AND id_lang = ' . (int) $id_lang . '
                LIMIT ' . (int) $limit;

            if (!Db::getInstance()->execute($sql)) {
                $this->postErrors[] = $this->l('Error on Products short description content');
            } else {
                $this->querySuccess[] = $this->l('Content of Products short description rewrited');
            }
        }

        //Categories
        if ($categ) {
            $sql =
                'UPDATE ' . _DB_PREFIX_ . 'category_lang
                SET description =
                REPLACE(
                    description,
                    "' . pSQL($searchedText, true) . '",
                    "' . $link . '"
                )
                WHERE INSTR(
                    description,
                    "' . pSQL($searchedText, true) . '"
                ) > 0
                AND id_shop = ' . (int) $id_shop . '
                AND id_lang = ' . (int) $id_lang . '
                LIMIT ' . (int) $limit;

            if (!Db::getInstance()->execute($sql)) {
                $this->postErrors[] = $this->l('Error on Categories description content');
            } else {
                $this->querySuccess[] = $this->l('Content of Categories description rewrited');
            }
        }
    }
#################### END INTERNAL LINKING ####################
#################### START SETTERS ####################
    protected function generateRobots()
    {
        if ((bool) Configuration::get('EVERSEO_ROBOTS_TXT_REWRITE') === false) {
            return;
        }
        $robots = Configuration::get('EVERSEO_ROBOTS_TXT');
        $fp = fopen(_PS_ROOT_DIR_ . '/robots.txt', 'wb');
        fwrite($fp, $robots);
        fclose($fp);
    }

    protected function setColumnDefault($tableName, $columnName, $default)
    {
        $sql =
            'ALTER TABLE '. _DB_PREFIX_ . pSQL($tableName) . '
            CHANGE ' . pSQL($columnName) . ' ' . pSQL($columnName) . ' TINYINT(1) NOT NULL DEFAULT ' . (int) $default . '';
        if (!Db::getInstance()->Execute($sql)) {
            $this->postErrors[] = $this->l('An error occured while altering column default value');
            return false;
        } else {
            return true;
        }
    }

    public function updateSeoProducts()
    {
        set_time_limit(0);
        $sql =
            'INSERT INTO `' . _DB_PREFIX_ . 'ever_seo_product` (
                id_seo_product,
                id_shop,
                id_seo_lang,
                meta_title,
                meta_description,
                indexable,
                follow,
                allowed_sitemap
            )
            SELECT
                pl.id_product,
                pl.id_shop,
                pl.id_lang,
                null,
                null,
                p.indexed,
                1,
                1
            FROM `' . _DB_PREFIX_ . 'product_lang` pl
            INNER JOIN `' . _DB_PREFIX_ . 'product` p
                ON (
                    p.id_product = pl.id_product
                )
            WHERE p.id_product NOT IN (
                SELECT id_seo_product FROM `' . _DB_PREFIX_ . 'ever_seo_product`
            )';
        if (!Db::getInstance()->execute($sql)) {
            return false;
        } else {
            $duplicates = 'DELETE FROM `' . _DB_PREFIX_ . 'ever_seo_product`
            WHERE id_seo_product
            NOT IN (
                SELECT id_product FROM ' . _DB_PREFIX_ . 'product
            )';
            return Db::getInstance()->execute($duplicates);
        }
    }

    public function updateSeoCategories()
    {
        set_time_limit(0);
        $sql =
            'INSERT INTO `' . _DB_PREFIX_ . 'ever_seo_category` (
                id_seo_category,
                id_shop,
                id_seo_lang,
                meta_title,
                meta_description,
                indexable,
                follow,
                allowed_sitemap
            )
            SELECT
                id_category,
                id_shop,
                id_lang,
                null,
                null,
                1,
                1,
                1
            FROM `' . _DB_PREFIX_ . 'category_lang`
            WHERE id_category NOT IN (
                SELECT id_seo_category FROM `' . _DB_PREFIX_ . 'ever_seo_category`
            )';
        if (!Db::getInstance()->execute($sql)) {
            return false;
        } else {
            $duplicates = 'DELETE FROM `' . _DB_PREFIX_ . 'ever_seo_category`
            WHERE id_seo_category
            NOT IN (
                SELECT id_category FROM ' . _DB_PREFIX_ . 'category
            )';
            return Db::getInstance()->execute($duplicates);
        }
    }

    public function updateSeoSuppliers()
    {
        set_time_limit(0);
        $sql =
            'INSERT INTO `' . _DB_PREFIX_ . 'ever_seo_supplier` (
                id_seo_supplier,
                id_shop,
                id_seo_lang
            )
            SELECT
                sl.id_supplier,
                ss.id_shop,
                sl.id_lang
            FROM `' . _DB_PREFIX_ . 'supplier_lang` sl
            INNER JOIN `' . _DB_PREFIX_ . 'supplier_shop` ss
                ON (
                    ss.id_supplier = sl.id_supplier
                )
            WHERE ss.id_supplier NOT IN (
                SELECT id_seo_supplier FROM `' . _DB_PREFIX_ . 'ever_seo_supplier`
            )';
        if (!Db::getInstance()->execute($sql)) {
            return false;
        } else {
            $duplicates = 'DELETE FROM `' . _DB_PREFIX_ . 'ever_seo_supplier`
            WHERE id_seo_supplier
            NOT IN (
                SELECT id_supplier FROM ' . _DB_PREFIX_ . 'supplier
            )';
            return Db::getInstance()->execute($duplicates);
        }
    }

    public function updateSeoManufacturers()
    {
        set_time_limit(0);
        $sql =
            'INSERT INTO `' . _DB_PREFIX_ . 'ever_seo_manufacturer` (
                id_seo_manufacturer,
                id_shop,
                id_seo_lang
            )
            SELECT
                ml.id_manufacturer,
                ms.id_shop,
                ml.id_lang
            FROM `' . _DB_PREFIX_ . 'manufacturer_lang` ml
            INNER JOIN `' . _DB_PREFIX_ . 'manufacturer_shop` ms
               ON (
                    ms.id_manufacturer = ml.id_manufacturer
               )
            WHERE ms.id_manufacturer NOT IN (
                SELECT id_seo_manufacturer FROM `' . _DB_PREFIX_ . 'ever_seo_manufacturer`
            )';
        if (!Db::getInstance()->execute($sql)) {
            return false;
        } else {
            $duplicates = 'DELETE FROM `' . _DB_PREFIX_ . 'ever_seo_manufacturer`
            WHERE id_seo_manufacturer
            NOT IN (
                SELECT id_manufacturer FROM ' . _DB_PREFIX_ . 'manufacturer
            )';
            return Db::getInstance()->execute($duplicates);
        }
    }

    public function updateSeoCms()
    {
        set_time_limit(0);
        $sql =
            'INSERT INTO `' . _DB_PREFIX_ . 'ever_seo_cms` (
                id_seo_cms,
                id_shop,
                id_seo_lang,
                meta_title,
                meta_description,
                indexable,
                follow,
                allowed_sitemap
            )
            SELECT
                cl.id_cms,
                cl.id_shop,
                cl.id_lang,
                null,
                null,
                c.indexation,
                1,
                1
            FROM `' . _DB_PREFIX_ . 'cms_lang` cl
            INNER JOIN `' . _DB_PREFIX_ . 'cms` c
                ON c.id_cms = cl.id_cms
            WHERE c.id_cms NOT IN (
                SELECT id_seo_cms FROM `' . _DB_PREFIX_ . 'ever_seo_cms`
            )';
        if (!Db::getInstance()->execute($sql)) {
            return false;
        } else {
            $duplicates = 'DELETE FROM `' . _DB_PREFIX_ . 'ever_seo_cms`
            WHERE id_seo_cms
            NOT IN (
                SELECT id_cms FROM ' . _DB_PREFIX_ . 'cms
            )';
            return Db::getInstance()->execute($duplicates);
        }
    }

    public function updateSeoPageMetas()
    {
        set_time_limit(0);
        $sql =
            'INSERT INTO `' . _DB_PREFIX_ . 'ever_seo_pagemeta` (
                id_seo_pagemeta,
                id_shop,
                id_seo_lang,
                meta_title,
                meta_description
            )
            SELECT
                id_meta,
                id_shop,
                id_lang,
                title,
                description
            FROM `' . _DB_PREFIX_ . 'meta_lang`
            WHERE id_meta NOT IN (
                SELECT id_seo_pagemeta FROM `' . _DB_PREFIX_ . 'ever_seo_pagemeta`
            )';
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
    }

    public function updateSeoImages()
    {
        set_time_limit(0);
        $sql =
            'INSERT INTO `' . _DB_PREFIX_ . 'ever_seo_image` (
                id_seo_img,
                id_seo_product,
                id_shop,
                id_seo_lang
            )
            SELECT
                il.id_image,
                i.id_product,
                i.id_shop,
                il.id_lang
            FROM `' . _DB_PREFIX_ . 'image_lang` il
            INNER JOIN `' . _DB_PREFIX_ . 'image_shop` i
                ON i.id_image = il.id_image
            WHERE il.id_image NOT IN (
                SELECT id_seo_img FROM `' . _DB_PREFIX_ . 'ever_seo_image`
            )
            GROUP BY il.id_image, il.id_lang, i.id_shop';
        if (!Db::getInstance()->execute($sql)) {
            return false;
        } else {
            $duplicates = 'DELETE FROM `' . _DB_PREFIX_ . 'ever_seo_image`
            WHERE id_seo_img
            NOT IN (
                SELECT id_image FROM ' . _DB_PREFIX_ . 'image
            )';
            return Db::getInstance()->execute($duplicates);
        }
    }

    protected function deleteDuplicate()
    {
        $sql = [];

        // SEO products
        $sql[] = 'DELETE esp
        FROM `' . _DB_PREFIX_ . 'ever_seo_product` esp,
        `' . _DB_PREFIX_ . 'ever_seo_product` esp2
        WHERE esp.id_seo_product < esp2.id_seo_product
        AND esp.id_shop = esp2.id_shop
        AND esp.id_seo_lang = esp2.id_seo_lang';

        // SEO categories
        $sql[] = 'DELETE esp
        FROM `' . _DB_PREFIX_ . 'ever_seo_category` esp,
        `' . _DB_PREFIX_ . 'ever_seo_category` esp2
        WHERE esp.id_seo_category < esp2.id_seo_category
        AND esp.id_shop = esp2.id_shop
        AND esp.id_seo_lang = esp2.id_seo_lang';

        // SEO images
        $sql[] = 'DELETE esp
        FROM `' . _DB_PREFIX_ . 'ever_seo_image` esp,
        `' . _DB_PREFIX_ . 'ever_seo_image` esp2
        WHERE esp.id_seo_img < esp2.id_seo_img
        AND esp.id_shop = esp2.id_shop
        AND esp.id_seo_lang = esp2.id_seo_lang';

        // SEO mannufacturers
        $sql[] = 'DELETE esp
        FROM `' . _DB_PREFIX_ . 'ever_seo_manufacturer` esp,
        `' . _DB_PREFIX_ . 'ever_seo_manufacturer` esp2
        WHERE esp.id_seo_manufacturer < esp2.id_seo_manufacturer
        AND esp.id_shop = esp2.id_shop
        AND esp.id_seo_lang = esp2.id_seo_lang';

        // SEO suppliers
        $sql[] = 'DELETE esp
        FROM `' . _DB_PREFIX_ . 'ever_seo_supplier` esp,
        `' . _DB_PREFIX_ . 'ever_seo_supplier` esp2
        WHERE esp.id_seo_supplier < esp2.id_seo_supplier
        AND esp.id_shop = esp2.id_shop
        AND esp.id_seo_lang = esp2.id_seo_lang';

        // SEO CMS
        $sql[] = 'DELETE esp
        FROM `' . _DB_PREFIX_ . 'ever_seo_product` esp,
        `' . _DB_PREFIX_ . 'ever_seo_product` esp2
        WHERE esp.id_product < esp2.id_product
        AND esp.id_shop = esp2.id_shop
        AND esp.id_seo_lang = esp2.id_seo_lang';

        // SEO Meta
        $sql[] = 'DELETE esp
        FROM `' . _DB_PREFIX_ . 'ever_seo_cms` esp,
        `' . _DB_PREFIX_ . 'ever_seo_cms` esp2
        WHERE esp.id_seo_cms < esp2.id_seo_cms
        AND esp.id_shop = esp2.id_shop
        AND esp.id_seo_lang = esp2.id_seo_lang';

        foreach ($sql as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }
    }

    protected function deleteShopDuplicate()
    {
        $sql = [];

        // SEO products
        $sql[] = 'DELETE esp
        FROM `' . _DB_PREFIX_ . 'ever_seo_product` esp,
        `' . _DB_PREFIX_ . 'ever_seo_product` esp2
        WHERE esp.id_seo_product < esp2.id_seo_product
        AND esp.id_shop = esp2.id_shop
        AND esp.id_seo_lang = esp2.id_seo_lang';

        // SEO categories
        $sql[] = 'DELETE esp
        FROM `' . _DB_PREFIX_ . 'ever_seo_category` esp,
        `' . _DB_PREFIX_ . 'ever_seo_category` esp2
        WHERE esp.id_seo_category < esp2.id_seo_category
        AND esp.id_shop = esp2.id_shop
        AND esp.id_seo_lang = esp2.id_seo_lang';

        // SEO images
        $sql[] = 'DELETE esp
        FROM `' . _DB_PREFIX_ . 'ever_seo_image` esp,
        `' . _DB_PREFIX_ . 'ever_seo_image` esp2
        WHERE esp.id_seo_img < esp2.id_seo_img
        AND esp.id_shop = esp2.id_shop
        AND esp.id_seo_lang = esp2.id_seo_lang';

        // SEO mannufacturers
        $sql[] = 'DELETE esp
        FROM `' . _DB_PREFIX_ . 'ever_seo_manufacturer` esp,
        `' . _DB_PREFIX_ . 'ever_seo_manufacturer` esp2
        WHERE esp.id_seo_manufacturer < esp2.id_seo_manufacturer
        AND esp.id_shop = esp2.id_shop
        AND esp.id_seo_lang = esp2.id_seo_lang';

        // SEO suppliers
        $sql[] = 'DELETE esp
        FROM `' . _DB_PREFIX_ . 'ever_seo_supplier` esp,
        `' . _DB_PREFIX_ . 'ever_seo_supplier` esp2
        WHERE esp.id_seo_supplier < esp2.id_seo_supplier
        AND esp.id_shop = esp2.id_shop
        AND esp.id_seo_lang = esp2.id_seo_lang';

        // SEO CMS
        $sql[] = 'DELETE esp
        FROM `' . _DB_PREFIX_ . 'ever_seo_product` esp,
        `' . _DB_PREFIX_ . 'ever_seo_product` esp2
        WHERE esp.id_product < esp2.id_product
        AND esp.id_shop = esp2.id_shop
        AND esp.id_seo_lang = esp2.id_seo_lang';

        // SEO Meta
        $sql[] = 'DELETE esp
        FROM `' . _DB_PREFIX_ . 'ever_seo_cms` esp,
        `' . _DB_PREFIX_ . 'ever_seo_cms` esp2
        WHERE esp.id_seo_cms < esp2.id_seo_cms
        AND esp.id_shop = esp2.id_shop
        AND esp.id_seo_lang = esp2.id_seo_lang';

        foreach ($sql as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }
    }
#################### END SETTERS ####################
#################### START GETTERS ####################

    protected function getAllowedSitemapLangs()
    {
        return json_decode(
            Configuration::get(
                'EVERSEO_SITEMAP_LANGS'
            )
        );
    }

    protected function getBannedLangs()
    {
        return json_decode(
            Configuration::get(
                'EVERSEO_BULK_LANGS'
            )
        );
    }

    protected function getAllowedShortcodesLangs($getter)
    {
        $allowedLangs = json_decode(
            Configuration::get(
                $getter
            )
        );
        if (!$allowedLangs) {
            $allowedLangs = [(int) Configuration::get('PS_LANG_DEFAULT')];
        }
        return $allowedLangs;
    }

    protected function getEverSeoTables()
    {
        $seotable = [];
        $seotable[] = 'ever_seo_category';
        $seotable[] = 'ever_seo_product';
        $seotable[] = 'ever_seo_image';
        $seotable[] = 'ever_seo_cms';
        $seotable[] = 'ever_seo_pagemeta';
        $seotable[] = 'ever_seo_manufacturer';
        $seotable[] = 'ever_seo_supplier';
        return $seotable;
    }

    protected function getSeoTableByPsTable($psTable)
    {
        switch ($psTable) {
            case 'category_lang':
                $seotable = 'ever_seo_category';
                break;

            case 'product_lang':
                $seotable = 'ever_seo_product';
                break;

            case 'image_lang':
                $seotable = 'ever_seo_image';
                break;

            case 'cms_lang':
                $seotable = 'ever_seo_cms';
                break;

            case 'meta_lang':
                $seotable = 'ever_seo_pagemeta';
                break;

            case 'manufacturer_lang':
                $seotable = 'ever_seo_manufacturer';
                break;

            case 'supplier_lang':
                $seotable = 'ever_seo_supplier';
                break;

            default:
                $seotable = false;
                break;
        }
        return $seotable;
    }

    protected function getSeoObjByPsTable($psTable)
    {
        switch ($psTable) {
            case 'ever_seo_category':
                $seoObj = 'id_seo_category';
                break;

            case 'ever_seo_product':
                $seoObj = 'id_seo_product';
                break;

            case 'ever_seo_image':
                $seoObj = 'id_seo_img';
                break;

            case 'ever_seo_cms':
                $seoObj = 'id_seo_cms';
                break;

            case 'ever_seo_manufacturer':
                $seoObj = 'id_seo_manufacturer';
                break;

            case 'ever_seo_supplier':
                $seoObj = 'id_seo_supplier';
                break;

            case 'ever_seo_pagemeta':
                $seoObj = 'id_seo_pagemeta';
                break;
        }
        return $seoObj;
    }

    protected function getColumnStructure($tableName, $columnName, $default)
    {
        $describe = Db::getInstance()->ExecuteS('DESCRIBE '. _DB_PREFIX_ . pSQL($tableName));
        foreach ($describe as $columnDatas) {
            if ($columnDatas['Field'] == $columnName
                && (int) $columnDatas['Default'] != (int) $default) {
                return $this->setColumnDefault($tableName, $columnName, (int) $default);
            }
        }
    }

    protected function getAllowedGeneratorCategories($isProduct = false)
    {
        if ((bool) $isProduct === true) {
            $categories = json_decode(
                Configuration::get(
                    'EVERSEO_PGENERATOR_CATEGORIES'
                )
            );
        } else {
            $categories = json_decode(
                Configuration::get(
                    'EVERSEO_CGENERATOR_CATEGORIES'
                )
            );
        }
        if (!is_array($categories)) {
            $categories = [$categories];
        }
        return $categories;
    }

    protected function uploadRedirectionFile()
    {
        /* upload the file */
        if (isset($_FILES['redirection_file'])
            && isset($_FILES['redirection_file']['tmp_name'])
            && !empty($_FILES['products_file']['tmp_name'])
        ) {
            $filename = $_FILES['redirection_file']['name'];
            $exploded_filename = explode('.', $filename);
            $ext = end($exploded_filename);
            if (Tools::strtolower($ext) != 'xlsx') {
                $this->postErrors[] = $this->l('Error : File is not valid.');
                return false;
            }
            if (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS'))
                || !move_uploaded_file($_FILES['redirection_file']['tmp_name'], $tmp_name)
            ) {
                return false;
            }
            copy($tmp_name, EverPsSeoTools::INPUT_FOLDER . 'redirect.xlsx');
            $this->html .= $this->displayConfirmation($this->l('File has been uploaded, please wait for cron task'));
        }
    }

    protected function uploadProductsFile()
    {
        /* upload the file */
        if (isset($_FILES['products_file'])
            && isset($_FILES['products_file']['tmp_name'])
            && !empty($_FILES['products_file']['tmp_name'])
        ) {
            $filename = $_FILES['products_file']['name'];
            $exploded_filename = explode('.', $filename);
            $ext = end($exploded_filename);
            if (Tools::strtolower($ext) != 'xlsx') {
                $this->postErrors[] = $this->l('Error : File is not valid.');
                return false;
            }
            if (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS'))
                || !move_uploaded_file($_FILES['products_file']['tmp_name'], $tmp_name)
            ) {
                return false;
            }
            copy($tmp_name, EverPsSeoTools::INPUT_FOLDER . 'products.xlsx');
            $this->html .= $this->displayConfirmation($this->l('File has been uploaded, please wait for cron task'));
        }
    }

    protected function uploadCategoriesFile()
    {
        /* upload the file */
        if (isset($_FILES['categories_file'])
            && isset($_FILES['categories_file']['tmp_name'])
            && !empty($_FILES['categories_file']['tmp_name'])
        ) {
            $filename = $_FILES['categories_file']['name'];
            $exploded_filename = explode('.', $filename);
            $ext = end($exploded_filename);
            if (Tools::strtolower($ext) != 'xlsx') {
                $this->postErrors[] = $this->l('Error : File is not valid.');
                return false;
            }
            if (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS'))
                || !move_uploaded_file($_FILES['categories_file']['tmp_name'], $tmp_name)
            ) {
                return false;
            }
            copy($tmp_name, EverPsSeoTools::INPUT_FOLDER . 'categories.xlsx');
            $this->html .= $this->displayConfirmation($this->l('File has been uploaded, please wait for cron task'));
        }
    }
#################### END GETTERS ####################
    /**
     * Get a single configuration value (in multiple languages).
     *
     * @param string $key Configuration Key
     * @param int $idShopGroup Shop Group ID
     * @param int $idShop Shop ID
     *
     * @return array Values in multiple languages
     */
    public static function getConfigInMultipleLangs($key, $idShopGroup = null, $idShop = null)
    {
        $resultsArray = [];
        foreach (Language::getIDs() as $idLang) {
            $resultsArray[$idLang] = Configuration::get($key, $idLang, $idShopGroup, $idShop);
        }

        return $resultsArray;
    }
}
