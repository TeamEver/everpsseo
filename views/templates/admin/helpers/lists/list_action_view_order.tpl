{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
<a href="{$href|escape:'htmlall':'UTF-8'}"{if isset($confirm)} onclick="if (confirm('{$confirm|escape:'htmlall':'UTF-8'}')){ldelim}return true;{rdelim}else{ldelim}event.stopPropagation(); event.preventDefault();{rdelim};"{/if} title="{$action|escape:'htmlall':'UTF-8'}" target="_blank">
	<i class="icon-file"></i> {$action|escape:'htmlall':'UTF-8'}
</a>