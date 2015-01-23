/* 
 * upload price
 */
$(document).ready(function(){
      $('#uploadprice').dmUploader({
        url: '/admin/?view=shop_suppliers_uploadprice',
        extraData: {
            id:$('#suppliers_id').html()
        },
        onUploadSuccess: function(id, data){
          //$('#upload_res').html(data);
          window.location.reload();
        },
        onUploadError: function(id, data){
          alert(data);
          //alert('Error upload');
        }
      });
})
