<?php
/**
 * Класс с вспомогательными функциями и полезными примочками
 */
class _pp {

    function __construct() {
        
    }
    /**
     * Пробуем получить имя контроллера из названия представления
     * 
     * @param type $view
     * @return string путь к котроллеру
     */
    public static function get_admin_controller( $view ){
        $res = '';
        $a = pathinfo(ADMIN_VIEW.$view.'.php');
        $b = preg_split("/_/", $view);
        $filename = $a['dirname'].DIRECTORY_SEPARATOR.$b[0].DIRECTORY_SEPARATOR.$b[0].'_controller.php';
        if (file_exists( $filename )){
            $res = $filename;
        }else{
            $filename = $a['dirname'].DIRECTORY_SEPARATOR.$b[0].DIRECTORY_SEPARATOR.$b[1].'_controller.php';
            
            if (file_exists( $filename )){
                $res = $filename;
            }else{
                $filename = $a['dirname'].DIRECTORY_SEPARATOR.$b[0].DIRECTORY_SEPARATOR.$b[1].DIRECTORY_SEPARATOR.$b[1].'_controller.php';
                if (file_exists($filename)){
                    $res = $filename;
                }
            }
        }
        return $res;
    }
    /**
     * Добавить в специальную перменную $script
     * новый путь к скрипту
     * 
     * @global string Глобальная строка для добавления скриптов в заголовки страниц
     * @param type $url
     * @return string Если скрипт не существует возвратим пустую строку
     */
    public static function add_script_to_header( $url ){
        global $script;
        $res = $url;
        
        $script .= '<script type="text/javascript" src="'.$url.'"></script>'."\n";
        
        return $res;
    }
    
    /**
     * Добавить в специальную перменную $css
     * новый путь к файлу стилей
     * 
     * @global string Глобальная строка для добавления стилей в заголовки страниц
     * @param type $url
     * @return string Если файл стилей не существует возвратим пустую строку
     */
    public static function add_css_to_header( $url ){
        global $css;
        $res = $url;
        
        $css .= '<link rel="stylesheet" type="text/css" href="'.$url.'" />'."\n";
        
        return $res;
    }
    /**
     * Блоки категорий с возможность вывода статистики по категории
     * @param type $list_child_category
     * @param bool $with_stat показывать статистику
     */
    public static function html_child_category_list_as_block( $list_child_category, $with_stat = true ){
        foreach( $list_child_category as $o ){
            $stat = _shop::stat_get_min_max_count_category_child( $o['category_id'] );
            echo '<div class="category-block">';
            if ($stat['cnt'] > 0){
                echo '<h3><a href="/'._puu::$url.'/'.$o['category_name_e'].'">'.$o['category_name'].'</a></h3>';
            }else{
                echo '<h3>'.$o['category_name'].'</h3>';
            }
            if ($o['urlimg'] != '') {
                if ($stat['cnt'] > 0){
                    echo '<a href="/'._puu::$url.'/'.$o['category_name_e'].'"><img src="'.$o['urlimg'].'"></a>';
                }else{
                    echo '<img src="'.$o['urlimg'].'">';
                }
            }
            if ($with_stat){
                if ($stat['cnt'] > 0 ){
                ?>
    <div class="category-block-costs">от: <?php self::html_format_cost($stat['min']); ?><br>до: <?php self::html_format_cost($stat['max']); ?></div>
                <?php
                }else{
                ?>
    <div class="category-block-costs">временно нет</div>
                <?php    
                }
            }
            echo '</div>';
        }
    }
    /**
     * Формируем путь для медиа файлов в зависимости от размера
     * размеры:
     * _s /s
     * _m /m
     * _b /b
     * то есть, это префиксы для размеров файлов
     * @param type $namemedia
     * @param type $type
     */
    public static function media_get_name_by_type( $namemedia, $type = '_s' ){
        $a = pathinfo($namemedia);
        if ($type == '_s'){            $p = 's/';        }
        if ($type == '_m'){            $p = 'm/';        }
        if ($type == '_b'){            $p = 'b/';        }
        
        return URL_MEDIA.$p.$a['filename'].$type.'.'.$a['extension'];
    }
    
    /**
     * 
     * @param int $val сумма
     * @param bool $show В консоль или в переменную
     * @return string Строка форматированная как сумма товара
     */
    public static function html_format_cost( $val, $show = true ){
        $res = number_format( $val, 0, '', ' ').' руб.';
        if ( $show ){
            echo $res;
        }
        return $res;
    }
    /**
     * Показать хлебные крошки
     * @param type $bread
     * @return type
     */
    public static function html_bread( $bread ){
        $urls = _puu::$urls;
        $last_url = array_pop( $urls );                                             // последняя часть из url

        $arr_bread = array();
        $tmp = '';
        $found = false;
        foreach($bread as $r){
            $tmp .= '/'.$r['url'];
            if ($last_url == $r['url']){
                $arr_bread[] = '<span class="bread active">'.$r['name'].'</span>';
                $found = true;
            }else{
                $arr_bread[] = '<a class="bread" href="'.$tmp.'">'.$r['name'].'</a>';
            }
        }
        // В адресе URL больше адресов чем в крошках - наверное это или товар или еще что-то
        if (count( _puu::$urls ) > count($bread) ){
            for($t=count($bread); $t < count( _puu::$urls ); $t++){
                $tmp .= '/'._puu::$urls[$t];
                if ($last_url == _puu::$urls[$t]){
                    $good = _shop::goods_one( _shop::goods_get_id_by_name_url( $last_url ) );
                    if (count($good)>0){
                        // Есть товар по url
                        $arr_bread[] = '<span class="bread active">'.$good[0]['name'].'</span>';
                    }else{
                        $arr_bread[] = '<span class="bread active">'._puu::$urls[$t].'</span>';
                    }
                    $found = true;
                }else{
                    $arr_bread[] = '<a class="bread" href="'.$tmp.'">'._puu::$urls[$t].'</a>';
                }
            }
        }
        $html_bread = implode('&nbsp;|&nbsp;',$arr_bread);
        return $html_bread;
    }    
}