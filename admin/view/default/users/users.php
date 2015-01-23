<?php defined('A') or die('Access denied'); ?>
<div class="content">
	<h2>Список пользователей</h2>
<?php
if(isset($_SESSION['answer'])){
    echo $_SESSION['answer'];
    unset($_SESSION['answer']);
}
?>
<a href="?view=users_add"><img class="add_some" src="<?=ADMIN_URL?>images/add_user.jpg" alt="добавить пользователя" /></a>

<table class="tabl" cellspacing="1">
    <tr>
        <th class="number">№</th>
        <th class="str_name">Имя</th>
        <th class="str_name">Логин</th>
        <th class="str_name">mail</th>
        <th class="str_sort">Роль</th>
        <th class="str_action">Действие</th>
    </tr>
<?php $i = 1; ?>
<?php foreach($users as $item): ?>
    <tr>
    	<td><?=$i?></td>
    	<td class="name_page"><?=htmlspecialchars($item['name'])?></td>
        <td class="name_page"><?=htmlspecialchars($item['login'])?></td>
        <td class="name_page"><?=htmlspecialchars($item['email'])?></td>
    	<td><?=htmlspecialchars($item['name_role'])?></td>
    	<td><a href="?view=users_edit&amp;user_id=<?=$item['customer_id']?>" class="edit">изменить</a>&nbsp; | &nbsp;<a href="?view=users_del&amp;user_id=<?=$item['customer_id']?>" class="del">удалить</a></td>
    </tr>
<?php $i++; ?>
<?php endforeach; ?>
</table>
<?php if($pages_count > 1) pagination($page, $pages_count, $modrew = 0); ?>
<a href="?view=users_add"><img class="add_some" src="<?=ADMIN_URL?>images/add_user.jpg" alt="добавить новость" /></a>
	</div> <!-- .content -->
