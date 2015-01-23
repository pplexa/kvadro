<?php
switch ($view){
    case 'shop_settings':
        $goods_per_page = _shop::settings_load('goods-per-page', 12);
        echo 'this is a controller';
    break;
    case 'shop_settings_ajax':
        _shop::settings_save($_POST['common'], $_POST['val']);
        echo 'Настройка '.$_POST['common']. ' сохранена';
        exit();
    break;
}