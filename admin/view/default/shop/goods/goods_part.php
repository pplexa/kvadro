<?php
    /**
     * Представление для вывода строк таблицы товаров
     * вывод осуществляется в тэг tbody
     */
    $crow = count($goods);
    for($r=0; $r<$crow; $r++){  
?>
<tr>
    <td><div rid="<?php echo $goods[$r]['id']; ?>" state="false" class="table-open-close ui-icon ui-widget-content ui-icon-triangle-1-e"></div></td>
    <td><?php echo $goods[$r]['id']; ?></td>
<?php 
    foreach($prop as $nc) {
        // Цена и количество не выбираются !
        if ($nc['name_column'] == 'cost_in' || $nc['name_column'] == 'amount' || $nc['id'] > 0 ){
            continue;
        }    
    ?>
    <td><?php 
        echo $goods[$r][$nc['name_column']];
    ?></td>
<?php }?>
    <td><?php echo $goods[$r]['cnt_child']; ?></td>
    <td><?php echo $goods[$r]['sell_cost_in']; ?></td>
    <td><?php echo $goods[$r]['sell_amount']; ?></td>
    <td><?php echo $goods[$r]['active_id']; ?></td>   
</tr>
<tr id="rid-<?php echo $goods[$r]['id']; ?>" style="display: none">
    <td colspan="<?php echo count($prop)-2+6; ?>" id="did-<?php echo $goods[$r]['id']; ?>" load="0"><div class="load-img"></div></td>
</tr>
<?php
    }
?>