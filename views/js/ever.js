/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link https://team-ever.com
 */
function everpsseoInitConfigurationNavigation() {
    var container = document.querySelector('.everpsseo-config-wrapper');
    if (!container) {
        return;
    }

    var navDataElement = document.getElementById('everpsseo-nav-sections-data');
    if (!navDataElement) {
        return;
    }

    var navMap;
    try {
        navMap = JSON.parse(navDataElement.textContent || navDataElement.innerHTML || '[]');
    } catch (error) {
        navMap = [];
    }

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
            target.scrollIntoView(true);
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
            scrollToAnchor(anchor, false);
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
}

$(document).ready(function() {
    // GTranslate switch
    $('label[for=EVERSEO_GTOP_on]').click(function(){
        $('label[for=EVERSEO_GCOLUMN_off]').trigger('click');
    });
    $('label[for=EVERSEO_GCOLUMN_on]').click(function(){
        $('label[for=EVERSEO_GTOP_off]').trigger('click');
    });

    everpsseoInitConfigurationNavigation();
});
