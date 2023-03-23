jQuery(document).ready( function($){

    //$('#boton').on('click',function(){
        $('#combo').change(function(e){
            e.preventDefault();
        $("#combo option:selected").each(function(){
             id = $(this).val();
             precio = $(this).attr("name");
                  $.ajax({
            type : 'GET',
            url: cf_woo_nonce.ajaxurl, // Pon aquí tu URL
            dataType: 'json',
            data : {
                action: 'cf_woo_buttonCalculator', 
                nonce : cf_woo_nonce.security,
                id : id,
                precio : precio,
            },
            error: function(response){
                console.log(JSON.stringify(response['responseText']))
            },
            success: function(response) {
                // Actualiza el mensaje con la respuesta
                $('#txtMessage').html(response);
                //alert("todo bien");
            }
            
        });
 
        })
    });
 });