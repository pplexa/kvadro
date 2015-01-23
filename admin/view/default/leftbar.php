<?php defined('A') or die('Access denied'); ?>
	<div class="content-main">
<?php if($count_new_orders > 0): ?> 
<p class="new_orders"><a href="?view=orders&amp;status=0">Есть новые заказы (<?=$count_new_orders?>)</a></p>
<?php endif; ?>
		<div class="leftBar">
			<ul class="nav-left">
				<li><a <?=active_url()?> href="<?=URL?>admin/">Основные страницы</a></li>
				<li><a <?=active_url("view=informers")?> href="?view=informers">Информеры</a></li>
                                <li><a <?=active_url("view=shop_category")?> href="?view=shop_category">Категории</a></li> 
                                <li><a <?=active_url("view=shop_goodsproperty")?> href="?view=shop_goodsproperty">Свойства</a></li>
                                <li><a <?=active_url("view=shop_goods")?> href="?view=shop_goods">Товары</a></li>
                                <li><a <?=active_url("view=shop_goodsexport")?> href="?view=shop_goodsexport">Товары экспорт</a></li>
                                <li><a <?=active_url("view=shop_suppliers")?> href="?view=shop_suppliers">Поставщики</a></li>
                                <li><a <?=active_url("view=shop_settings")?> href="?view=shop_settings">Общие настройки</a></li>
                                <li><a <?=active_url("view=shop_orders")?> href="?view=shop_orders">Заказы</a></li>
				<li><a <?=active_url("view=news")?> href="?view=news">Новости</a></li>
				<li><a <?=active_url("view=users")?> href="?view=users">Пользователи</a></li>
                                <li><a <?=active_url("view=media")?> href="?view=media">Медиа</a></li>
			</ul>
		</div> <!-- .leftBar -->