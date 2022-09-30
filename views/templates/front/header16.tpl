{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
{if isset($canonical_url) && $canonical_url}
  <link rel="canonical" href="{$canonical_url}">
{/if}
{if isset($controller_name) && $controller_name == 'product' && isset($richsnippet) && $richsnippet}
{literal}
<script type="application/ld+json">{"@context":"https:\/\/schema.org\/","@type":"Product","sku":"{/literal}{$everproduct->reference|escape:'htmlall':'UTF-8'}{literal}","mpn":"{/literal}{$everproduct->id_product|escape:'htmlall':'UTF-8'}{literal}","name":"{/literal}{$everproduct->name|escape:'htmlall':'UTF-8'}{literal}","description":"{/literal}{$everproduct->description_short|strip_tags:true}{literal}","releaseDate":"{/literal}{$everproduct->date_add nofilter}{literal}","brand":{"@type":"Thing","name":"{/literal}{if isset($everproduct_manufacturer) && $everproduct_manufacturer}{$everproduct_manufacturer->name|escape:'htmlall':'UTF-8'}{else}{$everproduct->category_name|escape:'htmlall':'UTF-8'}{/if}{literal}"},"url":"{/literal}{$pageUrl|escape:'htmlall':'UTF-8'}{literal}","category":"{/literal}{$everproduct->category_name|escape:'htmlall':'UTF-8'}{literal}","image":{"@type":"ImageObject","url":"{/literal}{$defaultImage|escape:'htmlall':'UTF-8'}{literal}","width":1024,"height":680},"offers":{"@type":"Offer","price":"{/literal}{$everproduct->price|escape:'htmlall':'UTF-8'}{literal}","priceCurrency":"{/literal}{$currency_iso|escape:'htmlall':'UTF-8'}{literal}","availability":"https:\/\/schema.org\/InStock","itemCondition":"NewCondition","seller":{"@type":"Organization","@id":"{/literal}{$site_url|escape:'htmlall':'UTF-8'}{literal}","name":"{/literal}{$sitename|escape:'htmlall':'UTF-8'}{literal}","url":"{/literal}{$site_url|escape:'htmlall':'UTF-8'}{literal}","logo":"{/literal}{$shop_logo|escape:'htmlall':'UTF-8'}{literal}"},"url":"{/literal}{$pageUrl|escape:'htmlall':'UTF-8'}{literal}","priceValidUntil":"{/literal}{$priceValidUntil|escape:'htmlall':'UTF-8'}{literal}"}}</script>
{/literal}
{/if}
{if isset($ever_theme_color) && $ever_theme_color}
<meta name="theme-color" content="#4285f4">
{/if}
{* Start Adwords tracking code *}
{if isset($adwords) && $adwords}
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
    <!-- add balise hreflang -->
    {if $page_name == 'category'}
        {foreach $languages as $lang}
            {if $lang_iso != $lang.iso_code}
            <link rel="alternate" hreflang="{$lang.language_code|escape:'htmlall':'UTF-8'}" href="{$link->getCategoryLink($smarty.get.id_category, null, $lang.id_lang,null,null )|escape:'htmlall':'UTF-8'}/" />
            {else}
            <link rel="alternate" href="{$link->getCategoryLink($smarty.get.id_category, null, $lang.id_lang,null,null )|escape:'htmlall':'UTF-8'}/" hreflang="x-default" />
            {/if}
        {/foreach}
    {/if}
    {if $page_name == 'product'}
        {foreach $languages as $lang}
            {if $lang_iso != $lang.iso_code}
            <link rel="alternate" hreflang="{$lang.language_code|escape:'htmlall':'UTF-8'}" href="{$link->getProductLink($smarty.get.id_product, null, null, null, $lang.id_lang, null, 0, false)|escape:'htmlall':'UTF-8'}/" />
            {else}
            <link rel="alternate" href="{$link->getProductLink($smarty.get.id_product, null, null, null, $lang.id_lang, null, 0, false)|escape:'htmlall':'UTF-8'}/" hreflang="x-default" />
            {/if}
        {/foreach}
    {/if}
    {if $page_name == 'cms'}
        {foreach $languages as $lang}
            {if $lang_iso != $lang.iso_code}
            <link rel="alternate" hreflang="{$lang.language_code|escape:'htmlall':'UTF-8'}" href="{$link->getCMSLink($smarty.get.id_cms, null, false, $lang.id_lang)|escape:'htmlall':'UTF-8'}/" />
            {else}
            <link rel="alternate" href="{$link->getCMSLink($smarty.get.id_cms, null, false, $lang.id_lang)|escape:'htmlall':'UTF-8'}/" hreflang="x-default" />
            {/if}
        {/foreach}
    {/if}
    {if $page_name == 'manufacturer'}
        {foreach $languages as $lang}
            {if $lang_iso != $lang.iso_code}
            <link rel="alternate" hreflang="{$lang.language_code|escape:'htmlall':'UTF-8'}" href="{$link->getManufacturerLink($smarty.get.id_manufacturer, null, $lang.id_lang , null)|escape:'htmlall':'UTF-8'}/" />
            {else}
            <link rel="alternate" href="{$link->getManufacturerLink($smarty.get.id_manufacturer, null, $lang.id_lang , null)|escape:'htmlall':'UTF-8'}/" hreflang="x-default" />
            {/if}
        {/foreach}
    {/if}
    {if $page_name == 'index'}
        {foreach $languages as $lang}
            {if $lang_iso != $lang.iso_code}
            <link rel="alternate" hreflang="{$lang.language_code|escape:'htmlall':'UTF-8'}" href="/{$lang.iso_code|escape:'htmlall':'UTF-8'}/"/>
            {else}
            <link rel="alternate" href="/" hreflang="x-default" />
            {/if}
        {/foreach}
    {/if}
{/if}
{if isset($useOpenGraph) && $useOpenGraph}
    {if isset($social_title) && $social_title}
    <meta property="og:title" content="{$social_title|escape:'htmlall':'UTF-8'}" />
    {else}
    <meta property="og:title" content="{$meta_title|escape:'htmlall':'UTF-8'}" />
    {/if}
    {if isset($controller_name) && $controller_name == 'product'}
    <meta property="og:type" content="product" />
    {else}
    <meta property="og:type" content="website" />
    {/if}
    <meta property="og:image" content="{$defaultImage|escape:'htmlall':'UTF-8'}" />
    <meta property="og:site_name" content="{$siteName|escape:'htmlall':'UTF-8'}" />
    {if isset($social_description) && $social_description}
    <meta property="og:description" content="{$social_description|escape:'htmlall':'UTF-8'}" />
    {else}
    <meta property="og:description" content="{$meta_description|escape:'htmlall':'UTF-8'}" />
    {/if}
    {if isset($canonical_url) && $canonical_url }
      <meta property="og:url" content="{$canonical_url|escape:'htmlall':'UTF-8'}" />
    {/if}
<!-- <meta property="product:pretax_price:amount" content="79.9">
<meta property="product:pretax_price:currency" content="{$currency_iso|escape:'htmlall':'UTF-8'}}">
<meta property="product:price:amount" content="95.88">
<meta property="product:price:currency" content="EUR">
<meta property="product:weight:value" content="0.210000">
<meta property="product:weight:units" content="kg"> -->

{/if}
{if isset($useTwitter) && $useTwitter}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="{$twitterAccount|escape:'htmlall':'UTF-8'}" />
    <meta name="twitter:creator" content="{$twitterAccount|escape:'htmlall':'UTF-8'}" />
    <meta name="twitter:domain" content="{$siteName|escape:'htmlall':'UTF-8'}" />
    {if isset($social_title) && $social_title}
    <meta name="twitter:title" content="{$social_title|escape:'htmlall':'UTF-8'}" />
    {else}
    <meta name="twitter:title" content="{$meta_title|escape:'htmlall':'UTF-8'}" />
    {/if}
    {if isset($social_description) && $social_description}
    <meta name="twitter:description" content="{$social_description|escape:'htmlall':'UTF-8'}" />
    {else}
    <meta name="twitter:description" content="{$meta_description|escape:'htmlall':'UTF-8'}" />
    {/if}
    <meta name="twitter:image" content="{$defaultImage|escape:'htmlall':'UTF-8'}" />
{/if}
{if isset($ever_ps_captcha_site_key) && $ever_ps_captcha_site_key}
{literal}
<script>
    var googlecaptchasitekey = "{/literal}{$ever_ps_captcha_site_key|escape:'htmlall':'UTF-8'}{literal}";
</script>
{/literal}
{/if}
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
{* Start Adwords Opart quotation tracking code *}
{if isset($adwordsopart) && $adwordsopart && isset($opart_total) && $opart_total}
{literal}
<!-- Event snippet for Demande de devis conversion page -->
<script>
  gtag('event', 'conversion', {
      'send_to': '{/literal}{$adwordsopart|escape:'htmlall':'UTF-8'}{literal}',
      'value': {/literal}{$opart_total|escape:'htmlall':'UTF-8'}{literal},
      'currency': 'EUR',
      'transaction_id': ''
  });
</script>
{/literal}
{/if}
{* End Adwords Opart quotation tracking code *}

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