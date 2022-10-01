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

require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoRedirect.php';

class AdminEverPsSeoRedirectController extends ModuleAdminController
{
    private $html;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = false;
        $this->table = 'ever_seo_redirect';
        $this->className = 'EverPsSeoRedirect';
        $this->context = Context::getContext();
        $this->identifier = "id_ever_seo_redirect";

        $this->isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;
        $moduleConfUrl  = 'index.php?controller=AdminModules&configure=everpsseo&token=';
        $moduleConfUrl .= Tools::getAdminTokenLite('AdminModules');

        $this->context->smarty->assign(array(
            'moduleConfUrl' => (string)$moduleConfUrl,
            'image_dir' => _PS_BASE_URL_ . '/modules/everpsseo/views/img/',
            'redirects_enabled' => (bool)Configuration::get('EVERSEO_REWRITE')
        ));

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            ),
        );

        $this->_where = 'AND a.id_shop ='.(int)$this->context->shop->id;

        $this->fields_list = array(
            'id_ever_seo_redirect' => array(
                'title' => $this->l('ID'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'not_found' => array(
                'title' => $this->l('404 found'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'everfrom' => array(
                'title' => $this->l('Comes from'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'redirection' => array(
                'title' => $this->l('Redirects to'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'count' => array(
                'title' => $this->l('Count'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'type' => 'bool',
                'active' => 'status',
                'orderby' => false,
                'class' => 'fixed-width-sm'
            ),
            'code' => array(
                'title' => $this->l('Code'),
                'align' => 'left',
                'width' => 'auto'
            ),
        );

        $this->colorOnBackground = true;

        parent::__construct();
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
                'Modules.Everpsseo.Admineverpsseoredirectcontroller'
            );
        }

        return parent::l($string, $class, $addslashes, $htmlentities);
    }

    /**
     * Gestion de la toolbar
     */
    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
            'desc' => $this->l('Add new element'),
            'icon' => 'process-icon-new'
        );
        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        $this->html = '';

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->toolbar_title = $this->l('Registered 404');

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
            $this->errors[] = $this->l('You have to select a shop before creating or editing new redirections.');
        }

        if (count($this->errors)) {
            return false;
        }

        $redirectCodes = array(
            array(
                'id_redirect' => '301',
                'name' => '301'
            ),
            array(
                'id_redirect' => '302',
                'name' => '302'
            ),
            array(
                'id_redirect' => '303',
                'name' => '303'
            ),
        );

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
                    'label' => $this->l('404'),
                    'required' => true,
                    'name' => 'not_found',
                    'lang' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Redirection'),
                    'required' => true,
                    'name' => 'redirection',
                    'lang' => false
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Redirection code'),
                    'name' => 'code',
                    'required' => true,
                    'lang' => false,
                    'options' => array(
                        'query' => $redirectCodes,
                        'id' => 'id_redirect',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'bool' => true,
                    'lang' => false,
                    'values' => array(
                        array(
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Activate')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Desactivate')
                        )
                    )
                )
            )
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        parent::postProcess();

        if (Tools::isSubmit('save')) {
            if (!Tools::getValue('not_found')
                || !Validate::isUrl(Tools::getValue('not_found'))
            ) {
                 $this->errors[] = $this->l('not_found is invalid');
            }
            if (!Tools::getValue('redirection')
                || !Validate::isUrl(Tools::getValue('redirection'))
            ) {
                 $this->errors[] = $this->l('redirection is invalid');
            }
            if (!Tools::getValue('code')
                || !Validate::isUnsignedInt(Tools::getValue('code'))
            ) {
                 $this->errors[] = $this->l('code is invalid');
            }
            $newRedirect = new EverPsSeoRedirect(Tools::getValue('id_ever_seo_redirect'));

            $redirectExists = EverPsSeoRedirect::ifRedirectExists(
                Tools::getValue('redirection'),
                (int)$this->context->shop->id
            );

            if (!$redirectExists) {
                $newRedirect->redirection = Tools::getValue('redirection');
                $newRedirect->not_found = Tools::getValue('not_found');
                $newRedirect->id_shop = (int)Context::getContext()->shop->id;
                $newRedirect->code = Tools::getValue('code');
                $newRedirect->active = Tools::getValue('active');

                if (!$newRedirect->save()) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t save the current object');
                } else {
                    // Quick Fix
                    Db::getInstance()->update(
                        'ever_seo_redirect',
                        array(
                            'active' => (int)Tools::getValue('active')
                        ),
                        'id_ever_seo_redirect = '.(int)Tools::getValue('id_ever_seo_redirect')
                    );
                    if ((bool)Configuration::get('EVERHTACCESS_404') === true) {
                        $this->module->hookActionHtaccessCreate();
                    }
                    Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                }
            }
        }

        if (Tools::isSubmit('deleteever_redirects')) {
            $redirect = new EverPsSeoRedirect(Tools::getValue('id_ever_seo_redirect'));

            if (!$redirect->delete()) {
                $this->errors[] = $this->l('An error has occurred : Can\'t delete the current object');
            }
        }

        if (Tools::isSubmit('submitBulkdeleteever_redirects')) {
            $this->processBulkDelete();
        }
    }

    protected function processBulkDelete()
    {
        if ($this->access('delete')) {
            if (is_array($this->boxes) && !empty($this->boxes)) {
                $object = new $this->className();

                if (isset($object->noZeroObject)) {
                    $this->errors[] = $this->l('You need at least one object.');
                } else {
                    $redirections = Tools::getValue($this->table.'Box');
                    if (is_array($redirections)) {
                        foreach ($redirections as $id_ever_seo_redirect) {
                            $notFound = new EverPsSeoRedirect(
                                (int)$id_ever_seo_redirect
                            );

                            if (!count($this->errors)) {
                                if ($notFound->delete()) {
                                    PrestaShopLogger::addLog(
                                        sprintf('%s deletion', $this->className),
                                        1,
                                        null,
                                        $this->className,
                                        (int)$notFound->id,
                                        true,
                                        (int)$this->context->employee->id
                                    );
                                }
                            } else {
                                $this->errors[] = $this->l('Errors on deleting object ').$id_ever_seo_redirect;
                            }
                        }
                    }
                }
            } else {
                $this->errors[] = $this->l('You must select at least one element to delete.');
            }
        } else {
            $this->errors[] = $this->l('You do not have permission to delete this.');
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
