{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
{if isset($smarty.get.id_category) && $smarty.get.id_category}
<link rel="alternate" hreflang="x-default" href="{$link->getCategoryLink($smarty.get.id_category, null, null, null, null )|escape:'htmlall':'UTF-8'}" />
{foreach $everpshreflang as $everlang}
<link rel="alternate" hreflang="{$everlang.locale|escape:'htmlall':'UTF-8'}" href="{$link->getCategoryLink($smarty.get.id_category, null, $everlang.id_lang,null,null )|escape:'htmlall':'UTF-8'}" />
{/foreach}
{/if}