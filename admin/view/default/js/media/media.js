/* 
 * media
 */
$(document).ready(function(){

    
      $('#upload').dmUploader({
        url: '/admin/?view=media_upload',
        dataType: 'json',
        allowedTypes: 'image/*',
        extraData: {
            id:'001'
        },
        onUploadSuccess: function(id, data){
            alert('UPLOAD:'+data.html );
          //$('#upload_res').html(data.html);
        },
        onUploadError: function(id, data){
          alert('DATA:'+data);
          //alert('Error upload');
        }
      });
    
    $("#setting-save").click(function(){
        $("#setting-form").submit();
    })
    $("#setting-form").submit(function( event ) {
        // отменяем действие формы
        event.preventDefault();
        var $form = $( this ),
        url = $form.attr( "action" );
        // Щлем POST запрос
        var posting = $.post( url,{ 
            'small-width':$("#small-width").val(),'small-height':$("#small-height").val(),'small-prefix':$("#small-prefix").val(),
            'medium-width':$("#medium-width").val(),'medium-height':$("#medium-height").val(),'medium-prefix':$("#medium-prefix").val(),
            'big-width':$("#big-width").val(),'big-height':$("#big-height").val(),'big-prefix':$("#big-prefix").val(),
            row_per_page:$("#row_per_page").val()
        });
        // Показываем результат
        posting.done(function( data ) {
            alert(data);
        })
    })

})
