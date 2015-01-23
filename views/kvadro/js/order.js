$(document).ready(function(){
    var spinner = $( "#goods-count" ).spinner({
        min: 1,
        change: function (event, ui ) {
           var val = $( "#goods-count" ).spinner( "value" );
           $( "#goods-count" ).spinner("value", parseInt(val,10) || 1);
       }
    }).val(1);
    
    $("#add-to-order").click(function(){
        id = $(this).attr('gid');
        am = $("#goods-count").val();
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
})