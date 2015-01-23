<?php defined('A') or die('Access denied'); ?>
<div class="content">
    <h2>Заказы</h2>
    <table width="100%" class="st-table">
        <tbody>
        <tr>
            <th></th>
            <th>#</th>
            <th>Дата</th>
            <th>Телефон</th>
            <th>Заказ</th>
            <th>Статус</th>
        </tr>
        </tbody>
        <tbody id="table-content" class="goods-block-one"><?php include_once 'orders_part.php';?></tbody>
    </table>
</div>