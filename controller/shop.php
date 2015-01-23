<?php
        /* номер товарной категории */
        $cat_inf = _shop::category_get();
        $id_category = $cat_inf['category_id'];
        /* хлебные крошки */
        $bread = _shop::category_get( true );
        /* стили для магазина */
        _pp::add_css_to_header( URL_VIEW.'css/shop.css');
        
        /**
         * Отбрабатываем запросы ajax
         * или же как часть html
         */
        if (isset ($_GET['act'])){
            
            if ($_GET['act'] == 'find'){
                
                // collect in where
                $w = array();
                foreach( $_GET as $k=>$v ){
                    if ($k == 'act'){
                        continue;
                    }
                    if ( trim($v) != ''){
                        $w[] = array('column'=>$k, 'value'=>$v);
                    }
                }
                if ( count( $w ) > 0 ){
                    $category_child = _shop::category_get_child( $id_category );
                    $where_category = implode(',', $category_child);
                    
                    $find_count = _shop::goods_count( $w, ' and category_id in ('.$where_category.')' );
                    
                    $wh = _db::query_prepare_where( $w, 'g1.');
                    $q = "select 
                        g1.category_id, g1.id, g1.parent_id, g2.name, g2.name_url, g1.cost_out, g1.amount, 
                        g1.c_24, g1.c_29, g1.c_31, g1.c_22, g1.c_32
                        from shop_goods g1 LEFT JOIN shop_goods g2 ON g1.parent_id = g2.id 
                        ".$wh.' and g1.category_id in ('.$where_category.')';
                    $r = _db::query ($q );
                    $find_arr = $r->fetchAll();
                    
                }
                if ($_REQUEST['raw'] == 'yes'){
                    include_once VIEW.'shop_find_category_17.php';
                    exit();
                }
            }
        }
        
        if (_puu::$url_last == 'shop'){
            /* главная страница магазина */
            $view = 'shop_main';
        }else if ( _puu::$url_last != $cat_inf['category_name_e'] && strlen(_puu::$url_last) > 0 ){
            /* Один товар или товар со список дочерних товаров */
            /* скрипты для фарша JavaScript */
            _pp::add_script_to_header( URL_VIEW.'js/order.js');
            _pp::add_script_to_header( URL_VIEW.'js/jquery-ui.min.js');
            _pp::add_css_to_header( URL_VIEW.'css/jquery-ui.min.css');
            _pp::add_css_to_header( URL_VIEW.'css/jquery-ui.theme.min.css');
            //_pp::add_css_to_header( URL_VIEW.'css/jquery-ui.structure.min.css');

            /* Один товар */
            $id_goods = _shop::goods_get_id_by_name_url( _puu::$url_last );
            $goods = _shop::goods_one($id_goods, true);
            $view = 'shop_goods_one';                                           // представление по умолчанию
            if ( count($goods) == 2){
                $meta['title'] = 'Товар '.$goods[0]['name'];  
                // Пытаемся найти представление по номеру категории 
                $found = false;
                for($t=count($bread)-1; $t > 0; $t--){
                    //echo $bread[$t]['category_id'].'<hr>';
                    $pattern  = VIEW.'shop_goods_one_'.$bread[$t]['category_id'].'.php';     // представление для каждой отдельной категории
                    if ( file_exists($pattern) ){
                        $view = 'shop_goods_one_'.$bread[$t]['category_id'];
                        $found = true;
                        break;
                    }
                }
                /*
                $pattern  = VIEW.'shop_goods_one_'.$goods[0]['category_id'].'.php';     // представление для каждой отдельной категории
                if ( file_exists($pattern) ){
                    $view = 'shop_goods_one_'.$goods[0]['category_id'];
                }
                */
            }
            // TODO: в url например так: /shop/..../goods/?id=1265
            if ( count($goods) > 2){
                $view = 'shop_goods_parent_child';
            }
        }else{
            /* Категории */
            /* список дочерних категорий */
            $list_child_category = _shop::category( $id_category );
            /* отбор по товарам */
            $where = array();
            $where[] = array('column'=>'category_id','value'=>$id_category);
            /* информация о товарах с учетом отбора */
            $goods_inf = _shop::goods_inf($where);                                  // Всего записей в категории и т.д.
            $goods_per_page = _shop::settings_load('goods-per-page', 10);           // Количество строк на страницу

            if (isset($_GET['page']) && $_GET['raw'] == yes){                       // запрос из ajax
                /* готовим список товаров категории */
                $start = $_GET['page'] * $goods_per_page - $goods_per_page;
                $view = 'shop_goods_part';                                              // как-быдкто представление дял части списка товаров
            }else{
                _pp::add_script_to_header( URL_VIEW.'js/jquery-ui.min.js');
                _pp::add_script_to_header( URL_VIEW.'js/jquery.jqpagination.min.js' );
                _pp::add_script_to_header( URL_VIEW.'js/jquery.anim.color.js' );        // Отличная штука для анимации цветов 
                _pp::add_script_to_header( URL_VIEW.'js/shop.js' );
                _pp::add_script_to_header( URL_VIEW.'js/shop-find.js' );
                _pp::add_css_to_header( URL_VIEW.'css/jqpagination.css' );
                _pp::add_css_to_header( URL_VIEW.'css/jquery-ui.min.css');
                _pp::add_css_to_header( URL_VIEW.'css/jquery-ui.theme.min.css');

                /* текущая страница - если вызываем через get запрос*/
                $current_page = 0;
                if (isset($_GET['page'])){
                    $current_page = $_GET['page'];
                }else if (count($goods_inf['count']) > 0 ){
                    $current_page = 1;
                }
                $start = $current_page * $goods_per_page - $goods_per_page; 
                // TODO: тут или страница раздела, или страница раздела и список подразделов или товаров
                // Если товар не нужен, то в шаблоне shop.php убираем вставку shop_goods_part.php , заодно и запрос на товар тоже убрать
            }
            $goods = _shop::goods($start, $goods_per_page, $where );


            $meta['title'] = $cat_inf['category_name'].' купить';
        }
?>
