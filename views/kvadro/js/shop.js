
window.onpopstate = function(event) {
    document.location.reload();
} 

$(document).ready(function() {
    if (goods_page_count >0){
        $('.pagination').css('display', 'block');
        $('.pagination').jqPagination({
            page_string: 'Страница {current_page} из {max_page}', 
            max_page	: goods_page_count,
            current_page: goods_current_page,
            paged	: function(page) {
                param = Array();
                $.ajax({
                    url: window.location.pathname+'/?page='+page+'&raw=yes',
                    type: "POST",
                    data: param,
                    success: function(res){
                        $('#shop-content').html( res );
                        init_anim();
                        history.pushState(null, null, '?page='+page);
                    },
                    error: function(){
                        alert("Error");
                    }
                });                 
            }
        });
    }
    // anim
    init_anim();
});

function init_anim(){
    $('.goods-inline').hover(function(){
        $(this).stop().animate({ "backgroundColor":'#f5f5f5', "color":'rgba(255,10,10,1)' }, 200);
    }, function(){
        $(this).stop().animate({ "backgroundColor":'#ffffff', "color":'#727272'  }, 200);
    });
    
    $('.category-block').hover(function(){
        $(this).stop().animate({ "backgroundColor":'#f5f5f5', "color":'#FF2020' }, 200);
        $(this).find("img").stop().animate({ "border-color":"#727272" }, 400);
    }, function(){
        $(this).stop().animate({ "backgroundColor":'#f8fbff', "color":'#00ffAA'  }, 200);
        $(this).find("img").stop().animate({ "border-color":"#fff" }, 400);
    });
}

