<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @see https://www.team-ever.com
 */

class Tools extends ToolsCore
{
    public static function setCookieLanguage($cookie = null)
    {
        parent::setCookieLanguage($cookie);

        if (!$cookie) {
            $cookie = Context::getContext()->cookie;
        }

        if (!Tools::getValue('isolang')
            && !Tools::getValue('id_lang')
            && !Tools::isSubmit('id_category_layered')
        ) {
            $cookie->id_lang = Configuration::get('PS_LANG_DEFAULT');
            Context::getContext()->language = new Language($cookie->id_lang);
        }

        /* If language file not present, you must use default language file */
        if (!$cookie->id_lang || !Validate::isUnsignedId($cookie->id_lang)) {
            $cookie->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
            Context::getContext()->language = new Language($cookie->id_lang);
        }

        $iso = Language::getIsoById((int) $cookie->id_lang);
        @include_once(_PS_THEME_DIR_ . 'lang/' . $iso . '.php');

        return $iso;
    }
}
