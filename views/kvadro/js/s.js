/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function() {
    $('#simple-menu').sidr();
});

function order_dialog(){
    
        $( "#dialog" ).dialog({
            modal: true,
            resizable: false,
            width: "auto",
            buttons: {
                "Добавить": function() {
                    $(this).dialog("close");
		},
                "Хватит! Оформляйте!": function() {
                    $(this).dialog("close");
                    window.location.href = "/cart";
		}
            }
	});    
}