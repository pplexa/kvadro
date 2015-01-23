<?php
/**
 * Модуль магазина
 * 
 * управления категориями   category    +
 * поставщиками             salers
 * товарами                 goods
 * свойствами товаров       properties  +
 * заказы                   orders
 */
class _shop {
    private static $_path = '';
    private static $_setting_file = '';
    
    function __construct() {
        
    }
    
    public static function init( $_path = '' ){
        self::$_path = $_path;
        self::$_setting_file = self::$_path.'shop.cfg';
        if (!file_exists(self::$_setting_file)){
            $src = array();
            file_put_contents(self::$_setting_file, json_encode($src));
        }
    }
    /**
     * Добавить файл медии к товару
     * @param type $id_media
     * @param type $id_goods
     * @return boolean
     */
    public static function media_add( $id_media, $id_goods){
        $q = _db::query("insert into shop_goods_media (id_media, id_goods) values (?,?)", array($id_media, $id_goods));
        return true;
    }
    /**
     * Удалить привязку медиа файла и товара
     * @param type $id_media
     * @param type $id_goods
     * @return boolean
     */
    public static function media_del( $id_media, $id_goods){
        $q = _db::query("delete from shop_goods_media where id_media = ? and id_goods = ?", array($id_media, $id_goods));
        return true;
    }
    /**
     * TODO: название файла !!!!! _s - берется из модуля медии - как быть?
     * Список медиа файлов
     * @param type $id_goods
     * @return type
     */
    public static function media_list( $id_goods ){
        $q = "select m.`id`, m.`filename` from shop_goods_media g join media m on g.id_media = m.`id` where g.id_goods = ?";
        $q = _db::query( $q, array($id_goods));
        $r = $q->fetchAll(PDO::FETCH_ASSOC);
        foreach($r as $k=>$v){
            $p = pathinfo( $v['filename'] );
            $nfile = $p['filename'].'_s.'.$p['extension'];
            $r[$k]['url_small'] = URL_MEDIA.'s/'.$nfile;
        }
        return $r;
    }
    /**
     * Получить номер товара по его имени в url
     * @param type $name_url
     * @return int
     */
    public static function goods_get_id_by_name_url( $name_url ){
        return _db::query_onevalue("select min(`id`) from shop_goods where name_url = ?", array($name_url) );
    }
    /**
     * Изменяем данные по товару
     * Товары хранятся в таблицах shop_goods shop_about
     * @param type $arr - массив массивов с названия колонок и занчениями для изменения
     * @return mixed
     */
    public static function goods_update( $arr ){
        $cm = $arr;
        unset($cm['about']);
        // Проверить! В name_url при parent_id = -1 только уникальные значения!
        $cnt = _db::query_onevalue("select count(1) as cnt from shop_goods where `id` <> ? and parent_id = -1 and name_url = ?", array($arr['id'], $arr['name_url']) );
        if ( $cnt > 0){
            return "Не уникальное значение в название URL.\nИзменить значение";
        }
        _db::table_update_by_array('shop_goods', $cm);
        
        if (isset( $arr['about']) ){
            $id_about = _db::query_onevalue("select ifnull(`id`,-1) from shop_goods_about where goods_id = ?", array( $arr['id'] ));
            if ($id_about > 0){
                _db::query("update shop_goods_about set about = ? where id = ?", array($arr['about'], $id_about));
            }else{
                _db::query("insert into shop_goods_about (goods_id, about) values( ? , ? )", array($arr['id'], $arr['about']));
            }
        }
        return true;
    }
    /**
     * Получаем всю информация о товаре
     * Используется и в админской части и в клиентской
     * @param type $id
     * @param bool true - для клиентов, добавляем цену и всю лубуду для клиентов
     * @param bool $only_first_row только первая строка или все 
     * @return array
     */
    public static function goods_one( $id, $for_client = false, $only_first_row = false ){
        $limit = "";
        if ($only_first_row){
            $limit = " limit 0, 1 ";
        }
        if ($for_client){
            /**
             * Для клиентов
             * Товар устроен так: первая строка - строка общего товара, где упоминается название, номер товара,
             * url, номе категории и поставщик. Все остальные строки - это строки конкретных товаров с конкретными свойствами
             * например строка 0: 56, Трактор, МТЗ, КатегорияТракторы.
             * последующие строки (1-n): 
             * 1056, 56, ЦветКрасный, Масса100т
             * 1057, 56, ЦветСиний, Масса90т
             * В первой строке указывается списокмедиа файлов - media
             */
            /* TODO:
             * в выводе не должно быть упоминания о закупочной цене, наценках  и т. д.
             */
            $query = "SELECT 
                    g.*, 
                    sga.about, 
                    ( SELECT GROUP_CONCAT(m.filename, ',') FROM shop_goods_media sgm LEFT JOIN media m ON sgm.id_media = m.`id` WHERE sgm.id_goods = g.`id` ) AS media,
                    g.cost_out
                FROM shop_goods g 
                LEFT JOIN shop_goods_about sga ON g.`id` = sga.goods_id
                LEFT JOIN shop_suppliers ss ON g.supplier_id = ss.`id`
                WHERE g.`id` = ? OR g.`id` = ( select max(`id`) from shop_goods where parent_id = ? and cost_in = (select min(cost_in) from shop_goods where parent_id = ? and cost_out > 0 ) )
                ORDER BY g.parent_id".$limit;
            $res = _db::query($query, array($id, $id, $id));
        }else{
            $query = "select g.*, a.about from shop_goods g left join shop_goods_about a on g.`id` = a.goods_id where g.`id` = ? or g.parent_id = ? order by g.parent_id".$limit;
            $res = _db::query($query, array($id, $id));
        }
        return $res->fetchAll(PDO::FETCH_ASSOC);        
    }
    /**
     * Информация по товарам с учетом условия отбора
     * Выводим в массив количство записей, уникальные значения колонок
     * @param array $where
     * @return array
     */
    public static function goods_inf( $where ){
        $res = array();
        $where[] = array( 'column'=>'parent_id', 'value'=>'-1');
        $res['count'] = _db::query_onevalue("select count(1) from shop_goods "._db::query_prepare_where($where));
        return $res;
    }
    /**
     * Список товаров
     * @param int $from limit от
     * @param int $per limit сколько строк
     * @param array $where массив с значениями условия отбора [[column=>x,value=>y],...]
     * @param string $where_str строка sql запроса в условие
     * @return array
     */
    public static function goods($from = 0, $per = 10, $where = null, $where_str = ''){
        $where[] = array('column'=>'parent_id', 'value'=>-1);
        if ( $per == 0 ){
            $per = self::goods_count( $where );
        }
        $wh = _db::query_prepare_where($where, 'g1.');
        //$query = "select * from shop_goods ".$wh." limit ?, ?";
        
        $query = "SELECT g1.*, count(cc.cost_in) cnt_child, cc.cost_in as sell_cost_in, cc.amount as sell_amount, cc.active_id, max(mm.filename) as img, round(cc.cost_out,2) as cost_out
            
  FROM shop_goods g1 LEFT JOIN shop_goods g2 ON g2.parent_id = g1.id
  LEFT JOIN (

  SELECT i.parent_id, sg.cost_in, sg.amount, MAX(sg.id) active_id, sg.cost_in/100*ss.margin+sg.cost_in as cost_out FROM 
  shop_goods sg join
 (
 SELECT g1.`parent_id`, i.cost_in, MAX(g1.amount) AS amount
  FROM shop_goods g1 LEFT JOIN 
 (SELECT parent_id, MIN(cost_in) AS cost_in FROM shop_goods where parent_id > 0 and cost_out>0 GROUP BY parent_id ) AS i ON g1.parent_id = i.parent_id AND g1.cost_in = i.cost_in
WHERE i.parent_id IS NOT NULL
  GROUP BY g1.`parent_id`, i.cost_in) AS i
  ON sg.cost_in = i.cost_in AND sg.amount = i.amount
  JOIN shop_suppliers ss ON sg.supplier_id = ss.`id`
  GROUP BY i.parent_id  
  ) AS cc ON g1.`id` = cc.parent_id 
  
  LEFT JOIN shop_goods_media gm ON g1.`id` = gm.id_goods
  LEFT JOIN media mm ON gm.id_media = mm.id  
".$wh." ".$where_str." GROUP BY g1.id limit ?, ?";

        $res = _db::query($query, array($from, $per));
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Удалить товары по условию (параметры из javascript column,value)
     * @param type $where
     * @return boolean
     */
    public static function goods_delete($where = null){
        $wh = '';
        if ($where !== null){   //   собираем параметры для отбора по параметрам
            $arrw = array();
            foreach($where as $w){
                $arrw[] = '`'.$w['column']."` = '".$w['value']."'";
            }
            $wh = 'where '.implode(" and ", $arrw);
        }
        $query = "delete from shop_goods ".$wh;
        $res = _db::query($query);
        return true;
    }
    /**
     * Количество товаров в справочнике
     * @parram array массив с значениями условия отбора [[column=>x,value=>y],...]
     * @param string $where_str строка sql запроса в условие
     * @return int 
     */
    public static function goods_count( $where = null, $where_str = ''){
        $wh = _db::query_prepare_where( $where );
        $query = "select count(1) from shop_goods ".$wh." ".$where_str;
//echo $query; 
        return _db::query_onevalue($query);
    }
    /**
     * Сохранить, конвертировать в json объект прайс
     * @param int $id_supplier Номер поставщика
     * @param string $filename Путь к загруженному прайсу
     * @return bool 
     */
    public static function price_save($id_supplier, $filename){
        $filename_src  = PATH_PRICE.$id_supplier.'.src';
        $filename_json = PATH_PRICE.$id_supplier.'.json';
        
        copy($filename, $filename_src);
        
        include_once PATH_LIB.'SimpleXLSX.php';
        $xlsx = new SimpleXLSX( $filename_src );
        $data = $xlsx->rows();
        // Если есть настройка price_added_column - добавим в загружаемый прайс нужное количество колонок
        $supplier_inf = _shop::suppliers_one( $id_supplier );
        $rule = json_decode( $supplier_inf['rule_price'], true );
        if( $rule['price_added_column'] > 0){
            $crow = count($data);
            for($r = 0; $r < $crow; $r++){
                for($n = 0; $n < $rule['price_added_column']; $n++){
                    $data[$r][] = 'empty';
                }
            }
        }
        file_put_contents( $filename_json ,json_encode($data) );

        //TODO: Проверка типа файла
        
        return true;
    }
    /**
     * Последний загруженный прайс поставщика как объект json
     * @param int $id_supplier - id поставщика
     * @return mixed false - проблемы, array - прайс
     */
    public static function price_header($id_supplier){
        $filename_json = PATH_PRICE.$id_supplier.'.json';
        if (file_exists($filename_json)){
            $data = file_get_contents($filename_json);
            $res = json_decode($data, true);
        }else{
            $res = false;
        }
        return $res;
    }
    /**
     * Обработать прайс с учетом настроек по поставщику и данные занести во временную таблицу shop_goods_tmp
     * @param int $id_supplier Номер поставщика
     * @return array ['time'=>время выполнения, 'cnt'=>количество строк в прайсе]
     */
    public static function price_pre_process($id_supplier){
        // собираем уникальные значение в массиве
        function in_check($list, $val){
            foreach($list as $v){
                if ($v == $val){
                    return true;
                }
            }
            return false;
        }
        
        $res = array();
        $tstart = time();
        $price = _shop::price_header( $id_supplier );
        
        $l = _shop::price_columns_scripts($id_supplier);
        $scripts = $l['scripts'];//array();     // номера колонок со скриптами 
        $columns = $l['columns'];//array();     // номера колонок с колонкаи у которых указаны свойства для загрузки
        
        $scol = count( $columns );
        $srow = count( $scripts );
        $crow = count( $price );
        $ccol = count( $price[0] );
        for($r = 0; $r < $crow; $r++){             // строки                                                                ////////////////////////////////// или 10 строк
            for ($c = 0; $c < $ccol; $c++){     // колонки
                for($s = 0; $s < $srow; $s++){  // скрипты для ячеек
                    if ( $scripts[$s]['column'] == $c){
                        $rcell = $price[$r][$c];    // $rcell - значение для обратного присвоения 
                        $cell = $price[$r][$c];     // $cell - значение ячейки до обработки скриптами
                        
                        eval( $scripts[$s]['script'] );
                        
                        $price[$r][$c] = $rcell;
                    }
                }
            }
            /* в вывод только колонки у которых было указано свойство (или все строки: $res[] = $price[$r]; ) */
            $nrow = array();//Наименование
            for($l = 0; $l < $scol; $l++){
                if ( trim($price[$r][$columns[$l]['column']]) == '' ){          // Пустые колонки не принимаются
                    $nrow = array();
                    break;
                }
                $nrow[] = $price[$r][$columns[$l]['column']];
                $columns[$l]['pocess_column'] = $l;
                if ( !in_check($columns[$l]['distinct'], $price[$r][$columns[$l]['column']]) ){  // собираем уникальные значения в колонке
                    $columns[$l]['distinct'][] = $price[$r][$columns[$l]['column']];
                }
            }
            /* Пустые колонки не принимаются */
            if ( count($nrow) > 0){
                $res[] = $nrow;
            }
        }

        // Заносим все данные во временную таблицу
        function get_in_name_column($list, $num){
            foreach($list as $r){
                if ($r['pocess_column'] == $num){
                    return $r['name_column'];
                }
            }
        }
        _db::query("delete from shop_goods_tmp where supplier_id = ?", array($id_supplier));
        foreach( $res as $r){
            $query = "insert into shop_goods_tmp (";
            foreach( $r as $k=>$v){
                $query .= '`'.get_in_name_column($columns, $k).'`,';
            }
            $nr = array();
            foreach($r as $rr){
                $nr[] = "'".$rr."'";
            }
            $query .= 'supplier_id) values ('.  implode(',', $nr).",'".$id_supplier."')";
            _db::query($query);
        }
        
        $tend = time();
        $result['time'] = $tend - $tstart;
        $result['cnt'] = count($res);
        return $result;
    }
    /**
     * Переносим прайс из временной таблицы в рабочую
     * @param type $id_supplier
     */
    public static function price_process( $id_supplier ){
        //
        //  процесс непосредственно  загрузки парйса
        //        
        $column_script = _shop::price_columns_scripts($id_supplier, true);
        $lc  = array();
        foreach($column_script['columns'] as $l){                               // собираем динамические свойства для запроса 
            $t = 't.`'.$l['name_column'].'`';
            $g = 'g.`'.$l['name_column'].'`';
            $g2 = 'g2.`'.$l['name_column'].'`';
            $lc['l'][] = '`'.$l['name_column'].'`';
            $lc['t'][] = $t;
            $lc['g'][] = $g;
            $lc['g2'][] = $g;
            $lc['eq'][] = $t.'='.$g;
            $lc['eq2'][] = $t.'='.$g2;
        }
        if (count($lc) == 0){
            echo 'нет ни одного динамического свойства - грузить не можем!';
            exit();
        }
        // Добавим все не достающие товары товары
        // Вроде тут еще должно быть условие определяющие уникальность товара
        // название-производитель TODO: разобратся!
        $query = "insert into shop_goods (".implode(",", $lc['l']).", `name`, `supplier_id`, `category_id`, `parent_id`) 
                select ".implode(",", $lc['t']).", t.`name`, t.`supplier_id`, t.`category_id`, g.`id`
                from shop_goods_tmp t left join shop_goods g 
                    on t.`name` = g.`name` 
                    and t.`category_id` = g.`category_id` 
                    and g.parent_id  = -1
                LEFT JOIN shop_goods g2 
                    ON t.`name` = g2.`name`
                    and t.`category_id` = g2.`category_id`
                    and ".  implode(' and ', $lc['eq2'])."
                    and g2.parent_id > 0
                where t.supplier_id = ? AND g.`id` IS NOT NULL AND g2.`id` IS null";
        /*
        $query = "insert into shop_goods (".implode(",", $lc['l']).", `name`, `supplier_id`, `category_id`) select ".implode(",", $lc['t']).", t.`name`, t.`supplier_id`, t.`category_id`
                from shop_goods_tmp t left join shop_goods g 
                on t.supplier_id = g.supplier_id 
                and t.`name` = g.`name` 
                and t.`category_id` = g.`category_id` 
                and g.parent_id  = -1
                and ".  implode(' and ', $lc['eq'])."
                where t.supplier_id = ? and ifnull(g.`id`,-1) = -1";
        echo $query;
        */
        $res = _db::query($query, array($id_supplier));
        
        /* остатки и цены в ноль */
        $query = "update shop_goods set amount=0, cost_in=0, cost_out=0 where supplier_id = ?";
        $res = _db::query($query,array($id_supplier));
        /* меняем остатки и цены из прайса */
        $query = "update shop_goods_tmp t left join shop_goods g on t.supplier_id = g.supplier_id 
                join shop_suppliers s on t.supplier_id = s.`id` 
                and t.`name` = g.`name` 
                and t.`category_id` = g.`category_id` 
                and ".  implode(' and ', $lc['eq'])."
                SET 
                    g.cost_in = t.cost_in, 
                    g.amount = t.amount,
                    g.cost_out = t.cost_in+(t.cost_in/100*s.margin)
                where t.supplier_id = ?";
        echo $query;
        $res = _db::query($query, array($id_supplier));
        return true;
    }
    /**
     * Получаем список колонок и список скриптов для колонок загружаемого прайса
     * @param type $id_supplier номер поставщика
     * #param boolean true - только динамические свойства, false все свойства
     * @return array массив колонок и скриптов
     */
    public static function price_columns_scripts($id_supplier, $type = false){
        $sup = _shop::suppliers_one( $id_supplier );
        $rules = json_decode( $sup['rule_price'], true);
        $propertys = $rules['propertys'];
        
        $scripts = array();     // номера колонок со скриптами 
        $columns = array();     // номера колонок с колонкаи у которых указаны свойства для загрузки
        if (count($propertys) > 0){
            foreach($propertys as $r){
                if (trim($r['column']) != ''){
                    if ($type && $r['idproperty'] < 0){
                        continue;
                    }
                    if (trim($r['idproperty']) != 0){
                        $spp = _shop::propertys(false, $r['idproperty']);
                        $r['name'] = $spp[0];
                        $r['name_column'] = $spp[1];
                        $r['distinct'] = array();   // сюда пишем уникальные значения в колонке
                        $columns[] = $r;
                    }
                    if ( trim($r['script']) != '' ){
                        $scripts[] = $r;
                    }
                }
            }
        }
        return array('columns'=>$columns, 'scripts'=>$scripts);
    }
    /**
     * Прайс из временной таблицы
     * @param int $id_supplier Номер поставщика
     * @param int $from limit 
     * @param int $default_row limit 
     * @param array $prop - свойтсва загрузки прайса
     * @return mixed Array or Bool 
     */
    public static function price_show_from_tmp($id_supplier, $from = 0, $default_row = 10, $prop = array() ){
        $res = false;
        // колокни для определения уникальности товара
        $u_columns = self::price_tmp_get_unicum_rows( $prop );
        $unicum_cols = '';
        foreach( $u_columns as $col){
            $unicum_cols .= 'and t.`'.$col.'` = g.`'.$col.'`';
        }
//echo $unicum_cols;
        // колонки динамических и постоянных свойств
        $columns = self::price_columns_for_sql( $id_supplier, 't.' );
        if ( count($columns) > 0 ){
            $query = "SELECT
                    t.id, ". implode(',', $columns) .", ifnull(g.id, -1) as link, ifnull(g2.id, -1) as link_parent
                FROM 
                    shop_goods_tmp t LEFT JOIN shop_goods g ON t.`name` = g.`name` AND t.category_id = g.category_id AND g.parent_id > 0 AND g.supplier_id = ? ".$unicum_cols."
                    LEFT JOIN shop_goods g2 ON t.`name` = g2.`name` AND t.category_id = g2.category_id AND g2.parent_id = -1
                WHERE 
                    t.supplier_id = ? 
                ORDER BY t.`id`
                LIMIT ?, ?";
            $res = _db::query($query, array($id_supplier, $id_supplier, $from, $default_row));
            $res = $res->fetchAll(PDO::FETCH_ASSOC);
        }
        //echo $query;
        return $res;
    }
    public static function price_tmp_count( $id_supplioer ){
        return _db::query_onevalue("select count(1) from shop_goods_tmp where supplier_id = ?", array($id_supplioer));
    }
    /**
     * Получить список колонок для определения уникальности товара на основании свойств загрузки прайса
     * @param array $prop свойства через функцию _shop::suppliers_one
     * @return array
     */
    public static function price_tmp_get_unicum_rows( $prop ){
        $res = array();
        if (count($prop) > 0){
            foreach($prop as $k=>$v){
                if (is_string($k)){
                    if (preg_match("/row-unicum-(.*)/", $k, $arr) && $v == 'on'){
                        $res[] = $arr[1];
                    }
                }
            }
        }
        return $res;
    }
    /**
     * Копируем строку из временного прайса в реальный и
     * указанную строку ставим дочерней к добавленной
     * В заглавную строку товара, переносим только:
     * Название, категорию и выставляем parent_id = -1
     * и те колонки которые укажет пользователь
     * 
     * @param int $id - номер строки из таблицы временных прайсов shop_goods_tmp
     * @return boolean
     */
    public static function price_new_row( $id, $supplier_id ){
        $col = self::price_columns_for_sql( $supplier_id );
        $st = implode(',', $col);
        
        $col_main = self::price_columns_for_sql( $supplier_id, '', -1 );
        $st_main = implode(',', $col_main);
        //return print_r( $st_main, true );
        // Головной товар - добавляются только основные свойства
        _db::query("insert into shop_goods (`parent_id`, ".$st_main.") select -1, ".$st_main." from shop_goods_tmp where `id` = ?", array( $id ) );
        $insert_id = _db::$db->lastInsertId();
        // Наводим красоту
        //TODO: Напиши нормальный запрос - на хера два запроса????
        _db::query("update shop_goods set cost_in = null, amount = null where `id` = ?", array( $insert_id ) );
        // Дочерний
        _db::query("insert into shop_goods (`parent_id`, supplier_id,  ".$st.") select ".$insert_id.", supplier_id, ".$st." from shop_goods_tmp where `id` = ?", array( $id ) );
        return true; 
    }
    /**
     * Список динамических и постояных свойств по поставщику
     * @param type $id_supplier
     * @param string $prefix - перфикс для названий колонок
     * @param int 0 - все, -1 только основные, 1 - динамические
     * @return array
     */
    public static function price_columns_for_sql($id_supplier, $prefix = '', $type = 0){
        $prop = _shop::price_columns_scripts($id_supplier);
        $listcolumn = array();
        foreach($prop['columns'] as $r){
            if (
                    ( $type == -1 && $r['idproperty'] <= -1 ) ||
                    ( $type == 0 ) ||
                    ( $type == 1 && $r['idproperty'] >= 0  ) 
                )
            {
                $listcolumn[] = $prefix.'`'.$r['name_column'].'`';
            }
        }
        return $listcolumn;
    }
    /**
     * Список всех поставщиков
     * @return array 
     */
    public static function suppliers(){
        $query = _db::query("select `id`, `name`, `margin`, `rule_price`, `active` from shop_suppliers order by `id`");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Добавить нового поставщика
     * @param sttring $name
     * @param int $margin
     * @return int номер поставщика
     */
    public static function suppliers_add($name, $margin){
        $query = _db::query("insert into shop_suppliers (`name`, `margin`) values (?,?)", array($name, $margin));
        return _db::$db->lastInsertId();
    }
    /**
     * Удалить поставщика
     * @param int $id номер поставщика
     * @return boolean 
     */
    public static function suppliers_del($id){
        $query = _db::query("delete from shop_suppliers where `id` = ?", array($id));
        return true;
    }
    /**
     * Изменяем поставщика
     * @param type $id Номер 
     * @param type $name Имя
     * @param type $margin  Наценка
     */
    public static function suppliers_edit( $id, $name, $margin ){
        _db::query("update shop_suppliers set `name` = ?, `margin` = ? where `id` = ?", array($name, $margin, $id));
        return true;
    }
    /**
     * Получаем всю информацию по поставщику
     * @param int $id - номер поставщика
     * @return array
     */
    public static function suppliers_one($id){
        $query = _db::query("select `id`, `name`, `margin`, `rule_price`, `active` from shop_suppliers where `id` = ?", array($id) );
        $res = $query->fetchAll(PDO::FETCH_ASSOC);
        return $res[0];
    }
    /**
     * Сохранить общую настройку загрузки прайса
     * @param type $idsupplier  - id поставщика
     * @param type $name - название общей настройки
     * @param type $val - значение настройки
     * @return boolean
     */
    public static function suppliers_price_common_property_save($idsupplier, $name, $val){
        //echo $name.'=>'.$val;
        $supplier_inf = _shop::suppliers_one( $idsupplier );
        $rule = json_decode( $supplier_inf['rule_price'], true );
        $rule[$name] = $val;
        _db::query("update shop_suppliers set `rule_price` = ? where `id` = ?", array(json_encode($rule), $idsupplier) );
        return true;
    }
    /**
     * Сохранить настроку прайсов. Привязка свойст к колонкам загруженого прайса
     * @param type $idsupplier Поставщик
     * @param type $column Номер колонки в прайсе
     * @param type $idproperty Номер свойства
     * @param type $script скрипт обработки колонки прайса
     * @return type
     */
    public static function suppliers_price_property_save($idsupplier, $column, $idproperty, $script = null){
        $supplier_inf = _shop::suppliers_one( $idsupplier );
        $rule = json_decode( $supplier_inf['rule_price'], true );
        $found = false;
        for($t=0; $t<count($rule['propertys']); $t++){
            if ($rule['propertys'][$t]['column'] == $column){
                if ($idproperty != null){
                    $rule['propertys'][$t]['idproperty'] = $idproperty;
                }
                if ($script != null){
                    $rule['propertys'][$t]['script'] = trim($script);
                }
                $found = true;
                break;
            }
        }
        if (!$found){
            $r = array('column'=>$column);
            if ($idproperty != null){
                $r['idproperty'] = $idproperty;
            }
            if ($script != null ){
                $r['script'] = trim($script);
            }
            $rule['propertys'][] = $r;
        }
        _db::query("update shop_suppliers set `rule_price` = ? where `id` = ?", array(json_encode($rule), $idsupplier) );
        return $rule;
    }
    /**
     * Свойства товаров
     * Список динамических свойств товаров где name_column является дополнительной колонкой в таблице goods
     * Это сделано для того чтобы после настрйоки магазина, товары хранились в одной таблице, 
     * борьба с множеством связей между таблицами, Да избыточность будет зашкаливать, да и хер с ней, зато запросы сможет
     * составлять любой дибилоид с университетским образованием
     * @param boolean если true - добавляем постоянные свойства товаров Название(name), Цена входная(cost_in), Количество(amount)
     * @param int если нужно только название свойства но его номеру
     * @param boolean если нужны уникальные значения свойств из таблиц товаров
     * @return mixed или массив свойств или массив пара: название, название колонки
     */
    public static function propertys( $all = false, $id_name = 0, $distinct = false ){
        // Постоянные свойства
        // TODO: вынести в константы
        $pa = array();
        $pa[] = array('id'=>-1, 'name'=>'Название', 'name_column'=>'name', 'type'=>'varchar', 'len'=>'100');
        $pa[] = array('id'=>-2, 'name'=>'Цена вход', 'name_column'=>'cost_in', 'type'=>'decimal', 'len'=>'19, 2');
        $pa[] = array('id'=>-6, 'name'=>'Цена выход', 'name_column'=>'cost_out', 'type'=>'decimal', 'len'=>'19, 2');
        $pa[] = array('id'=>-3, 'name'=>'Количество', 'name_column'=>'amount', 'type'=>'int', 'len'=>'11');
        $pa[] = array('id'=>-4, 'name'=>'Категория', 'name_column'=>'category_id', 'type'=>'int', 'len'=>'10');
        $pa[] = array('id'=>-5, 'name'=>'URL', 'name_column'=>'name_url', 'type'=>'varchar', 'len'=>'500');
        

        if ($id_name != 0){
            if ($id_name<0){
                foreach($pa as $p){
                    if ($p['id'] == $id_name){
                        $res = array($p['name'], $p['name_column']);
                    }
                }
            }else{
                $res = _db::query("select `name`, `name_column` from shop_goods_property where `id`= ?", array($id_name));
                $res = $res->fetchAll(PDO::FETCH_NUM);
                $res = $res[0];
            }
        }else{
            $query = _db::query("select `id`, `name`, `name_column`, `type`, `len` from shop_goods_property order by `id`");
            $list = $query->fetchAll(PDO::FETCH_ASSOC);
            if ( $all ){
                foreach( $list as $row){
                    $pa[] = $row;
                }
                $res = $pa;
            }else{
                $res = $list;
            }
        }
        if ($distinct){
            for($r=0; $r<count($res); $r++){
                $res[$r]['distinct'] = _shop::propertys_distinct_value($res[$r]['name_column']);
            }
        }
        return $res;
    }
    /**
     * Получаем уникальные значения колонки свойств
     * @param string $name_column Название колонки свойства
     * @param bool $with_null нулевые знаечние добавлять
     * @return array
     */
    public static function propertys_distinct_value($name_column, $with_null = true){
        $query = "select distinct `".$name_column."` from shop_goods order by `".$name_column."`";
//        echo $query;
        $query = _db::query( $query );
        $src = $query->fetchAll(PDO::FETCH_NUM);
        $cnt = count($src);
        
        $res = array();
        if ($with_null){
            for($r=0; $r<$cnt; $r++){
                $res[] = $src[$r][0];
            }
        }else{
            for($r=0; $r<$cnt; $r++){
                if ($src[$r][0] == ''){
                    continue;
                }
                $res[] = $src[$r][0];
            }
        }
        return $res;
    }
    /**
     * Добавить свойство
     * Добавляется не только свойтсов, но и колонка в таблицу shops_goods
     * @param string $name - Название свойства
     * @param string $type - Тип свойства. string, int
     * @param int $len - Параметр для sql колонки 
     * @return boolean
     */
    public static function propertys_add($name, $type = 'int', $len = 11){
        // Название новой колонки в таблице товаров - свойство
        $name_column = "c_" . _db::query_onevalue("select ifnull(max(`id`),0)+1 from shop_goods_property");
        // првоерим наличие колонки в таблице товаров, если нет - добавим
        $check_column = _db::query('SHOW FIELDS FROM shop_goods');
        $check_column = $check_column->fetchAll(PDO::FETCH_ASSOC);
        $found = false;
        foreach($check_column as $row ){
            if ($row['Field'] == $name_column) {
                $found = true;
                break;
            }
        }
        // Свйоства нет, жобавим в зависимости от типа свойства
        if (!$found){
            if ($type == 'int'){
                $add_column = _db::query('ALTER TABLE shop_goods ADD '.$name_column.' int('.$len.')');
                $add_column = _db::query('ALTER TABLE shop_goods_tmp ADD '.$name_column.' int('.$len.')');
            }else{
                $add_column = _db::query('ALTER TABLE shop_goods ADD '.$name_column.' varchar('.$len.')');
                $add_column = _db::query('ALTER TABLE shop_goods_tmp ADD '.$name_column.' varchar('.$len.')');   
            }
        }
        // Првоерим запись о свойстве в таблице свойства
        $hv = _db::query_onevalue("select ifnull(count(1),0) from shop_goods_property where `name_column` = ?", array($name_column));
        if ($hv <= 0){
            _db::query("insert into shop_goods_property (`name`, `name_column`, `type`, `len`) values(?,?,?,?)", array($name, $name_column, $type, $len));
        }
        //$query = _db::query("insert into shop_goods_property (`name`, `name_column`) values(?,?)", array($name, $name_column));
        return true;
    }
    /**
     * Удалить свойство товара
     * @param type $id
     * @return boolean
     */
    public static function propertys_del($id){
        $name_column = _db::query_onevalue("select name_column from shop_goods_property where id = ?", array($id));
        $del_column = _db::query("ALTER TABLE shop_goods DROP ".$name_column);
        $del_column = _db::query("ALTER TABLE shop_goods_tmp DROP ".$name_column);
        $del_column = _db::query("delete from shop_goods_property where `id` = ?", array( $id ));
        return true;
    }
    /**
     * Выдать все дочерние узлы 
     * @param int $id номер категории 
     * @param int $with_parent включить $id в результат функции
     * @return array С номерами категорий
     */
    public static function category_get_child( $id, $with_parent = true ){
        $cu = _db::query_onevalue("SELECT (SELECT MAX(dlevel) from shop_category sc1 )-sc.dlevel AS cnt from shop_category sc WHERE sc.category_id = ?", array($id));
        if ($cu >= 0){
            $q = " SELECT s2.category_id as `id` FROM 
                    shop_category s1 
                    LEFT JOIN shop_category s2 ON s1.category_id = s2.parent_id
                    WHERE s1.category_id = ".$id." and s2.category_id IS NOT NULL ";
            if ($with_parent){
                $q =  "select ".$id." as `id` union ".$q;
            }
            for($t=0;$t<$cu;$t++){
                $q .= " UNION SELECT DISTINCT s".($t+3-1).".category_id FROM 
                        shop_category s1 
                        LEFT JOIN shop_category s2 ON s1.category_id = s2.parent_id";
                for($d=0;$d<$cu-1;$d++){
                    $q .= " LEFT JOIN shop_category s".($d+3)." ON s".($d+3-1).".category_id = s".($d+3).".parent_id";
                }
                $q .= " WHERE s1.category_id = ".$id." and s".($t+3-1).".category_id IS NOT NULL";
            }
        }
        $r = _db::query($q);
        return $r->fetchAll(PDO::FETCH_COLUMN);
    }
    /**
     * Список родительских категорий для URL
     * @return array [id]=>'/xxx/yyy/zzz'
     */
    public static function category_list_parents(){
        $l = self::category();
        
        $pref = array();
        $p_dlevel = 0;
        foreach($l as $o){
            if ( $p_dlevel < $o['dlevel'] ){
                $p_dlevel = $o['dlevel'];
                $pref[] = $o['category_name_e'];
            }elseif( $p_dlevel > $o['dlevel'] ){
                $p_dlevel = $o['dlevel'];
                array_pop($pref);
                array_pop($pref);
                $pref[] = $o['category_name_e'];
            }else{
                array_pop($pref);
                $pref[] = $o['category_name_e'];
            }
            $res[$o['category_id']] = implode('/', $pref);
        }
        return $res;
    }
    /**
     * Список названий категорий по списку их номеров
     * @param array $arr массив с номерами категорий
     * @return mixed Массив с названиями или null
     */
    public static function category_name_by_array_id( $arr ){
        $res = null;
        if ( count($arr) > 0 ){
            // TODO: Откуда сюда могут попасть пустые значения? - Избавляемся
            $arr = array_diff($arr, array(''));
            $w = implode(',', $arr);
            $q = _db::query("select category_id, category_name from shop_category where category_id in (".$w.") ");
            $r = $q->fetchAll(PDO::FETCH_ASSOC);
            foreach($r as $k){
                $res[$k['category_id']] = $k['category_name'];
            }
        }
        return $res;
    }
    /**
     * Категории. Список всех категорий или только родительской
     * @param int Номер родителькой категории
     * @return array Категории
     */
    public static function category( $parent_id = null ){
        if ( $parent_id !== null ){
            $query = _db::query("select category_id, category_name, category_name_e, urlimg, parent_id, dlevel from shop_category where parent_id = ? order by dorder", array($parent_id));
        }else{
            $query = _db::query("select category_id, category_name, category_name_e, urlimg, parent_id, dlevel from shop_category order by dorder");
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Максимальный номер категорий
     * нужен для javascript скрипта изменения категорий
     * @return int
     */
    public static function category_max_id(){
        $res = _db::query_onevalue("select max(category_id) from shop_category");
        if ($res == ''){
            $res = 0;
        }
        return $res;
    }
    /**
     * Категории. Сохранить структуру категорий
     * 
     * @param array Массив со структурой категорий
     * @return bool
     */
    public static function category_save( $json ){
        $dorder = 0;
        $in = array();
        $_SESSION['in'] = array();
        function inserta($parent, $id, $name, $name_e, $urlimg, $dorder, $dlevel){
            $query = _db::query("delete from shop_category where category_id = ?", array($id));
            $query = _db::query("insert into shop_category (category_id, category_name, category_name_e, urlimg, parent_id, dorder, dlevel) values (?, ?, ?, ?, ?, ?, ?)", array($id, $name, $name_e, $urlimg, $parent, $dorder, $dlevel));
        }
        function showa($parent, $a, $dlevel){
            global $in;
            global $dorder;
            foreach($a as $k=>$v){
                $dorder += 1;
                $_SESSION['in'][] = $v['id'];
                inserta($parent, $v['id'], $v['nam'], $v['nam_e'], $v['nui'], $dorder, $dlevel);
                if ( count($v['children'])>0 ){
                    $dlevel += 1;
                    showa( $v['id'], $v['children'], $dlevel );
                    $dlevel -= 1;
                }
            }
        }
        showa('-1', $json, 0, 0);
        if (count( $_SESSION['in'] ) > 0 ){
            $_SESSION['in'] = implode(',', $_SESSION['in']);
            $query = _db::query("delete from shop_category where category_id not in (".$_SESSION['in'].")");
        }
        
        return $res;
    }
    /**
     * поулчить номер категории по массиву из url
     * или получить массив для html "крошек"
     * @param boolean $needbread
     * @return array
     */
    public static function category_get( $needbread = false ){
        /* выяснеям номер категории - первый элемент в массиве _puu::$urls = shop удаляем его и выяснеям номер */
        $arr = _puu::$urls;
        array_shift( $arr );
        
        $cat = _shop::category();
        //$id = 0;
        //$name = '';
        $res = array();
        $bread = array();
        foreach( $cat as $r ){
            if ($r['category_name_e'] == $arr[0]){
                array_shift($arr);
                //$id = $r['category_id'];
                //$name = $r['category_name'];
                $bread[] = array('name'=>$r['category_name'], 'url'=>$r['category_name_e'], 'category_id'=>$r['category_id']);
                $res = $r;
            }
        }
        if ($needbread){
            array_unshift($bread, array('name'=>'Магазин','url'=>'shop'));      // TODO: 'name'=>'Магазин', shop - вынести в настраиваемые перменные
            return $bread;
        }else{
            return $res;
        }
    }
    /**
     * Сохранить настройку 
     * @param type $key Ключ
     * @param type $value Значение
     * @return boolean
     */
    public static function settings_save($key, $value){
        $file_content = file_get_contents( self::$_setting_file );
        $json = json_decode( $file_content, true );
        $json[$key] = $value;
        file_put_contents(self::$_setting_file, json_encode( $json ) );
        return true;
    }
    /**
     * Получить настройку
     * @param type $key Ключ
     * @param mixed $default Если ключа нет, выдаем значение по умолчанию
     * @return mixed
     */
    public static function settings_load( $key, $default = null ){
        $file_content = file_get_contents( self::$_setting_file );
        $json = json_decode( $file_content, true );
        if (isset($json[$key])){
            return $json[$key];
        }else{
            return $default;
        }
    }
    /**
     * Пересчитываем корзину 
     * В сессии
     * @return bool 
     */
    public static function cart_recalc(){
        $_SESSION['count'] = 0;
        $_SESSION['total'] = 0;
        if ( count($_SESSION['cart']) > 0 ){
            foreach($_SESSION['cart'] as $k=>$v){
                $_SESSION['count'] += $v['count'];
                $_SESSION['total'] += ($v['count'] * $v['cost']);
            }
        }
        return true;
    }
    /**
     * Добавим товарв корзину
     * @param int $id номер товара
     * @param int $count количество товара
     * @return boolean
     */
    public static function cart_add( $id, $count ){
        $goods = self::goods_one( $id, true );
        $_SESSION['cart'][$id]['count'] += $count;
        $_SESSION['cart'][$id]['cost'] = $goods[0]['cost_out'];
        self::cart_recalc();
        return true;
    }
    /**
     * Удалить товар из корзины
     * @param type $id Номер товара
     * @return boolean
     */
    public static function cart_delete( $id ){
        unset( $_SESSION['cart'][$id] );
        self::cart_recalc();
        return true;
    }
    /**
     * Сохраняем заказ из сессии
     * @return boolean
     */
    public static function order_add(){
        //$_POST['cellphone'];
        //$_SESSION['cart'][id] => (count, cost)
        if (isset($_SESSION['cart']) && isset($_POST['cellphone'])){
            if (count($_SESSION['cart']) > 0){
                // Сохраняем заказ
                // TODO: Вообщем-то заказы хранить прально надо - заказ и дочерние записи о товарах - а то мало-ли поиск по товарам делать придется
                $content = json_encode( $_SESSION['cart'] );
                $q = _db::query("insert into shop_orders (`date`, `phone`, `content`) values (now(), ?, ?)", array($_POST['cellphone'], $content));
            }
        }
        return true;
    }
    /**
     * Список заказов
     * @param type $from - для пагинации
     * @param type $per - для пагинации per page
     * @return array
     */
    public static function orders( $from = 0, $per = 100){
        $query = _db::query("select `id`, `date`, `phone`, `content`, `status` from shop_orders limit ?, ?", array($from, $per));
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Получаем минимальную и максимальную цену по все дочерним группам группы $id
     * @param int $id родительская группа
     * @return array
     */
    public static function stat_get_min_max_count_category_child( $id ){
        $res = null;
        $l = self::category_get_child($id);
        if ( count($l) > 0 ){
            $query = "select min(cost_out) as `min`, max(cost_out) as `max`, count(`id`) as `cnt` from shop_goods where category_id in (". implode(',', $l) .")";
            $q = _db::query( $query );
            $r = $q->fetchAll( PDO::FETCH_ASSOC );
            $res = $r[0];
        }
        return $res;
    }
}