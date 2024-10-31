function showErrorMessage(id, string){
	var elm = jQuery('#error_'+id);
	elm.find( "span" ).text(string);
	toogleShowTimmer(elm, 2000);
}

function toogleShowTimmer(elm, tick){
	elm.show(500);
	setTimeout(function () {
		elm.hide(500);
	},tick);
}

function toogleDisable(elm, on = false){
	elm.prop( "disabled", !on );
}