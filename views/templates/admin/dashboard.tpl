{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
<section id="everpsseo" class="panel widget allow_push everheader everdashboard">
    <header class="panel-heading">
        <i class="icon-team-ever"></i> {l s='Ever SEO' mod='everpsseo'} {if isset($ever_seo_version) && $ever_seo_version}{$ever_seo_version|escape:'htmlall':'UTF-8'}{/if}
    </header>
    <div id="everpsseo_toolbar" class="row">
        <h3 style="padding: 3px;">{l s='Welcome to your website !' mod='everpsseo'}</h3>
        <div class="col-xs-12">
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
            <h4>{l s='Please set this cron to update your sitemaps' mod='everpsseo'}</h4>
            <p>{$everpsseo_cron|escape:'htmlall':'UTF-8'}</p>
            <h4>{l s='If you are using Store Commander, please set this cron to enable detecting new elements' mod='everpsseo'}</h4>
            <p>{$everpsseo_objects|escape:'htmlall':'UTF-8'}</p>
            <a href="{$everpsseo_objects|escape:'htmlall':'UTF-8'}" target="_blank" class="btn btn-default">{l s='Check and update elements now !' mod='everpsseo'}</a>
            {if isset($moduleConfUrl) && $moduleConfUrl}
            <a href="{$moduleConfUrl|escape:'htmlall':'UTF-8'}" class="btn btn-success">{l s='Direct link to module configuration' mod='everpsseo'}</a>
            {/if}
            {if isset($seo_configure) && $seo_configure}
            <a href="{$seo_configure|escape:'htmlall':'UTF-8'}" class="btn btn-success">{l s='First module configuration' mod='everpsseo'}</a>
            {/if}
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
            <div class="">
                <a href="{$everpsseo_cron|escape:'htmlall':'UTF-8'}" target="_blank" class="btn btn-default">{l s='Update sitemaps now !' mod='everpsseo'}</a>
                <a href="https://search.google.com/search-console/users?resource_id={$searchconsole|escape:'htmlall':'UTF-8'}" target="_blank" class="btn btn-default">{l s='Go to Google Search Console' mod='everpsseo'}</a>
                <a href="https://analytics.google.com/analytics/web/" target="_blank" class="btn btn-default">{l s='Go to Google Analytics' mod='everpsseo'}</a>
                <a href="https://www.bing.com/webmaster/home/dashboard" target="_blank" class="btn btn-default">{l s='Go to Bing Webmaster Tools' mod='everpsseo'}</a>
            </div>
        </div>
    </div>
    <p>
      <a class="btn btn-primary" data-toggle="collapse" href="#everbest_products" role="button" aria-expanded="false" aria-controls="everbest_products">
        {l s='10 most viewed products' mod='everpsseo'}
      </a>
      <a class="btn btn-primary" data-toggle="collapse" href="#best_categories" role="button" aria-expanded="false" aria-controls="best_categories">
        {l s='10 most viewed categories' mod='everpsseo'}
      </a>
      <a class="btn btn-primary" data-toggle="collapse" href="#best_404" role="button" aria-expanded="false" aria-controls="best_404">
        {l s='10 most viewed 404' mod='everpsseo'}
      </a>
      <a class="btn btn-primary" data-toggle="collapse" href="#best_referal" role="button" aria-expanded="false" aria-controls="best_referal">
        {l s='10 most viewed referals' mod='everpsseo'}
      </a>
    </p>
    <div class="collapse" id="everbest_products">
      <div class="card card-body">
        <div class="tab-content panel">
            <div class="tab-pane active" id="dash_best_products">
            <h3 class="text-center">{l s='10 most viewed products' mod='everpsseo'}</h3>
                <div class="table-responsive">
                    <table class="table data_table">
                        <thead>
                            <tr>
                                <th class="text-center">{l s='ID product' mod='everpsseo'}</th>
                                <th class="text-center">{l s='Product name' mod='everpsseo'}</th>
                                <th class="text-center">{l s='View count' mod='everpsseo'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$bestProducts item=bestProduct}
                                <tr>
                                    <td class="text-center">
                                        <a href="{$bestProduct->url|escape:'htmlall':'UTF-8'}" target="_blank">
                                            {$bestProduct->id|escape:'htmlall':'UTF-8'}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{$bestProduct->url|escape:'htmlall':'UTF-8'}" target="_blank">
                                            {$bestProduct->name|escape:'htmlall':'UTF-8'}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{$bestProduct->url|escape:'htmlall':'UTF-8'}" target="_blank">
                                            {$bestProduct->count|escape:'htmlall':'UTF-8'}
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
      </div>
    </div>
    <div class="collapse" id="best_categories">
      <div class="card card-body">
        <div class="tab-content panel">
            <div class="tab-pane active" id="dash_best_categories">
            <h3 class="text-center">{l s='10 most viewed categories' mod='everpsseo'}</h3>
                <div class="table-responsive">
                    <table class="table data_table">
                        <thead>
                            <tr>
                                <th class="text-center">{l s='ID category' mod='everpsseo'}</th>
                                <th class="text-center">{l s='Category name' mod='everpsseo'}</th>
                                <th class="text-center">{l s='View count' mod='everpsseo'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$bestCategories item=bestCategory}
                                <tr>
                                    <td class="text-center">
                                        <a href="{$bestCategory->url|escape:'htmlall':'UTF-8'}" target="_blank">
                                            {$bestCategory->id|escape:'htmlall':'UTF-8'}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{$bestCategory->url|escape:'htmlall':'UTF-8'}" target="_blank">
                                            {$bestCategory->name|escape:'htmlall':'UTF-8'}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{$bestCategory->url|escape:'htmlall':'UTF-8'}" target="_blank">
                                            {$bestCategory->count|escape:'htmlall':'UTF-8'}
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
      </div>
    </div>
    <div class="collapse" id="best_404">
      <div class="card card-body">
        <div class="tab-content panel">
            <div class="tab-pane active" id="dash_best_404">
            <h3 class="text-center">{l s='10 most viewed 404' mod='everpsseo'}</h3>
                <div class="table-responsive">
                    <table class="table data_table">
                        <thead>
                            <tr>
                                <th class="text-center">{l s='ID 404' mod='everpsseo'}</th>
                                <th class="text-center">{l s='404 URL' mod='everpsseo'}</th>
                                <th class="text-center">{l s='View count' mod='everpsseo'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$bestRedirects item=redirect}
                                <tr>
                                    <td class="text-center">
                                        <a href="{$redirect->not_found|escape:'htmlall':'UTF-8'}" target="_blank">
                                            {$redirect->id|escape:'htmlall':'UTF-8'}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{$redirect->not_found|escape:'htmlall':'UTF-8'}" target="_blank">
                                            {$redirect->not_found|escape:'htmlall':'UTF-8'}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{$redirect->not_found|escape:'htmlall':'UTF-8'}" target="_blank">
                                            {$redirect->count|escape:'htmlall':'UTF-8'}
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
      </div>
    </div>
    <div class="collapse" id="best_referal">
      <div class="card card-body">
        <div class="tab-content panel">
            <div class="tab-pane active" id="dash_best_referal">
            <h3 class="text-center">{l s='10 most viewed referals' mod='everpsseo'}</h3>
                <div class="table-responsive">
                    <table class="table data_table">
                        <thead>
                            <tr>
                                <th class="text-center">{l s='ID Referal' mod='everpsseo'}</th>
                                <th class="text-center">{l s='Referal URL' mod='everpsseo'}</th>
                                <th class="text-center">{l s='View count' mod='everpsseo'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$bestReferals item=referal}
                                <tr>
                                    <td class="text-center">
                                        <a href="{$referal->everfrom|escape:'htmlall':'UTF-8'}" target="_blank">
                                            {$referal->id|escape:'htmlall':'UTF-8'}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{$referal->everfrom|escape:'htmlall':'UTF-8'}" target="_blank">
                                            {$referal->everfrom|escape:'htmlall':'UTF-8'}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{$referal->everfrom|escape:'htmlall':'UTF-8'}" target="_blank">
                                            {$referal->count|escape:'htmlall':'UTF-8'}
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
      </div>
    </div>
</section>
