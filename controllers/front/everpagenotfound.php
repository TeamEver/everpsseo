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

class EverpsseoEverpagenotfoundModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::init();
    }

    public function initContent()
    {
        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
        $this->context->cookie->disallowWriting();
        parent::initContent();
        $top_text = Configuration::getConfigInMultipleLangs('EVERSEO_404_TOP');
        $bottom_text = Configuration::getConfigInMultipleLangs('EVERSEO_404_BOTTOM');
        $this->context->smarty->assign(array(
            'use_search' => Configuration::get('EVERSEO_404_SEARCH'),
            'top_text' => $top_text[(int)$this->context->language->id],
            'bottom_text' => $bottom_text[(int)$this->context->language->id],
        ));
        // If custom 404 not allowed
        if (!(int)Configuration::get('EVERSEO_CUSTOM_404')) {
            $this->setTemplate('errors/404');
        }
        if (_PS_VERSION_ >= '1.7') {
            $this->setTemplate(
                'module:everpsseo/views/templates/front/pagenotfound.tpl'
            );
        } else {
            $this->setTemplate('pagenotfound16.tpl');
        }
    }
}
