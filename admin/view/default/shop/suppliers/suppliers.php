<?php defined('A') or die('Access denied');?>
<div class="content">
    <h2>Поставщики</h2>
	<table id="sort" class="tabl sort" cellspacing="1">
	  <tr class="no_sort">
		<th class="number">№</th>
		<th class="str_sort">Название</th>
                <th class="str_sort">Наценка</th>
		<th class="str_sort">Состояние</th>
                <th></th>
	  </tr>
        <?php $i=0; foreach( $suppliers as $row ){ $i++; ?>
            <tr>
		<td><?=$row['id']?></td>
		<td><?=$row['name']?></td>
                <td><?=$row['margin']?></td>
		<td><?=$row['active']?></td>
                <td><a href="?view=shop_suppliers_del&amp;suppliers_id=<?=$row['id']?>" class="del">удалить</a>&nbsp;<a href="?view=shop_suppliers_uploadsettings&amp;suppliers_id=<?=$row['id']?>">Настроить</a></td>
            </tr>
        <?php } ?>
        </table>
    <h2>Добавить нового поставщика</h2>
    <form method="POST">
        <input type="hidden" name="act" value="add">
        <label>Название</label><input type="text" name="name">
        <label>Наценка</label><input type="text" name="margin">
        <input type="submit" name="add" value="Добавить">
    </form>

</div>
