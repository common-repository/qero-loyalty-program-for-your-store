<?php
/**
 * Created by PhpStorm.
 * User: tmota
 * Date: 12/08/2019
 * Time: 11:14
 */

?>

<p>
    <?=sprintf(__('Once complete this purchase will give you up to <strong>%s</strong> <em class="qero-icon"></em>  points!','qero-for-wp'), empty($points)?0:$points);?>
</p>