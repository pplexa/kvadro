<?php
/**
 * Шаблон вывода списка товаров по категории 17 - гусеницы
 */
if ($find_count >0){
?>
<table class="find-result-table">
    <tr>
        <th>Название</th>
        <th>Размеры, мм.</th>
        <th>Шаг зацепа, мм.</th>
        <th>Вес, кг.</th>
        <th>Цена</th>
        <th>Наличие</th>
        <th></th>
    </tr>
<?php
    $list_category_url = _shop::category_list_parents();
    $cnt = 0;
    foreach($find_arr as $o){
        $cnt++;
        $trclass = '';
        if ( $cnt % 2){
            $trclass = ' class="tr-odd"';
        }
?>
    <tr<?php echo $trclass; ?>>
        <td><a href="<?php echo '/shop/'.$list_category_url[$o['category_id']].'/'.$o['name_url']; ?>"><?php echo $o['name']; ?></a></td>
        <td><?php echo $o['c_29'].'/'.$o['c_32'].'/'.$o['c_31']; ?></td>
        <td><?php echo $o['c_24']; ?></td>
        <td><?php echo $o['c_22']; ?></td>
        <td><?php _pp::html_format_cost( $o['cost_out'] ); ?></td>
        <td><?php if ($o['amount']>0){ echo 'есть'; } ?></td>
        <td><div class="goods-button-order" id="add-to-order-<?php echo $o['id']; ?>" gid="<?php echo $o['id']; ?>">Заказать</div></td>
    </tr>
<?php
    }
?>
</table>
<?php
}else{
    if ($_GET['act'] == 'find'){
?><h4 style="color:red">Ничего не нашли, попробуйте изменить параметры поиска.</h4><?php
    }
}
?>

