$(document).ready(function() {
    if (goods_page_count >0){
        $('.pagination').css('display', 'block');
        $('.pagination').jqPagination({
            page_string: 'Страница {current_page} из {max_page}', 
            max_page	: goods_page_count,
            current_page: goods_current_page,
            paged		: function(page) {
                param = Array();
                $.ajax({
                    url: window.location.pathname+'?view=shop_suppliers_uploadsettings&suppliers_id='+suppliers_id+'&page='+page+'&raw=yes',
                    type: "POST",
                    data: param,
                    success: function(res){
                        $('#tmp-tablе-content').html( res );
                        initJ();
                    },
                    error: function(){
                        alert("Error");
                    }
                });                 
            }
        });
    }
    $("#price-process").click(function(){
        $.ajax({
            url: window.location.pathname+'?view=shop_suppliers_process&suppliers_id='+suppliers_id,
            type: "POST",
            data: {},
            success: function(res){
                alert(res);
            },
            error: function(){
                alert("Error");
            }
        }); 
    })
});

function initJ(){
    $(".add_one_row").click( function(){
        var checked_columns = [];
        /*
        $(".columns-tmp-row-unicum").each(function() {
            if ( $(this).is(':checked') ){
                checked_columns.push($(this).attr('name'));
            }
        });
        */
        id = $(this).attr('nid');
        is_supplier = $(this).attr('sid');
        $.ajax({
            url: '?view=shop_suppliers_ajax',
            type: "POST",
            data: { price:'new', id_supplier:is_supplier, id:id, checked_columns:checked_columns },
            success: function(res){
                $('#r'+id).html( res );
                $('#sr-'+id).css('display', 'none');
            },
            error: function(){
                alert("Error");
            }
        });
    })
}