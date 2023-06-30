{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
{if isset($controller) && $controller == 'product'}
    <div class="everrsnippets everseo ever-{$controller|escape:'htmlall':'UTF-8'}" style="display:none;">
        <div itemscope itemtype="http://schema.org/Product">
            <span itemprop="name">{$productName|escape:'htmlall':'UTF-8'}</span>
            <img itemprop="image" src="{$imgUrl|escape:'htmlall':'UTF-8'}" alt="{$productName|escape:'htmlall':'UTF-8'}" />
            {if isset($manufacturer) && $manufacturer}
            <span itemprop="brand">{$manufacturer|escape:'htmlall':'UTF-8'}</span>
            {else}
            <span itemprop="brand">{$shop_name|escape:'htmlall':'UTF-8'}</span>
            {/if}
            <span itemprop="description">{$descriptionShort|escape:'htmlall':'UTF-8'}</span>
            {if isset($productReference) && $productReference}
            <span itemprop="mpn">{$productReference|escape:'htmlall':'UTF-8'}</span>
            <span itemprop="sku">{$productReference|escape:'htmlall':'UTF-8'}</span>
            {else}
            <span itemprop="mpn">{$shop_name|escape:'htmlall':'UTF-8'}</span>
            <span itemprop="sku">{$shop_name|escape:'htmlall':'UTF-8'}</span>
            {/if}
            <span itemprop="productID">{$productID|escape:'htmlall':'UTF-8'}</span>
            <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                <span itemprop="priceValidUntil" content="{$priceValidUntil|escape:'htmlall':'UTF-8'}"></span>
                <span itemprop="url">{$currentUrl|escape:'htmlall':'UTF-8'}</span>
                <span itemprop="priceCurrency" content="{$currencyIsocode|escape:'htmlall':'UTF-8'}">{$currencyPrefix|escape:'htmlall':'UTF-8'}{$currencySuffix|escape:'htmlall':'UTF-8'}</span><span itemprop="price" content="{$productPrice|escape:'htmlall':'UTF-8'}">{$productPrice|escape:'htmlall':'UTF-8'}</span>
                <link itemprop="availability" href="http://schema.org/InStock" />{l s='In stock! Order now!' mod='everpsseo'}
            </div>
        </div>
    </div>
{/if}