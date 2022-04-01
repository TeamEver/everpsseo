{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}

<h1>{l s='Can not find the requested page' mod='everpsseo'}</h1>
{if isset($top_text) && $top_text}
{$top_text nofilter}
{/if}
{if isset($use_search) && $use_search}
    <form method="get" action="{$link->getPageLink('search', true)|escape:'html':'UTF-8'}" id="searchbox">
        <p>
            <label for="search_query_top"><!-- image on background --></label>
            <input type="hidden" name="controller" value="search" />
            <input type="hidden" name="orderby" value="position" />
            <input type="hidden" name="orderway" value="desc" />
            <input class="search_query" type="text" id="search_query_top" name="search_query" />
            <input type="submit" name="submit_search" value="{l s='Search' mod='everpsseo'}" class="button" />
        </p>
    </form>    
{/if}
{if isset($bottom_text) && $bottom_text}
{$bottom_text nofilter}
{/if}