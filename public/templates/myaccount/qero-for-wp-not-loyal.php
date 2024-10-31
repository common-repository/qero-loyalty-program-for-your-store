<?php
/**
 * Created by PhpStorm.
 * User: tmota
 * Date: 08/08/2019
 * Time: 10:25
 */

include_once 'qero-for-wp-points-header.php';

?>
<div class="qero-main" id="first_step">
    <div class="qero-sub-main" style="max-width: 70%;min-width: 70%;">
        <div class="qero-main">
            <span>
                <?=__('Campaigns','qero-for-wp');?>
            </span>
            <?=getLoader('campaigns_loader', true, true)?>
        </div>
        <div class="qero-main">
            <table id="qero-campaign-table" class="has-background">
                <thead>
                <tr>
                    <th>
                        <?=__('End','qero-for-wp');?>
                    </th>
                    <th>
                        <?=__('Title','qero-for-wp');?>
                    </th>
                    <th>
                        <?=__('Description','qero-for-wp');?>
                    </th>
                </tr>
                </thead>
                <tbody style="display: none;">
                </tbody>
            </table>
        </div>
    </div>
    <div class="qero-sub-main" style="width: 70%">
        <div class="qero-main" style="align-items: center; justify-content: left !important;">
            <span>
                <?=__('Become loyal','qero-for-wp');?>
            </span>
            <?=getLoader('check_cellphone_loader', false, true)?>
        </div>
        <table id="qero-campaign-table" class="has-background">
            <thead>
            <tr><th></th></tr>
            </thead>
        </table>
            <div class="qero-sub-main">
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
                    <input name="cellphone" id="qero_cellphone" placeholder="<?=__('Your cellphone here.','qero-for-wp')?>"/>
                    <span style="font-size: 0.700em;">
                        ex.930000000
                    </span>
                </p>
                <button type="submit" id="submit_cellphone">
                    <span>
                        <?=__('Connect','qero-for-wp');?>
                    </span>
                </button>
            </div>
        <div class="qero-main">
            <div class="qero_isa_error" id="error_first_step" style="display: none;">
                <em class="fa fa-times-circle"></em>
                <span></span>
            </div>
        </div>
    </div>
</div>

<div class="qero-main" id="second_step" style="display: none;">
    <div class="qero-sub-main" style="min-width: 100%;">
        <div class="qero-main" style="align-items: center; justify-content: left !important;">
            <span>
                <?=__('Become loyal','qero-for-wp');?>
            </span>
            <?=getLoader('check_form_loader', false, true)?>
        </div>
        <div class="qero-sub-main">
            <span class="qero-form-field-text">
                <?=__('Name','qero-for-wp');?>
            </span>
            <input name="name" id="qero_name" value="" placeholder="<?=__('Your Name here.','qero-for-wp')?>"/>
        </div>
        <div class="qero-sub-main">
            <span class="qero-form-field-text">
                <?=__('Cellphone','qero-for-wp');?>
            </span>
            <input name="cellphone" id="qero_cellphone_second" value="" placeholder="<?=__('Your cellphone here.','qero-for-wp')?>" disabled/>
        </div>
        <div class="qero-sub-main">
            <span class="qero-form-field-text">
                <?=__('E-mail','qero-for-wp');?>
            </span>
            <input name="email" id="qero_email" value="<?=getEmail();?>" placeholder="<?=__('Your Email here.','qero-for-wp')?>" disabled/>
        </div>
        <br>
        <div class="qero-sub-main">
            <button type="submit" id="submit_cellphone_second">
                <span>
                    <?=__('Make me loyal!','qero-for-wp');?>
                </span>
            </button>
        </div>
        <div class="qero-main">
            <div class="qero_isa_error" id="error_second_step" style="display: none;">
                <em class="fa fa-times-circle"></em>
                <span></span>
            </div>
        </div>
    </div>

</div>

