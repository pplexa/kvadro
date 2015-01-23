<?php
/**
 * Класс - обертка для PDO
 * Дабы упростить мелочи всякие, вроде выполнения запросов получения данных
 * или проверки данных при запросе
 */
class _db {
    /**
     * Объект PDO
     * @var PDO
     */
    public static $db = NULL;
    
    /**
     * 
     * @param PDO $db
     */
    function __construct($db = NULL) {
        self::$db = $db;
    }
    /**
     * Иницилизиурем класс
     * коннект получаем из вне
     * @param PDO $db
     */
    public static function init($db){
        if (self::$db == NULL){
            self::$db = $db;
        }
        self::$db->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
    }
    /**
     * Выполнить mysql запрос к базе
     * 
     * @param string $query Запрос
     * @param array $param параметры запроса
     * @return PDOStatement Объект pdo
     */
    public static function query($query, $param = NULL){
        $qry = self::$db->prepare($query);
        $error = self::$db->errorInfo();
        if ( $error[0] > 0){
            echo $error[2];
            exit();
        }else{
            if (is_array($param)){
                $qry->execute( $param );
            }else{
                $qry->execute();
            }
        }
        return $qry;
    }
    /**
     * Выполнить mysql запрос к базе
     * и вернуть только одно значение первая колонка в первой строке
     * 
     * @param string $query Запрос
     * @param array $param параметры запроса
     * @return mixed Sting int 
     */    
    public static function query_onevalue($query, $param = NULL){
        $query = self::query($query, $param);
        $res = $query->fetch();
        // TODO: воткнуть проверку, если в ответе будет dataset
        return $res[0];
    }
    /**
     * Подготовить условие where
     * @param array $where массив колонка=>,значение=>,знаксравнения=>
     * @param string $prefix префикс для названий колонок
     * @return string
     */
    public static function query_prepare_where( $where, $prefix = ''){
        $wh = '';
        if ($where !== null){   //   собираем параметры для отбора по параметрам
            $arrw = array();
            foreach($where as $w){
                $sign = '=';
                if (isset($w['sign'])){
                    $sign = $w['sign'];
                }
                if ($w['value'] == ''){             // если пустая строка - то заодно проверяем на null
                    $arrw[] = '('.$prefix.'`'.$w['column']."` ".$sign." '".$w['value']."' or ".$prefix."`".$w['column']."` is null)";
                }else{
                    $arrw[] = $prefix.'`'.$w['column']."` ".$sign." '".$w['value']."'";
                }
            }
            $wh = 'where '.implode(" and ", $arrw);
        }        
        return $wh;
    }
    /**
     * Изменяем таблицу на основе массива.
     * Значения присваиваются как name=>value
     * 
     * @param type $table Имя таблицы
     * @param type $arr массив массивов [name=>x,value=>y]
     * @param type $keycolumn ключевое поле по которому изменям таблицу
     * @return boolean
     */
    public static function table_update_by_array($table, $arr, $keycolumn = 'id'){
        $kv = $arr[$keycolumn];
        unset($arr[$keycolumn]);
        $q = 'update '.$table.' set ';
        $listset = array();
        foreach($arr as $k=>$v){
          $listset[] = "`".$k."`='".$v."'";
        }
        $q .= implode(',', $listset). ' where `'.$keycolumn."` = '".$kv."'";
        self::query( $q );
        return true;
    }
}
