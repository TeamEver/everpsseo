/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link http://team-ever.com
 */
 $(document).ready(function(){
    $(document).bind("contextmenu",function(e){
        e.preventDefault();
    });
    $(document).bind("keydown",function(e){
        e = e || window.event;//Get event
        if (e.ctrlKey || e.metaKey) {
            var c = e.which || e.keyCode || e.metaKey;//Get key code
            switch (c) {
                case 17://Block Ctrl
                case 65://Block Ctrl+A
                case 67://Block Ctrl+C
                case 80://Block Ctrl+P
                case 83://Block Ctrl+S
                case 87://Block Ctrl+W --Not work in Chrome
                    e.preventDefault();
                    e.stopPropagation();
                break;
            }
        }
    });
});
