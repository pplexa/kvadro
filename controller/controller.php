<?php

defined('A') or die('Access denied');

session_start();

// подключение модели
//require_once MODEL;

// подключение библиотеки функций
require_once 'functions/functions.php';

// получение массива каталога
//$cat = catalog(); 

// получение массива информеров
//$informers = informer();

// получени массива страниц
$pages = _page::all();

// получение названия новостей
//$news = get_title_news();

// регистрация
if($_POST['reg']){
    _user::registration();
    redirect();
}

// авторизация
if($_POST['auth']){
    _user::authorization();
    if($_SESSION['auth']['user']){
        // если пользователь авторизовался
        //echo "<p>Добро пожаловать, {$_SESSION['auth']['user']}</p>";
        //exit;
    }else{
        // если авторизация неудачна
        echo 'error:'.$_SESSION['auth']['error'];
        unset($_SESSION['auth']);
        exit;
    }
}

// выход пользователя
if($_GET['do'] == 'logout'){
    logout();
    redirect();
}

// массив метаданных
$meta = array();

// получение динамичной части шаблона #content
$view = _puu::$page;
switch($view){
    case 'shop':
        include_once 'shop.php';
    break;
    case('page'):
        // отдельная страница
        $get_page = _page::getByName($_GET['page_name']);
        /* проверим если указание плагинов на странице */
        preg_match_all("/\[(.*)\((.*)\)\]/imU", $get_page['text'], $plugin_s);
        if ( count($plugin_s) > 0){
            // подключаем файлы плагинов, выполняем встроенные функции, ответы записываем обратно в массив поиска плагинов, по порядку
            foreach( $plugin_s[1] as $k=>$v ){
                // TODO: выявлять повторные вызовы плагинов с одинаковыми параметрами - второй раз естно не выполнять
                $file_plugin = 'plugins'.DIRECTORY_SEPARATOR.$v.'.php';
                require_once $file_plugin;
                $plugin_s[3][$k] = $v($plugin_s[2][$k]);
                // замена
                $get_page['text'] = str_replace($plugin_s[0][$k], $plugin_s[3][$k], $get_page['text']);
            }
        }
        $meta['title'] = "{$get_page['title']} | " .TITLE;
        $meta['description'] = "{$get_page['description']} | " .TITLE;
    break;

    /* корзина */
    case('cart'):
        if ( $_GET['ida'] > 0 && $_GET['am'] > 0 ){
            _shop::cart_add( $_GET['ida'], $_GET['am'] );
            echo json_encode( array( 'count'=>$_SESSION['count'],'total'=> _pp::html_format_cost( $_SESSION['total'], false ) ) );
            exit();
        }
        if ( $_POST['do'] == 1){
            if ( count($_SESSION['cart']) > 0 ){
                //TODO: сохранить заказ
                _shop::order_add();
                $view = 'cart_thank';
                unset( $_SESSION['cart'] );
                _shop::cart_recalc();
            }
        }
        if ($_POST['idd'] > 0 ){
            _shop::cart_delete( $_POST['idd'] );
        }
        _pp::add_script_to_header( URL_VIEW.'js/cart.js');
        _pp::add_css_to_header( URL_VIEW.'css/jquery-ui.min.css');
        _pp::add_css_to_header( URL_VIEW.'css/jquery-ui.theme.min.css');
        // Собираем товары в корзине
        $goods = array();
        if (count($_SESSION['cart']) > 0){
            foreach( $_SESSION['cart'] as $k=>$v ){
                if ($k > 0 ){
                    $g = _shop::goods_one( $k, true );
                    // TODO: проще сделать метод выдающий только реальный заголовок товара, тоесть название, описание, медиа, 
                    // а не мудрить тут. Переделать.
                    $p = _shop::goods_one( $g[0]['parent_id'], true, true );
                    $g[0]['about'] = $p[0]['about'];
                    $g[0]['media'] = $p[0]['media'];
                    $goods[] = $g[0];
                }
            }
        }
    break;
    /* страница корня */
    case 'index':
        $view = 'index_page';
    break;
 
    default:
        $get_page = _page::getByName( _puu::$url );
        /* проверим если указание плагинов на странице */
        preg_match_all("/\[(.*)\((.*)\)\]/imU", $get_page['text'], $plugin_s);
        if ( count($plugin_s) > 0){
            // подключаем файлы плагинов, выполняем встроенные функции, ответы записываем обратно в массив поиска плагинов, по порядку
            foreach( $plugin_s[1] as $k=>$v ){
                // TODO: выявлять повторные вызовы плагинов с одинаковыми параметрами - второй раз естно не выполнять
                $file_plugin = 'plugins'.DIRECTORY_SEPARATOR.$v.'.php';
                if (file_exists($file_plugin)){
                    require_once $file_plugin;
                    $plugin_s[3][$k] = $v($plugin_s[2][$k]);
                    // замена
                    $get_page['text'] = str_replace($plugin_s[0][$k], $plugin_s[3][$k], $get_page['text']);
                }
            }
        }
        $meta['title'] = $get_page['title'];
        $meta['description'] = $get_page['description'];        
        $view = 'page';
        
        // если из адресной строки получено имя несуществующего вида
        //$view = 'hits';
        //$eyestoppers = eyestopper('hits');
}

// подключение вида
require_once VIEW.'index.php';



