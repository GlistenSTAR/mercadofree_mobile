var validatorCostoEnvio=$('#formAdicionarCostoEnvio').validate({
    errorPlacement: function(error, element) {
        // Append error within linked label
        $( element )
            .closest( "form" )
            .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
    errorElement: "span",
    rules:{

        nombreCostoEnvioAdicionar: {
            number:true
        }
    }
});
