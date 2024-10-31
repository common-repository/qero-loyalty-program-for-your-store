<?php
/**
 * Created by PhpStorm.
 * User: tmota
 * Date: 06/08/2019
 * Time: 12:10
 */

function getLoader($id ,$on = true, $spacer=false){
    $class = $on==false ?' style="display: none;" ' :' ';
    $spacer = $spacer == true?'&nbsp;&nbsp;':'';
    return $spacer.'<div id="'.$id.'" class="spinner-border" role="status"'.$class.'><span class="sr-only">'.__('Loading...','qero-for-wp').'</span></div>';

}

function getFullBlockLoader($id){
    return '<div class="d-flex justify-content-center">
                <div class="spinner-border" role="status" id="'.$id.'">
                    <span class="sr-only">'.__('Loading...','qero-for-wp').'</span>
                </div>
            </div>';
}

function getPages($id,$count=1){
    return '<ul class="pagination justify-content-center" id="'.$id.'" qero-direction="right"><input value="'.$count.'" disabled hidden></ul>';

    $blockLeft = '<ul class="pagination justify-content-center" id="'.$id.'" qero-direction="right"><li class="page-item disabled"><a class="page-link" tabindex="-1">&lt;&lt;</a></li>';
    if($count == 1){
        $blockRight = '<li class="page-item disabled" qero-direction="right"><a class="page-link">&gt;&gt;</a></li></ul>';
    }else{
        $blockRight = '<li class="page-item" qero-direction="right"><a class="page-link">&gt;&gt;</a></li></ul>';
    }
    $pages = '';
    for($i=0;$i<$count;$i++){
        if($i==0){
            $pages .= '<li class="page-item active"><a class="page-link">'.($i+1).'</a></li>';
        }else{
            $pages .= '<li class="page-item"><a class="page-link">'.($i+1).'</a></li>';
        }
    }
    return $blockLeft.$pages.$blockRight;
}