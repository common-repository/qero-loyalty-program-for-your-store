jQuery(document).ready(function() {
    (function ($) {
        $("#wpbody-content").children().each(function(a,c){
            var item = $(c);
            if(!item.hasClass( "qero-clean" ))
                item.hide();
        });
    })(jQuery);
});