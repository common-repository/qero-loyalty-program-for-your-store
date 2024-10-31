(function ($) {
    var qero_cellphone          = $('#qero_cellphone');
    var loader                  = $('#check_cellphone_loader');
    var foot_submit_cellphones  = $('#foot_submit_cellphones');
    var selected                = $('#qero_cellphone_indicative');
    var gain_form               = $('#gain_points_form');
    var title_gain              = $('#qero_table_title_gain');

    var convert_form            = $('#convert_points_form');
    var cellphone_signup        = $('#qero_cellphone_signup');
    var name_signup             = $('#qero_name_signup');
    var email_signup            = $('#qero_email_signup');
    var title_convert           = $('#qero_table_title_convert');
    var qero_back_form          = $('#qero_back_form');

    var qero_cellphone_lock     = $('#qero_cellphone_lock');

    var gain_points_lock        = $('#gain_points_lock');

    var submit_cellphones       = $('#submit_cellphones');
    var forget_cellphone        = $('#forget_cellphone_anon');

    var anim = 300;

    qero_cellphone.keypress(function( event ) {
        if ( event.which == 13 ) {
            event.preventDefault();
            checkNumber();
        }
    });

    qero_back_form.on('click', function () {
        if(!loader.is(':visible')){
            showForm(1);
        }
    });

    forget_cellphone.on('click', function(){

        if(loader.is(':visible')){
            return false;
        }

        var nonce   = qero_ajax_checkout_forget_qero_anon.ajax_nonce;
        var url     = qero_ajax_checkout_forget_qero_anon.ajax_url;

        loader.show();

        var data = {
            security:   nonce,
            action:     'qero_checkout_forget_cellphone_anon',
        };

        $.post( url, data)
            .done(function( data ) {
                loader.hide();

                if(typeof data == "string")
                    data = JSON.parse(data).data;
                else
                    data = data.data;

                if(typeof data.data != "undefined" && data.data == true){
                    qero_cellphone_lock.val('');
                    showForm(1);
                    triggerReload();
                    return;
                }
                else if(typeof data.ERROR != "undefined"){
                    showErrorMessage('cellphone', data.ERROR);
                    return;
                }
            });
    });

    submit_cellphones.on('click', function () {
        if(gain_form.is(':visible')){
            checkNumber();
        }else{
            becomeLoyal();
        }
    });

    function becomeLoyal(){
        var nonce   = qero_ajax_checkout_new_qero.ajax_nonce;
        var url     = qero_ajax_checkout_new_qero.ajax_url;

        toogleDisable(name_signup);
        toogleDisable(email_signup);
        toogleDisable(cellphone_signup);
        toogleDisable(submit_cellphones);
        loader.show();

        var data = {
            security:   nonce,
            action:     'qero_checkout_new_qero',
            cellphone:  cellphone_signup.val(),
            email:      email_signup.val(),
            name:       name_signup.val(),
        };

        $.post( url, data)
            .done(function( data ) {
                loader.hide();

                if(typeof data == "string")
                    data = JSON.parse(data).data;
                else
                    data = data.data;

                if(typeof data.data != "undefined" && data.data == true){
                    showForm(3, cellphone_signup.val());
                    triggerReload();
                    return;
                }
                else if(typeof data.ERROR != "undefined"){
                    showErrorMessage('cellphone', data.ERROR);
                    return;
                }
            });


    }

    function checkNumber() {
        var nonce   = qero_ajax_checkout_not_logged_cellphone.ajax_nonce;
        var url     = qero_ajax_checkout_not_logged_cellphone.ajax_url;

        toogleDisable(submit_cellphones);
        toogleDisable(qero_cellphone);
        toogleDisable(selected);

        var cellphone = selected.val()+qero_cellphone.val();
        var regex = new RegExp(selected.find(":selected").attr('qeroregex').replace('//','/'));

        if(qero_cellphone.val() == '' || selected.val() == '' || regex.test(cellphone) === false){
            showErrorMessage('cellphone','Invalid number format!');
            setTimeout(function () {
                toogleDisable(submit_cellphones,true);
                toogleDisable(qero_cellphone,true);
                toogleDisable(selected, true);
            },2500);
            return;
        }

        loader.show();

        var data = {
            security: nonce,
            action: 'qero_checkout_valid_cellphone',
            cellphone: cellphone
        };

        $.post( url, data)
            .done(function( data ) {
                loader.hide();

                if(typeof data == "string")
                    data = JSON.parse(data).data;
                else
                    data = data.data;

                if(typeof data.data != "undefined" && data.data == true){
                    foot_submit_cellphones.hide(anim);
                    showForm(3,cellphone);
                    triggerReload();
                    return;
                }
                else {//TODO: prop signup
                    showForm(2,cellphone);
                    return;
                }
            });


    }

    function showForm(id = 1, cellphone = ''){
        switch (id) {
            case 1:
                qero_cellphone_lock.val(cellphone);
                cellphone_signup.val(cellphone);
                gain_form.show(anim);
                convert_form.hide(anim);
                title_gain.show(anim);
                title_convert.hide(anim);
                gain_points_lock.hide(anim);
                submit_cellphones.show(anim);
                toogleDisable(name_signup, true);
                toogleDisable(email_signup, true);
                toogleDisable(cellphone_signup, true);
                toogleDisable(submit_cellphones, true);
                break;
            case 2:
                qero_cellphone_lock.val(cellphone);
                cellphone_signup.val(cellphone);
                gain_form.hide(anim);
                convert_form.show(anim);
                title_gain.hide(anim);
                title_convert.show(anim);
                gain_points_lock.hide(anim);
                toogleDisable(submit_cellphones,true);
                toogleDisable(qero_cellphone,true);
                toogleDisable(selected, true);
                break;
            case 3:
                qero_cellphone_lock.val(cellphone);
                cellphone_signup.val(cellphone);
                gain_form.hide(anim);
                convert_form.hide(anim);
                title_gain.show(anim);
                title_convert.hide(anim);
                gain_points_lock.show(anim);
                foot_submit_cellphones.hide(anim);
                toogleDisable(qero_cellphone,true);
                toogleDisable(selected, true);
                toogleDisable(name_signup, true);
                toogleDisable(email_signup, true);
                toogleDisable(cellphone_signup, true);
                toogleDisable(submit_cellphones, true);
                break;
            default:
                break;

        }
    }

    function triggerReload(){
        $( 'body' ).trigger( 'update_checkout' );
    }

})(jQuery);