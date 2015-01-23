<?php defined('A') or die('Access denied');?>
<div class="content">
    <h2>Общие настройки магазина</h2>
    <label>Количество товаров на странице:</label><input type="text" class="common_propertys" id="goods-per-page" value="<?php echo $goods_per_page; ?>">
    <h2>Переменные для админской части</h2>
    <label>Количество строк на странице товаров:</label><input type="text" class="common_propertys" id="admin-goods-per-page" value="<?php echo _shop::settings_load('admin-goods-per-page', 3); ?>">
</div>

<script>
$(document).ready(function(){
    $('.common_propertys').change( function(pp){
        par = { common:pp.target.id, val: $('#'+pp.target.id).val() };
        saveoptajax('?view=shop_settings_ajax', par, pp.target );
    })
})
function saveoptajax(url, param, elem){
    $.ajax({
        url: url,
        type: "POST",
        data: param,
        success: function(res){
            $('#ajaxw').css('top',$(elem).offset().top-10 );
            $('#ajaxw').css('left',$(elem).offset().left-10 );
            $('#ajaxc').html( res );
            $('#ajaxw' ).fadeIn( 500 ).delay( 3000 ).fadeOut( 500 );
        },
        error: function(){
            alert("Error");
        }
    });
    $('#ajaxx').click(function(){
        $('#ajaxw').stop( true );
        $('#ajaxw').fadeOut( 500 );
    })
    $('#ajaxc').click(function(){
        $('#ajaxw').stop( true );
    })
}
</script>
<div id="ajaxw" style="position: absolute; display: none; background-color: #EFEFEF; border: 1px solid red; padding: 5px">
    <img src="/admin/view/default/images/close.jpg" id="ajaxx" style="display:block; float:right;">
    <div id="ajaxc"></div>
</div>