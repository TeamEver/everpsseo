{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
<link rel="alternate" hreflang="x-default" href="{$link->getManufacturerLink($smarty.get.id_manufacturer, null, $xdefault , null)|escape:'htmlall':'UTF-8'}" />
{foreach $everpshreflang as $everlang}
<link rel="alternate" hreflang="{$everlang.locale|escape:'htmlall':'UTF-8'}" href="{$link->getManufacturerLink($smarty.get.id_manufacturer, null, $everlang.id_lang , null)|escape:'htmlall':'UTF-8'}" />
{/foreach}