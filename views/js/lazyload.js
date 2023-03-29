/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link https://team-ever.com
 */
if (typeof everlazy_exceptions !== 'undefined') {
    var lazyexceptions = everlazy_exceptions;
} else {
    var lazyexceptions = '#carousel img, #slider img';
}
$('img[loading="lazy"]:not(' + lazyexceptions + ')').unveil().addClass('everseo-lazy');