(function( $ ) {

    var qero_infos_container        = $('#qero_infos_container');
    var movements_loader            = $('#movements_loader');
    var info_loader                 = $('#info_loader');
    var qero_movement_table         = $('#qero-movement-table');
    var page_number                 = $('.qero-pagination a');
    var reqPage;

    page_number.on('click', function (event) {
        event.preventDefault();
        var id = $(this).attr('id');
        if(id == "qero_before_page"){
            var next_page = parseInt(page_number.filter('.active').text());
            next_page -=1;
            if($('#qero_page_'+next_page).show().length == 0){
                return;
            }
            page_number.removeClass('active');
            $('#qero_page_'+next_page).addClass('active');
            populateMovements(next_page);

            return;
        }else if(id == "qero_after_page"){
            var next_page = parseInt(page_number.filter('.active').text());
            next_page +=1;
            if($('#qero_page_'+next_page).show().length == 0){
                return;
            }
            page_number.removeClass('active');
            $('#qero_page_'+next_page).addClass('active');
            populateMovements(next_page);

            return;
        }
        page_number.removeClass('active');
        $(this).addClass('active');
        populateMovements(this.innerText);
    });

    function populateInfos(){
        info_loader.show();
        $.get(qero_ajax_get_infos.ajax_url).done(function(data){
            info_loader.hide();

            if(typeof data == "string")
                data = JSON.parse(data).data;
            else
                data = data.data;

            if(typeof data.ERROR != "undefined"){
                showErrorMessage('infos',data.ERROR);
                return;
            }

            if(typeof data.data != "undefined"){
                qero_infos_container.html('<br><p>Points: '+data.data.available+'</p><p>On-Hold Points: '+(data.data.points-data.data.available)+'</p><p>Total Earned: '+data.data.total_gain+'</p>');//TODO:fix biqueirada
                return;
            }

        });
    }

    function populateMovements(page = 1){
        movements_loader.show();
        if(typeof reqPage != "undefined")
            reqPage.abort();
        reqPage = $.get(qero_ajax_get_movements.ajax_url+'&page='+page).done(function(data){
            movements_loader.hide();
            qero_movement_table.find('tbody').find('tr').remove();
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
                    populateSingleMovement(index, elm);
                });
                qero_movement_table.find('tbody').show(400);
            }


        });
    }

    function populateSingleMovement(id, mov){
        var tclass = 'qero-border-table';
        if(mov.type == 'BUY' || mov.type=='MANUAL'){
            tclass += ' qero-movemnt-more';
        }else if(mov.type == 'RETURN' || mov.type == 'EXPIRE'){
            tclass += ' qero-movemnt-less';
        }else{
            tclass += ' qero-movemnt-wait';
        }
        qero_movement_table.find('tbody').append('<tr class="'+tclass+'"><td>'+mov.amount_gross+'</td><td>'+mov.credit_in+'</td><td>'+mov.credit_out+'</td><td>'+mov.date+'</td></tr>');
        //qero_movements_container.text(qero_movements_container.text()+JSON.stringify(mov)+'<br>');
    }

/*
    function populatePages(){
        $.get(qero_ajax_get_movements_count.ajax_url).done(function(data){
            if(typeof data == "string")
                data = JSON.parse(data).data;
            else
                data = data.data;

            if(typeof data.ERROR != "undefined"){
                showErrorMessage('moviments',data.ERROR);
                return;
            }

            qero_pagination.append('<a href="#">&laquo;</a>');
            var pages = data.data;
            for(var i = 1; i <= pages;i++){
                qero_pagination.append('<a href="#" '+ ((i==1)?'class="active"':'') +'>'+i+'</a>');
            }

            qero_pagination.append('<a href="#">&raquo;</a>');
            qero_pagination.show(400);
        });
    }
*/

    populateMovements();
    populateInfos();
})( jQuery );