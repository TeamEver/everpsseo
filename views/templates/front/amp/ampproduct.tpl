{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
<!doctype html>
<html ⚡>
<head>
  <meta charset="utf-8">
  <title>AMP for E-Commerce Getting Started</title>
  <script async src="https://cdn.ampproject.org/v0.js"></script>
  <link rel="canonical" href="/introduction/amp_for_e-commerce_getting_started/">
  <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
  <meta name="amp-experiment-token" content="HfmyLgNLmblRg3Alqy164Vywr">
    <script async custom-template="amp-mustache" src="https://cdn.ampproject.org/v0/amp-mustache-0.2.js"></script>
    <script async custom-element="amp-list" src="https://cdn.ampproject.org/v0/amp-list-0.1.js"></script>
    <script async custom-element="amp-bind" src="https://cdn.ampproject.org/v0/amp-bind-0.1.js"></script>
    <script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>
  <script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>

  <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
  <style amp-custom>
    .booking {
        margin: 1rem;
    }
    .booking > div {
        margin: 1rem 0;
    }
    .booking > button {
      width: 24px;
      height: 24px;
      vertical-align: middle;
      cursor: pointer;
      background-color: #fff;
      border: 1px solid #b60845;
      font-size: 1rem;
      line-height: 1.125rem;
      padding: 0;
      text-decoration: none;
      word-wrap: normal;
    }
    .abe-viewer-preview {
      display: flex;
    }
    .www-component-desc>.ampstart-mobile-frame {
      width: 380px;
      height: 669px;
      margin: 1rem auto;
      border-radius: 17px;
      position: relative;
    }
    .ampstart-mobile-frame amp-iframe {
      margin: 1px;
      top: 54px;
      bottom: 56px;
      left: 31px;
      right: 30px;
    }
  </style>
</head>
<body>
  <!--

    Here is a quick getting started guide for creating e-commerce webpages with AMP. This guide provides samples and tips covering the following topics:

    * [How to build Product pages](#product-pages)
    * [How to build Product category pages](#product-category-page)
    * [Handling Dynamic content in AMP](#dynamic-content)
    * [Personalizing AMPs and supporting Logins](#personalization-and-login)
    * [Checkout and Payments](#checkout-flow-and-payments)
    * [Analytics](#analytics)


    ## Product pages

    It's possible with AMP to create beautiful and interactive product pages. [This sample](/samples_templates/product_page/preview/) demonstrates how to build a complex product page with dynamic product configuration and an add-to-cart flow. With the introduction of [amp-bind](/components/amp-bind/) it's now possible to create truly interactive AMP pages: it can be used to coordinate page state based on user interaction to show and hide arbitrary divs.


    <div class="ampstart-mobile-frame xs-hide relative">
      <amp-img src="https://ampstart.com/img/www/mobile.svg" width="458" height="806" alt="Mobile outline" layout="responsive" class="absolute top-0 left-0 right-0 bottom-0"></amp-img>

      <amp-iframe title="AMP ecommerce app, product page"
          width="324"
          height="494"
          layout="responsive"
          sandbox="allow-scripts allow-popups"
          allowfullscreen=""
          frameborder="0"
          src="/samples_templates/product_page/preview/embed/"
          class="absolute xs-hide">
              <div placeholder=""></div>
      </amp-iframe>
    </div>


    These samples help you get started with building a product landing page with AMP:

    * **Product gallery:** with [amp-carousel](/components/amp-carousel/) and [amp-bind](/components/amp-bind) it is easy to create sophisticated image galleries. [Here](/advanced/image_galleries_with_amp-carousel/) are some samples how to implement image galleries with captions and thumbnails in AMPHTML.
    * **Product configuration:** use [amp-selector](/components/amp-selector) and [amp-bind](/components/ampbind) for advanced product configuration. For an advanced version see the [sample product page](/samples_templates/product_page/preview/).
    * **Add to Cart:** the [product sample](/samples_templates/product_page/preview/) demonstrates a fully functional add-to-cart flow which works across different origins.
    * **Tabs:** can be easily implemented by combining [amp-selector](/components/amp-selector/) with a flex layout. [Here](/advanced/tab_panels_with_amp-selector/) is a sample.
    * **Star ratings:** [here](/advanced/star_rating/) is a sample demonstrating how to implement a star rating system in AMP.
    * **Call tracking:** use [amp-call-tracking](/components/amp-call-tracking) to track calls initiated from your AMPs.
    * **Comments / reviews:** comment or review submission can be implemented using [amp-form](/components/amp-form). Combine with [amp-access](/components/amp-access/) if a user login is required. [Here](/samples_templates/comment_section/preview/) is a sample combining both.


    ## Product Category Page

    As popular landing pages for users, product category pages are well suited for AMP. They are often a mix of editorial content and the hero-style presentation of products. [Here](/samples_templates/product_browse_page/preview/) is a working sample of a product pages demonstrating common features of a category pages such as product listings or search.

    <div class="ampstart-mobile-frame xs-hide relative">
    <amp-img src="https://ampstart.com/img/www/mobile.svg" width="458" height="806" alt="Mobile outline" layout="responsive" class="absolute top-0 left-0 right-0 bottom-0"></amp-img>

    <amp-iframe title="AMP ecommerce app, product category page"
        width="324"
        height="494"
        layout="responsive"
        sandbox="allow-scripts allow-popups"
        allowfullscreen=""
        frameborder="0"
        src="/samples_templates/product_browse_page/preview/embed/"
        class="absolute xs-hide">
            <div placeholder=""></div>
    </amp-iframe>
    </div>

    Creating category pages with AMP is possible:

    * **Product search:** use [amp-form](/components/amp-form/) to implement a search form. You can serve search results either on a different page (which may not be AMP) or, even better, render search results directly inside the current page. See an example of [autosuggest](/advanced/autosuggest/).
    * **Product filtering and sorting:** see an example of [client-side filtering](/dynamic_amp/products_filter/).


    ## Dynamic Content

    AMPs can be served from an [AMP Cache](https://developers.google.com/amp/cache/overview) on a different origin, for example, when they're served in Google Search results. This makes it necessary to consider the AMP Cache's caching strategy when implementing AMPs:

    > The (AMP) cache follows a "stale-while-revalidate" model. It uses the origin's caching headers, such as `Max-Age`, as hints in deciding whether a particular document or resource is stale. When a user makes a request for something that is stale, that request causes a new copy to be fetched, so that the next user gets fresh content.
    [[source](https://developers.google.com/amp/cache/overview)]

    If it's critical that users never see stale data **or** that data is never older than 15s then additional steps are required. Product pricing or availability are typical examples for when this is the case. To ensure that users always see the latest content, you can use the [amp-list](/components/amp-list) component which will fetch and render content directly from your server.

    Here is a sample how to render product name and price using amp-list:
  -->
  <amp-list height="24" layout="fixed-height" src="https://ampbyexample.com/json/product.json" class="m1">
    <template type="amp-mustache">
      {{name}}: ${{price}}
    </template>
  </amp-list>

  <!-- ## Display personalized content

  It's common for e-commerce websites to display personalized content or recommendations in a carousel.
  One way to implement this is to have an `amp-list` returning the content of an `amp-carousel`.
  The `amp-mustache` default behaviour for rendering `amp-list` data is to cycle inside `items` objects; adding
  an `amp-carousel` inside the template would make the template render multiple carousels;
  one way to work around this is having the `amp-list` endpoint return a single entry in `items`, like this:

  ```
  {"items": [{
    "values": [/*...*/]
  ```

  -->
  <amp-list width="auto" height="400" layout="fixed-height" src="/json/product-single-item.json">
     <template type="amp-mustache">
       <amp-carousel height="400" layout="fixed-height" type="carousel">
       {{#values}}
           <amp-img src="{{img}}" layout="fixed" width="400" height="400" alt="{{name}}"></amp-img>
       {{/values}}
       </amp-carousel>
     </template>
   </amp-list>

  <!--
    <div class="ampstart-card info">
    **Best Practice:** use multiple `amp-list` instances with a single shared JSON endpoint to benefit from the AMP runtimes request caching and avoid multiple requests to this JSON endpoints.
    </div>

    Another approach is to use [amp-bind](/components/amp-bind/) with a JSON endpoint, This works well if up-to-date data needs to be available after an user interaction, for example, a hotel page displays room availability when the user selects specific dates.
  -->
  <div class="p1">
    <amp-state id="products" src="https://ampbyexample.com/json/products.json"></amp-state>
    <amp-img on="tap:AMP.setState({ productId: 0})" src="/img/red_apple_1_60x40.jpg" width="60" height="40" role="button" tabindex="0">
    </amp-img>
    <amp-img on="tap:AMP.setState({ productId: 1})" src="/img/green_apple_1_60x40.jpg" width="60" height="40" role="button" tabindex="0">
    </amp-img>
    <amp-img on="tap:AMP.setState({ productId: 2})" src="/img/product1_alt1_60x40.jpg" width="60" height="40" role="button" tabindex="0">
    </amp-img>
    <div [text]="products[productId] + ' available'">Please select a product</div>
  </div>

  <!--
    ## Personalization and Login

    An easy way to provide personalized content in AMPs is to use `amp-list`. You can either use cookies (using the attribute `credentials="include"`) or AMP's [client id](https://github.com/ampproject/amphtml/blob/master/spec/amp-var-substitutions.md#client-id):
  -->

  <amp-list credentials="include" height="24" layout="fixed-height" src="https://ampbyexample.com/json/product.json?clientId=CLIENT_ID(myCookieId)" class="m1">
    <template type="amp-mustache">
      Your personal offer: ${{price}}
    </template>
  </amp-list>

  <!--
    <div class="ampstart-card info">
    **Best Practice:** make sure to configure the [AMP CORS headers](https://github.com/ampproject/amphtml/blob/master/spec/amp-cors-requests.md) when using `amp-list` for personalization.
    </div>

    If you rely on logged-in users, you can use the [amp-access](/components/amp-access/) component to log users into your website directly from an AMP page. Check out [this sample](/samples_templates/comment_section/) for how to implement a login flow in AMP.

    <div class="ampstart-card info">
    **Good to know:** `amp-access` works on your origin and on AMP Caches.
    </div>


    ## Checkout flow and Payments

    To avoid user frustration, make it possible for users to initiate checkout directly from within your AMP pages.
    Here are three ways you can handle checkout in AMP pages:

    * In Chrome you can use the Payments Requests API. Checkout the [payments sample](/advanced/payments_in_amp/preview/) to see how it works.
    * Implement your checkout flow inside your AMP pages using [amp-form](/components/amp-form).
    * Re-direct users to the checkout flow on your website. Important: make the transition as seamless as possible, in particular, don't let users enter the same information twice.


    <div class="ampstart-card info">
    **Best Practice:** speed-up the transition from your AMPs to your normal website using [amp-install-serviceworker](/components/amp-install-serviceworker/) to pre-cache parts of your normal website.
    </div>

    ## Analytics

    Make sure to measure how users engage with your AMP Pages using [amp-analytics](https://www.ampproject.org/docs/reference/components/amp-analytics). **Important:** test your analytics integration and make sure that AMP traffic is correctly attributed, in particular if the AMP is served from a different origin. For testing, you can [load your AMPs via the Google AMP Cache](https://ampbyexample.com/advanced/using_the_google_amp_cache/).

    <div class="ampstart-card info">
    **Best Practice:** treat your AMPs as a different platform similar to like you would treat an email campaign. Make sure to properly attribute links from your AMPs back to your website.
    </div>

  -->

</body>
</html>