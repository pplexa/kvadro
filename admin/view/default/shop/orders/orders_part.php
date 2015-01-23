<?php
    /**
     * Представление для вывода строк таблицы заказов
     * вывод осуществляется в тэг tbody
     */
    $crow = count($orders);
    for($r=0; $r<$crow; $r++){  
?>
<tr>
    <td><div rid="<?php echo $orders[$r]['id']; ?>" state="false" class="table-open-close ui-icon ui-widget-content ui-icon-triangle-1-e"></div></td>
    <td><?php echo $orders[$r]['id']; ?></td>
    <td><?php echo $orders[$r]['date']; ?></td>
    <td><?php echo $orders[$r]['phone']; ?></td>
    <td><?php echo $orders[$r]['content']; ?></td>
    <td><?php echo $orders[$r]['status']; ?></td>
</tr>
<tr id="rid-<?php echo $goods[$r]['id']; ?>" style="display: none">
    <td colspan="6" id="did-<?php echo $orders[$r]['id']; ?>" load="0"><div class="load-img"></div></td>
</tr>
<?php
    }
?>