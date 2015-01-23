<?php
/**
 * Контроллер Заказов
 * Обрабатываем запросы orders_
 */
switch ($view){
    case 'shop_orders':
        $orders = _shop::orders();
        
    break;
}