$(document).ready(function () {
    //btn Next de conf Envio

    $('#btnNextConfEnvio').on('click',function (e) {
        e.preventDefault();

        var buyNow=$('#buyNow').val();
        var idProducto=$('#idProducto').val();

        var url = $('#urlConfEnvioCesta').val();

        var urlPago=$('#urlMetodoPago').val();

        var envioSelected=$('#envioSelected').val();

        if(envioSelected!='') {


            if (buyNow != 0) {
                urlPago += "?buyNow=1&idProducto=" + idProducto;
            }

            $.ajax(url, {
                dataType: 'json',
                type: 'post',
                data: {
                    'buyNow': buyNow,
                    'idProducto': idProducto,
                    'envioSelected': envioSelected
                }
            }).done(function (response) {
                if (response[0]) {
                    //console.log(urlPago);
                    location.href = urlPago;
                } else {
                    alert(response[1]);
                }
            });

        }
        else{
            $('#error-message-metodo-envio').html('Debes seleccionar un método de envío antes de continuar');
        }
    });

    //Seleccion del metodo de envio

    jQuery('.radioMetodoEnvio').on('click',function () {
        // var precio = parseInt(jQuery(this).attr('data-price'));
        //
        // var total = number_format((parseInt(jQuery('#subtotal').val()) + precio),2);
        //
        // jQuery('td#cartTotal').html('$'+total);

        if($(this).val()=='envio-domicilio-vendedor'){
            updateCostoEnvio();
        }
        else{
            jQuery('td#cartTotal').html('$'+number_format((parseInt(jQuery('#subtotal').val())),2));
        }

        jQuery('#envioSelected').val(jQuery(this).val());

    });
});

function updateCostoEnvio() {
    var idPedido = $('#idPedido').val();
    //var envioSelected = $('#envioSelected').val();

    wait('table.cart-table');

    $.ajax($('#urlUpdateCostoEnvio').val(),{
        'dataType' : 'json',
        'type' : 'post',
        'data' : {
            idPedido : idPedido
        }
    }).done(function (response) {

        endWait('table.cart-table');

       if(response[0]){

           var costoEnvio = response.costoEnvio;

           // Actualizar costo de envio y total

           $('#costoEnvioDomicilio').html('$'+number_format(costoEnvio,2));

           var total = number_format((parseInt(jQuery('#subtotal').val()) + costoEnvio),2);

           jQuery('td#cartTotal').html('$'+total);

           //location.reload();
       }
       else{
           alert(response[1]);
           //console.log(response[1]);
       }
    });
}
