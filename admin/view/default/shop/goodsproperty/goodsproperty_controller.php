<?php
/**
 * Контроллер 
 * Обрабатываем запросы users_
 */
switch ($view){
    case 'shop_goodsproperty':
        if ($_POST['act'] == 'add'){
            _shop::propertys_add($_POST['name'], $_POST['type'], $_POST['len']);
        }
        $propertys = _shop::propertys();
    break;
    case 'shop_goodsproperty_del':
        _shop::propertys_del( $_REQUEST['property_id'] );
        //$propertys = _shop::propertys();
        redirect();
    break;
}