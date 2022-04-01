{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
{literal}
    {if $adwords && $adwordssendto}
        <!-- Global site tag (gtag.js) - Google AdWords: 123456789 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={/literal}{$adwords|escape:'htmlall':'UTF-8'{literal}"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', '{/literal}{$adwords|escape:'htmlall':'UTF-8'{literal}');
        </script>

        <!-- Event snippet for Example conversion page -->
        <script>
          gtag('event', 'conversion', {'send_to': '{/literal}{$adwordssendto|escape:'htmlall':'UTF-8'}{literal}',
            'value': 1.0,
            'currency': 'USD'
          });
        </script>   
    {/if}
{/literal}
