/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link https://team-ever.com
 */
 $(document).ready(function() {
    // GTranslate switch
    $('label[for=EVERSEO_GTOP_on]').click(function(){
        $('label[for=EVERSEO_GCOLUMN_off]').trigger('click');
    });
    $('label[for=EVERSEO_GCOLUMN_on]').click(function(){
        $('label[for=EVERSEO_GTOP_off]').trigger('click');
    });
});
