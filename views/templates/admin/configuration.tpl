{*
* Project : everpsseo
* Enhanced configuration layout
*}
<div class="everpsseo-config-wrapper">
    {$ever_header nofilter}
    <div class="everpsseo-config-grid">
        <aside class="everpsseo-config-sidebar">
            {if !empty($quick_actions)}
                <div class="everpsseo-card-collection">
                    {foreach $quick_actions as $action}
                        <article class="everpsseo-card">
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
                                        <li>
                                            <a href="#" class="everpsseo-anchor" data-ever-anchor="{$link.anchor|escape:'htmlall':'UTF-8'}">
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
                            <li>
                                <a href="#" class="everpsseo-anchor" data-ever-anchor="{$section.anchor|escape:'htmlall':'UTF-8'}">
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
<script>
    (function () {
        var container = document.querySelector('.everpsseo-config-wrapper');
        if (!container) {
            return;
        }
        var navMap = {$nav_sections_json nofilter};
        if (!Array.isArray(navMap)) {
            return;
        }
        var panelElements = container.querySelectorAll('.everpsseo-form-container .panel');
        Array.prototype.forEach.call(panelElements, function (panel) {
            var heading = panel.querySelector('.panel-heading');
            if (!heading) {
                return;
            }
            var rawTitle = heading.textContent || '';
            var title = rawTitle.replace(/\s+/g, ' ').trim();
            var matched;
            for (var i = 0; i < navMap.length; i += 1) {
                if (navMap[i].title === title) {
                    matched = navMap[i];
                    break;
                }
            }
            if (!matched) {
                return;
            }
            panel.setAttribute('data-ever-anchor', matched.anchor);
            if (!panel.id) {
                panel.id = matched.anchor;
            }
            heading.setAttribute('id', matched.anchor + '-heading');
        });
        var anchorLinks = container.querySelectorAll('.everpsseo-anchor');
        Array.prototype.forEach.call(anchorLinks, function (link) {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                var anchor = this.getAttribute('data-ever-anchor');
                if (!anchor) {
                    return;
                }
                var target = container.querySelector('[data-ever-anchor="' + anchor + '"]');
                if (target) {
                    if (typeof target.scrollIntoView === 'function') {
                        try {
                            target.scrollIntoView({ldelim}behavior: 'smooth', block: 'start'{rdelim});
                        } catch (e) {
                            target.scrollIntoView(true);
                        }
                    }
                    target.classList.add('everpsseo-panel-highlight');
                    setTimeout(function () {
                        target.classList.remove('everpsseo-panel-highlight');
                    }, 2000);
                }
            });
        });
    })();
</script>
{/if}
