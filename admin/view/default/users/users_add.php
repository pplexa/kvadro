<?php defined('A') or die('Access denied'); ?>
<div class="content">
	
<h2>Добавление пользователя</h2>
<?php
if(isset($_SESSION['users_add']['res'])){
    echo $_SESSION['users_add']['res'];
}
?>
<form action="" method="post">
	
<table class="add_edit_page" cellspacing="0" cellpadding="0">
    <tr>
        <td class="add-edit-txt">*Имя пользователя:</td>
        <td><input class="head-text" type="text" name="name" value="<?=htmlspecialchars($_SESSION['users_add']['name'])?>" /></td>
    </tr>
    <tr>
        <td class="add-edit-txt">*Логин пользователя:</td>
        <td><input class="head-text" type="text" name="login" value="<?=htmlspecialchars($_SESSION['users_add']['login'])?>" /></td>
    </tr>
    <tr>
        <td class="add-edit-txt">*Пароль пользователя:</td>
        <td><input class="head-text" type="text" name="password" value="<?=htmlspecialchars($_SESSION['users_add']['password'])?>" /></td>
    </tr>
    <tr>
        <td class="add-edit-txt">*Email пользователя:</td>
        <td><input class="head-text" type="text" name="email" value="<?=htmlspecialchars($_SESSION['users_add']['email'])?>" /></td>
    </tr>
    <tr>
        <td class="add-edit-txt">Роль пользователя:</td>
        <td>
            <?php if($roles): ?>
            <select name="id_role">
                <?php foreach($roles as $item): ?>
                    <option value="<?=$item['id_role']?>"><?=$item['name_role']?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
        </td>
    </tr>
</table>
	
	<input type="image" src="<?=ADMIN_URL?>images/save_btn.jpg" /> 

</form>
<?php unset($_SESSION['users_add']); ?>

	</div> <!-- .content -->
