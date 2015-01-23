$(document).ready(function(){
    $("#cart-make-order").click( function(){
        var re = /^[+\d]+[\d\(\)\s-]+[\d]+$/;
        va = $("#cellphone").val();
        if ( re.test( va ) ){
            $( "#form-cart-do" ).submit();
        }else{
            alert('Странный номер телефона, проверте номер телефона'); 
        }
    })
})


