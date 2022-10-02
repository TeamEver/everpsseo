{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
{if isset($controller_name) && $controller_name == 'product' && isset($richsnippet) && $richsnippet && isset($product)}
{literal}
<script type="application/ld+json">{"@context":"https:\/\/schema.org\/","@type":"Product","sku":"{/literal}{$product.reference|escape:'htmlall':'UTF-8'}{literal}","mpn":"{/literal}{$product.id_product|escape:'htmlall':'UTF-8'}{literal}","name":"{/literal}{$product.name|escape:'htmlall':'UTF-8'}{literal}","description":"{/literal}{$product.description_short|strip_tags:true}{literal}","releaseDate":"{/literal}{$product.date_add nofilter}{literal}","brand":{"@type":"Thing","name":"{/literal}{if isset($product_manufacturer) && $product_manufacturer}{$product_manufacturer->name|escape:'htmlall':'UTF-8'}{else}{$product.category_name|escape:'htmlall':'UTF-8'}{/if}{literal}"},"url":"{/literal}{$link->getProductLink($product.id_product)|escape:'htmlall':'UTF-8'}{literal}","category":"{/literal}{$product.category_name|escape:'htmlall':'UTF-8'}{literal}","image":{"@type":"ImageObject","url":"{/literal}{$defaultImage|escape:'htmlall':'UTF-8'}{literal}","width":1024,"height":680},"offers":{"@type":"Offer","price":"{/literal}{$product.price_amount|escape:'htmlall':'UTF-8'}{literal}","priceCurrency":"{/literal}{$currency_iso|escape:'htmlall':'UTF-8'}{literal}","availability":"https:\/\/schema.org\/InStock","itemCondition":"NewCondition","seller":{"@type":"Organization","@id":"{/literal}{$site_url|escape:'htmlall':'UTF-8'}{literal}","name":"{/literal}{$sitename|escape:'htmlall':'UTF-8'}{literal}","url":"{/literal}{$site_url|escape:'htmlall':'UTF-8'}{literal}","logo":"{/literal}{$shop_logo|escape:'htmlall':'UTF-8'}{literal}"},"url":"{/literal}{$link->getProductLink($product.id_product)|escape:'htmlall':'UTF-8'}{literal}","priceValidUntil":"{/literal}{$priceValidUntil|escape:'htmlall':'UTF-8'}{literal}"}}</script>
{/literal}
{/if}
{if isset($ever_theme_color) && $ever_theme_color}
<meta name="theme-color" content="#4285f4">
{/if}
<link rel="preconnect" href="https://analytics.twitter.com">
<link rel="preconnect" href="https://t.co">
<link rel="preconnect" href="https://stats.g.doubleclick.net">
<link rel="preconnect" href="https://www.google-analytics.com">
<link rel="preconnect" href="https://www.googleadservices.com">
<link rel="preconnect" href="https://sjs.bizographics.com">
<link rel="preconnect" href="https://www.google.com">
<link rel="preconnect" href="https://www.facebook.com">
<link rel="preconnect" href="https://www.google.fr">
<link rel="preconnect" href="https://googleads.g.doubleclick.net">
<link rel="preconnect" href="https://static.ads-twitter.com">
<link rel="preconnect" href="https://connect.facebook.net">
<link rel="preconnect" href="https://www.googletagmanager.com">
<link rel="preconnect" href="https://px.ads.linkedin.com">
{if isset($replyto) && $replyto }
<meta name="reply-to" content="{$replyto|escape:'htmlall':'UTF-8'}">
{/if}
{if isset($identifierUrl) && $identifierUrl }
<meta name="identifier-url" content="{$identifierUrl|escape:'htmlall':'UTF-8'}">
{/if}
{if isset($everseo_use_author) && $everseo_use_author && isset($everseo_author) && $everseo_author}
<meta name="author" content="{$everseo_author|escape:'htmlall':'UTF-8'}">
<meta name="publisher" content="{$everseo_author|escape:'htmlall':'UTF-8'}">
{/if}
{if isset($everyear) && $everyear && isset($sitename) && $sitename}
<meta name="copyright" content="Copyright &copy;{$everyear|escape:'htmlall':'UTF-8'} {$sitename|escape:'htmlall':'UTF-8'}">
{/if}
{if isset($header_tags) && $header_tags}
  <script type="text/javascript" defer>{$header_tags nofilter}</script>
{/if}
{if isset($analytics) && $analytics}
    {literal}
        <!-- Global Site Tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={/literal}{$analytics|escape:'htmlall':'UTF-8'}{literal}"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', '{/literal}{$analytics|escape:'htmlall':'UTF-8'}{literal}');
        </script>
    {/literal}
{/if}
{if isset($searchconsole) && $searchconsole}
    <meta name="google-site-verification" content="{$searchconsole|escape:'htmlall':'UTF-8'}">
{/if}
{if isset($pixelfacebook) && $pixelfacebook}
    {literal}
    <!-- Facebook Pixel Code -->
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '{/literal}{$pixelfacebook|escape:'htmlall':'UTF-8'}{literal}');
      fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
      src="https://www.facebook.com/tr?id={/literal}{$pixelfacebook|escape:'htmlall':'UTF-8'}{literal}&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Facebook Pixel Code -->
    {/literal}
{/if}
{if isset($usehreflang) && $usehreflang}
    {foreach $everpshreflang as $everlang}
            {if $page.page_name == 'index'}
                <link rel="alternate" hreflang="{if $everlang.id_lang == $xdefault}x-default{else}{$everlang.locale|escape:'htmlall':'UTF-8'}{/if}" href="/"/>
            {/if}
            {if $page.page_name == 'category'}
                <link rel="alternate" hreflang="{if $everlang.id_lang == $xdefault}x-default{else}{$everlang.locale|escape:'htmlall':'UTF-8'}{/if}" href="{if $everlang.id_lang == $xdefault}{$link->getCategoryLink($smarty.get.id_category, null, null, null, null )|escape:'htmlall':'UTF-8'}{else}{$link->getCategoryLink($smarty.get.id_category, null, $everlang.id_lang,null,null )|escape:'htmlall':'UTF-8'}{/if}" />
            {/if}
            {if $page.page_name == 'product'}
                <link rel="alternate" hreflang="{if $everlang.id_lang == $xdefault}x-default{else}{$everlang.locale|escape:'htmlall':'UTF-8'}{/if}" href="{$link->getProductLink($smarty.get.id_product, null, null, null, $everlang.id_lang, null, 0, false)|escape:'htmlall':'UTF-8'}" />
            {/if}
            {if $page.page_name == 'cms'}
                <link rel="alternate" hreflang="{if $everlang.id_lang == $xdefault}x-default{else}{$everlang.locale|escape:'htmlall':'UTF-8'}{/if}" href="{$link->getCMSLink($smarty.get.id_cms, null, false, $everlang.id_lang)|escape:'htmlall':'UTF-8'}" />
            {/if}
            {if $page.page_name == 'manufacturer'}
                <link rel="alternate" hreflang="{if $everlang.id_lang == $xdefault}x-default{else}{$everlang.locale|escape:'htmlall':'UTF-8'}{/if}" href="{$link->getManufacturerLink($smarty.get.id_manufacturer, null, $everlang.id_lang , null)|escape:'htmlall':'UTF-8'}" />
            {/if}
    {/foreach}
{/if}
{if isset($useOpenGraph) && $useOpenGraph}
    {if isset($social_title) && $social_title}
    <meta property="og:title" content="{$social_title|escape:'htmlall':'UTF-8'}" />
    {else}
    <meta property="og:title" content="{$page.meta.title|escape:'htmlall':'UTF-8'}" />
    {/if}
    {if isset($controller_name) && $controller_name == 'product'}
    <meta property="og:type" content="product" />
    {else}
    <meta property="og:type" content="website" />
    {/if}
    {if isset($social_img_url) && $social_img_url}
    <meta property="og:image" content="{$social_img_url|escape:'htmlall':'UTF-8'}" />
    {else}
    <meta property="og:image" content="{$defaultImage|escape:'htmlall':'UTF-8'}" />
    {/if}
    <meta property="og:site_name" content="{$siteName|escape:'htmlall':'UTF-8'}" />
    {if isset($social_description) && $social_description}
    <meta property="og:description" content="{$social_description|escape:'htmlall':'UTF-8'}" />
    {else}
    <meta property="og:description" content="{$page.meta.description|escape:'htmlall':'UTF-8'}" />
    {/if}
    {if isset($controller_name) && $controller_name == 'product'}
    <meta property="product:pretax_price:amount" content="{$product->price_tax_exc|escape:'htmlall':'UTF-8'}">
    <meta property="product:pretax_price:currency" content="{$currency_iso|escape:'htmlall':'UTF-8'}">
    <meta property="product:pretax_price:amount" content="{$product->price|escape:'htmlall':'UTF-8'}">
    <meta property="product:weight:value" content="{$product->weight|escape:'htmlall':'UTF-8'}">
    <meta property="product:weight:units" content="{$everweight_unit|escape:'htmlall':'UTF-8'}">
    {/if}
    {if isset($canonical_url) && $canonical_url }
      <meta property="og:url" content="{$canonical_url|escape:'htmlall':'UTF-8'}" />
    {/if}
{/if}
{if isset($useTwitter) && $useTwitter}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="{$twitterAccount|escape:'htmlall':'UTF-8'}" />
    <meta name="twitter:creator" content="{$twitterAccount|escape:'htmlall':'UTF-8'}" />
    <meta name="twitter:domain" content="{$siteName|escape:'htmlall':'UTF-8'}" />
    {if isset($social_title) && $social_title}
    <meta name="twitter:title" content="{$social_title|escape:'htmlall':'UTF-8'}" />
    {elseif isset($meta_title) && $meta_title}
    <meta name="twitter:title" content="{$meta_title|escape:'htmlall':'UTF-8'}" />
    {/if}
    {if isset($social_description) && $social_description}
    <meta name="twitter:description" content="{$social_description|escape:'htmlall':'UTF-8'}" />
    {elseif isset($meta_description) && $meta_description}
    <meta name="twitter:description" content="{$meta_description|escape:'htmlall':'UTF-8'}" />
    {/if}
    {if isset($social_img_url) && $social_img_url}
    <meta name="twitter:image" content="{$social_img_url|escape:'htmlall':'UTF-8'}" />
    {else}
    <meta name="twitter:image" content="{$defaultImage|escape:'htmlall':'UTF-8'}" />
    {/if}
{/if}
{if isset($ever_ps_captcha_site_key) && $ever_ps_captcha_site_key}
<script>
    var googlecaptchasitekey = "{$ever_ps_captcha_site_key|escape:'htmlall':'UTF-8'}";
