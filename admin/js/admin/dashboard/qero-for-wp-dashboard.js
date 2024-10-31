
(function( $ ) {
    var ctxSales = document.getElementById('ctxSales').getContext('2d');
    var ctxClientCreation = document.getElementById('ctxClientCreation').getContext('2d');

    var mixedChart = [];

    var nonce                   = qero_ajax_object.ajax_nonce;
    var url                     = qero_ajax_object.ajax_url;

    var sales_loader            = $('#sales_loader');
    var clients_creation_loader = $('#clients_creation_loader');
    var campaigns_loader        = $('#campaigns_loader');
    var clients_loader          = $('#clients_loader');

    var message_campaigns       = $('#qero-campaigns-message');
    var table_campaigns         = $('#qero-dashboard-campaigns');
    var message_clients         = $('#qero-clients-message');
    var table_clients           = $('#qero-dashboard-clients');

    var pagination_campaigns    = $('#campaigns_pages');
    var pagination_clients      = $('#clients_pages');

    var reqCampaigns;
    var reqClients;


    var anim = 300;

    function init(){
        loadDashboardSales();
        loadDashboardClientCreation();
        loadDashboardClientSales();
        loadDashboardCampaigns();
        printPages(pagination_campaigns, loadDashboardCampaigns);
        printPages(pagination_clients, loadDashboardClientSales);
    }

    function loadDashboardClientSales(page = 1){
        clients_loader.show();
        var data={
            security: nonce,
            action: 'qero_dashboard_client_sales',
            page: page
        };

        if(typeof reqClients != "undefined")
            reqClients.abort();

        reqClients = $.post( url, data)
            .done(function( data ) {

                if(typeof data == "string")
                    data = JSON.parse(data);

                clients_loader.hide();

                populateTable(table_clients, data.data.data, message_clients);
                return;
            });
    }

    function loadDashboardCampaigns(page = 1){
        campaigns_loader.show();
        var data={
            security: nonce,
            action: 'qero_dashboard_campaigns',
            page:page
        };

        if(typeof reqCampaigns != "undefined")
            reqCampaigns.abort();

        reqCampaigns = $.post( url, data)
            .done(function( data ) {

                if(typeof data == "string")
                    data = JSON.parse(data);

                campaigns_loader.hide();
                populateTable(table_campaigns, data.data.data, message_campaigns);
                return;
            });
    }

    function populateTable(table, data, message){
        if(Array.isArray(data) == false){
            message.append('<h5>No data yet...</h5>');
            message.show(anim);
            return;
        }
        table.find('tbody').html('');
        data.forEach(function (row) {
            var tr = table.find('tbody').append('<tr></tr>').find('tr').last();
            for (var prop in row) {
                if (Object.prototype.hasOwnProperty.call(row, prop)) {
                    tr.append('<td>'+row[prop]+'</td>')
                }
            }
        });
        table.show(anim)
    }

    function loadDashboardSales(){
        var data={
            security: nonce,
            action: 'qero_dashboard_sales',
        };
        $.post( url, data)
            .done(function( data ) {

                if(typeof data == "string")
                    data = JSON.parse(data);

                chartDashboardSales(data.data.data);
                return;
            });
    }

    function loadDashboardClientCreation(){
        var data={
            security: nonce,
            action: 'qero_dashboard_client_creation',
        };
        $.post( url, data)
            .done(function( data ) {

                if(typeof data == "string")
                    data = JSON.parse(data);

                chartDashboardClientCreation(data.data.data);
                return;
            });
    }

    /* creation of charts */

    function chartDashboardClientCreation ($data){
        clients_creation_loader.hide();
        mixedChart.push(
            new Chart(ctxClientCreation, {
                type: 'line',
                data: {
                    datasets: [{
                        label: $data.datasets[0].label,
                        data: $data.datasets[0].data,
                        backgroundColor: 'rgb(60,88,155)',
                        borderColor: 'rgb(81,111,181)',
                        fill: false
                    }],
                    labels: $data.labels
                },
                options: {
                }
            })
        );
        $('#ctxClientCreation').show(anim);
    }



    function chartDashboardSales ($data){
        sales_loader.hide();
        mixedChart.push(
            new Chart(ctxSales, {
                type: 'line',
                data: {
                    datasets: [{
                        label: $data.datasets[0].label,
                        data: $data.datasets[0].data,
                        yAxisID: 'y-axis-1',
                        backgroundColor: 'rgb(60,88,155)',
                        borderColor: 'rgb(81,111,181)',
                        fill: false
                    }, {
                        label: $data.datasets[1].label,
                        data: $data.datasets[1].data,
                        yAxisID: 'y-axis-2',
                        backgroundColor: 'rgb(239,99,48)',
                        borderColor: 'rgb(240,129,89)',
                        fill: false
                    }],
                    labels: $data.labels
                },
                options: {
                    responsive: true,
                    hoverMode: 'index',
                    stacked: false,
                    title: {
                        display: false,
                    },
                    scales: {
                        yAxes: [{
                            type: 'linear',
                            display: true,
                            position: 'left',
                            id: 'y-axis-1',
                        }, {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            id: 'y-axis-2',

                            gridLines: {
                                drawOnChartArea: false,
                            },
                        }],
                    }
                }
            })
        );
        $('#ctxSales').show(anim);
    }

    function printPages(obj, callback){
        var num = obj.find('input').val();
        obj.pagination({
            items: num,
            itemsOnPage: 5,
            prevText: '<<',
            nextText: '>>',
            onPageClick: callback
        });
    }

    init();
})( jQuery );