<?php
    /* хлебные крошки */
    echo _pp::html_bread( $bread ).'<br>';
    
    if (count($goods) == 0){
        $html_goods_inf = '';
    }else{
        $html_goods_inf = 'Количество товаров:'.$goods_inf['count'].'<br>';
    }
    // Ищем для каждой категории свой шаблон
    $patter_category = __DIR__.'/shop_category_'.$id_category.'.php';
    if ( file_exists( $patter_category) ){
        include_once( $patter_category );
    }
?>
<div class="goods-inf"><?php echo $html_goods_inf; ?></div>
<div id="shop-content" class="shop-content"><?php 
    // ищем для каждой категории свой шаблон отображения списка товаров
    $pattern_category_goods = __DIR__.'/shop_category_goods_part_'.$id_category.'.php';
    if ( file_exists( $pattern_category_goods) ){
        include_once( $pattern_category_goods );
    }else{  // шаблон по-умолчанию
        include ('shop_goods_part.php'); 
    }
?></div>
<?php
    // Если нужна навигация по страницам
    if ( $goods_inf['count'] > $goods_per_page){
        //	history.pushState({text:text},text,element.href); // добавляем новый елемент истории
?>
<div class="pagination">
    <a href="#" class="first" data-action="first">&laquo;</a>
    <a href="#" class="previous" data-action="previous">&lsaquo;</a>
    <input type="text" readonly="readonly" data-max-page="40" />
    <a href="#" class="next" data-action="next">&rsaquo;</a>
    <a href="#" class="last" data-action="last">&raquo;</a>
</div>
<?php
    }
?>
<script>
    var goods_count = <?php echo $goods_inf['count']; ?>;
    var goods_per_page = <?php echo $goods_per_page; ?>;
    var goods_page_count = <?php echo ceil($goods_inf['count']/$goods_per_page); ?>;
    var goods_current_page = <?php echo $current_page; ?>;
</script>
<div id="dialog" title="Заказ" style="display: none">
<p>Добавить еще товар или оформить заказ?</p>
<p id="dialog-txt"></p>
</div>