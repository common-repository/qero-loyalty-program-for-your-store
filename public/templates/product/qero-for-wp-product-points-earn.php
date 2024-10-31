<?php
/**
 * Created by PhpStorm.
 * User: tmota
 * Date: 23/08/2019
 * Time: 12:53
 */

if(empty($movement['credit_in']))
    return;
?>

<p>
    <?=sprintf(__('Get <strong>%s</strong>  <em class="qero-icon"></em>  points with this product!','qero-for-wp'), $movement['credit_in']);?>
</p>
