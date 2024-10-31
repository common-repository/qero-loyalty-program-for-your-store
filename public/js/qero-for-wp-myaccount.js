(function( $ ) {
    var loader                  = $('#check_cellphone_loader');
    var button_cellphone        = $('#submit_cellphone');
    var qero_cellphone          = $('#qero_cellphone');
    var first_step              = $('#first_step');
    var campaigns_loader        = $('#campaigns_loader');
    var qero_campaign_table     = $('#qero-campaign-table');
    var selected                = $('#qero_cellphone_indicative');


    ///--
    var second_step             = $('#second_step');
    var submit_cellphone_second = $('#submit_cellphone_second');
    var check_form_loader       = $('#check_form_loader');
    var qero_name               = $('#qero_name');
    var qero_email              = $('#qero_email');
    var qero_cellphone_second   = $('#qero_cellphone_second');

    button_cellphone.on('click', function () {
        checkNumber();
    });

    submit_cellphone_second.on('click', function () {
        sendLoyalForm();
    });

    qero_cellphone.keypress(function( event ) {
        if ( event.which == 13 ) {
            event.preventDefault();
            checkNumber();
        }
    });

    function checkNumber(){
        var nonce                   = qero_ajax_my_account_cellphone.ajax_nonce;
        var url                     = qero_ajax_my_account_cellphone.ajax_url;

        toogleDisable(button_cellphone);
        toogleDisable(qero_cellphone);
        toogleDisable(selected);

        var cellphone = selected.val()+qero_cellphone.val();
        var regex = new RegExp(selected.find(":selected").attr('qeroregex').replace('//','/'));

        if(qero_cellphone.val() == '' || selected.val() == '' || regex.test(cellphone) === false){
            showErrorMessage('first_step','Invalid number format!');
            setTimeout(function () {
                toogleDisable(button_cellphone,true);
                toogleDisable(qero_cellphone,true);
                toogleDisable(selected, true);
            },2500);
            return;
        }

        loader.show();

        var data = {
            security: nonce,
            action: 'qero_my_account',
            cellphone: cellphone
        };

        $.post( url, data)
            .done(function( data ) {
                loader.hide();

                if(typeof data == "string")
                    data = JSON.parse(data).data;
                else
                    data = data.data;

                if(typeof data.ERROR != "undefined"){
                    showErrorMessage('first_step',data.ERROR);
                    setTimeout(function () {
                        location.reload();
                    },2500);
                    return;
                }
                if(typeof data.SHOW != "undefined" && data.SHOW == 'second_step'){
                    showSecondStep(cellphone);
                    return;
                }
                if(typeof data.data != "undefined" && data.data == true){
                    check();
                    location.reload();
                    return;
                }


                return;
            });


    }

    function sendLoyalForm(){
        var nonce                   = qero_ajax_my_account_new_account.ajax_nonce;
        var url                     = qero_ajax_my_account_new_account.ajax_url;

        toogleDisable(qero_name);
        toogleDisable(submit_cellphone_second);

        if(/^([ a-zA-Z\u00C0-\u017F]{3,})$/.test(qero_name.val()) === false){

            showErrorMessage('second_step','Invalid Name!');
            setTimeout(function () {
                toogleDisable(qero_name,true);
                toogleDisable(submit_cellphone_second, true);
            },2500);
            return;
        }


        check_form_loader.show();

        var data = {
            security: nonce,
            action: 'qero_my_account_new_qero',
            cellphone: qero_cellphone_second.val(),
            email: qero_email.val(),
            name: qero_name.val(),
        };

        $.post( url, data).done(function( data ) {
            check_form_loader.hide();

            if(typeof data == "string")
                data = JSON.parse(data).data;
            else
                data = data.data;

            if(typeof data.data != "undefined" && data.data == true){
                check();
                location.reload();
                return;
            }
            if(typeof data.ERROR != "undefined"){
                showErrorMessage('second_step',data.ERROR);
                setTimeout(function () {
                    toogleDisable(qero_name,true);
                    toogleDisable(submit_cellphone_second, true);
                },2500);
                return;
            }
        });

    }

    function check(){

    }

    function showSecondStep(cellphone){
        var i = 500;
        qero_cellphone_second.val(cellphone);
        first_step.hide(i);
        setTimeout(function () {
           second_step.show(i);
        },i)
    }

    function populateCampaigns(){
        campaigns_loader.show();

        $.get(qero_ajax_get_campaigns.ajax_url).done(function(data){
            campaigns_loader.hide();
            qero_campaign_table.find('tbody').find('tr').remove();
            if(typeof data == "string")
                data = JSON.parse(data).data;
            else
                data = data.data;

            if(typeof data.ERROR != "undefined"){
                showErrorMessage('moviments',data.ERROR);
                return;
            }
            if(typeof data.data != "undefined" && Array.isArray(data.data)){
                data.data.forEach(function (elm, index) {
                    populateSingleCampaign(index, elm);
                });
                qero_campaign_table.find('tbody').show(400);
            }


        });
    }

    function populateSingleCampaign(id, mov){
        var tclass = 'qero-movemnt-wait';
        qero_campaign_table.find('tbody').append('<tr class="'+tclass+'"><td>'+mov.end+'</td><td>'+mov.title+'</td><td>'+mov.description+'</td></tr>');
        //qero_movements_container.text(qero_movements_container.text()+JSON.stringify(mov)+'<br>');
    }
    populateCampaigns();

})( jQuery );
