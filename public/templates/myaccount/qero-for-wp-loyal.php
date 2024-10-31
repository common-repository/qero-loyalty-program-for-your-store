<?php
/**
 * Created by PhpStorm.
 * User: tmota
 * Date: 08/08/2019
 * Time: 10:25
 */

include_once 'qero-for-wp-points-header.php';

?>
<div class="qero-main">
    <div class="qero_isa_error" id="error_moviments" style="display: none;">
        <i class="fa fa-times-circle"></i>
        <span></span>
    </div>
</div>

<div class="qero-main">

    <div class="qero-sub-main" style="min-width: 69%;">
        <div class="qero-main" style="align-items: center; justify-content: left !important;">
            <span>
                <?=__('Movements','qero-for-wp');?>
            </span>
            <?=getLoader('movements_loader', true, true)?>
        </div>
        <div class="qero-sub-main" id="qero_movements_container">
            <?php

            $pages = $qero->getMovementsPages();
            if (is_numeric($pages) && $pages>=1) {

            ?>
                <table id="qero-movement-table" class="has-background">
                    <thead>
                        <tr>
                            <th>
                                <?=__('Value','qero-for-wp').' ('.get_option('woocommerce_currency').')';?>
                            </th>
                            <th>
                                <?=__('Credit In','qero-for-wp');?>
                            </th>
                            <th>
                                <?=__('Credit Out','qero-for-wp');?>
                            </th>
                            <th>
                                <?=__('Date','qero-for-wp');?>
                            </th>
                        </tr>
                    </thead>
                    <tbody style="display: none;">
                    </tbody>
                </table>
                <div class="qero-pagination">
                    <a href="#" id="qero_before_page">&laquo;</a>

                    <?php
                        for($i = 1; $i<=$pages;$i++){
                            ?>
                                <a href="#" id="qero_page_<?=$i;?>"<?=$i==1?'class="active"':'';?> ><?=$i;?></a>
                            <?php
                        }
                    ?>

                    <a href="#" id="qero_after_page">&raquo;</a>
                </div>
            <?php

            }elseif (is_numeric($pages) && $pages==0) {
                ?>
                <div class="qero-pagination">
                    <br>
                    <h3><?=__('No movements yet...','qero-for-wp');?></h3>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <div style="min-width: 2%;">

    </div>
    <div class="qero-sub-main" style="min-width: 29%;">
        <div class="qero-main" style="align-items: center; justify-content: left !important;">
            <span>
                <?=__('Account Info','qero-for-wp');?>
            </span>
            <?=getLoader('info_loader', true, true)?>
        </div>
        <div class="qero-sub-main" id="qero_infos_container">

        </div>
    </div>

</div>
