{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
{if isset($manufacturer) && isset($supplier)}
<div class="tabs container">
    {* Start manufacturer reassurance*}
    {if isset($manufacturer) && $manufacturer}
    <div class="everpsseo_manufacturer everpsseo_reassurance col-md-6 col-xs-12">
        <a href="{$link->getManufacturerLink($manufacturer)|escape:'htmlall':'UTF-8'}" title="{$manufacturer->name|escape:'htmlall':'UTF-8'}">
            <span class="h1">{$manufacturer->name|escape:'htmlall':'UTF-8'}</span>
            <img src="{$link->getManufacturerImageLink($manufacturer->id)|escape:'htmlall':'UTF-8'}" alt="{$manufacturer->name|escape:'htmlall':'UTF-8'}" class="img img-responsive rounded img-thumbnail">
        </a>
    </div>
    {/if}
    {* End manufacturer reassurance *}
    {* Start supplier reassurance*}
    {if isset($supplier) && $supplier}
    <div class="everpsseo_supplier everpsseo_reassurance col-md-6 col-xs-12">
        <a href="{$link->getSupplierLink($supplier)|escape:'htmlall':'UTF-8'}" title="{$supplier->name|escape:'htmlall':'UTF-8'}">
            <span class="h1">{$supplier->name|escape:'htmlall':'UTF-8'}</span>
            <img src="{$link->getSupplierImageLink($supplier->id)|escape:'htmlall':'UTF-8'}" alt="{$supplier->name|escape:'htmlall':'UTF-8'}" class="img img-responsive rounded img-thumbnail">
        </a>
    </div>
    {/if}
    {* End supplier reassurance *}
</div>
{/if}