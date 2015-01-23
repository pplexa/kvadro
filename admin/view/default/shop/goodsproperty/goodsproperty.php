<?php defined('A') or die('Access denied'); ?>
<div class="content">
	<h2>Свойства товаров</h2>
	<table id="sort" class="tabl sort" cellspacing="1">
	  <tr class="no_sort">
		<th class="number">№</th>
		<th class="str_sort">Название</th>
                <th class="str_sort">Колонка</th>
		<th class="str_sort">Тип</th>
                <th class="str_sort">Длина</th>
                <th></th>
	  </tr>
        <?php $i = 0; foreach($propertys as $row){ $i++; ?>
          <tr id="<?=$row['id']?>">
              <td><?=$i?></td>
              <td><?=$row['name']?></td>
              <td><?=$row['name_column']?></td>
              <td><?=$row['type']?></td>
              <td><?=$row['len']?></td>
              <td><a href="?view=shop_goodsproperty_del&amp;property_id=<?=$row['id']?>" class="del">удалить</a></td>
          </tr>
        <?php } ?>
        <h3>Добавить новое</h3>
        <div>
            <form method="post">
                <input type="hidden" name="act" value="add">
                <label>Название</label><input type="text" name="name">
                <label>Тип</label><select name="type"><option value="int">int</option><option value="string">string</option></select>
                <label>Длина</label><input type="text" name="len" value="11">
                <input type="submit" name="add" value="Добавить">
            </form>
        </div>
<?php

//_shop::propertys_add('asd');
?>