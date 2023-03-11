{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
<div class="panel everheader">
    <div class="panel-heading">
        <i class="icon icon-smile"></i> {l s='Ever SEO' mod='everpsseo'}
    </div>
    <div class="panel-body">
        <div class="col-md-6">
            <a href="#everbottom" id="evertop">
               <img id="everlogo" src="{$image_dir|escape:'htmlall':'UTF-8'}/ever.png" style="max-width: 120px;">
            </a>
            <strong>{l s='Welcome to Ever SEO !' mod='everpsseo'}</strong><br />{l s='Please configure your this form to set redirections for 404 pages' mod='everpsseo'}<br />
            <p>
                <strong>{l s='Click on our logo to go direct to bottom' mod='everpsseo'}</strong>
            </p>
            {if isset($ever_seo_version) && $ever_seo_version}
            <p>
                {l s='Ever SEO version' mod='everpsseo'} {$ever_seo_version|escape:'htmlall':'UTF-8'}
            </p>
            {/if}
            {if isset($moduleConfUrl) && $moduleConfUrl}
            <p>
                <a href="{$moduleConfUrl|escape:'htmlall':'UTF-8'}" target="_blank" class="btn btn-success">{l s='Direct link to module configuration' mod='everpsseo'}</a>
            </p>
            {/if}
            {if isset($seo_configure) && $seo_configure}
            <p>
                <a href="{$seo_configure|escape:'htmlall':'UTF-8'}" target="_blank" class="btn btn-default">{l s='First module configuration' mod='everpsseo'}</a>
            </p>
            {/if}
            {if isset($rewrite_enabled) && $rewrite_enabled === 0}
            <p class="alert alert-warning">
                {l s='Please enable rewriting rules on SEO admin page' mod='everpsseo'}
            </p>
            {/if}
            {if isset($ssl_enabled) && $ssl_enabled === 0}
            <p class="alert alert-warning">
                {l s='Please enable SSL rules on your shop' mod='everpsseo'}
            </p>
            {/if}
            {if isset($canonical) && $canonical === 0}
            <p class="alert alert-warning">
                {l s='Please set canonical URL to 301 on SEO admin page' mod='everpsseo'}
            </p>
            {/if}
            {if isset($redirects_enabled) && $redirects_enabled === 0}
            <p class="alert alert-warning">
                {l s='Auto redirect 404 is not enabled on your shop, please check module settings on 404 tab' mod='everpsseo'}
            </p>
            {/if}
            <h4>{l s='PHP console commands' mod='everpsseo'}</h4>
            <ul>
                <li><code>php bin/console everpsseo:seo:metas idshop 1</code> {l s='generate all metas for id shop 1, depending on module settings' mod='everpsseo'}</li>
                <li><code>php bin/console everpsseo:seo:content idshop 1</code> {l s='generate all content (products, categories, suppliers, manufacturers) for id shop 1, depending on module settings' mod='everpsseo'}</li>
                <li><code>php bin/console everpsseo:seo:sitemaps idshop 1</code> {l s='generate all sitemaps and ping search engines for id shop 1, depending on module settings' mod='everpsseo'}</li>
                <li><code>php bin/console everpsseo:seo:import</code> {l s='update objects depending on XLSX file' mod='everpsseo'}</li>
                <li><code>php bin/console everpsseo:seo:export categories</code> {l s='export SEO categories on XLSX file' mod='everpsseo'}</li>
                <li><code>php bin/console everpsseo:seo:export products</code> {l s='export SEO products on XLSX file' mod='everpsseo'}</li>
                <li><code>php bin/console everpsseo:seo:execute createWebpImage</code> {l s='create webp images' mod='everpsseo'}</li>
            </ul>
        </div>
        <div class="col-md-6 col-xs-12">
            {if $sitemaps}
                <div class="dropdown">
                  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {l s='See all sitemaps' mod='everpsseo'}
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    {foreach from=$sitemaps item=sitemap}
                        {if $sitemap != '.' && $sitemap != '..' && $sitemap != 'index.php' && $sitemap != 'indexes'}
                        <p><a class="dropdown-item" href="{$sitemap|escape:'htmlall':'UTF-8'}" target="_blank">{$sitemap|escape:'htmlall':'UTF-8'}</a></p>
                        {/if}
                    {/foreach}
                  </div>
                </div>
            {/if}
            {if $indexes}
                <div class="dropdown">
                  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {l s='See all indexes' mod='everpsseo'}
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    {foreach from=$indexes item=indexe}
                        {if $indexe != '.' && $indexe != '..' && $indexe != 'index.php'}
                        <p><a class="dropdown-item" href="{$indexe|escape:'htmlall':'UTF-8'}" target="_blank">{$indexe|escape:'htmlall':'UTF-8'}</a></p>
                        {/if}
                    {/foreach}
                  </div>
                </div>
            {/if}
            <h4>{l s='Please set this cron to update your sitemaps' mod='everpsseo'}</h4>
            <p>{$everpsseo_cron|escape:'htmlall':'UTF-8'}</p>
            <a href="{$everpsseo_cron|escape:'htmlall':'UTF-8'}" target="_blank" class="btn btn-default">{l s='Update sitemaps now !' mod='everpsseo'}</a>
            <a href="https://search.google.com/search-console/users?resource_id={$searchconsole|escape:'htmlall':'UTF-8'}" target="_blank" class="btn btn-default">{l s='Go to Google Search Console' mod='everpsseo'}</a>
            <a href="https://analytics.google.com/analytics/web/" target="_blank" class="btn btn-default">{l s='Go to Google Analytics' mod='everpsseo'}</a>
            <a href="https://www.bing.com/webmaster/home/dashboard" target="_blank" class="btn btn-default">{l s='Go to Bing Webmaster Tools' mod='everpsseo'}</a>
            <h4>{l s='If you are using Store Commander, please set this cron to enable detecting new elements' mod='everpsseo'}</h4>
            <p>{$everpsseo_objects|escape:'htmlall':'UTF-8'}</p>
            <a href="{$everpsseo_objects|escape:'htmlall':'UTF-8'}" target="_blank" class="btn btn-default">{l s='Check and update elements now !' mod='everpsseo'}</a>
            <h4>{l s='XLSX files examples' mod='everpsseo'}</h4>
            <ul>
                <li>
                    {l s='Categories update file example :' mod='everpsseo'} <a href="{$input_dir|escape:'htmlall':'UTF-8'}categories.xlsx" target="_blank">{l s='Download' mod='everpsseo'}</a>
                </li>
                <li>
                    {l s='Products update file example :' mod='everpsseo'} <a href="{$input_dir|escape:'htmlall':'UTF-8'}products.xlsx" target="_blank">{l s='Download' mod='everpsseo'}</a>
                </li>
            </ul>
        </div>
    </div>
</div>
