<?php 
preg_match("/(.*)\?/", $_SERVER['REQUEST_URI'], $r);
if (count($r) == 0 ){
    $gr = $_SERVER['REQUEST_URI'];
}else{
    $gr = $r[1];
}

    //$prefix_small = _media::settings_load('small-prefix', '_s');
    $prefix_medium = _media::settings_load('medium-prefix', '_m');
    foreach($goods as $r){
        if ( $r['img'] != ''){
            $pp = pathinfo( $r['img'] );
            $url = URL_MEDIA.'m/'.$pp['filename'].$prefix_medium.'.'.$pp['extension'];
        }
if ( $r['name_url'] != '' ){  echo '<a href="'.$_SERVER['HTTP_REFERER'].'/'.$r['name_url'].'">';} 
//if ( $r['name_url'] != '' ){  echo '<a href="'.$gr.'/'.$r['name_url'].'">';} 
?>
<div class="goods-inline-27 goods-inline">
    <div class="name"><?php echo $r['name']; ?></div>
    <div class="img-border-27"><img class="img" src="<?php echo $url; ?>"></div>
    <div class="cost"><?php _pp::html_format_cost( $r['cost_out'] ); ?></div>
</div>
<?php if ( $r['name_url'] != '' ){  echo '</a>';}
    } 
?>