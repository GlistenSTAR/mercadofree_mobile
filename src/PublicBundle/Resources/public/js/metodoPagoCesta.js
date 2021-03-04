$(document).ready(function () {

    //Seleccion de metodo de pago

    $('.optionMetodoPago').on('click',function (e) {
       e.preventDefault();

       var option=$(this).attr('data-option');
       $('#public_factura_metodoPago').val(option);

       if(option=='pago_facil' || option=='rapipago'){
           $(this).parent().attr('style','border: 2px solid #7f2272;');

           if(option=='pago_facil'){
               $('#optRapiPago').parent().attr('style','');
           }
           else{
               $('#optPagoFacil').parent().attr('style','');
           }
       }
       else{
           $('#optPagoFacil').parent().attr('style','');
           $('#optRapiPago').parent().attr('style','');
       }
    });

    //Seleccion metodo de pago 2

    $('.metodoPago').on('click',function (e) {
        e.preventDefault();

        $('#metodoPagoSelected').val($(this).attr('data-value'));
    });

    //Action Boton Next

    $('#btnPagoNext').on('click',function (e) {
        e.preventDefault();

        //var option=$('#public_factura_metodoPago').val();

        var validator=validatorFactura;

        var enviar=true;

        // if((option=='pago_facil' || option=='rapipago') && validatorFactura.form()){
        //     enviar=true;
        // }
        // else if(option!='pago_facil' && option!='rapipago'){
        //     enviar=true;
        // }

        if(enviar){
            var form=$('#metodoPagoForm');

            $.ajax(form.attr('action'),{
                type:form.attr('method'),
                dataType:'json',
                data:form.serialize()
            }).done(function (response) {
                if(response[0]){
                    location.href=$('#urlResumen').val();
                }
                else{
                    alert(response[1]);
                }
            });
        }
    });
});
