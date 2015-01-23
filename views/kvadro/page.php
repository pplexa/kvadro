<?php defined('A') or die('Access denied'); ?>
<!--
    <div class="kroshka">
            <a href="/">Главная</a> / <span><?=$get_page['title']?></span>
    </div>-->
<?php if($get_page): ?>
        <h1><?=$get_page['title']?></h1>
        <?=$get_page['text']?>
    <?php else: ?>
        <p>Такой страницы нет!</p>
    <?php endif; ?>