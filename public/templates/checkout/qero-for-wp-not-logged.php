<?php
/**
 * Created by PhpStorm.
 * User: tmota
 * Date: 12/08/2019
 * Time: 09:09
 */

include_once 'qero-for-wp-checkout-header.php';

?>

<table class="shop_table">
    <thead>
    <tr>
        <th class="qero-main">
            <span id="qero_table_title_gain" >
               <?=__('Gain points!','qero-for-wp');?>
            </span>
            <span id="qero_table_title_convert" style="display: none;">
                <i class="fa fa-arrow-left qero-pointer-button" id="qero_back_form" aria-hidden="true"></i>
                &nbsp;
                <?=__('Become loyal!','qero-for-wp');?>
            </span>
            <?=getLoader('check_cellphone_loader',false,true);?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <div class="qero-sub-main" id="gain_points_lock" style="<?=!empty($_SESSION['qero_cellphone'])?'':'display:none;';?>">

                    <div class="qero-main">
                        <label for="qero_cellphone" class=""><?=__('Cellphone','qero-for-wp');?></label>
                        <em class="fa fa-times-circle qero-pointer-button" id="forget_cellphone_anon" ></em>
                    </div>

                    <span class="woocommerce-input-wrapper qero-main">
                        <input type="text" class="input-text " value="<?=$_SESSION['qero_cellphone'];?>" id="qero_cellphone_lock" name="qero_cellphone_lock" placeholder="<?=__('Your cellphone here.','qero-for-wp')?>" readonly="readonly"/>
                    </span>
            </div>
            <div class="qero-sub-main" id="gain_points_form" style="<?=empty($_SESSION['qero_cellphone'])?'':'display:none;';?>">
                <p class="form-row form-row-wide" >
                    <label for="qero_cellphone_indicative" ><?=__('Country','qero-for-wp');?></label>
                    <span class="woocommerce-input-wrapper qero-main">
                        <select name="qero_cellphone_indicative" id="qero_cellphone_indicative" class="country_to_state country_select " autocomplete="indicative" style="flex-shrink: 3;">
                            <option value="" selected='selected' qeroregex=""><?=__('Select a country','qero-for-wp');?>&hellip;</option>
                            <?php foreach($indicatives as $contry){ ?>
                                <option value="<?=$contry['code'];?>" qeroregex="<?=$contry['regex'];?>" ><?=$contry['name'];?></option>
                            <?php } ?>
                        </select>
                    </span>
                </p>

                <p class="form-row form-row-wide" >
                <label for="qero_cellphone" class=""><?=__('Cellphone','qero-for-wp');?></label>
                    <span class="woocommerce-input-wrapper qero-main">
                        <input type="text" class="input-text " name="qero_cellphone" id="qero_cellphone" placeholder="<?=__('Your cellphone here.','qero-for-wp')?>"/>
                    </span>
                </p>
            </div>
            <div class="qero-sub-main" id="convert_points_form" style="display: none">
                <p class="formqero_cellphone-row form-row-wide" >
                    <label for="qero_name_signup" class=""><?=__('Name','qero-for-wp');?></label>
                    <span class="woocommerce-input-wrapper qero-main">
                        <input type="text" class="input-text " id="qero_name_signup" placeholder="<?=__('Your name here.','qero-for-wp')?>"/>
                    </span>
                </p>

                <p class="form-row form-row-wide" >
                    <label for="qero_email_signup" class=""><?=__('E-mail','qero-for-wp');?></label>
                    <span class="woocommerce-input-wrapper qero-main">
                        <input type="text" class="input-text " id="qero_email_signup" placeholder="<?=__('Your email here.','qero-for-wp')?>"/>
                    </span>
                </p>

                <p class="form-row form-row-wide" >
                    <label for="qero_cellphone_signup" class=""><?=__('Cellphone','qero-for-wp');?>&nbsp;<abbr class="required" title="<?=__('mandatory','qero-for-wp');?>">*</abbr></label>
                    <span class="woocommerce-input-wrapper qero-main">
                        <input type="text" class="input-text " id="qero_cellphone_signup" placeholder="<?=__('Your cellphone here.','qero-for-wp')?>" disabled/>
                    </span>
                </p>
            </div>
            <div class="qero_isa_error" id="error_cellphone" style="display: none;">
                <em class="fa fa-times-circle"></em>
                <span></span>
            </div>
        </td>
    </tr>
    </tbody>
    <tfoot id="foot_submit_cellphones">
    <tr>
        <th>
            <span id="submit_cellphones" class="button alt qero-pointer-button" style="<?=empty($_SESSION['qero_cellphone'])?'':'display:none;'?>">
                Submit
            </span>
        </th>
    </tr>
    </tfoot>
</table>


