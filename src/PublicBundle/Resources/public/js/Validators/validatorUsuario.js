var validatorUsuarioStep2=$('#formAnuncio2').validate({
    errorPlacement: function(error, element) {
        // Append error within linked label
        $( element )
            .closest( "form" )
            .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
    errorElement: "span"

});
var validatorUsuarioStep3=$('#formAnuncio3').validate({
    errorPlacement: function(error, element) {
        // Append error within linked label
        $( element )
            .closest( "form" )
            .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
    errorElement: "span",
    rules:{

        cantidad: {
            number:true
        },
        precio: {
            number:true
        }
    }
});

var validatorUsuarioStep4=$('#formAnuncio4').validate({
    errorPlacement: function(error, element) {
        // Append error within linked label
        $( element )
            .closest( "form" )
            .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
    errorElement: "span"

});



var validatorCuentaClientePanelUsuario=$('#formClienteCuenta').validate({
    errorPlacement: function(error, element) {
        // Append error within linked label
        $( element )
            .closest( "form" )
            .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
    errorElement: "span"

});

var validatorPersonalesClientePanelUsuario=$('#formDatosPersonales').validate({
    errorPlacement: function(error, element) {
        // Append error within linked label
        $( element )
            .closest( "form" )
            .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
    errorElement: "span"

});

var validatorCuentaBancariaPanelUsuario=$('#formCuentaBancaria').validate({
    errorPlacement: function(error, element) {
        // Append error within linked label
        $( element )
            .closest( "form" )
            .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
    errorElement: "span"

});

var validatorCuentaPaypalPanelUsuario=$('#formCuentaPaypal').validate({
    errorPlacement: function(error, element) {
        // Append error within linked label
        $( element )
            .closest( "form" )
            .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
    errorElement: "span"
});

var validatorPersonalesEmpresaPanelUsuario=$('#formEmpresaCuenta').validate({
    errorPlacement: function(error, element) {
        // Append error within linked label
        $( element )
            .closest( "form" )
            .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
    errorElement: "span"

});

var validatorGarantiaPanelUsuario=$('#formGarantia').validate({
    errorPlacement: function(error, element) {
        // Append error within linked label
        $( element )
            .closest( "form" )
            .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
    errorElement: "span"

});