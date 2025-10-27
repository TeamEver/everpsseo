{*
* Project : everpsseo
* Enhanced configuration layout
*}
<div class="everpsseo-config-wrapper">
    {$ever_header nofilter}
    <div class="everpsseo-config-grid">
        <aside class="everpsseo-config-sidebar">
            <div class="everpsseo-sidebar-block everpsseo-sidebar-block--search">
                <label class="everpsseo-sidebar-label" for="everpsseo-nav-search">
                    <i class="icon-search"></i>
                    {l s='Quick search' mod='everpsseo'}
                </label>
                <input
                    type="search"
                    id="everpsseo-nav-search"
                    class="everpsseo-sidebar-search"
                    placeholder="{l s='Find a configuration section…' mod='everpsseo'}"
                    autocomplete="off"
                />
                <p class="everpsseo-sidebar-hint">
                    {l s='Filter the shortcuts and navigation to jump directly to the right panel.' mod='everpsseo'}
                </p>
            </div>
            {if !empty($quick_actions)}
                <div class="everpsseo-card-collection">
                    {foreach $quick_actions as $action}
                        {assign var=cardKeywords value=$action.title|cat:' '|cat:$action.description}
                        {if !empty($action.links)}
                            {foreach $action.links as $link}
                                {assign var=cardKeywords value=$cardKeywords|cat:' '|cat:$link.label}
                            {/foreach}
                        {/if}
                        <article class="everpsseo-card" data-ever-keywords="{$cardKeywords|escape:'htmlall':'UTF-8'}">
                            <header class="everpsseo-card__header">
                                <span class="everpsseo-card__icon"><i class="{$action.icon|escape:'htmlall':'UTF-8'}"></i></span>
                                <h3 class="everpsseo-card__title">{$action.title|escape:'htmlall':'UTF-8'}</h3>
                            </header>
                            <p class="everpsseo-card__description">
                                {$action.description|escape:'htmlall':'UTF-8'}
                            </p>
                            {if !empty($action.links)}
                                <ul class="everpsseo-card__links">
                                    {foreach $action.links as $link}
                                        <li data-ever-keywords="{$link.label|escape:'htmlall':'UTF-8'}">
                                            <a href="#{$link.anchor|escape:'htmlall':'UTF-8'}" class="everpsseo-anchor" data-ever-anchor="{$link.anchor|escape:'htmlall':'UTF-8'}">
                                                {$link.label|escape:'htmlall':'UTF-8'}
                                            </a>
                                        </li>
                                    {/foreach}
                                </ul>
                            {/if}
                        </article>
                    {/foreach}
                </div>
            {/if}
            {if !empty($nav_sections)}
                <div class="everpsseo-card everpsseo-card--nav">
                    <header class="everpsseo-card__header">
                        <span class="everpsseo-card__icon"><i class="icon-list-ul"></i></span>
                        <h3 class="everpsseo-card__title">{l s='All configuration sections' mod='everpsseo'}</h3>
                    </header>
                    <ul class="everpsseo-card__links everpsseo-nav">
                        {foreach $nav_sections as $section}
                            <li data-ever-keywords="{$section.title|escape:'htmlall':'UTF-8'}">
                                <a href="#{$section.anchor|escape:'htmlall':'UTF-8'}" class="everpsseo-anchor" data-ever-anchor="{$section.anchor|escape:'htmlall':'UTF-8'}">
                                    {$section.title|escape:'htmlall':'UTF-8'}
                                </a>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            {/if}
        </aside>
        <div class="everpsseo-config-content">
            {if !empty($ever_success)}{$ever_success nofilter}{/if}
            {if !empty($ever_errors)}{$ever_errors nofilter}{/if}
            <div class="everpsseo-form-container">
                {$ever_form nofilter}
            </div>
        </div>
    </div>
    {$ever_footer nofilter}
</div>
{if !empty($nav_sections_json)}
    <script type="application/json" id="everpsseo-nav-sections-data">{$nav_sections_json nofilter}</script>
{/if}
