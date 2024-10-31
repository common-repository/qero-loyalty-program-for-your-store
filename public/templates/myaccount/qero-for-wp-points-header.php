<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function getLoader($id ,$on = true, $spacer=false){
    $class = $on==false ?' style="display: none;" ' :' ';
    $spacer = $spacer == true?'&nbsp;&nbsp;':'';
    return $spacer.'<div id="'.$id.'" class="qero-loader"'.$class.'></div>';

}

function getEmail(){
    $current_user = wp_get_current_user();
    if ( ! $current_user->exists() ) {
        return null;
    }

    return $current_user->user_email;
}
?>

<div>
    <h3>
        <?=__('Qero\'s Page','qero-for-wp');?>
    </h3>
</div>
