<?php
/**
 * Контроллер 
 * Обрабатываем запросы goodsexport_
 */
switch ($view){
    case 'shop_goodsexport':
        _pp::add_script_to_header( ADMIN_URL.'js/shop/goodsexport.js' );
    break;
    case 'shop_goodsexport_do':
        /**
         * Export в yandex market
         */
        $query = "";
        $r = _db::query($query);
        $res = $r->fetchAll(PDO::FETCH_ASSOC);
        
/*
SELECT g.parent_id, c_19, g.c_22, g.c_23, g.c_24, g.c_25, g.c_26, g.c_27, g.c_28, g.c_28, g.c_29, g.c_31, g.c_32, min(g.cost_out) AS cost_out
FROM shop_goods g LEFT JOIN shop_goods gh ON g.parent_id = gh.id
WHERE g.parent_id > 0  AND g.cost_out > 0
GROUP BY g.parent_id, c_19, g.c_22, g.c_23, g.c_24, g.c_25, g.c_26, g.c_27, g.c_28, g.c_28, g.c_29, g.c_31, g.c_32
*/        
        
        echo 'строк:'.count($goods)."\n";
        print_r( $res );
        exit();
    break;
}