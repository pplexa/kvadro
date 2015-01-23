<?php 

// запрет прямого обращения
define('A', TRUE);

// подключение файла конфигурации
require_once 'config.php';

// Обращение в папке admin - админская часть
if (_puu::$page == 'admin'){
    exit();
}

// подключение контроллера
require_once CONTROLLER;

/**
 * $db PDO в config.php соединение устанавливается, здесь удаляется
 */
$db = null;
?>