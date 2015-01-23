<?php defined('A') or die('Access denied'); ?>
<div class="content">
	<h2>Медиа</h2>
<?php
if(isset($_SESSION['answer'])){
    echo $_SESSION['answer'];
    unset($_SESSION['answer']);
}
?>
<h3>Настройки</h3>
<div>
    <form action="/admin/?view=media_setting" id="setting-form">
    <h4>Размер маленьких картинок</h4>
    <label>ширина:</label><input type="text" name="small-width" id="small-width" value="<?php echo _media::settings_load('small-width', 150)?>">
    <label>высота:</label><input type="text" name="small-height" id="small-height" value="<?php echo _media::settings_load('small-height', 100)?>">
    <label>префикс к файлу:</label><input type="text" name="small-prefix" id="small-prefix" value="<?php echo _media::settings_load('small-prefix', '_s')?>">
    <h4>Размер средних картинок</h4>
    <label>ширина:</label><input type="text" name="medium-width" id="medium-width" value="<?php echo _media::settings_load('medium-width', 150)?>">
    <label>высота:</label><input type="text" name="medium-height" id="medium-height" value="<?php echo _media::settings_load('medium-height', 100)?>">
    <label>префикс к файлу:</label><input type="text" name="medium-prefix" id="medium-prefix" value="<?php echo _media::settings_load('medium-prefix', '_m')?>">
    <h4>Размер больших картинок</h4>
    <label>ширина:</label><input type="text" name="big-width" id="big-width" value="<?php echo _media::settings_load('big-width', 150)?>">
    <label>высота:</label><input type="text" name="big-height" id="big-height" value="<?php echo _media::settings_load('big-height', 100)?>">
    <label>префикс к файлу:</label><input type="text" name="big-prefix" id="big-prefix" value="<?php echo _media::settings_load('big-prefix', '_b')?>"><br>
    <label>Элементов на странице:</label><input type="text" name="row_per_page" id="row_per_page" value="<?php echo _media::settings_load('row_per_page', 10)?>"><br>
    <div id="setting-save" class="act-button">Сохранить</div>
    </form>
</div>
<div id="upload" class="uploader">
    <div>Put here</div>
    <div class="browser">
        <label>
            <span>Искать</span>
            <input type="file" name="files[]" multiple="multiple" title='Жмем для поиска файла'>
        </label>
    </div>
</div>
<h3>Загруженные файлы</h3>
<div id="upload_res">test</div>
<h3>Список сохраненных файлов</h3>
<?php
//echo _media::
?>
    <div id="upload_media">
        <table border="1">
            <tr>
                <td>#</td>
                <td>Контент</td>
                <td>Файл</td>
                <td>urls(Оригинал, маленький, средний, большой)</td>
                <td>Описание</td>
                <td></td>
            </tr>
            <?php foreach($list as $row) { ?>
            <tr>
                <td><?php echo $row['id']?></td>
                <td><img src="<?php echo $row['url_small']; ?>"></td>
                <td><?php echo $row['filename']?></td>
                <td><a href="<?php echo $row['url_original']; ?>" target="_blank"><?php echo $row['url_original']; ?></a><br>
                    <a href="<?php echo $row['url_small']; ?>" target="_blank"><?php echo $row['url_small']; ?></a><br>
                    <a href="<?php echo $row['url_medium']; ?>" target="_blank"><?php echo $row['url_medium']; ?></a><br>
                    <a href="<?php echo $row['url_big']; ?>" target="_blank"><?php echo $row['url_big']; ?></a>
                </td>
                <td><?php echo $row['alt']?></td>
                <td><a href="/admin/?view=media&d=<?=$row['id']?>" class="act-button">Удалить</a></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div> <!-- .content -->