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

class AdminEverPsSeoConfigureController extends ModuleAdminController
{
    private $html;
    private $postErrors = [];
    private $postSuccess = [];

    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = false;
        $this->context = Context::getContext();
        $moduleConfUrl  = 'index.php?controller=AdminModules&configure=everpsseo&token=';
        $moduleConfUrl .= Tools::getAdminTokenLite('AdminModules');
        $this->everpsseo_module = $moduleConfUrl;
        parent::__construct();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addCSS(_PS_MODULE_DIR_.'everpsseo/views/css/ever.css');
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Context::getContext()->getTranslator()->trans(
            $string,
            [],
            'Modules.Everpsseo.Admineverpsseoconfigurecontroller'
        );
    }

    public function initContent()
    {
        $this->display = 'view';
        $this->page_header_toolbar_title = $this->toolbar_title = 'Patterns design sample';

        parent::initContent();

        $this->content .= $this->renderForm();
        $this->context->smarty->assign(
            array(
                'everseo_dir' => _PS_BASE_URL_ . '/modules/everpsseo/views/img/',
                'languages' => Language::getLanguages(false),
            )
        );

        $this->context->smarty->assign(array('content' => $this->content));
    }

    public function renderForm()
    {
        $this->html = '';
        $this->html .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . '/everpsseo/views/templates/admin/header.tpl');
        if (Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            $this->errors[] = $this->l(
                'You have to select a shop before configuring this module.'
            );
        }
        
        if (Tools::isSubmit('submitFinishConfig')) {
            $this->postValidation();

            if (!count($this->postErrors)) {
                $this->postProcess();
                Configuration::updateValue('EVERSEO_CONFIGURE', true);
                Tools::redirectAdmin($this->everpsseo_module);
            }
        }
        if (Tools::isSubmit('submitAddconfiguration')) {
            $this->postValidation();

            if (!count($this->postErrors)) {
                $this->postProcess();
                $this->html .= $this->context->smarty->fetch(
                    _PS_MODULE_DIR_ . '/everpsseo/views/templates/admin/success.tpl'
                );
            }
        }
        // Display errors
        if (count($this->postErrors)) {
            foreach ($this->postErrors as $error) {
                $this->html .= $this->displayError($error);
            }
        }

        $orderby = array(
            array(
                'id_orderby' => 0,
                'name' => $this->l('Oldest value found')
            ),
            array(
                'id_orderby' => 1,
                'name' => $this->l('Most recent value found')
            ),
        );

        $priority = array(
            array(
                'id_priority' => 1,
                'name' => $this->l('Products, categories, tags')
            ),
            array(
                'id_priority' => 2,
                'name' => $this->l('Products, tags, categories')
            ),
            array(
                'id_priority' => 3,
                'name' => $this->l('Categories, products, tags')
            ),
            array(
                'id_priority' => 4,
                'name' => $this->l('Categories, tags, products')
            ),
            array(
                'id_priority' => 5,
                'name' => $this->l('Tags, products, categories')
            ),
            array(
                'id_priority' => 6,
                'name' => $this->l('Tags, categories, products')
            ),
        );

        $qualityRiskLevel = array(
            array(
                'id_quality_risk_level' => 0,
                'name' => $this->l('Shaggy quality risk')
            ),
            array(
                'id_quality_risk_level' => 1,
                'name' => $this->l('God quality risk')
            ),
            array(
                'id_quality_risk_level' => 2,
                'name' => $this->l('Expert quality risk')
            ),
            array(
                'id_quality_risk_level' => 3,
                'name' => $this->l('High quality risk')
            ),
            array(
                'id_quality_risk_level' => 4,
                'name' => $this->l('Advanced quality risk')
            ),
            array(
                'id_quality_risk_level' => 5,
                'name' => $this->l('Normal quality risk')
            ),
            array(
                'id_quality_risk_level' => 6,
                'name' => $this->l('Low quality risk')
            ),
        );

        $redirectCodes = array(
            array(
                'id_redirect' => '301',// 301 Moved Permanently
                'name' => '301'
            ),
            array(
                'id_redirect' => '302',// 302 Found
                'name' => '302'
            ),
            array(
                'id_redirect' => '303',// 303 See Other
                'name' => '303'
            ),
            array(
                'id_redirect' => '307',// 307 Temporary Redirect
                'name' => '307'
            ),
        );
        $this->fields_value = $this->getConfigFormValues();

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('SEO basic configuration'),
                'icon' => 'icon-smile'
            ),
            'tabs' => array(
                'ever_modules' => $this->l('First advices & requirements'),
                'general_seo' => $this->l('General SEO settings'),
                'social_networks' => $this->l('Social networks'),
                'url_richsnippets' => $this->l('URL and rich snippets'),
                'redirections' => $this->l('404 pages'),
                'index_sitemaps' => $this->l('Indexation / sitemaps'),
                'sea_settings' => $this->l('SEA settings'),
            ),
            'input' => array(
                // General SEO settings
                array(
                    'type' => 'select',
                    'label' => $this->l('Ever Quality level ?'),
                    'desc' => $this->l('Notation system level'),
                    'hint' => $this->l('At what level of difficulty do you want to start?'),
                    'name' => 'EVERSEO_QUALITY_LEVEL',
                    'required' => true,
                    'options' => array(
                        'query' => $qualityRiskLevel,
                        'id' => 'id_quality_risk_level',
                        'name' => 'name'
                    ),
                    'tab' => 'general_seo',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Analytics tracking code'),
                    'desc' => $this->l('This is your GA_TRACKING_ID'),
                    'hint' => $this->l('Format is often UA-12345678-1'),
                    'required' => false,
                    'name' => 'EVERSEO_ANALYTICS',
                    'lang' => false,
                    'tab' => 'general_seo',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Search Console site verification'),
                    'desc' => $this->l('Please add Search Console meta content'),
                    'hint' => $this->l('Meta content given by Google Search Console'),
                    'required' => false,
                    'name' => 'EVERSEO_SEARCHCONSOLE',
                    'lang' => false,
                    'tab' => 'general_seo',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Use rich snippets'),
                    'desc' => $this->l('Will add prices on search console'),
                    'hint' => $this->l('Will not add notations, please use King Avis'),
                    'name' => 'EVERSEO_RSNIPPETS',
                    'is_bool' => true,
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
                    'tab' => 'general_seo',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Add canonical URL'),
                    'desc' => $this->l('Only if your theme does not support it'),
                    'hint' => $this->l('Is fully required by Google'),
                    'name' => 'EVERSEO_CANONICAL',
                    'is_bool' => true,
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
                    'tab' => 'general_seo',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Add hreflangs'),
                    'desc' => $this->l('Is your site multilingual ?'),
                    'hint' => $this->l('Set not if your site is not multilingual'),
                    'name' => 'EVERSEO_HREF_LANG',
                    'is_bool' => true,
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
                    'tab' => 'general_seo',
                ),
                // Social networks
                array(
                    'type' => 'switch',
                    'label' => $this->l('Add Facebook Open Graph metas'),
                    'desc' => $this->l('Do you share pages to Facebook ?'),
                    'hint' => $this->l('Only if your theme does not support it'),
                    'name' => 'EVERSEO_USE_OPENGRAPH',
                    'is_bool' => true,
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
                    'tab' => 'social_networks',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Add twitter metas'),
                    'desc' => $this->l('Do you have Twitter account ?'),
                    'hint' => $this->l('You should !'),
                    'name' => 'EVERSEO_USE_TWITTER',
                    'is_bool' => true,
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
                    'tab' => 'social_networks',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Twitter account'),
                    'desc' => $this->l('Add your @, no spaces'),
                    'hint' => $this->l('You can add a false @ :-)'),
                    'name' => 'EVERSEO_TWITTER_NAME',
                    'lang' => false,
                    'tab' => 'social_networks',
                ),
                // 404 redirections
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
                    'tab' => 'redirections',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Redirect to products ?'),
                    'desc' => $this->l('Do you want to redirect 404 to products ?'),
                    'hint' => $this->l('You are on an e-shop... Of course you want !'),
                    'name' => 'EVERSEO_PRODUCT',
                    'is_bool' => true,
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
                    'tab' => 'redirections',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Redirect to categories ?'),
                    'desc' => $this->l('Will redirect 404 to categories'),
                    'hint' => $this->l('Perhaps categories are more useful than products ?'),
                    'name' => 'EVERSEO_CATEGORY',
                    'is_bool' => true,
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
                    'tab' => 'redirections',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Redirect using tags ?'),
                    'desc' => $this->l('Will redirect to products using tags'),
                    'hint' => $this->l('You should if you use tags on products'),
                    'name' => 'EVERSEO_TAGS',
                    'is_bool' => true,
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
                    'tab' => 'redirections',
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
                    ),
                    'tab' => 'redirections',
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
                        'name' => 'name'
                    ),
                    'tab' => 'redirections',
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
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'tab' => 'redirections',
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
                    ),
                    'tab' => 'redirections',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable custom 404 page'),
                    'desc' => $this->l('Override 404 page with Ever SEO'),
                    'hint' => $this->l('Add some custom content on 404 page !'),
                    'name' => 'EVERSEO_CUSTOM_404',
                    'is_bool' => true,
                    'tab' => 'redirections',
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
                    'label' => $this->l('Use search bar on module 404 page'),
                    'desc' => $this->l('Will add a search form on your 404 page'),
                    'hint' => $this->l('Only if module\'s custom 404 page is used'),
                    'name' => 'EVERSEO_404_SEARCH',
                    'is_bool' => true,
                    'tab' => 'redirections',
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
                    'type' => 'textarea',
                    'label' => $this->l('404 top content'),
                    'desc' => $this->l('Type 404 top content'),
                    'hint' => $this->l('Will appear on top of 404 page'),
                    'required' => false,
                    'name' => 'EVERSEO_404_TOP',
                    'lang' => true,
                    'autoload_rte' => true,
                    'tab' => 'redirections',
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
                    'tab' => 'redirections',
                ),
                // Sitemaps and indexation
                array(
                    'type' => 'select',
                    'label' => 'Allowed languages in sitemap',
                    'desc' => 'Choose allowed langs for sitemaps',
                    'hint' => 'Useful for multilingual sites',
                    'required' => true,
                    'name' => 'EVERSEO_SITEMAP_LANGS[]',
                    'class' => 'chosen',
                    'identifier' => 'name',
                    'multiple' => true,
                    'options' => array(
                        'query' => Language::getLanguages(false),
                        'id' => 'id_lang',
                        'name' => 'name',
                    ),
                    'tab' => 'index_sitemaps',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Index/sitemap all available products'),
                    'desc' => $this->l('Will index/sitemap all available products'),
                    'hint' => $this->l('Disabled products will be noindex/out of sitemaps'),
                    'name' => 'EVERSEO_INDEX_PRODUCT',
                    'is_bool' => true,
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
                    'tab' => 'index_sitemaps',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Index/sitemap all available categories'),
                    'desc' => $this->l('Will index/sitemap all available categories'),
                    'hint' => $this->l('Disabled categories will be noindex/out of sitemap'),
                    'name' => 'EVERSEO_INDEX_CATEGORY',
                    'is_bool' => true,
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
                    'tab' => 'index_sitemaps',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Index/sitemap all available manufacturers'),
                    'desc' => $this->l('Will index/sitemap all available manufacturers'),
                    'hint' => $this->l('Disabled manufacturers will be noindex/out of sitemap'),
                    'name' => 'EVERSEO_INDEX_MANUFACTURER',
                    'is_bool' => true,
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
                    'tab' => 'index_sitemaps',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Index/sitemap all available suppliers'),
                    'desc' => $this->l('Will index/sitemap all available suppliers'),
                    'hint' => $this->l('Disabled suppliers will be noindex/out of sitemap'),
                    'name' => 'EVERSEO_INDEX_SUPPLIER',
                    'is_bool' => true,
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
                    'tab' => 'index_sitemaps',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Index/sitemap all available CMS'),
                    'desc' => $this->l('Will index/sitemap all available CMS'),
                    'hint' => $this->l('Disabled CMS will be noindex/out of sitemap'),
                    'name' => 'EVERSEO_INDEX_CMS',
                    'is_bool' => true,
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
                    'tab' => 'index_sitemaps',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Index page metas'),
                    'desc' => $this->l('Will index/sitemap all available pages'),
                    'hint' => $this->l('Else all pages will be noindex/out of sitemap'),
                    'name' => 'EVERSEO_INDEX_PAGE_META',
                    'is_bool' => true,
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
                    'tab' => 'index_sitemaps',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Adwords tracking code'),
                    'required' => false,
                    'name' => 'EVERSEO_ADWORDS',
                    'lang' => false,
                    'tab' => 'sea_settings',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Adwords event snippet code'),
                    'required' => false,
                    'name' => 'EVERSEO_ADWORDS_SENDTO',
                    'lang' => false,
                    'tab' => 'sea_settings',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-primary pull-right',
            ),
            'buttons' => array(
                'updateSeoProducts' => array(
                    'name' => 'submitFinishConfig',
                    'type' => 'submit',
                    'class' => 'btn btn-success',
                    'icon' => 'process-icon-save',
                    'title' => $this->l('Finish')
                ),
            ),
        );
        $this->html .= parent::renderForm();
        $this->html .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . '/everpsseo/views/templates/admin/footer.tpl');
        return $this->html;
    }
    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $everseo_404_top = [];
        $everseo_404_bottom = [];
        foreach (Language::getLanguages(false) as $lang) {
            $everseo_404_top[$lang['id_lang']] = (Tools::getValue('EVERSEO_404_TOP_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_404_TOP_' . $lang['id_lang']) : '';
            $everseo_404_bottom[$lang['id_lang']] = (Tools::getValue('EVERSEO_404_BOTTOM_' . $lang['id_lang']))
            ? Tools::getValue('EVERSEO_404_BOTTOM_' . $lang['id_lang']) : '';
        }
        return array(
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
            'EVERSEO_404_TOP' => Configuration::getConfigInMultipleLangs('EVERSEO_404_TOP'),
            'EVERSEO_404_BOTTOM' => Configuration::getConfigInMultipleLangs('EVERSEO_404_BOTTOM'),

            //General SEO
            'EVERSEO_QUALITY_LEVEL' => Configuration::get(
                'EVERSEO_QUALITY_LEVEL'
            ),
            'EVERSEO_ANALYTICS' => Configuration::get(
                'EVERSEO_ANALYTICS'
            ),
            'EVERSEO_RSNIPPETS' => Configuration::get(
                'EVERSEO_RSNIPPETS'
            ),
            'EVERSEO_CANONICAL' => Configuration::get(
                'EVERSEO_CANONICAL'
            ),
            'EVERSEO_SEARCHCONSOLE' => Configuration::get(
                'EVERSEO_SEARCHCONSOLE'
            ),
            
            //Multilingual tags
            'EVERSEO_SITEMAP_QTY_ELEMENTS' => Configuration::get(
                'EVERSEO_SITEMAP_QTY_ELEMENTS'
            ),
            'EVERSEO_SITEMAP_LANGS[]' => Tools::getValue(
                'EVERSEO_SITEMAP_LANGS',
                json_decode(
                    Configuration::get(
                        'EVERSEO_SITEMAP_LANGS'
                    )
                )
            ),
            'EVERSEO_HREF_LANG' => Configuration::get(
                'EVERSEO_HREF_LANG'
            ),
            // Indexation and sitemaps
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
            // Social networks
            'EVERSEO_USE_TWITTER' => Configuration::get(
                'EVERSEO_USE_TWITTER'
            ),
            'EVERSEO_TWITTER_NAME' => Configuration::get(
                'EVERSEO_TWITTER_NAME'
            ),
            'EVERSEO_USE_OPENGRAPH' => Configuration::get(
                'EVERSEO_USE_OPENGRAPH'
            ),
            // SEA
            'EVERSEO_FBPIXEL' => Configuration::get(
                'EVERSEO_FBPIXEL'
            ),
            'EVERPSAWIN_TRACKING_CODE' => Configuration::get(
                'EVERPSAWIN_TRACKING_CODE'
            ),
            'EVERPSAWIN_CONVERSION_CODE' => Configuration::get(
                'EVERPSAWIN_CONVERSION_CODE'
            ),
            'EVERPSAWIN_TEST' => Configuration::get(
                'EVERPSAWIN_TEST'
            ),
            'EVERSEO_ADWORDS' => Configuration::get(
                'EVERSEO_ADWORDS'
            ),
            'EVERSEO_ADWORDS_SENDTO' => Configuration::get(
                'EVERSEO_ADWORDS_SENDTO'
            ),
            //Keywords strategy
            'EVERSEO_KEYWORDS_STRATEGY' => Configuration::get(
                'EVERSEO_KEYWORDS_STRATEGY'
            ),
        );
    }

    public function postValidation()
    {
        if (Tools::isSubmit('submitAddconfiguration')) {
            if (Tools::getValue('EVERSEO_LANG')
                && !Validate::isInt(Tools::getValue('EVERSEO_LANG'))
            ) {
                $this->posterrors[] = $this->l('error : [Language] is not valid');
            }

            if (Tools::getValue('EVERSEO_PRODUCT')
                && !Validate::isBool(Tools::getValue('EVERSEO_PRODUCT'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Redirect to products" is not valid');
            }

            if (Tools::getValue('EVERSEO_CATEGORY')
                && !Validate::isBool(Tools::getValue('EVERSEO_CATEGORY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Redirect to categories" is not valid');
            }

            if (Tools::getValue('EVERSEO_TAGS')
                && !Validate::isBool(Tools::getValue('EVERSEO_TAGS'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Redirect using tags" is not valid');
            }

            if (Tools::getValue('EVERSEO_PRIORITY')
                && !Validate::isInt(Tools::getValue('EVERSEO_PRIORITY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Redirect priorities" is not valid');
            }

            if (Tools::getValue('EVERSEO_REDIRECT')
                && !Validate::isInt(Tools::getValue('EVERSEO_REDIRECT'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Use 301 redirect" is not valid');
            }

            if (Tools::getValue('EVERSEO_NOT_FOUND')
                && !Validate::isBool(Tools::getValue('EVERSEO_NOT_FOUND'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "What if is not found" is not valid');
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

            if (!Tools::getIsset('EVERSEO_QUALITY_LEVEL')
                || !Validate::isInt(Tools::getValue('EVERSEO_QUALITY_LEVEL'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Ever Quality level" is not valid');
            }

            if (Tools::getValue('EVERSEO_ANALYTICS')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_ANALYTICS'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Analytics tracking code" is not valid');
            }

            if (Tools::getValue('EVERSEO_SEARCHCONSOLE')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_SEARCHCONSOLE'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Search console" is not valid');
            }

            if (Tools::getValue('EVERSEO_FBPIXEL')
                && !Validate::isGenericName(Tools::getValue('EVERSEO_FBPIXEL'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Facebook pixel" is not valid');
            }

            if (!Tools::getIsset('EVERSEO_ADWORDS')
                || !Validate::isGenericName(Tools::getValue('EVERSEO_ADWORDS'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Adwords tracking code" is not valid');
            }

            if (!Tools::getIsset('EVERSEO_ADWORDS_SENDTO')
                || !Validate::isGenericName(Tools::getValue('EVERSEO_ADWORDS_SENDTO'))) {
                $this->postErrors[] = $this->l('Error : The field "Adwords event snippet code" is not valid');
            }

            if (!Tools::getIsset('EVERSEO_USE_OPENGRAPH')
                || !Validate::isBool(Tools::getValue('EVERSEO_USE_OPENGRAPH'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Add Facebook Open Graph metas" is not valid');
            }

            if (!Tools::getIsset('EVERSEO_USE_TWITTER')
                || !Validate::isBool(Tools::getValue('EVERSEO_USE_TWITTER'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Add twitter metas" is not valid');
            }

            if (!Tools::getIsset('EVERSEO_TWITTER_NAME')
                || !Validate::isGenericName(Tools::getValue('EVERSEO_TWITTER_NAME'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Twitter account" is not valid');
            }

            if (!Tools::getIsset('EVERSEO_CANONICAL')
                || !Validate::isBool(Tools::getValue('EVERSEO_CANONICAL'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Canonical URL" is not valid');
            }

            if (!Tools::getIsset('EVERSEO_HREF_LANG')
                || !Validate::isBool(Tools::getValue('EVERSEO_HREF_LANG'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Add hreflangs" is not valid');
            }

            if (!Tools::getIsset('EVERSEO_RSNIPPETS')
                || !Validate::isBool(Tools::getValue('EVERSEO_RSNIPPETS'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Google Rich Snippets" is not valid');
            }

            if (!Tools::getValue('EVERSEO_SITEMAP_LANGS')
                || !Validate::isArrayWithIds(Tools::getValue('EVERSEO_SITEMAP_LANGS'))
            ) {
                $this->posterrors[] = $this->l('error : [Sitemap langs] is not valid');
            }

            if (!Tools::getIsset('EVERSEO_INDEX_CATEGORY')
                || !Validate::isBool(Tools::getValue('EVERSEO_INDEX_CATEGORY'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Index categories" is not valid');
            }

            if (!Tools::getIsset('EVERSEO_INDEX_PRODUCT')
                || !Validate::isBool(Tools::getValue('EVERSEO_INDEX_PRODUCT'))
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
            }
        }
    }

    /**
     * Save form data.
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitAddconfiguration')) {
            $form_values = $this->getConfigFormValues();
            $everseo_404_top = [];
            $everseo_404_bottom = [];
            foreach (Language::getLanguages(false) as $lang) {
                $everseo_404_top[$lang['id_lang']] = (Tools::getValue('EVERSEO_404_TOP_' . $lang['id_lang']))
                ? Tools::getValue('EVERSEO_404_TOP_' . $lang['id_lang']) : '';
                $everseo_404_bottom[$lang['id_lang']] = (Tools::getValue('EVERSEO_404_BOTTOM_' . $lang['id_lang']))
                ? Tools::getValue('EVERSEO_404_BOTTOM_' . $lang['id_lang']) : '';
            }

            foreach (array_keys($form_values) as $key) {
                if ($key == 'EVERSEO_SITEMAP_LANGS[]') {
                    Configuration::updateValue(
                        'EVERSEO_SITEMAP_LANGS',
                        json_encode(Tools::getValue('EVERSEO_SITEMAP_LANGS')),
                        true
                    );
                } elseif ($key == 'EVERSEO_404_TOP') {
                    Configuration::updateValue('EVERSEO_404_TOP', $everseo_404_top, true);
                } elseif ($key == 'EVERSEO_404_BOTTOM') {
                    Configuration::updateValue('EVERSEO_404_BOTTOM', $everseo_404_bottom, true);
                } else {
                    Configuration::updateValue($key, Tools::getValue($key));
                }
            }
            if ((bool) Configuration::get('EVERSEO_INDEX_PRODUCT')) {
                $this->indexObject('product');
            }
            if ((bool) Configuration::get('EVERSEO_INDEX_CATEGORY')) {
                $this->indexObject('category');
            }
            if ((bool) Configuration::get('EVERSEO_INDEX_CMS')) {
                $this->indexObject('cms');
            }
            if ((bool) Configuration::get('EVERSEO_INDEX_MANUFACTURER')) {
                $this->indexObject('manufacturer');
            }
            if ((bool) Configuration::get('EVERSEO_INDEX_SUPPLIER')) {
                $this->indexObject('supplier');
            }
            if ((bool) Configuration::get('EVERSEO_INDEX_PAGE_META')) {
                $this->indexObject('pagemeta');
            }
        }
    }

    private function indexObject($object)
    {
        switch ($object) {
            case 'product':
                $seo_table = 'ever_seo_product';
                $seo_element = 'id_seo_product';
                $ps_table = 'product';
                $ps_element = 'id_product';
                $where = 'active = 1';
                break;

            case 'category':
                $seo_table = 'ever_seo_category';
                $seo_element = 'id_seo_category';
                $ps_table = 'category';
                $ps_element = 'id_category';
                $where = 'active = 1';
                break;

            case 'manufacturer':
                $seo_table = 'ever_seo_manufacturer';
                $seo_element = 'id_seo_manufacturer';
                $ps_table = 'manufacturer';
                $ps_element = 'id_manufacturer';
                $where = 'active = 1';
                break;

            case 'supplier':
                $seo_table = 'ever_seo_supplier';
                $seo_element = 'id_seo_supplier';
                $ps_table = 'supplier';
                $ps_element = 'id_supplier';
                $where = 'active = 1';
                break;

            case 'cms':
                $seo_table = 'ever_seo_cms';
                $seo_element = 'id_seo_cms';
                $ps_table = 'cms';
                $ps_element = 'id_cms';
                $where = 'indexation = 1';
                break;

            case 'pagemeta':
                $seo_table = 'ever_seo_pagemeta';
                $seo_element = 'id_seo_pagemeta';
                $ps_table = 'meta';
                $ps_element = 'id_meta';
                $where = '1 = 1';
                break;
        }
        $sql = [];
        // Set indexable
        $sql[] = 'UPDATE '. _DB_PREFIX_ .pSQL((string) $seo_table).'
        SET indexable = 1
        WHERE '.pSQL((string) $seo_element).' IN
        (
            SELECT '.pSQL((string) $ps_element).'
            FROM '. _DB_PREFIX_ .pSQL((string) $ps_table).'
            WHERE '.pSQL((string) $where).'
        )';
        // Set allowed_sitemap
        $sql[] = 'UPDATE '. _DB_PREFIX_ .pSQL((string) $seo_table).'
        SET allowed_sitemap = 1
        WHERE ' . $seo_element.' IN
        (
            SELECT '.pSQL((string) $ps_element).'
            FROM '. _DB_PREFIX_ .pSQL((string) $ps_table).'
            WHERE '.pSQL((string) $where).'
        )';
        foreach ($sql as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }
    }

    protected function displayError($message, $description = false)
    {
        /**
         * Set error message and description for the template.
         */
        array_push($this->errors, $this->module->l($message), $description);

        return $this->context->smarty->fetch('error.tpl');
    }
}
