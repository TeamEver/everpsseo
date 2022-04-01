{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}

{extends file='page.tpl'}

{capture name=path}{l s='Can not find the requested page' mod='everpsseo'}{/capture}

{block name="page_content"}
    <h1>{l s='Can not find the requested page' mod='everpsseo'}</h1>
    {if isset($top_text) && $top_text}
    {$top_text nofilter}
    {/if}
    {if isset($use_search) && $use_search}
      {block name='search'}
        {hook h='displaySearch'}
      {/block}
    {/if}
    {if isset($bottom_text) && $bottom_text}
    {$bottom_text nofilter}
    {/if}

    {block name='hook_not_found'}
      {hook h='displayNotFound'}
    {/block}
{/block}
