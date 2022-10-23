{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
<link rel="alternate" hreflang="x-default" href="{$link->getCMSLink($smarty.get.id_cms, null, false, $xdefault)|escape:'htmlall':'UTF-8'}" />
{foreach $everpshreflang as $everlang}
<link rel="alternate" hreflang="{$everlang.locale|escape:'htmlall':'UTF-8'}" href="{$link->getCMSLink($smarty.get.id_cms, null, false, $everlang.id_lang)|escape:'htmlall':'UTF-8'}" />
{/foreach}