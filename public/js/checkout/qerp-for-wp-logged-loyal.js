(function ($) {

    var textbox = $('#qero_points_input');
    var button  = $('#submit_qero_points');
    var loader  = $('#qero_loader_points');
    var max     = $('#max_to_spend');
    var icon    = $('#qero_button_icon');

    button.on('click', function () {
        if(textbox.is(':disabled')){
            removePoints()
        }else{
            spendPoints();
        }
    });

    textbox.keypress(function( event ) {
        if ( event.which == 13 ) {
            event.preventDefault();
            spendPoints();
        }
    });

    function spendPoints() {
        if(/^([0-9]+[,.]?[0-9]{0,2})$/.test(textbox.val()) === false){
            showErrorMessage('qero_points','Invalid format!');
            return;
        }
        loader.show();
        toogleDisable(button);
        toogleDisable(textbox);

        var data = {
            security:qero_ajax_add_points_discount.ajax_nonce,
            action:  'add_points_discount',
            points:  textbox.val(),
        };

        $.post(qero_ajax_add_points_discount.ajax_url,data).done(function (data) {
            loader.hide();
            toogleDisable(button, true);
            toogleDisable(textbox,true);

            if(typeof data == "string")
                data = JSON.parse(data).data;
            else
                data = data.data;

            if(typeof data.ERROR != "undefined"){
                showErrorMessage('qero_points',data.ERROR);
                return;
            }

            if(typeof data.data != "undefined"){
                triggerUpdate();
                return;
            }
        });
    }

    function populatePoints(){
        toogleDisable(button);
        toogleDisable(textbox);

        loader.show();
        setMaxPoints('');
        setPoints('');
        $.get(qero_ajax_get_infos.ajax_url).done(function(data){
            loader.hide();

            if(typeof data == "string")
                data = JSON.parse(data).data;
            else
                data = data.data;

            if(typeof data.ERROR != "undefined"){
                showErrorMessage('qero_points',data.ERROR);
                return;
            }

            if(typeof data.data != "undefined"){
                toogleDisable(button, true);
                toogleDisable(textbox, true);
                setPoints(data.data.available);
                setMaxPoints(data.data.max_points);
                setOnHold(data.data.points_on_hold);
                return;
            }

        });
    }

    function setPoints($points){
        $('#qero-points-table').find('tbody').find('tr').eq(0).find('td').eq(1).text($points);
    }

    function setMaxPoints($num){
        max.text(max.text().split(':')[0]+': '+$num);
    }

    function setOnHold($num) {
        if(typeof $num == "undefined"){
            toogleDisable(textbox,true);
            textbox.text('');
            return;
        }
        textbox.val($num);
        button.find('span').show();
        button.find('img').hide();
        toogleDisable(textbox);
    }

    function triggerUpdate() {
        populatePoints();
        $( 'body' ).trigger( 'update_checkout' );
    }

    function removePoints(){
        loader.show();

        var data = {
            security:qero_ajax_remove_points_discount.ajax_nonce,
            action:  'remove_points_discount',
        };

        $.post(qero_ajax_remove_points_discount.ajax_url,data).done(function (data) {
            loader.hide();
            //icon.text('');
            button.find('img').show();
            button.find('span').hide();
            triggerUpdate();
        });
    }

    populatePoints();

})(jQuery);