<?php
switch ($view){
    case 'shop_suppliers':
        if ($_REQUEST['act'] == 'add'){
            _shop::suppliers_add( $_POST['name'], $_POST['margin'] );
        }
        $suppliers = _shop::suppliers();
    break;
    case 'shop_suppliers_del':
        _shop::suppliers_del( $_REQUEST['suppliers_id'] );
        redirect();
    break;
    case 'shop_suppliers_edit':
        _shop::suppliers_edit( $_REQUEST['suppliers_id'], $_POST['name'], $_POST['margin'] );
        //print_r( $_POST );
        redirect();
    break;
    case 'shop_suppliers_uploadsettings':
        $one = _shop::suppliers_one( $_REQUEST['suppliers_id']);                // данные по поставщику
        $rule = json_decode( $one['rule_price'], true);                         // правила для загрузки
            if ($rule['price_tmp_show_rows'] == ''){ $rule['price_tmp_show_rows'] = 10; }   // По-умолчанию. Кол-во показываемыъ строк во временном прайсе
            if ($rule['price_added_column'] == ''){ $rule['price_added_column'] = 1; }   // По-умолчанию. Кол-во добавляемых колонок в прайсе
            if ($rule['price_skip_rows'] == ''){ $rule['price_skip_rows'] = -1; }   // По-умолчанию. кол-во пропускаемых строк в прайсе
        $list_columns = _shop::propertys( true );                               // Список свойств товара (постоянные и динамические
        $price_tmp_count = _shop::price_tmp_count( $_REQUEST['suppliers_id'] ); // количество чтрок во временной таблице по поставщику

        // TODO: хрень тут какая-та, работает, но переделать, чтоб понятно было как и что.
        if ($_GET['raw'] == 'yes'){
            $start = $_GET['page'] * $rule['price_tmp_show_rows'] - $rule['price_tmp_show_rows'];
            $price_tmp = _shop::price_show_from_tmp( $_REQUEST['suppliers_id'], $start, $rule['price_tmp_show_rows'], $rule );   // Прайс из временной таблицы
            include_once('suppliers_uploadsettings_part.php');
            exit();
        }else{
            // стили для uploader
            _pp::add_css_to_header(ADMIN_URL.'css/uploader.css');
            // скрипты для uploader
            _pp::add_script_to_header(ADMIN_URL.'js/media/dmuploader.js');
            _pp::add_script_to_header(ADMIN_URL.'js/shop/uploadprice.js');
            _pp::add_script_to_header(ADMIN_URL.'js/shop/suppliers_uploadsettings.js');
            // скрипты для пагинации
            _pp::add_script_to_header( ADMIN_URL.'js/jquery.jqpagination.min.js' );
            _pp::add_css_to_header( ADMIN_URL.'css/jqpagination.css' );
            
            $headprice = _shop::price_header( $_REQUEST['suppliers_id'] );          // последний загруженный прайс
            
            $current_page = 0;
            if (isset($_GET['page'])){
                $current_page = $_GET['page'];
            }else if ($price_tmp_count > 0 ){
                $current_page = 1;
            }
            $start = $current_page * $rule['price_tmp_show_rows'] - $rule['price_tmp_show_rows']; 
            
            $price_tmp = _shop::price_show_from_tmp( $_REQUEST['suppliers_id'], $start, $rule['price_tmp_show_rows'], $rule );   // Прайс из временной таблицы
        }
        
    break;
    case 'shop_suppliers_uploadprice':
        // Принимаем прайс, конвертим в json, сохраняем
        if ( _shop::price_save($_REQUEST['id'], $_FILES['file']['tmp_name']) ){
            $headprice = _shop::price_header( $_REQUEST['id'] );
            echo 'ok';
        }else{
            echo 'faild';
            //echo 'result:'.$res;
        }
        exit();
        //redirect();
    break;
    case 'shop_suppliers_pre_process':
        $res = _shop::price_pre_process( $_POST['suppliers_id'] );              // обрабатываем прайс и переносим во временную таблицу 
        redirect();        
    break;
    case 'shop_suppliers_process':
        _shop::price_process( $_REQUEST['suppliers_id'] );
        echo 'Загружено';
        exit();
    break;
    case 'shop_suppliers_ajax':                                                 // Обрабатываем ajax запросы
        if (isset($_POST['col'])){
            preg_match("/.*_(\d*)/", $_POST['col'], $column);                   // Номер колонки в прайсе, начиная с 0 $column[1]
        }
        if (isset($_POST['property'])){                     // Устанавливаем настройку по колонкам
            _shop::suppliers_price_property_save($_POST['suppliers_id'], $column[1], $_POST['property'] );
        }else if (isset($_POST['script'])){                 // настройки по скриптам на колонки
            _shop::suppliers_price_property_save($_POST['suppliers_id'], $column[1], null, $_POST['script'] );
        }else if (isset($_POST['common'])){                 // обшие настройки, вроде количество пропускаемых строк, количество добавляемых колонок
            _shop::suppliers_price_common_property_save($_POST['suppliers_id'], $_POST['common'], $_POST['val']);
        }else if (isset($_POST['price'])){                  // переносим строку из временного прайса в рабочий
            if ($_POST['price'] == 'new'){
                $res = _shop::price_new_row( $_POST['id'], $_POST['id_supplier'] );
                echo 'moved:'.$res;
                //if ( $_POST['checked_columns'] )
            }
            exit();
        }else if (isset($_POST['preview'])){                // обработка прайса - предпросмотр
            echo 'Preview';
        }
        echo 'Значение сохранено';
        exit();
    break;
/*
 * 
        $res = _media::upload( $_FILES['file'] );
        $res = array('status'=>'ok', 'data'=> print_r( $_FILES, true ), 'html'=>'this is return:' );
        echo json_encode( $res );
        exit();
 * 
 */
}
