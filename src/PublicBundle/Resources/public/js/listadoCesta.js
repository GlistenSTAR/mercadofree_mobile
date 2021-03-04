$(document).ready(function () {
    // Eliminar item de cesta

    $('.eliminarItemCesta').on('click',function(e){
        e.preventDefault();

        var idCesta=$(this).attr('data-id');

        var urlEliminarCesta=$('#urlEliminarItemCesta').val();

        $.ajax(urlEliminarCesta,{
            'type':'post',
            'dataType':'json',
            'data':{
                'idCesta':idCesta
            }
        }).done(function (response){
               if(!response[0]){
                   alert(response[1]);
               }
               else{
                   //Eliminar fila del producto

                   var idItem='#item-'+idCesta;

                   $(idItem).fadeOut();

                   //Actualizar precio total

                   var total='$ '+response[1];

                   $('#totalCesta').empty();

                   $('#totalCesta').append(total);

                   //Actualizar el preview de la cesta

                   var cantidadTotal=response[2];

                   $('.cart-qty-preview').empty();
                   $('.cart-qty-preview').append(cantidadTotal);

                   var previewItemId='#preview-item-'+response[3];
                   $(previewItemId).remove();
               }
           }
        );
    });

    //Modificar cantidad de un item de la cesta

    $('.cantProdCesta').on('change',function(){
        var idCesta=$(this).attr('data-id');

        var cant=$(this).val();

        var urlModificarCantidad=$('#urlModificarCantidadCesta').val();

        $.ajax(urlModificarCantidad,{
            'type':'post',
            'dataType':'json',
            'data':{
                'idCesta':idCesta,
                'cant':cant
            }
        }).done(function (response) {
            if(!response[0]){
                alert(response[1]);
            }
            else{

                //actualizar el precio en la linea del prooducto modificado

                var newPrice='$ '+response[1];

                var idItemPrecio='#precioProd-'+idCesta;

                $(idItemPrecio).empty();
                $(idItemPrecio).append(newPrice);

                //Actualizar precio total

                var total='$ '+response[2];

                $('#totalCesta').empty();

                $('#totalCesta').append(total);

                // Actualizar preview de la cesta

                var idProd=response[3];

                var idPreviewCant='#preview-cant-prod-'+idProd;

                var cantPreview=cant+' x';
                $(idPreviewCant).empty();
                $(idPreviewCant).append(cantPreview);

                var idPreviewPrecio='#preview-precio-prod-'+idProd;

                $(idPreviewPrecio).empty();
                $(idPreviewPrecio).append(newPrice);

            }
        });
    });

    // Hacer submit hacia el paso 2

    $('#btnListadoCestaNext').on('click',function (e) {
        e.preventDefault();

        var url=$('#urlListadoCestaNext').val();

        $.ajax(url,{
            dataType:'json',
            type:'post',
            data: {
                'idPedido':$('#idPedido').val()
            }
        }).done(function (response) {
            if(response[0]){
                location.href=$('#urlCestaEnvio').val();
            }
            else{
                alert(response[1]);
            }
        });
    })



});
