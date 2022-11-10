{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
{if isset($smarty.get.id_product) && $smarty.get.id_product}
<link rel="alternate" hreflang="x-default" href="{$link->getProductLink($smarty.get.id_product, null, null, null, $xdefault, null, 0, false)|escape:'htmlall':'UTF-8'}" />
{foreach $everpshreflang as $everlang}
<link rel="alternate" hreflang="{$everlang.locale|escape:'htmlall':'UTF-8'}" href="{$link->getProductLink($smarty.get.id_product, null, null, null, $everlang.id_lang, null, 0, false)|escape:'htmlall':'UTF-8'}" />
{/foreach}
{/if}