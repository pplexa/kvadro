<?php
/**
 * Контроллер 
 * Обрабатываем запросы pages_
 */
switch($view){
        case('pages'):
            // страницы
            $pages = _page::all();
        break;
        case('pages_edit'):
            // редактирование страницы
            $get_page = _page::get($_GET['page_id']);

            if($_POST){
                if( _page::edit($_GET['page_id'])) redirect('?view=pages');
                    else redirect();
            }
        break;
        case('pages_add'):
            if($_POST){
                if( _page::add()) redirect('?view=pages');
                    else redirect();
            }
        break;
        case('pages_del'):
            _page::del($_GET['page_id']);
            redirect();
        break;
}
