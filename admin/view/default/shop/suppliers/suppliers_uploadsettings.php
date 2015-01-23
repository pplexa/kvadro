<?php defined('A') or die('Access denied'); ?>
<div class="content">
    <h2>Настройка и загрузка прайса поставщика</h2>
    <h3><?=$one['name']?> (#<span id="suppliers_id"><?=$one['id']?></span>)</h3>

<div id="uploadprice" class="uploader">
    <div>Put here</div>
    <div class="browser">
        <label>
            <span>Искать</span>
            <input type="file" name="files[]" multiple="multiple" title='Жвем для поиска файла'>
        </label>
    </div>
</div>
    <form action="/admin/?view=shop_suppliers_edit&suppliers_id=<?php echo $one['id'];?>" method="POST">
        <label>Название</label><input type="text" name="name" id="name" value="<?php echo $one['name']; ?>"><br>
        <label>Наценка</label><input type="text" name="margin" id="margin" value="<?php echo $one['margin']; ?>"><br>
        <input type="submit" value="Сохранить">
    </form>
<h3>Общие настройки</h3>
<label>Количество добавляемых колонок в загружаемые прайсы:</label><input class="common_propertys" type="text" id="price_added_column" value="<?php echo $rule['price_added_column']; ?>"><br>
<label>В загружемомо файле не показывать строки(через запятую):</label><input class="common_propertys" type="text" id="price_skip_rows" value="<?php echo $rule['price_skip_rows']; ?>"><br>
<p>список колонок, которые будут опредеять уникальность строки товара. Например, Производитель, название.</p>
<?php
    foreach( $list_columns as $o ){
        $nam_row = 'row-unicum-'.$o['name_column'];
        if ($o['id'] > 0 ){
            $icheck = '';
            if ( isset($rule[$nam_row]) ){
                if ( $rule[$nam_row] == 'on' ){
                    $icheck = ' checked';
                }
            }
            echo $o['name'].'<input type="checkbox" name="'.$nam_row.'" id="'.$nam_row.'" class="common_propertys" '.$icheck.'><br>';
        }
    }
?>
<h3>Заголовок прайса</h3>
<div id="upload_res">
    <?php 
        $crow = count($headprice);
        $crow = 20;
        $ccol = count($headprice[0]);
        if ( $crow > 0) {
            function show_propertys( $list_columns, $selectid = false ){
                $res = '<option value="0">---</option>';
                foreach($list_columns as $row){
                    if ( $selectid == $row['id'] ){
                        $res .= '<option value="'.$row['id'].'" selected>'.$row['name'].'</option>'."\n";
                    }else{
                        $res .= '<option value="'.$row['id'].'">'.$row['name'].'</option>'."\n";
                    }
                }
                return $res;
            }
    ?>
    <div>Используемые переменные, $rcell, $cell, $price[$r][$c], $res[] = $price[$r], $crow, $ccol</div>
    <table>
        <tr>
            <?php for($r=0; $r < $ccol; $r++ ) {?>
            <th>col_<?php echo $r?></th>
            <?php } ?>
        </tr>
        <tr>
            <?php for($r=0; $r < $ccol; $r++ ) {
                $v = false;
                if (isset($rule['propertys'])){
                    foreach($rule['propertys'] as $d){
                        if ($d['column'] == $r){
                            $v = $d['idproperty'];
                            break;
                        }
                    }
                }
            ?>
            <th><select id="col_property_<?php echo $r?>" name="col_property_<?php echo $r?>" class="col_property"><?php echo show_propertys( $list_columns, $v ); ?></select></th>
            <?php } ?>
        </tr>
        <tr>
            <?php for($r=0; $r < $ccol; $r++ ) {?>
            <th><textarea id="col_reg_<?php echo $r?>" name="col_reg_<?php echo $r?>" class="col_reg" style="width:120px;"><?php
                if (isset($rule['propertys'])){
                    foreach($rule['propertys'] as $d){
                        if ($d['column'] == $r){
                            echo $d['script'];
                            break;
                        }
                    }
                }
            ?></textarea></th>
            <?php } ?>
        </tr>
        <?php 
            $skip_rows = explode(',', $rule['price_skip_rows'] );
            for($r=0; $r < $crow; $r++ ) {
                // пропускаем строки которые нам не нужны
                $need_skip = false;
                if ( count($skip_rows) > 0 ){
                    foreach( $skip_rows as $skiprow){
                        if ($skiprow == $r){
                            $need_skip = true;
                            break;
                        }
                    }
                }
                if ( $need_skip ){
                    continue;
                }
        ?>
        <tr>
            <?php for($c=0; $c < $ccol; $c++ ) {?>
            <td style="border: 1px solid black"><?php echo $headprice[$r][$c] ?></td>
            <?php } ?>
        </tr>
        <?php } ?>
    </table>

    <?php } ?>
</div>
    <form action="?view=shop_suppliers_pre_process" method="POST">
        <input type="hidden" name="suppliers_id" value="<?php echo $one[id]; ?>">
        <input id="preview" type="submit" value="Показать/Загрузить">
    </form>
<div>
    <?php 
        $crow = count($price_tmp);
        $ccol = count($price_tmp[0]);
    ?>
    <h2>Прайс во временной таблице</h2>
    <div class="act-button" id="price-process">Загрузить на сайт</div>
    <p>количество строк:<?php echo $price_tmp_count; ?></p>
    <label>Строк на страницу:</label><input class="common_propertys" type="text" id="price_tmp_show_rows" value="<?php echo $rule['price_tmp_show_rows']; ?>">
    <div><table border="1">
        <?php
        echo '<tr>';
        echo '<td></td>';
        echo '<td></td>';
        foreach($price_tmp[0] as $k=>$v){
            for($rc=0; $rc<count($list_columns); $rc++){
                if ($k == $list_columns[$rc]['name_column']){
                    echo '<td>'.$list_columns[$rc]['name'].'</td>';
                    break;
                }else
                if ($k == 'id'){
                    echo '<td>id</td>';
                    break;
                }
            }
        }
        echo '</tr>';
        echo '<tbody id="tmp-tablе-content">';
        include_once 'suppliers_uploadsettings_part.php';
    ?>
        </tbody>
        </table>
        
<div class="pagination">
    <a href="#" class="first" data-action="first">&laquo;</a>
    <a href="#" class="previous" data-action="previous">&lsaquo;</a>
    <input type="text" readonly="readonly" data-max-page="40" />
    <a href="#" class="next" data-action="next">&rsaquo;</a>
    <a href="#" class="last" data-action="last">&raquo;</a>
</div>
<script>
    var goods_count = <?php echo $price_tmp_count ?>;
    var goods_per_page = <?php echo $rule['price_tmp_show_rows']; ?>;
    var goods_page_count = <?php echo ceil( $price_tmp_count / $rule['price_tmp_show_rows'] ); ?>;
    var goods_current_page = <?php echo $current_page; ?>;
    var suppliers_id = <?php echo $one['id']; ?>
</script>

    </div>
</div>

</div>
<script>
$(document).ready(function(){
    $('.col_property').change( function(pp){
        par = { col:pp.target.id, property: $('#'+pp.target.id).val(), suppliers_id: <?php echo $one[id]; ?>};
        saveoptajax('?view=shop_suppliers_ajax', par, pp.target );
    })
    $('.col_reg').change( function(pp){
        par = { col:pp.target.id, script: $('#'+pp.target.id).val(), suppliers_id: <?php echo $one[id]; ?>};
        saveoptajax('?view=shop_suppliers_ajax', par, pp.target );
    })
    $('.common_propertys').change( function(pp){
        if( $('#'+pp.target.id).attr('type') == 'checkbox' ){
            if ( $('#'+pp.target.id).is(':checked') ){
                vv = 'on'
            }else{
                vv = 'off';
            }
        }else{
            vv = $('#'+pp.target.id).val();
        }
        par = { common:pp.target.id, val: vv, suppliers_id: <?php echo $one[id]; ?>};
        saveoptajax('?view=shop_suppliers_ajax', par, pp.target );
    })
    $('#ajaxx').click(function(){
        $('#ajaxw').stop( true );
        $('#ajaxw').fadeOut( 500 );
    })
    $('#ajaxc').click(function(){
        $('#ajaxw').stop( true );
    })
    initJ();
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
}
</script>
<div id="ajaxw" style="position: absolute; display: none; background-color: #EFEFEF; border: 1px solid red; padding: 5px">
    <img src="/admin/view/default/images/close.jpg" id="ajaxx" style="display:block; float:right;">
    <div id="ajaxc"></div>
</div>
