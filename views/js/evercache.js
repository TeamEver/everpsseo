/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link http://team-ever.com
 */
 $(document).ready(function() {
    console.log('ever cache : cart refresh enabled');
    setTimeout("prestashop.emit('updateCart', {reason: {linkAction: 'refresh'}, resp: {}});", 10);
});
