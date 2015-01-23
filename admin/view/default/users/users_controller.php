<?php
/**
 * Контроллер 
 * Обрабатываем запросы users_
 */
switch($view){
    /*@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@*/
    case('users'):
        // параметры для навигации
        if(isset($_GET['page'])){
            $page = (int)$_GET['page'];
            if($page < 1) $page = 1;
        }else{
            $page = 1;
        }
        $count_rows = _user::count(); // общее кол-во пользователей
        $pages_count = ceil($count_rows / _user::$per_page); // кол-во страниц
        if(!$pages_count) $pages_count = 1; // минимум 1 страница
        if($page > $pages_count) $page = $pages_count; // если запрошенная страница больше максимума
        $start_pos = ($page - 1) * _user::$per_page; // начальная позиция для запроса
        
        $users = _user::get_users($start_pos, _user::$per_page);
    break;
    case('users_add'):
        $roles = _user::roles();
        if($_POST){
            if( _user::add() ) redirect("?view=users");
                else redirect();
        }
    break;
    case('users_edit'):
        
        $get_user = _user::get((int)$_GET['user_id']);
        $roles = _user::roles();
        if($_POST){
            if( _user::edit((int)$_GET['user_id']) ) redirect("?view=users");
                else redirect();
        }
    break;
    case('users_del'):
        _user::del( (int)$_GET['user_id'] );
        redirect();
    break;    
}
