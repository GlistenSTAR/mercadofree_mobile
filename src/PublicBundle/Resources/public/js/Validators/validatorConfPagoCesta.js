var validatorFactura=$('#metodoPagoForm').validate({
    errorPlacement: function(error, element) {
        // Append error within linked label
        $( element )
            .closest( "form" )
            .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
    errorElement: "span",

    messages: { // custom messages
        'public_factura[nombre]': {
            required: "Debe entrar su nombre"
        },
        "public_factura[apellidos]":{
            required: "Debe entrar sus apellidos"
        },
        "public_factura[dni]":{
            required: "Debe entrar un DNI válido"
        },
        "public_factura[calle]":{
            required: "Debe entrar la calle de su dirección de facturación"
        },
        "public_factura[numero]":{
            required: "Debe entrar el número de su residencia"
        },
        "public_factura[codPostal]":{
            required: "Debe entrar su código postal"
        },
        "public_factura[provincia]":{
            required: "Debe seleccionar su provincia"
        },
        "public_factura[localidad]":{
            required: "Debe entrar su localidad"
        }
    }

});
