/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link https://team-ever.com
 */

$(document).ready(function(){
    $('form').submit(function() {
        grecaptcha.ready(function() {
          grecaptcha.execute(googlecaptchasitekey, {
            action: 'e-commerce'
          });
        });
    });
});
