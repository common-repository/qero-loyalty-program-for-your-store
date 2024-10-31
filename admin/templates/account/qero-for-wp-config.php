<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://qero.io/pt/fidelizacao-qero/
 * @since      1.0.0
 *
 * @package    Qero_For_Wp
 * @subpackage Qero_For_Wp/admin/partials
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$hide = get_option(base64_encode(QERO_LAST_CRON_EXEC)) !== false;
include_once plugin_dir_path(__FILE__).'../../partials/qero-for-wp-header.php';
?>
<div class="qero-page-title">
    <h1><img src="<?= plugins_url('../../assets/logo_qero.png',__FILE__)?>" alt="Qero">&nbsp;&nbsp;Qero - <?= __('Account', 'qero-for-wp').'&nbsp;&nbsp;'.getLoader('loader_new_app', false);?> </h1>
</div>
<hr class="my-4">

<?php if($apikey===false){ ?>
    <div class="qero-overlay">
        <div class="qero-form-api-key qero-white-panel" style="width: auto !important;">
            <h4> <?= __('Make sure you have E-goi ApiKey connected!', 'qero-for-wp');?></h4>
        </div>
    </div>
<?php } ?>

<?php if(isset($stores) && (is_string($stores) || !$stores)){ ?>
    <div class="qero-overlay">
        <div class="qero-form-api-key qero-white-panel" style="width: auto !important;">
            <h4> <?php echo $stores; ?></h4>
        </div>
    </div>
<?php } ?>

<div class="qero-sub-main">
    <div class="qero-main" style="width: 100%;height: 100%">
        <div class="qero-sub-main" style="width: 64%">
            <div class="qero-main qero-white-panel qero-display-white-bar" style="align-items: center !important;">
                <span><?=__('APIKEY','qero-for-wp');?></span>
                <input type="text" class="form-control" placeholder="APIKEY" aria-describedby="basic-addon1" value="<?=!empty($apikey)?preg_replace('/.{24}$/', '************************', $apikey):''?>" disabled>
            </div>
            <div class="qero-main qero-white-panel qero-display-white-bar">
                <span><?=__('Store','qero-for-wp');?></span>
                <div class="qero-main" style="width: 100%">
                    <?php if(!empty($stores)){?>
                        <div class="form-group" style="margin: 0 20px 0 0;flex-grow: 1;width: 50%;">
                            <select id="qero_app_name" style="width: 100%;">
                                <option value="0"><?php _e('Select a store...', 'egoi-for-wp'); ?></option>
                                <?php if(is_array($stores)){
                                    $selected = get_option('qero_app_name');
                                    foreach ($stores as $store){ ?>
                                        <option value="<?=$store['store_id']?>" <?=$selected==$store['name']?'selected':''?>><?=$store['name']?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    <?php } ?>

                    <button class="qero-button-green" data-toggle="collapse" data-target="#newAppForm" aria-expanded="false" aria-controls="newAppForm" >
                        <?=__('+ Add new store','qero-for-wp');?>
                    </button>

                    <button class="qero-button-inverse" type="submit" class='btn btn-danger' id="qero_login">
                        <?=__('Save changes','qero-for-wp');?>
                    </button>
                </div>
            </div>
            <div class="collapse qero-main qero-white-panel qero-display-white-bar" id="newAppForm">
                <span><?=__('Name','qero-for-wp');?></span>
                <div class="qero-main" style="flex-grow: 1;">
                    <input class="form-control" id="qero_new_app_name" value="" placeholder="<?=__('Enter App name','qero-for-wp');?>">
                    <button type="submit" class="qero-button-inverse" id="qero_new_app_button" style="width: 140px;min-width: 140px"><?=__('Submit','qero-for-wp');?></button>
                    <button type="submit" class="qero-button-close" data-toggle="collapse" data-target="#newAppForm" >X</button>
                </div>
            </div>
        </div>
        <div class="qero-sub-main" style="width: 35%;padding-right: 1%;">
            <div class="qero-white-panel" style="width: 100%">
                <h5 style="margin: 0;"><?=__('Step', 'qero-for-wp');?> 1</h5>
                <p><?=__('Create your first discount in Qero\'s dashboard', 'qero-for-wp');?></p>
                <div class="qero-center-div">
                    <button class="qero-button" onclick="var win = window.open('https://bo-qero-saas.e-goi.com/#/qeroapp/apikey:<?=$apikey?>', '_blank');win.focus();">
                        <?=__('Create campaign','qero-for-wp');?>
                    </button>
                </div>
            </div>
            <div class="qero-white-panel" style="width: 100%">
                <h5 style="margin: 0;"><?=__('Step', 'qero-for-wp');?> 2</h5>
                <p><?=__('Invite your clients and convert them into loyal one in your online store', 'qero-for-wp');?></p>
                <div class="qero-center-div">
                    <button class="qero-button" id="open_modal_button" data-toggle="modal" data-target="#qero_confirm_send_invite_modal" <?php echo $hide?'disabled':'' ?>>
                        <?=__('Send Invite','qero-for-wp');?>
                    </button>
                </div>
            </div>
            <div class="qero-white-panel" style="width: 100%">
                <h5 style="margin: 0;"><?=__('Step', 'qero-for-wp');?> 3</h5>
                <p><?=__('You\'r all set to start!', 'qero-for-wp');?></p>
            </div>
        </div>
    </div>
</div>

<?php if(!$hide){ ?>
    <div class="modal fade" id="qero_confirm_send_invite_modal" tabindex="-1" role="dialog" aria-labelledby="qero_confirm_send_invite_modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 style="display: flex" class="modal-title" id="qero_confirm_send_invite_modalTitle"><?php _e('Warning, this is one time usage!','qero-for-wp'); echo getLoader('loader_force_invite', false, true); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><?php _e('This will send an email to all of your customers','qero-for-wp'); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e('Close','qero-for-wp'); ?></button>
                    <button class="qero-button-inverse" class='btn btn-danger' id="qero_confirmed_force_invite">
                        <?=__('Continue','qero-for-wp');?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>