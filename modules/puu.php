<?php
/**
 * Человеко понятные урлы
 */
class _puu {
    /**
     * Массив из url
     * @var arrat
     */
    public static $urls;
    /**
     * Первый параметр из url - он же модуль
     * @var string 
     */
    public static $page;
    /**
     * Полный урл до символа ?
     * @var string
     */
    public static $url;
    /**
     * Последее название в url
     * @var string
     */
    public static $url_last;
    
    function __construct() {
        
    }

    /**
     * Разбираем $_SERVER['REQUEST_URI']
     * 
     */
    public static function init(){
        $res = explode("?", $_SERVER['REQUEST_URI']);
        $res = explode("/", $res[0]);
        array_shift ($res); // всегда есть слэш в запрашиваемом url
        
        self::$urls = $res;         // список url
        self::$url_last = self::$urls[ count(self::$urls) - 1 ];
        
        if ($res[0] == ''){
            self::$page = 'index'; // модуль по умолчанию
        }else{
            self::$page = $res[0];
        }
        // режем последний слэш
        self::$url = implode("/", $res);
        if ( substr(self::$url, strlen(self::$url)-1, 1) == '/'){
            self::$url = substr(self::$url, 0, strlen(self::$url)-1);
        }
    }
    
    /**
     * Приводим русское название URL - к латинице, убираем пробелы и т.д.
     * @param string $to_url Название
     * @return string
     */
    public static function translit($to_url) {
        $trans = array('А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 
                       'Е' => 'E', 'Ё' => 'Jo', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I', 
                       'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 
                       'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 
                       'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch', 
                       'Ш' => 'Sh', 'Щ' => 'Shh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 
                       'Э' => 'Je', 'Ю' => 'Ju', 'Я' => 'Ja',

                       'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 
                       'е' => 'e', 'ё' => 'jo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 
                       'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 
                       'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 
                       'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 
                       'ш' => 'sh', 'щ' => 'shh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 
                       'э' => 'je', 'ю' => 'ju', 'я' => 'ja');

        $url = strtr($to_url, $trans);
        $url = mb_strtolower($url);	

        $url = preg_replace("/[^a-z0-9\s,]/i", "", $url);
        $url = preg_replace("/[,-]/ui", "-", $url);
        $url = preg_replace("/[\s]+/ui", "-", $url);

        return $url;
    }
}