<?php 
/**
 * Контроллер 
 * Обрабатываем запросы media_
 */
switch($view){
    /*@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@*/
    case('media'):
        // стили для uploader
        _pp::add_css_to_header(ADMIN_URL.'css/uploader.css');
        // скрипты для uploader
        _pp::add_script_to_header(ADMIN_URL.'js/media/dmuploader.js');
        _pp::add_script_to_header(ADMIN_URL.'js/media/media.js');
        
        if (isset($_REQUEST['d'])){
            _media::delete($_REQUEST['d']);
        }
        
        $list = _media::listmedia();
    break;
    case 'media_upload':
        // Загружаем файлы или файл
        /*
        $filename = _media::upload( $_FILES['file'] );
        $res = array('status'=>'ok', 'data'=> print_r( $_FILES, true ), 'html'=>$filename );
        echo json_encode( $res );
        */
        $upload = _media::upload( $_FILES['file'] );
        $res = array('status'=>'ok', 'data'=>array('url_small'=>$upload['url_small'], 'id'=>$upload['id']), 'html'=>print_r( $upload, true ) );

        if ( $_POST['id_goods'] > 0 ){   
            _shop::media_add($upload['id'], $_POST['id_goods']);
            $res['html'] = '<a href="'.$upload['url_small'].'" target="_blank"><img src="'.$upload['url_small'].'"></a>';
        }
        echo json_encode( $res );
        exit();
    break;
    case 'media_setting':
        // сохраняем настройки со страницы медиа
        foreach($_POST as $k=>$v){
            _media::settings_save($k, $v);
        }
        echo 'save';
        exit();
    break;
    
}