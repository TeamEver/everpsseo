{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
{* Start Adwords tracking code *}
{if isset($adwordssendto) && $adwordssendto}
{literal}
<!-- Event snippet for Example conversion page -->
<script>
  gtag('event', 'purchase', {'send_to':
    '{/literal}{$adwordssendto|escape:'htmlall':'UTF-8'}{literal}',
    'value': {/literal}{$totalAmount|escape:'htmlall':'UTF-8'}{literal},
    'currency': '{/literal}{$evercurrency|escape:'htmlall':'UTF-8'}{literal}',
    'transaction_id': {/literal}{$transaction_id|escape:'htmlall':'UTF-8'}{literal},
  });
</script>
{/literal}
{/if}
{* End Adwords tracking code *}
{* Start Analytics ecommerce tracking code *}
{if isset($analytics) && $analytics}
{literal}
<script>
var dataLayer  = window.dataLayer || [];
dataLayer.push({
   'transactionId': '{/literal}{$transaction_id|escape:'htmlall':'UTF-8'}{literal}',
   'transactionAffiliation': '{/literal}{$shop_name|escape:'htmlall':'UTF-8'}{literal}',
   'transactionTotal': {/literal}{$totalPaid|escape:'htmlall':'UTF-8'}{literal},
   'transactionTax': {/literal}{$totalTaxFull|escape:'htmlall':'UTF-8'}{literal},
   'transactionShipping': {/literal}{$totalShipping|escape:'htmlall':'UTF-8'}{literal},
   'transactionProducts': [{/literal}{foreach from=$products item=product}{literal}{
       'sku': '{/literal}{$product->reference|escape:'htmlall':'UTF-8'}{literal}',
       'name': '{/literal}{$product->name|escape:'htmlall':'UTF-8'}{literal} | {/literal}{$product->combination_selected|escape:'htmlall':'UTF-8'}{literal}',
       'category': '{/literal}{$product->category_name|escape:'htmlall':'UTF-8'}{literal}',
       'price': {/literal}{$product->unit_price_tax_excl|escape:'htmlall':'UTF-8'}{literal},
       'quantity': {/literal}{$product->qty_ordered|escape:'htmlall':'UTF-8'}{literal}
   },{/literal}{/foreach}{literal}]
});
</script>
{/literal}
{/if}
{* End Analytics ecommerce tracking code *}
{* Tag Manager tracking awcReady *}
{if isset($everorder) && $everorder && isset($gtag_manager) && !empty($gtag_manager) && isset($controller_name) && $controller_name == 'order-confirmation'}
  {literal}
  <script type="text/javascript">
  dataLayer.push({
  'orderTotal': {/literal}{$everorder.totals.total['amount']|escape:'htmlall':'UTF-8'}{literal},
  'orderID': '{/literal}{$everorder.details['reference']|escape:'htmlall':'UTF-8'}{literal}',
  'orderCurrency': '{/literal}{$evercurrency['iso_code']|escape:'htmlall':'UTF-8'}{literal}',
  'event': 'awcReady'
  });
  </script>
  {/literal}
{/if}
{* End Tag Manager tracking awcReady *}