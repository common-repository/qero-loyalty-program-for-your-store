<?php
/**
 * Created by PhpStorm.
 * User: tmota
 * Date: 12/08/2019
 * Time: 09:54
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function getLoader($id ,$on = true, $spacer=false){
    $class = $on==false ?' style="display: none;" ' :' ';
    $spacer = $spacer == true?'&nbsp;&nbsp;':'';
    return $spacer.'<div id="'.$id.'" class="qero-loader"'.$class.'></div>';

}