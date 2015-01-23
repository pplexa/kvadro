<?php defined('A') or die('Access denied'); ?>
<div class="content">
    <h2>Товары</h2>
    <h3>Товаров по отбору: <span id="good-count-where"><?php echo $g_count; ?></span></h3>
    <h3>Количество колонок(свойств):<?php echo $col_count; ?></h3>
    <label for="good_per_page">Строк на странице</label><input class="common_propertys" name="good_per_page" id="good_per_page" value="<?php echo _shop::settings_load('admin-goods-per-page', 3); ?>">
    <table width="100%" class="st-table">
        <tbody>
        <tr>
            <th></th>
            <th>#</th>
            <?php 
                foreach( $prop as $r){
                    // Цена и количество не выбираются !
                    if ($r['name_column'] == 'cost_in' || $r['name_column'] == 'amount' || $r['id'] > 0){
                        continue;
                    }
                    /**
                     * Ужос - для колонки с категориями дополняем, помимо значений, названиями категорий по их номеру  
                     */
                    $arr_name = null;
                    if ( $r['id'] == -4 ){
                        $arr_name = _shop::category_name_by_array_id( $r['distinct'] );
                    }
            ?>
            <th><?php echo $r['name']; echo '<br>'; property_select_distinct( $r['name_column'], $r['distinct'], $arr_name); ?></th>
            <?php
                }
            ?>
            <th>Дочерних записей</th>
            <th>Цена минимум</th>
            <th>остаток</th>
            <th>id продаваемого</th>
        </tr>
        </tbody>
        <tbody id="table-content" class="goods-block-one"><?php include_once 'goods_part.php';?></tbody>
    </table>
<div class="pagination">
    <a href="#" class="first" data-action="first">&laquo;</a>
    <a href="#" class="previous" data-action="previous">&lsaquo;</a>
    <input type="text" readonly="readonly" data-max-page="40" />
    <a href="#" class="next" data-action="next">&rsaquo;</a>
    <a href="#" class="last" data-action="last">&raquo;</a>
</div>
    <div id="delete" class="act-button">Удалить товары</div>
</div>
<script>
    var goods_count = <?php echo $g_count; ?>;
    var goods_cur_page = 1;
    var goods_per_page = <?php echo _shop::settings_load('admin-goods-per-page', 3); ?>;
    var goods_page_count = Math.ceil(goods_count/goods_per_page);
    var goods_w = Array();
</script>
<?php
/**
 * HTML select с уникальными зачениями (для объеденения спользуем class="p_s_d")
 * значене -xOOx- используем для выбора всех
 * @param string $name название
 * @param array список уникальных значений
 * #param array список названий уникальных значений
 */
function property_select_distinct($name, $arr, $arr_name = null){
    echo "<select id=\"".$name."\" class=\"p_s_d\">\n";
    echo "<option value=\"-xOOx-\">Все</option>\n";
    foreach($arr as $v){
        $name = $v;
        if ($arr_name !== null){
            if (isset($arr_name[$v])){
                $name = $arr_name[$v];
            }
        }
        echo "<option value=\"".$v."\">".$name."</option>\n";
    }
    echo "</select>\n";
}
?>