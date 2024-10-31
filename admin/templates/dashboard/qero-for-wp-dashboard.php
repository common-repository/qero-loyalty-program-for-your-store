<?php
/**
 * Created by PhpStorm.
 * User: tmota
 * Date: 07/08/2019
 * Time: 13:11
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

include_once plugin_dir_path(__FILE__).'../../partials/qero-for-wp-header.php';

?>

<div class="qero-page-title">
    <h1><img src="<?= plugins_url('../../assets/logo_qero.png',__FILE__)?> " alt="Qero">&nbsp;&nbsp;Qero - <?= __('Dashboard', 'qero-for-wp');?></h1>
</div>
<hr class="my-4">

<div class="qero-sub-main">
    <div class="qero-main" style="width: 100%;height: 100%">
        <div class="qero-white-panel">
            <h6 style="margin: 0;"><?=sprintf( __('Total loyal clients: %s', 'qero-for-wp'), $movement['count']);?></h6>
        </div>
        <div class="qero-white-panel">
            <h6 style="margin: 0;"><?=sprintf( __('Total sales value: %s %s', 'qero-for-wp') , $movement['sum'], get_woocommerce_currency_symbol());?></h6>
        </div>
    </div>
    <div class="qero-main" style="width: 100%;height: 100%">
        <div class="qero-white-panel">
            <h6><?=__('Store sales (last month)','qero-for-wp');?> <?=getLoader('sales_loader');?></h6>
            <hr class="my-4">
            <canvas id="ctxSales" style="display: none;"></canvas>
        </div>
        <div class="qero-white-panel">
            <h6><?=__('Campaigns','qero-for-wp');?><?=getLoader('campaigns_loader');?></h6>
            <hr class="my-4">
            <div id="qero-campaigns-message" class="qero-message-hiden" style="display: none;"></div>
            <table class="qero-table-round table" id="qero-dashboard-campaigns" style="display: none;">
                <thead>
                <tr>
                    <th>
                        <?=__('Campaign title','qero-for-wp');?>
                    </th>
                    <th>
                        <?=__('Starting date','qero-for-wp');?>
                    </th>
                    <th>
                        <?=__('Ending date','qero-for-wp');?>
                    </th>
                    <th>
                        <?=__('Number of sales','qero-for-wp');?>
                    </th>
                    <th>
                        <?=__('Amount','qero-for-wp').' ('.get_woocommerce_currency_symbol().')';?>
                    </th>
                </tr>
                </thead>
                <tbody>

                </tbody>
                <tfoot>
                <tr>
                    <th colspan="5"> <?=getPages('campaigns_pages',$pages['campaigns']);?></th>
                </tr>
                </tfoot>
            </table>

        </div>
    </div>
    <div class="qero-main" style="width: 100%;height: 100%">
        <div class="qero-white-panel">
            <h6><?=__('Clients','qero-for-wp');?> <?=getLoader('clients_loader');?></h6>
            <hr class="my-4">
            <div id="qero-clients-message" class="qero-message-hiden" style="display: none;"></div>
            <table class="qero-table-round table" id="qero-dashboard-clients" style="display: none;">
                <thead>
                <tr>
                    <th>
                        <?=__('ID','qero-for-wp');?>
                    </th>
                    <th>
                        <?=__('Name','qero-for-wp');?>
                    </th>
                    <th>
                        <?=__('Cellphone','qero-for-wp');?>
                    </th>
                    <th>
                        <?=__('Total spent','qero-for-wp').' ('.get_woocommerce_currency_symbol().')';?>
                    </th>
                </tr>
                </thead>
                <tbody>

                </tbody>
                <tfoot>
                <tr>
                    <th colspan="4"><?=getPages('clients_pages',$pages['clients']);?></th>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="qero-white-panel">
            <h6><?=__('Loyal clients (last month)','qero-for-wp');?><?=getLoader('clients_creation_loader');?></h6>
            <hr class="my-4">
            <canvas id="ctxClientCreation"></canvas>
        </div>
    </div>
</div>
