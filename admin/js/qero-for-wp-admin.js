function toggleError(elm){
    elm.addClass('invalidjQuery');
    setTimeout(function () {
        elm.removeClass('invalidjQuery');
    },2000);
}

function toogleDisable(elm, on = false){
    elm.prop( "disabled", !on );
}