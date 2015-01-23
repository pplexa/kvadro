<?php defined('A') or die('Access denied'); ?>
<div class="content">
    <h2>Товары - экспорт</h2>
    <label for="path-export-market">Путь к файлу экспорта для market</label>
    <input class="common_propertys" type="text" name="path-export-market" id="path-export-market" value="<?php echo _shop::settings_load('path-export-market', '/'); ?>">
    <div class="act-button" id="do-yandex">Экспорт в ynadex market</div>
    <div id="result-yandex"></div>
</div>
<div id="ajaxw" style="position: absolute; display: none; background-color: #EFEFEF; border: 1px solid red; padding: 5px">
    <img src="/admin/view/default/images/close.jpg" id="ajaxx" style="display:block; float:right;">
    <div id="ajaxc"></div>
</div>
