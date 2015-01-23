<?php
/**
 * Контроллер 
 * Обрабатываем запросы users_
 */
_pp::add_script_to_header(ADMIN_URL.'js/nestable/jquery.nestable.js');
_pp::add_css_to_header(ADMIN_URL.'css/nestable.css');

switch($view){
    /*@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@*/
    
    case('shop_category'):
        
        if (isset($_POST['json'])){
            _shop::category_save( json_decode($_POST['json'], true) );
        }
        $max_id = _shop::category_max_id() + 1;
        $category = _shop::category();
    break;
}
