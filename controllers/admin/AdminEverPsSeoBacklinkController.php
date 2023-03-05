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

require_once _PS_MODULE_DIR_ . 'everpsseo/models/EverPsSeoBacklink.php';

class AdminEverPsSeoBacklinkController extends ModuleAdminController
{
    private $html;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = false;
        $this->table = 'ever_seo_backlink';
        $this->className = 'EverPsSeoBacklink';
        $this->context = Context::getContext();
        $this->identifier = 'id_ever_seo_backlink';

        $this->isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;
        $moduleConfUrl  = 'index.php?controller=AdminModules&configure=everpsseo&token=';
        $moduleConfUrl .= Tools::getAdminTokenLite('AdminModules');

        $this->context->smarty->assign(array(
            'moduleConfUrl' => (string) $moduleConfUrl,
            'image_dir' => _PS_BASE_URL_ . '/modules/everpsseo/views/img/'
        ));

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            ),
        );

        $this->_where = 'AND a.id_shop ='.(int) $this->context->shop->id;

        $this->fields_list = array(
            'id_ever_seo_backlink' => array(
                'title' => $this->l('ID'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'everfrom' => array(
                'title' => $this->l('From'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'everto' => array(
                'title' => $this->l('To'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'count' => array(
                'title' => $this->l('Count'),
                'align' => 'left',
                'width' => 'auto'
            ),
        );

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

        $this->addCSS(_PS_MODULE_DIR_.'everpsseo/views/css/ever.css');
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ($this->isSeven) {
            return Context::getContext()->getTranslator()->trans(
                $string,
                [],
                'Modules.Everpsseo.Admineverpsseobacklinkcontroller'
            );
        }

        return parent::l($string, $class, $addslashes, $htmlentities);
    }

    public function renderList()
    {
        $this->html = '';

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->toolbar_title = $this->l('Registered backlinks');

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
            $this->errors[] = $this->l('You have to select a shop before creating or editing new backlink.');
        }

        if (count($this->errors)) {
            return false;
        }

        $this->fields_form = array(
            'submit' => array(
                'name' => 'save',
                'title' => $this->l('Save'),
                'class' => 'button pull-right',
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
                    'label' => $this->l('From'),
                    'name' => 'everfrom',
                    'lang' => false,
                    'readonly' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('To'),
                    'name' => 'everto',
                    'lang' => false,
                    'readonly' => true,
                ),
            )
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        parent::postProcess();

        if (Tools::isSubmit('save')) {
            if (!Tools::getValue('from')
                || !Validate::isUrl(Tools::getValue('from'))
            ) {
                 $this->errors[] = $this->l('from is invalid');
            }
            if (!Tools::getValue('to')
                || !Validate::isUrl(Tools::getValue('to'))
            ) {
                 $this->errors[] = $this->l('to is invalid');
            }
            if (!count($this->errors)) {
                $newBacklink = new EverPsSeoBacklink(
                    (int) Tools::getValue('id_ever_seo_backlink')
                );

                $newBacklink->everfrom = Tools::getValue('from');
                $newBacklink->everto = Tools::getValue('to');
                $newBacklink->id_shop = (int) $this->context->shop->id;

                if (!$newBacklink->save()) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t save the current object');
                } else {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                }
            }
        }

        if (Tools::isSubmit('deleteever_backlinks')) {
            $backlink = new EverPsSeoBacklink(
                (int) Tools::getValue('id_ever_seo_backlink')
            );

            if (!$backlink->delete()) {
                $this->errors[] = $this->l('An error has occurred : Can\'t delete the current object');
            }
        }

        if (Tools::isSubmit('submitBulkdeleteever_backlinks')) {
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
                    $backlink = Tools::getValue($this->table.'Box');
                    if (is_array($backlink)) {
                        foreach ($backlink as $id_ever_seo_backlink) {
                            $notFound = new EverPsSeoBacklink((int) $id_ever_seo_backlink);

                            if (!count($this->errors)) {
                                if ($notFound->delete()) {
                                    PrestaShopLogger::addLog(
                                        sprintf('%s deletion', $this->className),
                                        1,
                                        null,
                                        $this->className,
                                        (int) $notFound->id,
                                        true,
                                        (int) $this->context->employee->id
                                    );
                                }
                            } else {
                                $this->errors[] = $this->l('Errors on deleting object ').$id_ever_seo_backlink;
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
