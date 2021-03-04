$(document).ready(function () {

    $('.guardar').on('click', function (e) {

       e.preventDefault();

       var form=$('#formGarantia');

        $.ajax(form.action, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: form.serialize()
        }).done(function (response) {

            alertify.log('Los cambios se han guardado satisfactoriamente');

        });

    });

    //Habilitar o deshabilitar checks de otros tipos de envio si se marca el envio gratis
    $('#envio-gratis').on('click',function () {
       if($(this).prop('checked')){
           $('#envio-mercadofree').attr('disabled','disabled');
           $('#edv').attr('disabled','disabled');
           $('#recogida-domicilio-vendedor').attr('disabled','disabled');

           $('#envio-mercadofree').removeAttrs('checked');
           $('#edv').removeAttrs('checked');
           $('#recogida-domicilio-vendedor').removeAttrs('checked');

           $('#regiones-envio-container').hide();
       }
       else{
           $('#envio-mercadofree').removeAttrs('disabled');
           $('#edv').removeAttrs('disabled');
           $('#recogida-domicilio-vendedor').removeAttrs('disabled');
       }
    });

    //Desmarcar el check de envio a domicilio por el vendedor, en caso que se marque el envio por mercadofree

    $('#envio-mercadofree').on('click',function () {
        if($(this).prop('checked')){
            $('#edv').removeAttrs('checked');

            $('#regiones-envio-container').hide();
        }
    });

});
