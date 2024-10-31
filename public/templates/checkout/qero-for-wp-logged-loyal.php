<?php
/**
 * Created by PhpStorm.
 * User: tmota
 * Date: 12/08/2019
 * Time: 16:42
 */

include_once 'qero-for-wp-checkout-header.php';

?>


<table class="shop_table" id="qero-points-table">
    <thead>
    <tr>
        <th class="qero-main">
            <?=__('Spend points!','qero-for-wp').getLoader('qero_loader_points',true,true);?>
        </th>
        <th>
            <span id="max_to_spend" style="font-size: 0.700em;">
                <?=__('Max:','qero-for-wp')?>
            </span>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <?=__('Points','qero-for-wp');?>
        </td>
        <td>

        </td>
    </tr>
    </tbody>
    <tfoot id="foot_submit_cellphones">
    <tr>
        <th>
            <span class="woocommerce-input-wrapper">
                <input style="width: 100%" type="text" class="input-text " name="qero_points_input" id="qero_points_input" placeholder="<?=__('Points to spend','qero-for-wp')?>"/>
            </span>
            <div class="qero_isa_error" id="error_qero_points" style="display: none;">
                <em class="fa fa-times-circle"></em>
                <span></span>
            </div>
        </th>
        <th>
            <span id="submit_qero_points" class="button alt qero-button" style="cursor:pointer;padding: 0.2180469716em 1.01575em;display:flex;justify-content:center;height: 100%;width: 100%;">
                    <img src="<?=plugins_url('../../../admin/assets/_qerobutton.png',__FILE__)?>" alt="Qero"/>
                    <span style="display: none;">X</span>
            </span>
        </th>
    </tr>
    </tfoot>
</table>