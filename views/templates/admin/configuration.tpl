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
<script>
    (function () {
        var container = document.querySelector('.everpsseo-config-wrapper');
        if (!container) {
            return;
        }
        var navMap = {$nav_sections_json nofilter};
        if (!Array.isArray(navMap)) {
            navMap = [];
        }
        var panelElements = container.querySelectorAll('.everpsseo-form-container .panel, .everpsseo-form-container .card');
        Array.prototype.forEach.call(panelElements, function (panel) {
            var heading = panel.querySelector('.panel-heading, .card-header');
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
        function findTarget(anchor) {
            if (!anchor) {
                return null;
            }
            var selectorTarget = container.querySelector('[data-ever-anchor="' + anchor + '"]');
            if (selectorTarget) {
                return selectorTarget;
            }
            return document.getElementById(anchor);
        }

        function highlightTarget(target) {
            if (!target) {
                return;
            }
            target.classList.add('everpsseo-panel-highlight');
            setTimeout(function () {
                target.classList.remove('everpsseo-panel-highlight');
            }, 2000);
        }

        function scrollToAnchor(anchor, updateHash) {
            if (typeof updateHash === 'undefined') {
                updateHash = true;
            }
            var target = findTarget(anchor);
            if (!target) {
                return;
            }
            if (typeof target.scrollIntoView === 'function') {
                try {
                    target.scrollIntoView({ldelim}behavior: 'smooth', block: 'start'{rdelim});
                } catch (e) {
                    target.scrollIntoView(true);
                }
            } else if (target.offsetTop !== undefined) {
                window.scrollTo(0, target.offsetTop);
            }
            highlightTarget(target);
            if (updateHash) {
                if (history.replaceState) {
                    history.replaceState(null, '', '#' + anchor);
                } else {
                    window.location.hash = anchor;
                }
            }
        }

        var anchorLinks = container.querySelectorAll('.everpsseo-anchor');
        Array.prototype.forEach.call(anchorLinks, function (link) {
            link.addEventListener('click', function (event) {
                var anchor = this.getAttribute('data-ever-anchor');
                if (!anchor) {
                    return;
                }
                event.preventDefault();
                scrollToAnchor(anchor);
            });
        });

        if (window.location.hash) {
            scrollToAnchor(window.location.hash.replace(/^#/, ''), false);
        }

        var searchInput = document.getElementById('everpsseo-nav-search');
        if (searchInput) {
            var searchableCollections = container.querySelectorAll('[data-ever-keywords]');
            searchInput.addEventListener('input', function () {
                var query = (this.value || '').toLowerCase();
                Array.prototype.forEach.call(searchableCollections, function (element) {
                    var keywords = (element.getAttribute('data-ever-keywords') || '').toLowerCase();
                    if (!query) {
                        element.classList.remove('everpsseo-is-hidden');
                        return;
                    }
                    if (keywords.indexOf(query) !== -1) {
                        element.classList.remove('everpsseo-is-hidden');
                    } else {
                        element.classList.add('everpsseo-is-hidden');
                    }
                });
            });
        }
    })();
</script>
{/if}