</script>
{/if}
{* Start Adwords tracking code *}
{if isset($adwords) && $adwords && isset($product.price) && $product.price && isset($product.id) && $product.id}
    {literal}
    <!-- Global site tag (gtag.js) - Google AdWords: 123456789 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={/literal}{$adwords|escape:'htmlall':'UTF-8'}{literal}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{/literal}{$adwords|escape:'htmlall':'UTF-8'}{literal}');
    </script>
    {/literal}
    {literal}
    <script>
      gtag('event', 'page_view', {
        'send_to': '{/literal}{$adwords|escape:'htmlall':'UTF-8'}{literal}',
        'user_id': {/literal}{if isset($ever_customer) && $ever_customer}{$ever_customer->id|escape:'htmlall':'UTF-8'}{else}false{/if}{literal},
        'value': {/literal}{$product.price|escape:'htmlall':'UTF-8'}{literal},
        'items': [{
          'id': {/literal}{$product.id|escape:'htmlall':'UTF-8'}{literal},
          'google_business_vertical': 'retail'
        }, {
          'id': {/literal}{$product.id|escape:'htmlall':'UTF-8'}{literal},
          'location_id': false,
          'google_business_vertical': 'custom'
        }]
      });
    </script>
    {/literal}
{/if}
{* End Adwords tracking code *}
{* Start Adwords contact tracking code *}
{if isset($controller_name) && $controller_name &&  isset($adwordscontact) && $adwordscontact && $controller_name == 'contact'}
{literal}
<script>
  gtag(
  'event',
  'conversion',
  {'send_to': '{/literal}{$adwordscontact|escape:'htmlall':'UTF-8'}{literal}'}
);
</script>
{/literal}
{/if}
{* End Adwords contact tracking code *}

{if isset($pixelfacebook) && $pixelfacebook}
    {literal}
    <!-- Facebook Pixel Code -->
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '{/literal}{$pixelfacebook|escape:'htmlall':'UTF-8'}{literal}');
      fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
      src="https://www.facebook.com/tr?id={/literal}{$pixelfacebook|escape:'htmlall':'UTF-8'}{literal}&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Facebook Pixel Code -->
    {/literal}
{/if}

{* Start GTM tracking code *}
{if isset($gtag_manager) && !empty($gtag_manager)}
{literal}
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{/literal}{$gtag_manager|escape:'htmlall':'UTF-8'}{literal}');</script>
<!-- End Google Tag Manager -->
{/literal}
{/if}
{* End GTM tracking code *}