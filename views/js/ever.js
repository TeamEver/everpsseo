/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link https://team-ever.com
 */
 $(document).ready(function() {
    $('.adminmodules #module_form').addClass('row');
    $('.adminmodules #content.bootstrap #module_form .panel .form-wrapper, .adminmodules #content.bootstrap #module_form .panel .panel-footer').hide();
    $('.adminmodules #content.bootstrap #module_form .panel').css({
       'float' : 'left',
       'width' : '50%',
    });
    $('.adminmodules #content.bootstrap #module_form .panel-heading').click(function() {
        if ($('.form-wrapper, .panel-footer').is(':visible')) {
            $('.form-wrapper, .panel-footer').parent().animate({'width': '50%'}, 200);
            $('.form-wrapper, .panel-footer').slideUp();
        }
        if ($(this).parent().find('.form-wrapper, .panel-footer').is(':visible')) {
            $(this).parent().animate({'width': '50%'}, 200);
            $(this).parent().find('.form-wrapper, .panel-footer').slideUp();
        } else {
            $(this).parent().animate({'width': '100%'}, 200);
            $(this).parent().find('.form-wrapper, .panel-footer').slideDown();
        }
    });
    // GTranslate switch
    $('label[for=EVERSEO_GTOP_on]').click(function(){
        $('label[for=EVERSEO_GCOLUMN_off]').trigger('click');
    });
    $('label[for=EVERSEO_GCOLUMN_on]').click(function(){
        $('label[for=EVERSEO_GTOP_off]').trigger('click');
    });
});
