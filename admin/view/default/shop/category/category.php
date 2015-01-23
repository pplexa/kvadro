<?php defined('A') or die('Access denied'); ?>
<div class="content">
	<h2>Категории товаров</h2>
<?php
if(isset($_SESSION['answer'])){
    echo $_SESSION['answer'];
    unset($_SESSION['answer']);
}
?>
<div class="dd" id="nestable1">
    <ol class="dd-list" id="pp1">
        <?php
            $p_l = 0;
            $f = false;
            foreach( $category as $k=>$v ){

        ?>
        <li class="dd-item dd3-item" data-id="<?php echo $v['category_id'] ?>" id="li<?php echo $v['category_id'] ?>">
            <div class="dd-handle dd3-handle"></div>
            <div class="dd3-content">
                <input type="text" id="n<?php echo $v['category_id'] ?>" value="<?php echo $v['category_name']?>">
                <input type="text" id="ne<?php echo $v['category_id'] ?>" value="<?php echo $v['category_name_e']?>">
                <input type="text" id="nui<?php echo $v['category_id'] ?>" value="<?php echo $v['urlimg']?>">
                <span class="numcat" id="nc<?php echo $v['category_id'] ?>"><?php echo $v['category_id'] ?></span>
                <span class="delhier" id="d<?php echo $v['category_id'] ?>" data="<?php echo $v['category_id'] ?>">Удалить</span>
            </div>
            <?php
                if ($category[$k+1]['dlevel'] > $category[$k]['dlevel']){
                    echo '<ol class="dd-list">';
                }else if ($category[$k+1]['dlevel'] < $category[$k]['dlevel']){
                    $cnt = $category[$k]['dlevel'] - $category[$k+1]['dlevel'];
                    for($t=0; $t<$cnt; $t++){
                        echo '</ol>';
                    }
                }            
            ?>
            
        </li>            
        <?php
            }
        ?>
    </ol>
</div>
        

<script>
    var max_id = <?php echo $max_id?>;
    
$(document).ready(function(){
    $('#nestable1').nestable({ /* config options */ });
    $('#nestable1').change(function(){
        j = window.JSON.stringify( $('#nestable1').nestable('serialize') );
        $('#nestable-output').html( j );
    })

    $('#category_add').click(function(){
        $('#pp1').append('\
<li class="dd-item dd3-item" data-id="'+max_id+'" id="li'+max_id+'">\n\
<div class="dd-handle dd3-handle"></div>\n\
<div class="dd3-content">\n\
<input type="text" id="n'+max_id+'" value="'+$('#category_new').val()+'">\n\
<input type="text" id="ne'+max_id+'" value="'+$('#category_new_e').val()+'">\n\
<input type="text" id="nui'+max_id+'" value="'+$('#urlimg_new').val()+'">\n\
<span class="numcat" id="nc'+max_id+'">'+max_id+'</span>\n\
<span class="delhier" id="d'+max_id+'" data="'+max_id+'">Удалить</span>\n\
</div></li>');
        
        max_id += 1;
        
        $('#d'+(max_id-1)).click(function(e){
            d = e.target.getAttribute("data");
            $('#li'+d).remove();
        });
    });
    $('#category_save').click(function(){
        j = window.JSON.stringify( $('#nestable1').nestable('serialize') );
        $('#nestable-output').html( j );
        $('#json').val( j );

        $('#f').submit();
    })
    $('.delhier').click(function(e){
        d = e.target.getAttribute("data");
        $('#li'+d).remove();
    });
});
</script>
<div style="clear: both;">
    <h4>Новая категория</h4>
    <label>Название:</label><input type="text" name="category_new" id='category_new'><br>
    <label>Имя в url:</label><input type="text" name="category_new_e" id='category_new_e'>
    <label>url картинки:</label><input type="text" name="urlimg_new" id='urlimg_new'>
    <button id="category_add" style="width:150px; height:25px;">Добавить</button>
</div>
<div style="clear: both;">
    <button id="category_save" style="width:150px; height:25px;">Сохранить</button>
</div>
<hr>
<div style="clear: both;">
    <textarea id="nestable-output"></textarea>
</div>
<form method="POST" id="f" action="?view=shop_category">
    <input type="hidden" name="json" value="" id="json">
</form>


	</div> <!-- .content -->
