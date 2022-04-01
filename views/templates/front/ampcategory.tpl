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
  <title>Product Browse Page</title>
      <script async src="https://cdn.ampproject.org/v0.js"></script>
      
      <script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
      <script async custom-element="amp-list" src="https://cdn.ampproject.org/v0/amp-list-0.1.js"></script>
      <script async custom-template="amp-mustache" src="https://cdn.ampproject.org/v0/amp-mustache-0.2.js"></script>
      <script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>
      <script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
      <script async custom-element="amp-fit-text" src="https://cdn.ampproject.org/v0/amp-fit-text-0.1.js"></script>
      <script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
      <script async custom-element="amp-bind" src="https://cdn.ampproject.org/v0/amp-bind-0.1.js"></script>
      <script async custom-element="amp-lightbox" src="https://cdn.ampproject.org/v0/amp-lightbox-0.1.js"></script>
      <script async custom-element="amp-selector" src="https://cdn.ampproject.org/v0/amp-selector-0.1.js"></script>
      <link rel="canonical" href="https://ampbyexample.com/samples_templates/product_browse_page/">
      <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
      <style amp-custom>
article#preview {
  background: #ECEFF1;
}
         article#preview {
           background-color:#f5f5f5
         }

         amp-carousel {
           margin:0
         }

         .header {
           position:relative;
           box-shadow:0 2px 2px 0 rgba(0,0,0,.14),0 3px 1px -2px rgba(0,0,0,.2),0 1px 5px 0 rgba(0,0,0,.12);
           padding-top:52px;
           background-color:#607D8B
         }

         .header #sample-menu {
           position:absolute;
           top:0;
           left:0;
           font-size:18px;
           font-weight:700;
           color:#fff;
           text-transform:uppercase;
           padding:13px
         }

         .header #sample-logo {
           position:absolute;
           top:0;
           left:36px;
           font-size:18px;
           font-weight:400;
           color:#fff;
           text-transform:uppercase;
           margin:0 16px;
           height:32px;
           text-decoration:none;
           line-height:52px
         }

         .header form {
           padding:0
         }

         .header input::-webkit-search-decoration,.header input::-webkit-search-cancel-button {
           display:none
         }

         .header input[type=submit] {
           position:absolute;
           top:10px;
           right:8px;
           background-color:#eb407a;
           background-position:center;
           background-repeat:no-repeat;
           background-image:-webkit-image-set(url(/img/ic_search_white_1x_web_24dp.png) 1x,url(/img/ic_search_white_2x_web_24dp.png) 2x);
           border:0;
           height:32px;
           width:36px;
           -webkit-appearance:none;
           border-radius:4px 0
         }

         .header input[type=search],.header input[type=text] {
           position:absolute;
           top:10px;
           right:24px;
           background:#ededed;
           padding:0 8px;
           width:20vw;
           height:32px;
           font-family:inherit;
           font-size:100%;
           border:solid 0 #ccc;
           border-radius:4px 0;
           transition:all .9s ease
         }

         .header input[type=search]:focus,.header input[type=text]:focus {
           width:80vw;
           max-width:700px;
           background-color:#fff;
           border-color:#eb407a;
           box-shadow:0 0 5px rgba(109,207,246,.5);
           outline:none
         }

         .header input:-moz-placeholder,.header input::-webkit-input-placeholder {
           color:#999
         }

         .header input:focus + #sample-logo {
           visibility:hidden;
           opacity:0;
           transition:visibility 0 0.7s,opacity .7s ease
         }

         .header input + #sample-logo {
           visibility:visible;
           opacity:1;
           transition:opacity 1.5s ease
         }

         @media (min-width: 600px) {
           .header input[type=search] {
             width:100px
           }

           .header input[type=search]:focus {
             width:600px
           }
         }

         .items,amp-list.items > div {
           display:flex;
           justify-content:space-around;
           flex-wrap:wrap
         }

         .item {
           flex-grow:1;
           flex-shrink:1
         }

         .tile {
           background-color:#fff;
           width:120px;
           height:200px;
           display:block;
           margin:8px;
           -webkit-tap-highlight-color:#eee;
           max-width:200px
         }

         @media (max-width: 500px) {
           .items .tile {
             width:150px;
             height:200px
           }
         }

         .tile:active {
           background-color:#eee
         }

         .tile p {
           margin:0;
           padding:0 8px;
           font-size:14px;
           line-height:20px
         }

         .tile .name {
           margin-top:8px
         }

         .tile .price {
           margin-bottom:8px
         }

         .tile .price,.tile .star {
           color:#000
         }         #hero-images {
           object-fit: contain;
         }
         #hero-images .caption {
           text-align: center;
           position: absolute;
           bottom: 0;
           left: 0;
           right: 0;
           padding: 24px;
           background: rgba(200, 200, 200, 0.5);
           color: white;
           font-size: 36px;
           max-height: 30%;
           font-weight: 300;
         }
         .list-overflow {
           width: 160px;
           margin-top: 150px;
         }
         .refine {
           display: none;
         }
         .filter-mobile {
           display: none;
         }
         @media (min-width: 600px) {
           .refine {
             display:inline;
             float: left;
             width: 20%;
             box-shadow: 5px 0 5px -5px rgba(0,0,0,.4);
           }
           .content {
             float: right;
             width: 80%;
           }
           .grid {
             display: flex;
             justify-content: space-around;
             flex-wrap: wrap;
           }
           .products {
             display: inline-block;
           }
         }
         @media (max-width: 500px) {
           .filter-mobile {
             display:inline;
           }
           .products {
             display: block;
             height: 100px;
             padding: 0;
             padding-right: 8px;
           }
           .products amp-img {
             line-height: 24px;
             font-weight: 400;
             font-size: 24px;
             vertical-align: middle;
             float: left;
             margin-right: 10px;
           }
         }
         #main-wrap {
           overflow: hidden;
         }
         #info-wrap {
           overflow: hidden;
         }
         .info {
           width: 50%;
           float: left;
         }
         amp-lightbox {
           background: white;
           width: 100%;
           height: 100%;
           position: absolute;
         }
         #info-wrap .ampstart-input {
           width: 200px;
         }
         .autosuggest-container {
            position: relative;
          }

         .autosuggest-box {
            position: absolute;
            width: 100%;
            background-color: #fafafa;
            box-shadow: 0px 2px 6px rgba(0,0,0,.3);
          }
          .hidden {
            display: none;
          }
      </style>
      <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style>
      <noscript>
        <style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style>
      </noscript>
    </head>
    <body>
      
      
      <amp-sidebar id="drawermenu" layout="nodisplay">
        <a href="/" class="caps text-decoration-none block p1">Products</a>
        <hr/>
        <a class="caps text-decoration-none block p1" href="/samples_templates/product_browse_page/preview/">Fruit</a>
        <a class="caps text-decoration-none block p1" href="/samples_templates/product_browse_page/preview/">Vegetable</a>
        <a class="caps text-decoration-none block p1" href="/samples_templates/product_browse_page/preview/">More</a>
      </amp-sidebar>
      
      
      <div class="header">
        <a id="sample-menu" on="tap:drawermenu.toggle">
          <amp-img srcset="/img/ic_menu_white_1x_web_24dp.png 1x, /img/ic_menu_white_2x_web_24dp.png 2x" width="24" height="24" alt="navigation"></amp-img>
        </a>
        <input name="search" type="text" placeholder="Search" on="change:AMP.setState({
          products: {
          listSrc: '/samples_templates/products?searchProduct='+ event.value +'&sort='+ products.sortChoiceValue +'&searchColor='+ products.searchColor +'&_=RANDOM',
          searchProduct: event.value
        }});
          input-debounced:
            AMP.setState({
              query: event.value,
              showDropdown: event.value,
            }),
          autosuggest-list.show;
          tap:
            AMP.setState({
              query: query == null ? '' : query,
              showDropdown: 'true'
            }),
            autosuggest-list.show"
          [value]="query || ''"
          value="">
        <a id="sample-logo" href="/">Products</a>
        <div class="suggest">
            <div
              class="autosuggest-container hidden"
              [class]="(showDropdown && query) ?
                'autosuggest-container' :
                'autosuggest-container hidden'"
            >
              <amp-list
                class="autosuggest-box"
                layout="fixed-height"
                height="120"
                src="/samples_templates/products_autosuggest"
                [src]="query ?
                  autosuggest.endpoint + query :
                  autosuggest.emptyAndInitialTemplateJson"
                id="autosuggest-list"
              >
                <template type="amp-mustache">
                  <amp-selector
                    id="autosuggest-selector"
                    keyboard-select-mode="focus"
                    layout="container"
                    on="
                      select:
                        AMP.setState({
                          query: event.targetOption,
                          showDropdown: false
                        }),
                        autosuggest-list.hide"
                  >
                    {{#results}}
                      <div
                        class="select-option no-outline ml2"
                        role="option"
                        tabindex="0"
                        on="tap:autosuggest-list.hide"
                        option="{{.}}"
                      >{{.}}</div>
                    {{/results}}
                    {{^results}}
                      <div class="select-option {{#query}}empty{{/query}}">
                        {{#query}}Sorry! We couldn't find the product 😰{{/query}}
                      </div>
                    {{/results}}
                  </amp-selector>
                </template>
              </amp-list>
            </div>
          </div>
        
        <input type="submit" value="">
      </div>
      
      
      <amp-carousel id="hero-images" width="1024" height="480" layout="responsive" type="slides" autoplay>
        <a href="#">
          <amp-img src="/img/product_hero1_1024x683.jpg" layout="fill" alt="product hero 1" attribution="visualhunt"></amp-img>

          <amp-fit-text class="caption" width="300" height="200" layout="responsive"
                                                                 max-font-size="36">
            The 10 best Apples
          </amp-fit-text>
        </a>
        <a href="#">
          <amp-img src="/img/product_hero2_1024x683.jpg" layout="fill" alt="product hero 2" attribution="visualhunt"></amp-img>
          <amp-fit-text class="caption" width="300" height="200" layout="responsive"
                                                                 max-font-size="36">
            So much orange!
          </amp-fit-text>
        </a>
        <a href="#">
          <amp-img src="/img/product_hero3_1024x683.jpg" layout="fill" alt="product hero 3" attribution="visualhunt"></amp-img>
          <amp-fit-text class="caption" width="300" height="200" layout="responsive"
                                                                 max-font-size="36">
            So healthy!
          </amp-fit-text>
        </a>
      </amp-carousel>
      <amp-state id="products">
        <script type="application/json">
{
              "sortChoiceValue": "price-descendent",
              "searchProduct": "",
              "searchColor": "all",
              "listSrc": "/samples_templates/products?sortChoiceValue=price-descendent&searchColor=all&_=RANDOM"
            }
        </script>
      </amp-state>
      <amp-lightbox id="filter-lightbox" layout="nodisplay">
        <div>
          <h3 class="m1 mt2">Filter results</h3>
          <h4 class="p1">Color</h4>
          <div class="ampstart-input ampstart-input-chk inline-block relative mb3 mx1">
            <input type="radio" name="colorFilter" class="relative" value="Yellow" on="change:AMP.setState({
            products: {
            searchColor: event.checked == true ? 'yellow' : '',
            listSrc: '/samples_templates/products?searchProduct='+ products.searchProduct +'&sort='+ products.sortChoiceValue +'&searchColor='+ (event.checked == true ? 'yellow' : '') +'&_=RANDOM'
            }
            }), filter-lightbox.close">
            <label for="yellow"> yellow </label>
          </div>
          <div class="ampstart-input ampstart-input-chk inline-block relative mb3 mx1">
            <input type="radio" name="colorFilter" class="relative" value="Orange" on="change:AMP.setState({
            products: {
            searchColor: event.checked == true ? 'orange' : '',
            listSrc: '/samples_templates/products?searchProduct='+ products.searchProduct +'&sort='+ products.sortChoiceValue +'&searchColor='+ (event.checked == true ? 'orange' : '') +'&_=RANDOM'
            }
            }), filter-lightbox.close">
            <label for="orange"> orange </label>
          </div>
          <div class="ampstart-input ampstart-input-chk inline-block relative mb3 mx1">
            <input type="radio" name="colorFilter" class="relative" value="Green" on="change:AMP.setState({
            products: {
            searchColor: event.checked == true ? 'green' : '',
            listSrc: '/samples_templates/products?searchProduct='+ products.searchProduct +'&sort='+ products.sortChoiceValue +'&searchColor='+ (event.checked == true ? 'green' : '') +'&_=RANDOM'
            }
            }), filter-lightbox.close">
            <label for="green"> green </label>
          </div>
          <div class="ampstart-input ampstart-input-chk inline-block relative mb3 mx1">
            <input type="radio" name="colorFilter" class="relative" value="all" on="change:AMP.setState({
            products: {
            searchColor: event.checked == true ? 'all' : '',
            listSrc: '/samples_templates/products?searchProduct='+ products.searchProduct +'&sort='+ products.sortChoiceValue +'&searchColor='+ (event.checked == true ? 'all' : '') +'&_=RANDOM'
            }
            }), filter-lightbox.close">
            <label for="all"> all </label>
          </div>
          <div class="mx1">
            <a on="tap:filter-lightbox.close" role="button" tabindex="0" class="ampstart-btn inline-block">
              Close
            </a>
          </div>
        </div>
      </amp-lightbox>
      
      
      <div id="main-wrap">
        <div class="refine p1">
          <h3>Filter results</h3>
          <h4 class="p1">Color</h4>
          <div class="ampstart-input ampstart-input-chk inline-block relative mb3 mx1">
            <input type="radio" id="yellow" name="colorFilter" class="relative" value="Yellow" on="change:AMP.setState({
            products: {
            searchColor: event.checked == true ? 'yellow' : '',
            listSrc: '/samples_templates/products?searchProduct='+ products.searchProduct +'&sort='+ products.sortChoiceValue +'&searchColor='+ (event.checked == true ? 'yellow' : '') +'&_=RANDOM'
            }
            })">
            <label for="yellow"> yellow </label>
          </div>
          <div class="ampstart-input ampstart-input-chk inline-block relative mb3 mx1">
            <input type="radio" id="orange" name="colorFilter" class="relative" value="Orange" on="change:AMP.setState({
            products: {
            searchColor: event.checked == true ? 'orange' : '',
            listSrc: '/samples_templates/products?searchProduct='+ products.searchProduct +'&sort='+ products.sortChoiceValue +'&searchColor='+ (event.checked == true ? 'orange' : '') +'&_=RANDOM'
            }
            })">
            <label for="orange"> orange </label>
          </div>
          <div class="ampstart-input ampstart-input-chk inline-block relative mb3 mx1">
            <input type="radio" id="green" name="colorFilter" class="relative" value="Green" on="change:AMP.setState({
            products: {
            searchColor: event.checked == true ? 'green' : '',
            listSrc: '/samples_templates/products?searchProduct='+ products.searchProduct +'&sort='+ products.sortChoiceValue +'&searchColor='+ (event.checked == true ? 'green' : '') +'&_=RANDOM'
            }
            })">
            <label for="green"> green </label>
          </div>
          <div class="ampstart-input ampstart-input-chk inline-block relative mb3 mx1">
            <input type="radio" id="all" name="colorFilter" class="relative" value="all" on="change:AMP.setState({
            products: {
            searchColor: event.checked == true ? 'all' : '',
            listSrc: '/samples_templates/products?searchProduct='+ products.searchProduct +'&sort='+ products.sortChoiceValue +'&searchColor='+ (event.checked == true ? 'all' : '') +'&_=RANDOM'
            }
            })">
            <label for="all"> all </label>
          </div>
        </div>
        <div class="content">
          <div id="info-wrap">
            <div class="ampstart-input inline-block relative m1">
              <select id="sort" on="change:AMP.setState({
              products: {
              sortChoiceValue: event.value,
              listSrc: '/samples_templates/products?searchProduct='+products.searchProduct+'&sort='+event.value+'&searchColor='+products.searchColor+'&_=RANDOM'
              }
              })">
                <option value="price-descendent">Price high to low
                </option>
                <option value="price-ascendent">Price low to high
                </option>
              </select>
              <label for="sort" class="absolute top-0 right-0 bottom-0 left-0">Sort</label>
            </div>
            <button class="ampstart-btn caps m1 mb3 filter-mobile" on="tap:filter-lightbox">
              Filter
            </button>
          </div>
          <amp-list width="auto"
                    height="600"
                    layout="fixed-height"
                    src="/samples_templates/products?sortChoiceValue=price-descendent&searchColor=all&_=RANDOM'"
                    [src]="products.listSrc"
                    class="grid">
            <template type="amp-mustache">
              <a class="m1 text-decoration-none products" href="/samples_templates/product//source">
                <amp-img width="150"
                         height="100"
                         alt="{{name}}"
                         src="{{img}}"></amp-img>
                       <p class="name">{{name}}</p>
                       <p class="star">{{{stars}}}</p>
                       <p class="price">${{price}}</p>
              </a>
            </template>
          </amp-list>
        </div>
      </div>
      
      
      <h3 class="pl1 pt2 pb2">Recommendations</h3>
      
      <amp-carousel class="m1" width="auto" height="160" layout="fixed-height" type="carousel">
        <a class="m1 text-decoration-none" href="#" role="listitem">
          <amp-img width="640" height="426" layout="responsive" alt="Apple" src="/img/product1_640x426.jpg"></amp-img>
          <p class="name">Apple</p>
          <p class="star">★★★★★</p>
          <p class="price">$1.99</p>
        </a>
        <a class="m1 text-decoration-none" href="#" role="listitem">
          <amp-img width="640" height="426" layout="responsive" alt="Orange" src="/img/product2_640x426.jpg"></amp-img>
          <p class="name">Orange</p>
          <p class="star">★★★★☆</p>
          <p class="price">$0.99</p>
        </a>
        <a class="m1 text-decoration-none" href="#" role="listitem">
          <amp-img width="640" height="426" layout="responsive" alt="Pear" src="/img/product3_640x426.jpg"></amp-img>
          <p class="name">Pear</p>
          <p class="star">★★★☆☆</p>
          <p class="price">$1.50</p>
        </a>
        <a class="m1 text-decoration-none" href="#" role="listitem">
          <amp-img width="640" height="426" layout="responsive" alt="Banana" src="/img/product4_640x426.jpg"></amp-img>
          <p class="name">Banana</p>
          <p class="star">★★★★★</p>
          <p class="price">$1.50</p>
        </a>
        <a class="m1 text-decoration-none" href="#" role="listitem">
          <amp-img width="640" height="426" layout="responsive" alt="Apple" src="/img/product1_640x426.jpg"></amp-img>
          <p class="name">Apple 2</p>
          <p class="star">★★★★★</p>
          <p class="price">$1.99</p>
        </a>
        <a class="m1 text-decoration-none" href="#" role="listitem">
          <amp-img width="640" height="426" layout="responsive" alt="Orange" src="/img/product2_640x426.jpg"></amp-img>
          <p class="name">Orange 2</p>
          <p class="star">★★★★☆</p>
          <p class="price">$0.99</p>
        </a>
        <a class="m1 text-decoration-none" href="#" role="listitem">
          <amp-img width="640" height="426" layout="responsive" alt="Pear" src="/img/product3_640x426.jpg"></amp-img>
          <p class="name">Pear 2</p>
          <p class="star">★★★☆☆</p>
          <p class="price">$1.50</p>
        </a>
        <a class="m1 text-decoration-none" href="#" role="listitem">
          <amp-img width="640" height="426" layout="responsive" alt="Banana" src="/img/product4_640x426.jpg"></amp-img>
          <p class="name">Banana 2</p>
          <p class="star">★★★★★</p>
          <p class="price">$1.50</p>
        </a>
      </amp-carousel>
      
      <h3 class="pl1 pt2 pb2">Bestsellers</h3>
      
      <amp-list class="items"
                width="auto"
                height="160"
                layout="fixed-height"
                src="/json/related_products.json">
        <template type="amp-mustache">
          <a class="m1 text-decoration-none" href="/samples_templates/product/preview/">
            <amp-img width="640"
                     height="426"
                     layout="responsive"
                     alt="{{name}}"
                     src="{{img}}"></amp-img>
                   <p class="name">{{name}}</p>
                   <p class="star">{{{stars}}}</p>
                   <p class="price">${{price}}</p>
          </a>
        </template>
      </amp-list>
      
    <amp-state id="autosuggest">
        <script type="application/json">
        {
          "endpoint": "/samples_templates/products_autosuggest?q=",
          "emptyAndInitialTemplateJson": [{
            "query": "",
            "results": []
          }]
        }
        </script>
    </amp-state>
      
      
      <amp-analytics type="googleanalytics">
        <script type="application/json">
{
                    "vars": {
                        "account": "UA-80609902-1"
                    },
                    "triggers": {
                        "default pageview": {
                            "on": "visible",
                            "request": "pageview",
                            "vars": {
                                "title": "{{title}}"
                            }
                        }
                    }
                }

        </script>
      </amp-analytics>
    </body>
  </html>