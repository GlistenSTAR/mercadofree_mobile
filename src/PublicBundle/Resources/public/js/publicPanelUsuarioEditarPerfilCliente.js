$(document).ready(function () {


//////////////////////////////////////////////////////////////////////////////////////////Modificar Nombre y Clave

    $('#btnSubmitEditarCuentaCliente').on('click',function (e) {

        var validator=null;

        validator=validatorCuentaClientePanelUsuario;

        var flag=false;

        if(validator.form())
        {
            if($('#passwordC2').val()==$('#passwordC').val())
            {
                var $form = $('#formClienteCuenta');

                $.ajax($form.attr('action'), {
                    type: 'post',
                    dataType: 'json',
                    async: false,
                    data: $form.serialize()
                }).done(function (response) {

                    $('#emailP').text($('#emailC').val());

                    $('#modalEditarDatosCuentaCliente').modal('hide');

                    alertify.success('La cuenta ha sido editada satisfactoriamente.');
                });
            }
            else
            {
                $('#dul').show();

            }

        }


    });

    //////////////////////////////////////////////////////////////////////////////////////////Modificar Telefono, Nombre y DNI

    $('#btnSubmitEditarPersonalesCliente').on('click',function (e) {

        var validator=null;

        validator=validatorPersonalesClientePanelUsuario;

        var flag=false;

        if(validator.form()) {
            var $form = $('#formDatosPersonales');

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                async: false,
                data: $form.serialize()
            }).done(function (response) {

                $('#nombreP').text($('#nombreC').val()+" "+$('#apellidosC').val());
                $('#dniP').text($('#dniC').val());
                $('#telefonoP').text($('#telefonoC').val());

                $('#modalEditarDatosPersonales').modal('hide');

                alertify.success('Los datos personales del cliente han sido guardados satisfactoriamente.');
            });
        }


    });

    //////////////////////////////////////////////////////////////////////////////////////////Modificar Cuenta

    $('#btnSubmitEditarCuenta').on('click',function (e) {

        var validator=null;

        validator=validatorCuentaBancariaPanelUsuario;

        var flag=false;

        if(validator.form()) {
            var $form = $('#formCuentaBancaria');

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                async: false,
                data: $form.serialize()
            }).done(function (response) {

                $('#ibanP').text($('#ibanC').val());


                $('#modalEditarCuenta').modal('hide');

                alertify.success('Su cuenta se ha actualizada satisfactoriamente.');
            });
        }


    });
    
    //////////////////////////////////////////////////////////////////////////////////////////Modificar Cuenta

    $('#btnSubmitEditarCuentaPaypal').on('click',function (e) {

        var validator=null;

        validator=validatorCuentaPaypalPanelUsuario;

        var flag=false;

        if(validator.form()) {
            var $form = $('#formCuentaPaypal');

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                async: false,
                data: $form.serialize()
            }).done(function (response) {

                var cuenta = $('#cuentaPaypalC').val();
                if(!cuenta) {
                    cuenta = "--";
                }
                
                $('#emailPaypalP').text(cuenta);


                $('#modalEditarCuentaPaypal').modal('hide');

                alertify.success('Su cuenta paypal se ha actualizada satisfactoriamente.');
            });
        }


    });


});
