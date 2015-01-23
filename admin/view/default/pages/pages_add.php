<?php defined('A') or die('Access denied'); ?>
<div class="content">
<?php //print_arr($get_page) ?>
	
<h2>Добавление страницы</h2>
<?php
if(isset($_SESSION['pages_add']['res'])){
    echo $_SESSION['pages_add']['res'];
    unset($_SESSION['pages_add']['res']);
}
?>
<form action="" method="post">
	
	<table class="add_edit_page" cellspacing="0" cellpadding="0">
	  <tr>
		<td class="add-edit-txt">Название страницы:</td>
		<td><input class="head-text" type="text" name="title" /></td>
	  </tr>
	  <tr>
		<td>Ключевые слова:</td>
		<td><input class="head-text" type="text" name="keywords" value="<?=htmlspecialchars($_SESSION['pages_add']['keywords'])?>" /></td>
	  </tr>
      <tr>
		<td>Описание:</td>
		<td><input class="head-text" type="text" name="description" value="<?=htmlspecialchars($_SESSION['pages_add']['description'])?>" /></td>
	  </tr>
	  <tr>
		<td>Позиция страницы:</td>
		<td><input class="num-text" type="text" name="position" value="<?=$_SESSION['pages_add']['position']?>" /></td>
	  </tr>
          <tr>
		<td>Url:</td>
		<td><?=URL?>page/<input class="head-text" type="text" name="url" value="<?=$_SESSION['pages_add']['url']?>" /></td>
	  </tr>
	   <tr>
		<td>Содержание страницы:</td>
		<td></td>
	  </tr>
	  <tr>
		<td colspan="2">
			<textarea id="editor1" class="full-text" name="text"><?=$_SESSION['pages_add']['text']?></textarea>
<script type="text/javascript">
	CKEDITOR.replace( 'editor1' );
</script>
		</td>
	  </tr>
	</table>
	
	<input type="image" src="<?=ADMIN_URL?>images/save_btn.jpg" /> 

</form>
<?php unset($_SESSION['pages_add']); ?>

	</div> <!-- .content -->
	