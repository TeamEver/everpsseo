<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link https://www.team-ever.com
 */

class Link extends LinkCore
{
    protected function getLangLink($id_lang = null, Context $context = null, $id_shop = null)
    {
        $parent_result = parent::getLangLink($id_lang, $context, $id_shop);

        if ($parent_result) {
            if ($id_lang == Configuration::get('PS_LANG_DEFAULT')) {
                return '';
            } else {
                return $parent_result;
            }
        }
    }
}
