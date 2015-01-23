$(document).ready(function(){
    $('.common_propertys').change( function(pp){
        par = { common:pp.target.id, val: $('#'+pp.target.id).val() };
        saveoptajax('?view=shop_settings_ajax', par, pp.target );
    })
    $('#do-yandex').click(function(){
        $.ajax({
            url: '?view=shop_goodsexport_do',
            type: "POST",
            data: {do:'yandex'},
            success: function(res){
                $('#result-yandex').html(res);
            },
            error: function(){
                alert("Error");
            }
        });    
    })
})
function saveoptajax(url, param, elem){
    $.ajax({
        url: url,
        type: "POST",
        data: param,
        success: function(res){
            $('#ajaxw').css('top',$(elem).offset().top-10 );
            $('#ajaxw').css('left',$(elem).offset().left-10 );
            $('#ajaxc').html( res );
            $('#ajaxw' ).fadeIn( 500 ).delay( 3000 ).fadeOut( 500 );
        },
        error: function(){
            alert("Error");
        }
    });    
}


