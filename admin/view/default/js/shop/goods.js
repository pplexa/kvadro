$(document).ready(function() {
    if (goods_page_count >0){
        $('.pagination').css('display', 'block');
        $('.pagination').jqPagination({
            page_string: 'Страница {current_page} из {max_page}', 
            max_page	: goods_page_count,
            current_page: goods_cur_page,
            paged	: function(page) {
                param = Array();
                param = {raw:true,all:goods_page_count,page:page,per:goods_per_page,where:goods_w,act:'view'};
                $.ajax({
                    url: window.location.pathname+'?view=shop_goods_part',
                    type: "POST",
                    data: param,
                    success: function(res){
                        $('#table-content').html( res );
                        prep_open_close();
                    },
                    error: function(){
                        alert("Error");
                    }
                });                 
            }
        });
    }
    
    $('#good_per_page').change( function(){
        goods_prepare_where();
    })
    $('.p_s_d').change( function(){
        goods_prepare_where();
    })
    $('#delete').click(function(){
        if (confirm('Будет удалено товаров: '+goods_count+'\nУверены, что хотите удалить товары по отбору? ')){
            del_goods('many', goods_w);
        }
    })
    
    prep_open_close();
});
function prep_open_close(){
    $('.table-open-close').click(function(){
        $(this).prop('state', !$(this).prop('state') );
        if ($(this).prop('state')){
            $(this).attr('class', 'table-open-close ui-icon ui-widget-content ui-icon-triangle-1-s');
            $('#rid-'+$(this).attr('rid')).css('display', 'table-row');
            if ( !$('#did-'+$(this).attr('rid')).prop('load') ){
                load_goods_inf( $(this).attr('rid') );
            }
        }else{
            $(this).attr('class', 'table-open-close ui-icon ui-widget-content ui-icon-triangle-1-e');
            $('#rid-'+$(this).attr('rid')).css('display', 'none');
        }
    })    
}
function prep_del_goods(){
    $('.delete-goods').click(function(){
        if (confirm('Точно удалить?')){
            var id = $(this).attr('rid');
            del_goods('one', id);
        }
    })
}
function prep_regen_url(){
    $('.regen-url-name').click(function(){
        var id = $(this).attr('rid');
        urli = $('#frm-'+id).find('#name_url');
        console.log('regen for:'+id);
        $.ajax({
                url: '?view=shop_goods_regen_url',
                type: "POST",
                data: {id:id},
                success: function(res){
                    urli.val( res );
                    console.log('get regen:'+res);
                },
                error: function(){
                    alert("Error");
                }
        });
    })
}

function del_goods(type, param) {
    var par = {delete_type:type, param:param};
    $.ajax({
            url: '?view=shop_goods_delete',
            type: "POST",
            data: par,
            success: function(res){
                if (type == 'one'){
                    $('#row-id-'+param).remove();
                }else{
                    alert('Товары удалены');
                }
            },
            error: function(){
                alert("Error");
            }
    });
}

function load_goods_inf(id){
    param = {id:id};
    $.ajax({
        url: '?view=shop_goods_one',
        type: "POST",
        data: param,
        success: function(res){
            $('#did-'+id).prop('load', true);
            $('#did-'+id).html( res );
            prep_del_goods();
            prep_regen_url();
        },
        error: function(){
            alert("Error");
        }
    });    
}
function goods_prepare_where(){
    goods_per_page = $('#good_per_page').val();
    
    goods_w = [];
    $('.p_s_d').each(function() {
        if ($(this).val() != '-xOOx-') {
            goods_w.push( {column:$(this).attr('id'), value:$(this).val() } );
        }
    })
    
    param = {where:goods_w};
    $.ajax({
        url: '?view=shop_goods_count',
        type: "POST",
        data: param,
        success: function(res){
            goods_count = res;
            goods_page_count = Math.ceil(goods_count/goods_per_page);
            if (res == 0){
                $('.pagination').css('display','none');
                $('#table-content').html( 'No goods' );
            }else{
                $('.pagination').css('display','block');
                $('.pagination').jqPagination('option', 'max_page',goods_page_count);
            }
            $('#good-count-where').html( res );
        },
        error: function(){
            alert("Error");
        }
    });
}

function goods_submit( id ){
    //$('#frm-'+id).html()
    $('#save-'+id).click(function(){
        var param = Array();
        param.push( {name:'id',value:id} );
        $('form#frm-'+id+' :input, form#frm-'+id+' textarea').each(function(){
            var input = $(this); // This is the jquery object of the input, do what you will
            param.push( {name:input.attr('id'),value:input.val()} );
        });
        $.ajax({
            url: '?view=shop_goods_edit',
            type: "POST",
            data: param,
            success: function(res){
                alert( res );
            },
            error: function(){
                alert("Error");
            }
        });
    })
}
