<?php
/**
 * Представление для админской части
 * $view - название представления из /admin/index.php
 * 
 * Здесь выбираем в зависимости от параметра $view какой модуль подключить
 */
if ($_REQUEST['raw'] != 'true'){
    include 'header.php';
    include 'leftbar.php';
}

$filename = ADMIN_VIEW.$view.'.php'; 
if (file_exists($filename)){
    include $filename;
    $res = true;
}else{
    $a = pathinfo($filename);
    $filename = $a['dirname'].DIRECTORY_SEPARATOR.$a['filename'].DIRECTORY_SEPARATOR.$a['basename'];
    if (file_exists($filename)){
        include $filename;
    }else{
        
        $b = preg_split("/_/", DIRECTORY_SEPARATOR.$a['filename']);
        // часть представления
        $filename = $a['dirname'].$b[0].DIRECTORY_SEPARATOR.$a['basename'];
        if (file_exists($filename)){
            include $filename;
        }else{
            if (count($b) >1){
                $filename = $a['dirname'].$b[0].DIRECTORY_SEPARATOR.$b[1].DIRECTORY_SEPARATOR.$b[1].'_'.$b[2].'.php';
                if (file_exists($filename)){
                    include $filename;
                }else{
                    $filename = $a['dirname'].$b[0].DIRECTORY_SEPARATOR.$b[1].DIRECTORY_SEPARATOR.$b[1].'.php';
                    if (file_exists($filename)){
                        include $filename;
                    }else{
                        echo 'Error. cant find view:'.$filename;
                    }
                }
            }
            
        }
        
    }
}
if ($_REQUEST['raw'] != 'true'){
    include 'footer.php';
}