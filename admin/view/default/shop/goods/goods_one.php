<h3>Заглавный товар</h3>
<form id="frm-<?php echo $good[0]['id']; ?>" class="goods-form-edit">
<label for="name">Имя</label><input type="text" class="frm-goods-input" id="name" value="<?php echo $good[0]['name']; ?>"><br>
<label for="category_id">Категория</label><input type="text" class="frm-goods-input" id="category_id" value="<?php echo $good[0]['category_id']; ?>"><br>
<label for="name_url">Имя в url</label><input type="text" class="frm-goods-input" id="name_url" value="<?php echo $good[0]['name_url']; ?>"><span rid="<?php echo $good[0]['id']; ?>" class="ui-icon ui-icon-refresh regen-url-name"></span><br>
<label for="about">Текст описания</label><textarea class="frm-goods-input" id="about"><?php echo $good[0]['about']; ?></textarea><br>
<div id="save-<?php echo $good[0]['id']; ?>" class="act-button inquatr">Сохранить</div>
<h3>Дочерние товары поставщиков</h3>
<?php 
    $cnt = count($good);
if ($cnt>1){
?>
    <table>
        <tr>
            <th></th>
            <th>id</th>
            <th>Поставщик</th>
<?php
    foreach ($propertys as $p){
?>
            <th><?php echo $p['name']; ?></th>
<?php 
    }
?>
            
        </tr>
<?php
    echo '<hr>';
    for($t=1;$t<$cnt;$t++){
?>
        <tr id="row-id-<?php echo $good[$t]['id']?>">
            <td><div rid="<?php echo $good[$t]['id']?>" class="delete-goods cursor-pointer ui-icon ui-widget-content ui-icon-trash"></div></td>
            <td><?php echo $good[$t]['id']?></td>
            <td><?php echo $good[$t]['supplier_id']?></td>
<?php
    foreach($propertys as $p){
        foreach( $good[$t] as $gk=>$gv){
            if ($p['name_column'] == $gk){
?>
            <td><?php echo $gv; ?></td>
<?php
                break;
            }
        }
    }
?>
        </tr>
<?php
    }
?>
    </table>
<?php
}else{
?>
<p style="color: red">Дочерних товаров нет! Этот товар не будет показан клиентам.</p>
<?php
}
?>
</form>

<h3>Медиа</h3>
<div id="img-list">
    <?php 
        foreach($media as $r){
    ?>
    <div id="img-<?php echo $r['id']?>" class="goods-img-one-small">
    <a href="<?php echo $r['url_small']?>" target="_blank"><img src="<?php echo $r['url_small']?>"></a><div class="delete-img-goods act-button" did="<?php echo $r['id']?>" gid="<?php echo $good[0]['id']; ?>">Удалить</div>
    </div>
    <?php }?>
    <div style="clear:both"></div>
    <div id="img-uploaded"></div>
</div>

<div id="upload-img-for-<?php echo $good[0]['id']; ?>" class="uploader">
    <div>Перетащите сюда фотографию</div>
    <div class="browser">
        <label>
            <span>Искать</span>
            <input type="file" name="files[]" multiple="multiple" title='Жмем для поиска файла'>
        </label>
    </div>
</div>
<script>
    goods_submit(<?php echo $good[0]['id']; ?>);
    
      $('#upload-img-for-<?php echo $good[0]['id']; ?>').dmUploader({
        url: '/admin/?view=media_upload',
        dataType: 'json',
        allowedTypes: 'image/*',
        extraData: {
            id_goods:'<?php echo $good[0]['id']; ?>'
        },
        onUploadSuccess: function(id, data){
            //alert('UPLOAD id:'+id+' |data:'+data.html );
            //alert(data.data.url_small);
            
            htm = $("#img-uploaded").html();
            htm2 =  '<div id="img-'+data.data.id+'" class="goods-img-one-small">';
            htm2 += '<a href="'+data.data.url_small+'" target="_blank"><img src="'+data.data.url_small+'"></a><div class="delete-img-goods act-button" did="'+data.data.id+'" gid="<?php echo $good[0]['id']; ?>" id="delete-img-'+data.data.id+'">Удалить</div>';
            htm2 += '</div>';
            $("#img-uploaded").html( htm + htm2 );
            setControl();
          //$('#upload_res').html(data.html);
        },
        onUploadError: function(id, data){
          alert('ERROR:'+data);
          //alert('Error upload');
        }
      });
      setControl();
function setControl(){
      $(".delete-img-goods").click(function(){
        var th = this;
        //alert( $(this).attr('did')+' -> '+$(this).attr('gid') );
        $.ajax({
            url: '?view=shop_goods_delete_media',
            type: "POST",
            data: {did:$(th).attr('did'),gid:$(th).attr('gid')},
            success: function(res){
                $("#img-"+$(th).attr('did')).remove();
            },
            error: function(){
                alert("Error");
            }
        });
      })
}    
</script>
