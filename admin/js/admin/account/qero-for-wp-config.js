(function( $ ) {
    var nonce               = qero_ajax_object.ajax_nonce;
    var url                 = qero_ajax_object.ajax_url;
    var submitButton        = $('#qero_login');
    var qero_app_name       = $('#qero_app_name');
    //var loader_apps         = $('#loader_apps');
    var qero_new_app_button = $('#qero_new_app_button');
    var qero_new_app_name   = $('#qero_new_app_name');
    var loader_new_app      = $('#loader_new_app');

    var loader_force_invite = $('#loader_force_invite');
    var qero_confirmed_force_invite = $('#qero_confirmed_force_invite');

    qero_new_app_button.on('click', function (event) {
        event.preventDefault();
        if(qero_new_app_name.val() == ''){
            toggleError(qero_new_app_name);
            return;
        }
        loader_new_app.show();
        toogleDisable(qero_new_app_button);
        toogleDisable(qero_new_app_name);
        var data={
            security: nonce,
            action: 'qero_config_new_app',
            app_name: qero_new_app_name.val()
        };

        $.post( url, data)
            .done(function( data ) {
                loader_new_app.hide();
                if(typeof data.data.ERROR != "undefined"){
                    alert(data.data.ERROR);
                    toogleDisable(qero_new_app_button,true);
                    toogleDisable(qero_new_app_name,true);
                    submitButton.prop( "disabled", false );
                    return;
                }
                window.location.reload();
                return;
            });
    });

    submitButton.on('click', function () {
        if(qero_app_name.val() == '' || qero_app_name.val() == 0){
            toggleError(qero_app_name);
            return;
        }
        loader_new_app.show();
        submitButton.prop( "disabled", true );
        var data={
            security: nonce,
            action: 'qero_config',
            app_id: qero_app_name.val()//TODO:VALIDATION
        };

        $.post( url, data)
            .done(function( data ) {
                loader_new_app.hide();
                if(typeof data.data.ERROR != "undefined"){
                    alert(data.data.ERROR);
                    submitButton.prop( "disabled", false );
                    return;
                }
                window.location.reload();
                return;
            });
    });

    qero_confirmed_force_invite.on('click', function(){
        qero_confirmed_force_invite.prop( "disabled", true );
        var data={
            security: nonce,
            action: 'qero_force_invite_all',
        };
        loader_force_invite.show();
        $.post( url, data)
            .done(function( data ) {
                loader_force_invite.hide();

                if(data.success !== true){
                    alert(data.data);
                    return;
                }

                $('#open_modal_button').prop( "disabled", true );
                $('#qero_confirm_send_invite_modal').modal('hide');
                return;
            });
    });
})( jQuery );
