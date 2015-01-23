<h1>Корзина</h1>
<?php
    
    if ( count($goods) > 0 ){
//        print_r( $goods );
?>
    <table class="cart-list-goods">
        <tr>
            <th>№</th>
            <th>Код</th>
            <th>Товар</th>
            <th>Кол-во</th>
            <th>Цена</th>
            <th>Итого</th>
            <th></th>
        </tr>
<?php
        $cnt = 0;
        $total = 0;
        foreach( $goods as $o ){
            $cnt++;
            $itogo = $_SESSION['cart'][$o['id']]['count'] * $o['cost_out'];
            $total += $itogo;
            $media = explode(',', $o['media']);
            if ( count($media) > 0 ){
                $media = _pp::media_get_name_by_type( $media[0], '_s' );
            }
            
?>
        <tr>
            <td><?php echo $cnt; ?></td>
            <td><?php echo $o['id']; ?></td>
            <td><img src="<?php echo $media; ?>"><?php echo $o['name']; ?></td>
            <td><?php echo $_SESSION['cart'][$o['id']]['count']; ?></td>
            <td><?php _pp::html_format_cost( $o['cost_out'] ); ?></td>
            <td><?php _pp::html_format_cost( $itogo ); ?></td>
            <td><form method="POST" action="/cart">
                <input type="hidden" name="idd" value="<?php echo $o['id']; ?>">
                <button type="submit" title="Удалить" class="cart-del ui-button ui-widget ui-state-error ui-corner-all ui-button-icon-only">
                    <span class="ui-icon ui-icon-closethick"></span>
                    <span class="ui-button-text">Удалить</span></button>
                </form>
            </td>
        </tr>
<?php        
        }
?>
        <tr><td colspan="5" align="right">Итого:</td><td><?php _pp::html_format_cost( $total ); ?></td></tr>
    </table>
<div class="cart-order-do">
    <form method="POST" action="/cart" id="form-cart-do">
        <input type="hidden" name="do" value="1">
<label for="cellphone">Номер вашего телефона: </label><input class="ui-widget ui-widget-content ui-corner-all" type="text" name="cellphone" id="cellphone" value="<?php echo $_SESSION['ellphone']; ?>"><br>
На всякий случай мы Вам отправим sms с номером заказа, мало-ли что. Если Ваш телефон не принимает sms, то мы все равно перезвоним.<br>
<div class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" id="cart-make-order"><span class="ui-button-text">Жду звонка!</span></div>
    </form>
</div>
<?php
    }else{
?>
<strong>Вот же-ж хреновина, нету товаров в корзине.</strong>
<?php
    }
?>