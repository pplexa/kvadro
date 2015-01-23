<?php 
preg_match("/(.*)\?/", $_SERVER['REQUEST_URI'], $r);
if (count($r) == 0 ){
    $gr = $_SERVER['REQUEST_URI'];
}else{
    $gr = $r[1];
}
//REQUEST_URI
//HTTP_REFERER
    $prefix_small = _media::settings_load('small-prefix', '_s');
    foreach($goods as $r){
        if ( $r['img'] != ''){
            $pp = pathinfo( $r['img'] );
            $url = URL_MEDIA.'s/'.$pp['filename'].$prefix_small.'.'.$pp['extension'];
        }
if ( $r['name_url'] != '' ){  echo '<a href="'.$gr.'/'.$r['name_url'].'">';} 
?>
<div class="goods-inline">
    <div class="name"><?php echo $r['name']; ?></div>
    <?php if ($url != '') {?>
    <img class="img" src="<?php echo $url; ?>">
    <?php } else { ?>
    <div class="goods-no-photo">нет фотографии</div>
    <?php } ?>
    <div class="cost"><?php _pp::html_format_cost( $r['cost_out'] ); ?></div>
</div>
<?php if ( $r['name_url'] != '' ){  echo '</a>';}
    } 
?>