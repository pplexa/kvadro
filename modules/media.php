<?php
/**
 * Работа с медиа контентом
 * /s - маленькие фотографии
 * /m - средние
 * /  - оригиналы
 */
class _media {
    /**
     * @var string путь к дериктории медиа контента
     */
    private static $path = "";
    /**
     * @var string путь к дериктории медиа контента с маленькими картинками
     */
    private static $path_s = "";
    /**
     * @var string путь к дериктории медиа контента с средними картинками
     */
    private static $path_m = "";
    /**
     * @var string путь к дериктории медиа контента с большими картинками
     */
    private static $path_b = "";
    /**
     * @var string url медиа по-умолчанию
     */
    private static $url = "";
    /**
     * @var int колв-о интераций для создания уникального имени в media
     */
    private static $interation_count = 100;
    /**
     * @var string файл настройки медиа контента
     */
    private static $_setting_file = '';
    /**
     * @param int $_path
     * @param int $_url
     */
    public static function init( $_path = '', $_url = '', $_path_setting){
        self::$path = $_path;
        self::$path_s = self::$path.'s/';
        self::$path_m = self::$path.'m/';
        self::$path_b = self::$path.'b/';
        self::$url = $_url;
        self::$_setting_file = $_path_setting.'media.cfg';
        if (!file_exists(self::$_setting_file)){
            $src = array();
            file_put_contents(self::$_setting_file, json_encode($src));
        }
    }
    
    /**
     * Список медиа файлов из базы данных 
     *  
     * @param int $from параметр limit
     * @param int $to параметр limit 
     * @param string $order сортировка, по-умолчанию id
     * @param int $id id картинки
     * @return Array
     */
    public static function listmedia($from = -1, $to = -1, $order = ' `id` ', $id = -1){
        $query = "select `id`, `filename`, `alt` from media ";
        if ($id > -1){
            $query .= ' where id = '.$id;
        }
        if ( $order != ''){
            $query .= ' order by '.$order;
        }
        $query = _db::query($query);
        $res = $query->fetchAll(PDO::FETCH_ASSOC);
        // Добавляем результат запроса колонками указывающие где лежат копии файлов
        foreach($res as $k=>$v){
            $arr = pathinfo( $v['filename'] );
            $f_name = $arr['filename'];
            $f_ext  = $arr['extension'];
            $res[$k]['url_original'] = URL_MEDIA.$f_name.'.'.$f_ext;
            $res[$k]['url_small'] = URL_MEDIA.'s/'.$f_name.self::settings_load('small-prefix', '_s').'.'.$f_ext;
            $res[$k]['url_medium'] = URL_MEDIA.'m/'.$f_name.self::settings_load('small-medium', '_m').'.'.$f_ext;
            $res[$k]['url_big'] = URL_MEDIA.'b/'.$f_name.self::settings_load('small-big', '_b').'.'.$f_ext;
        }
        return $res;
    }
    /**
     * Сохраняем файл, создаем три копии с разными размерами
     * 
     * @param array $filename $_FILES[]
     * @return mixed boolean false ошибка загрузки, или путь к сохраненному файлу
     */
    public static function upload($fl){
        include_once PATH_LIB.'SimpleImage.php';
        $res = 'SAVED:'.$fl['name'].' tmp:'.$fl['tmp_name'];
        $filename = self::getGenerateName($fl['name']);
        if( !copy($fl['tmp_name'], $filename ) ){
            $res = false;
        }else{
            $arr = pathinfo($filename);
            $r_filename  = $arr['filename'];    // настоящее имя файла
            $r_extension = $arr['extension'];
            // Меняем размер и сохраняем
            $sw = self::settings_load('small-width', 150);
            $sh = self::settings_load('small-height', 100);
            $sp = self::settings_load('small-prefix', '_s');
            
            $image = new SimpleImage(); 
            $image->load( $filename ); 
            $image->resizeTo( $sw, $sh );
            
            $sfilename = self::$path_s.$r_filename.$sp.'.'.$r_extension;
            $image->save( $sfilename );
            // Меняем размер и сохраняем
            $mw = self::settings_load('medium-width', 150);
            $mh = self::settings_load('medium-height', 100);
            $mp = self::settings_load('medium-prefix', '_m');
            
            $image = new SimpleImage(); 
            $image->load( $filename ); 
            $image->resizeTo( $mw, $mh );
            
            $mfilename = self::$path_m.$r_filename.$mp.'.'.$r_extension;
            $image->save( $mfilename );
            // Меняем размер и сохраняем
            $bw = self::settings_load('big-width', 150);
            $bh = self::settings_load('big-height', 100);
            $bp = self::settings_load('big-prefix', '_b');
            
            $image = new SimpleImage(); 
            $image->load( $filename ); 
            $image->resizeTo( $bw, $bh );
            
            $bfilename = self::$path_b.$r_filename.$bp.'.'.$r_extension;
            $image->save( $bfilename );

            // /home/dtohxcov/pplexa.ru/test.root/media/Tulips_dhi.jpg
            $query = _db::query("insert into media (filename) value (?)", array( $r_filename.'.'.$r_extension ) );
            $id = _db::$db->lastInsertId();
            
            $res = array();
            $res['filename'] = $r_filename.'.'.$r_extension;
            $res['id'] = $id;
            $res['url_small'] = URL_MEDIA.'s/'.$r_filename.$sp.'.'.$r_extension;
            $res['1']=1;
        }
        //$res = pathinfo( $filename );
        return $res;
    }
    /**
     * Уданм файл медиа
     * @param type $id
     * @return boolean
     */
    public static function delete($id){
        $data = self::listmedia(-1, -1, '', $id);
        unlink( PATH_MEDIA. $data[0]['filename']);
        $query = _db::query("delete from media where id = ".$id);
        return true;
    }
    /**
     * Сгенерить новое уникальное имя в папки media или оставить прежнее
     * 
     * @param String $name Изначальное имя, загружаемого файла
     * @return string
     */
    private static function getGenerateName( $name ){
        //TODO: не очень правильный алгоритм создания уникальных имен - переделать
        $filename = self::$path.$name;
        $path_parts = pathinfo( $filename );
        if ($path_parts['extension'] == ''){
            $path_parts['extension'] = 'dat';
        }
        $path_parts['basename'] = _puu::translit( $path_parts['basename'] );    // называние в латиницу
        $interation = 0;
        $res = false;
        while( true ){
            if (file_exists( $filename ) ){
                $filename = self::$path. basename( $path_parts['basename'], '.'.$path_parts['extension'] ).'_'. self::generateRandomString(3).'.'.$path_parts['extension'];
            }else{
                $res = $filename;
                break;
            }
            if ( $interation > self::$interation_count ){
                $res = false;
                break;
            }
        }
        return $res;
    }
    /**
     * Генерим случайную строку из $length символов
     * @param int $length
     * @return string
     */
    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz'; //ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
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
}

