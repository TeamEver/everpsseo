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

require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoShortcode.php';

class AdminEverPsSeoShortcodeController extends ModuleAdminController
{
    private $html;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = true;
        $this->table = 'ever_seo_shortcode';
        $this->className = 'EverPsSeoShortcode';
        $this->context = Context::getContext();
        $this->identifier = 'id_ever_seo_shortcode';

        $this->isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;
        $moduleConfUrl  = 'index.php?controller=AdminModules&configure=everpsseo&token=';
        $moduleConfUrl .= Tools::getAdminTokenLite('AdminModules');

        $this->context->smarty->assign(array(
            'moduleConfUrl' => (string)$moduleConfUrl,
            'image_dir' => _PS_BASE_URL_ . '/modules/everpsseo/views/img/'
        ));

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            ),
        );

        $this->fields_list = array(
            'id_ever_seo_shortcode' => array(
                'title' => $this->l('ID'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'shortcode' => array(
                'title' => $this->l('Shortcode'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'title' => array(
                'title' => $this->l('Title'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'content' => array(
                'title' => $this->l('Content'),
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
                'Modules.Everpsseo.Admineverpsseoshortcodecontroller'
            );
        }

        return parent::l($string, $class, $addslashes, $htmlentities);
    }

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

        $this->toolbar_title = $this->l('Registered shortcodes');

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
            $this->errors[] = $this->l('You have to select a shop before creating or editing new shortcode.');
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
                    'name' => 'submitAdd'.$this->table.'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                ),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'desc' => $this->l('Title is just a reminder, won\'t be shown'),
                    'hint' => $this->l('Will be only shown on admin list'),
                    'required' => true,
                    'name' => 'title',
                    'lang' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Shortcode, no space allowed'),
                    'desc' => $this->l('Please type shortcode with brackets like [shortcode], no space allowed'),
                    'hint' => $this->l('Type shortcode like [shortcode], no space allowed'),
                    'required' => true,
                    'name' => 'shortcode',
                    'lang' => false,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Shortcode content'),
                    'desc' => $this->l('Shortcode will be changed to this value'),
                    'hint' => $this->l('Module will auto translate shortcode using this value'),
                    'required' => true,
                    'name' => 'content',
                    'lang' => true,
                ),
            )
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('save') || Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
            if (!Tools::getValue('title')
                || !Validate::isGenericName(Tools::getValue('title'))
            ) {
                 $this->errors[] = $this->l('Title is not valid or missing');
            }
            $everblock_obj = new EverPsSeoShortcode(
                (int)Tools::getValue('id_seo_shortcode')
            );
            $everblock_obj->shortcode = Tools::getValue('name');
            $everblock_obj->id_shop = (int)Context::getContext()->shop->id;
            foreach (Language::getLanguages(false) as $language) {
                if (!Tools::getValue('content_'.$language['id_lang'])
                ) {
                    $this->errors[] = $this->l('Content is missing for lang ').$language['id_lang'];
                } else {
                    $everblock_obj->content[$language['id_lang']] = Tools::getValue('content_'.$language['id_lang']);
                }
            }

            if (!count($this->errors)) {
                if ($everblock_obj->save()) {
                    if (Tools::isSubmit('save')) {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                    }
                } else {
                    $this->errors[] = $this->l('Can\'t update the current object');
                }
            }
        }

        if (Tools::isSubmit('deleteever_shortcodes')) {
            $shortcode = new EverPsSeoShortcode(
                (int)Tools::getValue('id_ever_seo_shortcode')
            );

            if (!$shortcode->delete()) {
                $this->errors[] = $this->l('An error has occurred : Can\'t delete the current object');
            }
        }

        if (Tools::isSubmit('submitBulkdeleteever_shortcodes')) {
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
                    $shortcode = Tools::getValue($this->table.'Box');
                    if (is_array($shortcode)) {
                        foreach ($shortcode as $id_ever_seo_shortcode) {
                            $shortcode = new EverPsSeoShortcode(
                                (int)$id_ever_seo_shortcode
                            );
                            if (!count($this->errors) && $shortcode->delete()) {
                            } else {
                                $this->errors[] = $this->l('Errors on deleting object ').$id_ever_seo_shortcode;
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
