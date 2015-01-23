$(document).ready(function() {
    $("#button-find").click(function(){
        page = '?c_29='+$('#c_29').val()+'&c_32='+$('#c_32').val()+'&c_31='+$('#c_31').val()+'&c_24='+$('#c_24').val()+'&act=find';
        $.ajax({
            url: window.location.pathname+'/'+page,
            type: "POST",
            data: {raw:'yes'},
            success: function(res){
                $('#find-content').html( res );
                init_order();
                history.pushState(null, null, page);
            },
                error: function(){
                alert("Error");
            }
        });                 
    });
    init_order();
});

function init_order(){
    $('.goods-button-order').click(function(){
        id = $(this).attr('gid');
        am = 1;
        console.log( $(this).attr('gid') );
        $.ajax({
            url: '/cart/?ida='+id+'&am='+am+'&raw=yes',
            type: "POST",
            success: function(res){
               o = JSON.parse( res );
               $("#dialog-txt").html( 'В корзине уже товаров:' + o.count + ' на сумму:' + o.total );
               order_dialog();
            },
            error: function(){
                alert("Error");
            }
        });
    })    
}

