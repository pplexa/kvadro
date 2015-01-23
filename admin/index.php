<?php

// запрет прямого обращения
define('A', TRUE);

session_start();

if($_GET['do'] == "logout"){
    
    unset($_SESSION['auth']);
}

if(!$_SESSION['auth']['admin']){
   // подключение авторизации
   include $_SERVER['DOCUMENT_ROOT'].'/admin/auth/index.php';
}

// подключение файла конфигурации
require_once '../config.php';

// подключение файла функций пользовательской части
require_once '../functions/functions.php';

// подключение файла функций административной части
require_once 'functions/functions.php';

// получение количества необработанных заказов
$count_new_orders = count_new_orders();


// сортировка страниц
if($_POST['sortable']) {
	
	$result = sort_pages($_POST['sortable']);
	if(!$result) {
		exit(FALSE);
	}
	
	exit(json_encode($result));
}

//сортировка ссылок
if($_POST['sort_link']) {
	
	//проверяем есть ли идентификатор информера к которому принадлежат ссылки
	if(array_key_exists('parent',$_POST)) {
		$parent = $_POST['parent'];
		unset($_POST['parent']);
	}
	else {
		exit(FALSE);
	}
	
	$result = sort_links($_POST['sort_link'],$parent);
	if(!$result) {
		exit(FALSE);
	}
	exit(json_encode($result));
}

//сортировка информеров
if($_POST['sort_inf']) {
	
	$result = sort_informers($_POST['sort_inf'],$parent);
	if(!$result) {
		exit(FALSE);
	}
	exit(TRUE);
}

// получение массива каталога
$cat = catalog(); 

// получение динамичной части шаблона #content
$view = empty($_GET['view']) ? 'pages' : $_GET['view'];
// Попробуем подключить контроллер 
$controller = _pp::get_admin_controller($view);

if ( !empty($controller) ){
    include $controller;
}else{
    echo '<h1>no controller</h1>';
    $view = 'pages'; 
}
// Подключаем представления
include ADMIN_VIEW.'index.php';
?>