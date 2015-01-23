<?php
/**
 * link - точное совпадение по названию, категории, указаных колонок, поставщика - товар уже был когда-то добавлен
 * link-parent - совпадение по названию, категории, указаных колонок с головным товаром - товар никогда не добавлялся, добавлять можно как дочерний
 */
foreach( $price_tmp as $rcd){
    $class = "";
    if ($rcd['link_parent'] > 0){   $class = "tmp-price-link-parent";    }
    if ($rcd['link'] > 0){          $class = "tmp-price-link";    }
    
    echo "<tr>\n";
    echo '<td>';
    if ($rcd['link_parent'] <= 0 && $rcd['link'] <= 0){
        echo '<div class="add_one_row act-button" nid="'.$rcd['id'].'" sid="'.$one[id].'" id="sr-'.$rcd['id'].'">Новый</div>';
    }
    echo '</td>';
    echo '<td><div id="r'.$rcd['id'].'">wait</div></td>';
        foreach($rcd as $k=>$v){
            //$found = false;
            for($rc=0; $rc<count($list_columns); $rc++){
                if ($k == $list_columns[$rc]['name_column'] || $k == 'id'){
                    echo '<td class="'.$class.'">'.$v.'</td>';
                    //$found = true;
                    break;
                }
            }
            //if (!$found){
                //echo '<td></td>';
            //}
        }
    echo "</tr>\n";
}
?>    