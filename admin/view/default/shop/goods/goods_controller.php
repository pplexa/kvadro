<?php
/**
 * Контроллер 
 * Обрабатываем запросы goods_
 */
switch ($view){
    case 'shop_goods':
        // стили для uploader
        _pp::add_css_to_header(ADMIN_URL.'css/uploader.css');
        // скрипты для uploader
        _pp::add_script_to_header(ADMIN_URL.'js/media/dmuploader.js');
        
        // скрипты для пагинации
        _pp::add_script_to_header( ADMIN_URL.'js/shop/goods.js' );
        _pp::add_script_to_header( ADMIN_URL.'js/jquery.jqpagination.min.js' );
        _pp::add_css_to_header( ADMIN_URL.'css/jqpagination.css' );
            
        $prop = _shop::propertys(true, 0, true);
        $w = $_POST['where'];
        $w[] = array('column'=>'parent_id', 'value'=>-1); // только заголовки товаров
        $g_count = _shop::goods_count( $w );
        
        $goods = _shop::goods(0, _shop::settings_load('admin-goods-per-page', 3), $_POST['where'] );
//        print_r($goods);
    break;
    case 'shop_goods_part':             // Показываем только часть списка товаров
        if ( $_POST['per'] > 0 ){
            _shop::settings_save('admin-goods-per-page', $_POST['per']);
        }
        $prop = _shop::propertys(true);
        $from = $_POST['page']*$_POST['per']-$_POST['per'];
        
        if ($_POST['act'] == 'delete'){
            _shop::goods_delete($where);
        }
        $goods = _shop::goods($from, $_POST['per'], $_POST['where'] );
        if (count($goods) == 0){
            echo 'No goods';
            exit();
        }
    break;
    case 'shop_goods_count':
        $w = $_POST['where'];
        $w[] = array('column'=>'parent_id', 'value'=>-1); // только заголовки товаров
        $g_count = _shop::goods_count( $w );
        echo $g_count;
        exit();
    break;
    case 'shop_goods_one':
        $propertys = _shop::propertys( true );
        $good = _shop::goods_one( $_POST['id'] );
        $media = _shop::media_list( $_POST['id'] );
        include_once('goods_one.php');
        exit();
    break;
    case 'shop_goods_edit':
        $res = _shop::goods_update( $_POST );
        if ($res === true){
            echo 'Сохранено';
        }else{
            echo $res;
        }
        exit();
    break;
    case 'shop_goods_delete':
        if (isset($_POST['delete_type'])){
            if ( $_POST['delete_type'] == 'one'){
                _shop::goods_delete( array(array('column'=>'id', 'value'=>$_POST['param'])) );
                echo 'ok';
            }else{
                if (isset($_POST['param'])){
                    _shop::goods_delete( $_POST['param'] );
                }else{
                    _shop::goods_delete( null );
                }
            }
        }
        exit();
    break;
    case 'shop_goods_delete_media':
        _shop::media_del($_POST['did'], $_POST['gid']);
        echo 'Удалили';
        exit();
    break;
}