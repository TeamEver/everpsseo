{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
{if isset($gtag_manager) && !empty($gtag_manager)}
{literal}
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={/literal}{$gtag_manager|escape:'htmlall':'UTF-8'}{literal}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
{/literal}
{/if}
{if isset($translate_top) && !empty($translate_top)}
<div class="everseo_translate ">
    <div class="title_block flex_container title_align_0 title_style_3">
      <p class="text-uppercase h6 hidden-sm-down title_block_inner">{l s='Translate this page' mod='everpsseo'}</p>
    </div>
      <div id="google_translate_element" style="text-align:center;"></div><script type="text/javascript">
          {literal}
          function googleTranslateElementInit() {
              new google.translate.TranslateElement({pageLanguage: '{/literal}{$default_iso_code|escape:'htmlall':'UTF-8'}{literal}', layout: google.translate.TranslateElement.FloatPosition.TOP_RIGHT}, 'google_translate_element');
          }
          {/literal}
      </script>
      <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</div>
{/if}
