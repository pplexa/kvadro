<?php
/**
 * Основоной файл представления
 * если передается параметр raw=yes - то не выводим html, body, head
 */

if ($_REQUEST['raw'] == 'yes'){    
    $include_file = VIEW.$view. '.php';
    if (file_exists($include_file)){
        include  $include_file;
    }else{
        echo 'not found'.$include_file;
    }
 }else{
     
     

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1"/>
    <title><?=$meta['title']?></title>
    <meta name="Description" content="<?=$meta['description']?>">
    <meta name="Keywords" content="<?=$meta['keywords']?>">    
    <link rel="shortcut icon" href="/favicon.ico"  type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="/views/kvadro/css/m.css" />
    <link rel="stylesheet" type="text/css" href="/views/kvadro/css/jquery.sidr.dark.css" />
    <script type="text/javascript" src="/views/kvadro/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="/views/kvadro/js/jquery.sidr.min.js"></script>
    <script type="text/javascript" src="/views/kvadro/js/s.js"></script>
    
    <?php echo $css; ?>
    <?php echo $script; ?>
<!-- script google uandex -->
<!-- script google analytics -->
    <script>
        var cart_count  = 0;
        var cart_amount = 0;
    </script>
</head>
    <body>
        <div id="back1">
        <div id="back2">
            <div class="header">
            <?php
                include_once 'header.php';
            ?>
            </div>
            <div class="wrapper">
                <a href="/"><div class="logo">Запчасти и аксессуары для мототехники, лодочные моторы</div></a>
                <div class="wrapper-adres"><strong>8(812)9814851</strong><br>E-mail: info@kvadrolife.ru</div>
                <a href="/cart"><div class="cart">товаров:<label id="cart-count"><?php echo $_SESSION['count']; ?></label><br><label id="cart-amount"><?php _pp::html_format_cost($_SESSION['total']); ?></label></div></a>
            </div>
            <div class="menu">
                <ul class="umenu">
                    <?php 
                        if($pages){
                            foreach($pages as $item){ 
                            if ( $item['url'] != 'Variator-belts' 
                                    && $item['url'] != 'SHini-dlya-kvadrotsikla' 
                                    && $item['url'] != 'Diski-dlya-kvadrotsiklov'
                                    && $item['url'] != 'Naduvnie-lodki'){
                    ?>
                                <li><a href="<?=URL?><?=$item['url']?>"><?=$item['title']?></a></li>
                    <?php
                                }
                            }
                        }
                    ?>
                </ul>                
                <a id="simple-menu" href="#sidr"><img src="/views/kvadro/images/mobile_menu_icon.gif"/></a>
            </div>
    <!-- mobilemenu -->
    <div id="sidr" class="sidr">
        <?php html_show_category( '', '', 'shop'); ?>
    </div>
            <div class="layout">
                <div class="sidebar">
                    <?php html_show_category( 'class="lmenu"', 'active', 'shop'); ?>
                </div>
                <div class="content">
                    <?php 
                        $include_file = VIEW.$view. '.php';
                        if (file_exists($include_file)){
                            include  $include_file;
                        }else{
                            echo 'not found'.$include_file;
                        }
                    ?>
                </div>
                <div class="page-buffer"></div>
            </div>
            <div class="footer"></div>
        </div>
        </div>
    </body>
</html>
<?php  
}
/**
 * показываем список категорий как дрвеовидное меню ul,li
 * TODO: хренотень, но работает
 */
function html_show_category( $ulclass = '', $a_active = '', $parent_a = ''){
    $urls = _puu::$urls;
    $stu = '/'.implode('/', $urls);
    
    $cat = _shop::category();
    
    echo "<ul ".$ulclass.">\n";
    foreach($cat as $rr){
        if ($rr['parent_id'] == -1){
            $pa = "/".$parent_a.'/'.$rr['category_name_e'];
            if ( $pa == $stu ){ $a_class = ' class="'.$a_active.'"'; }else{ $a_class = ''; }
            if ( have_child($rr['category_id'], $cat) ){
                echo "<li>\n";
                echo "<a href='".$pa."'".$a_class.">".$rr['category_name']."</a>\n";
                echo "<ul ".$ulclass.">\n";
                show_in($rr['category_id'], $cat, $pa, $a_active);
                echo "</ul>\n";
                echo "</li>\n";
            }else{
                echo "<li><a href=\"".$pa."\"".$a_class.">".$rr['category_name']."</a></li>\n";
            }
        }
    }
    echo '</ul>';
}
    function have_child($id, $list){
        foreach($list as $r){
            if ( $r['parent_id'] == $id){
                return true;
            }
        }
        return false;
    }
    function show_in($parent, $list, $parent_a, $a_active){
        $urls = _puu::$urls;
        $stu = '/'.implode('/', $urls);
        
        $inner = '';
        $na = '';
        foreach($list as $r){
            if ($r['parent_id'] == $parent){
                $na = $parent_a.'/'.$r['category_name_e'];
                if ( $stu == $na ){ $a_class = ' class="'.$a_active.'"'; }else{ $a_class = ""; }
                echo '<li><a href="'.$na.'"'.$a_class.'>'.$r['category_name'].'</a></li>'."\n";
                if (have_child($r['category_id'], $list) && $r['category_id'] != 29 ){
                    echo "<li>\n";
                    echo "<ul ".$ulclass.">\n";
                    show_in($r['category_id'], $list, $na, $a_active);
                    echo "</ul>\n";
                    echo "</li>\n";
                }           
            }
        }
    }
?>