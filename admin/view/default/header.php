<?php defined('A') or die('Access denied'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="<?=ADMIN_URL?>style.css" />
<link rel="stylesheet" type="text/css" href="<?=ADMIN_URL?>css/jquery-ui.css" />
<!-- <link rel="stylesheet" type="text/css" href="<?=ADMIN_URL?>css/jquery-ui.theme.css" /> -->
<?php echo $css; ?>
<!-- <script type="text/javascript" src="<?=ADMIN_URL;?>js/jquery-1.7.2.min.js"></script> -->
    <script type="text/javascript" src="<?=ADMIN_URL;?>js/jquery.js"></script>
<!-- <script type="text/javascript" src="<?=ADMIN_URL;?>js/jquery-ui-1.9.2.custom.min.js"></script> -->
    <script type="text/javascript" src="<?=ADMIN_URL;?>js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=ADMIN_URL;?>js/jquery.cookie.js"></script>
<script type="text/javascript" src="<?=ADMIN_URL;?>js/workscripts.js"></script>
<script type="text/javascript" src="<?=ADMIN_URL;?>js/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?=ADMIN_URL;?>js/ajaxupload.js"></script>
<?php echo $script; ?>
<title>Список страниц</title>
</head>

<body>
<div class="karkas">
	<div class="head">
		<a href="<?=URL?>admin/"><img src="<?=ADMIN_URL?>images/logoAdm.jpg" /></a>
		<p><a href="<?=URL?>" target="_blank">На сайт</a> | <a href="?view=edit_user&amp;user_id=<?=$_SESSION['auth']['user_id']?>"><?=$_SESSION['auth']['admin']?></a> | <a href="?do=logout"><strong>Выйти</strong></a></p>
	</div> <!-- .head -->