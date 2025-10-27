{*
 * Project : everpsseo
 * Redesigned configuration header with dashboard insights
 *}
<div class="everpsseo-dashboard" id="evertop">
    <section class="everpsseo-hero">
        <div class="everpsseo-hero__main">
            <div class="everpsseo-hero__brand">
                <img src="{$image_dir|escape:'htmlall':'UTF-8'}/ever.png" alt="{l s='Ever SEO logo' mod='everpsseo'}" class="everpsseo-hero__logo" />
                <div class="everpsseo-hero__text">
                    <span class="everpsseo-badge">{l s='Version' mod='everpsseo'} {$ever_configuration_overview.hero.version|escape:'htmlall':'UTF-8'}</span>
                    <h2 class="everpsseo-hero__title">{$ever_configuration_overview.hero.title|escape:'htmlall':'UTF-8'}</h2>
                    <p class="everpsseo-hero__subtitle">{$ever_configuration_overview.hero.subtitle|escape:'htmlall':'UTF-8'}</p>
                </div>
            </div>
            {if isset($ever_configuration_overview.hero.actions) && $ever_configuration_overview.hero.actions}
                <div class="everpsseo-hero__actions">
                    {foreach from=$ever_configuration_overview.hero.actions item=action}
                        <a href="{$action.href|escape:'htmlall':'UTF-8'}" class="everpsseo-btn everpsseo-btn--{if isset($action.type)}{$action.type|escape:'htmlall':'UTF-8'}{else}primary{/if}"{if isset($action.target)} target="{$action.target|escape:'htmlall':'UTF-8'}" rel="noopener"{/if}>
                            <i class="icon-play"></i>
                            {$action.label|escape:'htmlall':'UTF-8'}
                        </a>
                    {/foreach}
                </div>
            {/if}
            {if isset($ever_configuration_overview.hero.secondary_actions) && $ever_configuration_overview.hero.secondary_actions}
                <div class="everpsseo-hero__links">
                    {foreach from=$ever_configuration_overview.hero.secondary_actions item=link}
                        <a href="{$link.href|escape:'htmlall':'UTF-8'}" class="everpsseo-link"{if isset($link.target)} target="{$link.target|escape:'htmlall':'UTF-8'}" rel="noopener"{/if}>
                            {$link.label|escape:'htmlall':'UTF-8'}
                        </a>
                    {/foreach}
                </div>
            {/if}
        </div>
        {if isset($ever_configuration_overview.status_chips) && $ever_configuration_overview.status_chips}
            <aside class="everpsseo-hero__status">
                <h3 class="everpsseo-section-title">{l s='Environment status' mod='everpsseo'}</h3>
                <ul class="everpsseo-status-list">
                    {foreach from=$ever_configuration_overview.status_chips item=chip}
                        <li class="everpsseo-chip everpsseo-chip--{$chip.state|escape:'htmlall':'UTF-8'}">
                            <span class="everpsseo-chip__dot"></span>
                            <div class="everpsseo-chip__content">
                                <span class="everpsseo-chip__label">{$chip.label|escape:'htmlall':'UTF-8'}</span>
                                <small class="everpsseo-chip__message">{$chip.message|escape:'htmlall':'UTF-8'}</small>
                            </div>
                        </li>
                    {/foreach}
                </ul>
            </aside>
        {/if}
    </section>
    {if isset($ever_configuration_overview.stats_cards) && $ever_configuration_overview.stats_cards}
        <section class="everpsseo-stats">
            <h3 class="everpsseo-section-title">{l s='SEO coverage & activity' mod='everpsseo'}</h3>
            <div class="everpsseo-stats-grid">
                {foreach from=$ever_configuration_overview.stats_cards item=card}
                    <article class="everpsseo-stat-card">
                        <header class="everpsseo-stat-card__header">
                            <span class="everpsseo-stat-card__icon"><i class="{$card.icon|escape:'htmlall':'UTF-8'}"></i></span>
                            <div class="everpsseo-stat-card__meta">
                                <span class="everpsseo-stat-card__label">{$card.label|escape:'htmlall':'UTF-8'}</span>
                                <span class="everpsseo-stat-card__value">{$card.value|escape:'htmlall':'UTF-8'}</span>
                            </div>
                        </header>
                        {if isset($card.progress)}
                            <div class="everpsseo-progress" role="presentation">
                                <span class="everpsseo-progress__bar" style="width: {$card.progress.percent|escape:'htmlall':'UTF-8'}%"></span>
                            </div>
                        {/if}
                        <p class="everpsseo-stat-card__description">
                            {$card.description|escape:'htmlall':'UTF-8'}
                        </p>
                    </article>
                {/foreach}
            </div>
        </section>
    {/if}
    {if isset($ever_configuration_overview.activity) && $ever_configuration_overview.activity}
        <section class="everpsseo-activity">
            <h3 class="everpsseo-section-title">{l s='Live insights' mod='everpsseo'}</h3>
            <div class="everpsseo-activity-grid">
                {foreach from=$ever_configuration_overview.activity item=panel}
                    <article class="everpsseo-activity-card">
                        <header class="everpsseo-activity-card__header">
                            <span class="everpsseo-activity-card__icon"><i class="{$panel.icon|escape:'htmlall':'UTF-8'}"></i></span>
                            <h4 class="everpsseo-activity-card__title">{$panel.title|escape:'htmlall':'UTF-8'}</h4>
                        </header>
                        <ol class="everpsseo-activity-card__list">
                            {foreach from=$panel.items item=item}
                                <li class="everpsseo-activity-card__item">
                                    {if isset($item.url) && $item.url}
                                        <a href="{$item.url|escape:'htmlall':'UTF-8'}" target="_blank" rel="noopener" class="everpsseo-activity-card__link">
                                            {$item.label|escape:'htmlall':'UTF-8'}
                                        </a>
                                    {else}
                                        <span class="everpsseo-activity-card__link">{$item.label|escape:'htmlall':'UTF-8'}</span>
                                    {/if}
                                    <span class="everpsseo-activity-card__badge">{$item.value|escape:'htmlall':'UTF-8'}</span>
                                </li>
                            {/foreach}
                        </ol>
                    </article>
                {/foreach}
            </div>
        </section>
    {/if}
    <section class="everpsseo-resources">
        <div class="everpsseo-resources__content">
            <div class="everpsseo-resources__branding">
                <img src="{$image_dir|escape:'htmlall':'UTF-8'}/ever.png" alt="{l s='Ever SEO logo' mod='everpsseo'}" class="everpsseo-resources__logo" />
                <div>
                    <h3>{l s='Automation resources' mod='everpsseo'}</h3>
                    <p>{l s='Launch cron jobs, download templates and access your analytics dashboards instantly.' mod='everpsseo'}</p>
                </div>
            </div>
            <div class="everpsseo-resources__actions">
                <div class="everpsseo-resources__group">
                    <span class="everpsseo-resources__label">{l s='Cron shortcuts' mod='everpsseo'}</span>
                    <a href="{$everpsseo_cron|escape:'htmlall':'UTF-8'}" target="_blank" rel="noopener" class="everpsseo-link">{l s='Generate sitemaps' mod='everpsseo'}</a>
                    <a href="{$everpsseo_objects|escape:'htmlall':'UTF-8'}" target="_blank" rel="noopener" class="everpsseo-link">{l s='Refresh SEO objects' mod='everpsseo'}</a>
                </div>
                <div class="everpsseo-resources__group">
                    <span class="everpsseo-resources__label">{l s='Sample files' mod='everpsseo'}</span>
                    {if isset($categoriesFileExample) && $categoriesFileExample}
                        <a href="{$categoriesFileExample|escape:'htmlall':'UTF-8'}" target="_blank" rel="noopener" class="everpsseo-link">{l s='Categories template' mod='everpsseo'}</a>
                    {/if}
                    {if isset($productsFileExample) && $productsFileExample}
                        <a href="{$productsFileExample|escape:'htmlall':'UTF-8'}" target="_blank" rel="noopener" class="everpsseo-link">{l s='Products template' mod='everpsseo'}</a>
                    {/if}
                </div>
                {if isset($sitemaps) && $sitemaps}
                    <div class="everpsseo-resources__group">
                        <span class="everpsseo-resources__label">{l s='Sitemaps' mod='everpsseo'}</span>
                        {foreach from=$sitemaps item=sitemap}
                            {if $sitemap != '.' && $sitemap != '..' && $sitemap != 'index.php' && $sitemap != 'indexes'}
                                <a href="{$sitemap|escape:'htmlall':'UTF-8'}" target="_blank" rel="noopener" class="everpsseo-link">{$sitemap|escape:'htmlall':'UTF-8'}</a>
                            {/if}
                        {/foreach}
                    </div>
                {/if}
                {if isset($indexes) && $indexes}
                    <div class="everpsseo-resources__group">
                        <span class="everpsseo-resources__label">{l s='Sitemap indexes' mod='everpsseo'}</span>
                        {foreach from=$indexes item=indexe}
                            {if $indexe != '.' && $indexe != '..' && $indexe != 'index.php'}
                                <a href="{$indexe|escape:'htmlall':'UTF-8'}" target="_blank" rel="noopener" class="everpsseo-link">{$indexe|escape:'htmlall':'UTF-8'}</a>
                            {/if}
                        {/foreach}
                    </div>
                {/if}
                <div class="everpsseo-resources__group">
                    <span class="everpsseo-resources__label">{l s='Analytics hubs' mod='everpsseo'}</span>
                    <a href="https://search.google.com/search-console/users?resource_id={$searchconsole|escape:'htmlall':'UTF-8'}" target="_blank" rel="noopener" class="everpsseo-link">{l s='Google Search Console' mod='everpsseo'}</a>
                    <a href="https://analytics.google.com/analytics/web/" target="_blank" rel="noopener" class="everpsseo-link">{l s='Google Analytics' mod='everpsseo'}</a>
                    <a href="https://www.bing.com/webmaster/home/dashboard" target="_blank" rel="noopener" class="everpsseo-link">{l s='Bing Webmaster Tools' mod='everpsseo'}</a>
                </div>
            </div>
        </div>
    </section>
</div>
