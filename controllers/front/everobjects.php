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

class EverpsseoEverobjectsModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        if (!Tools::getValue('token')
            || Tools::substr(Tools::encrypt('everpsseo/cron'), 0, 10) != Tools::getValue('token')
            || !Module::isInstalled('everpsseo')
        ) {
            Tools::redirect('index.php');
        }
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::init();
    }

    public function initContent()
    {
        if (!Tools::getValue('token')
            || Tools::substr(Tools::encrypt('everpsseo/cron'), 0, 10) != Tools::getValue('token')
            || !Module::isInstalled('everpsseo')
        ) {
            Tools::redirect('index.php');
        }
        $everpsseo = Module::getInstanceByName('everpsseo');
        if (!$everpsseo->active) {
            die('Sorry, the module EverPsSeo is not active 😬');
        }
        $everpsseo->cron = true;

        $everpsseo->updateSeoProducts();
        $everpsseo->updateSeoCategories();
        $everpsseo->updateSeoManufacturers();
        $everpsseo->updateSeoSuppliers();
        $everpsseo->updateSeoCms();
        $everpsseo->updateSeoPageMetas();
        $everpsseo->updateSeoImages();

        die('Objects fully updated for each shop 🙂');
    }
}
