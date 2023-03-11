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

class EverpsseoEversitemapsModuleFrontController extends ModuleFrontController
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
            Tools::redirect('index.php');
        }
        /* Check if the requested shop exists */
        $shops = Db::getInstance()->ExecuteS('SELECT id_shop FROM `' . _DB_PREFIX_ . 'shop`');

        $list_id_shop = [];
        foreach ($shops as $shop) {
            $list_id_shop[] = (int) $shop['id_shop'];
        }

        $id_shop = (Tools::getIsset('id_shop') && in_array(Tools::getValue('id_shop'), $list_id_shop))
            ? (int) Tools::getValue('id_shop') : (int) Configuration::get('PS_SHOP_DEFAULT');

        $everpsseo->cron = true;
        // Drop all sitemaps before regeneration
        $indexes = glob(_PS_ROOT_DIR_.'/*');
        foreach ($indexes as $index) {
            $info = new SplFileInfo(basename($index));
            if (is_file($index) && $info->getExtension() == 'xml') {
                unlink($index);
            }
        }

        $everpsseo->everGenerateSitemaps((int) $id_shop);
        echo 'Sitemaps fully generated. Please submit Search Console if not already set 🙂';
        exit();
    }
}
