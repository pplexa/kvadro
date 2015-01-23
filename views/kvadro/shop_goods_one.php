<?php
echo _pp::html_bread( $bread ).'<br>';
$media = explode(',', $goods[0]['media']);
$photo = _pp::media_get_name_by_type( $media[0], '_m');
?>
<div class="goods-one-end">
    <div class="photo"><?php if ($media[0] != '') { ?><img src="<?php echo $photo; ?>"><?php } else { ?><div class="goods-no-photo">нет фотографии</div><?php } ?></div>
    <h1 class="goods-name"><?php echo $goods[0]['name']; ?></h1>
    <div class="goods-about"><?php echo $goods[0]['about']; ?></div>
    <div class="goods-cost" id="goods-cost"><?php  _pp::html_format_cost( $goods[1]['cost_out'], true ); ?></div>
</div>
<div class="goods-put-cart">
<label for="goods-count">Количество:</label>
<input id="goods-count" name="value" class="goods-count"><br>
<div class="goods-button-order" id="add-to-order" gid="<?php echo $goods[1]['id']; ?>">Заказать</div>
</div>
<div id="dialog" title="Заказ" style="display: none">
<p>Добавить еще товар или оформить заказ?</p>
<p id="dialog-txt"></p>
</div>