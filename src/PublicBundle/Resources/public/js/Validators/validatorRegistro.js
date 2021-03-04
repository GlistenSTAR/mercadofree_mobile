var validatorRegistro=$('#formRegistro').validate({
    errorPlacement: function(error, element) {
        // Append error within linked label
        $( element )
            .closest( "form" )
            .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
    errorElement: "span",
    rules:{
        passwordRepeat: {
            equalTo: "#passwordRegistrarUsuario"
        }
    },
    messages: {
        passwordRepeat: "Las contraseñas no coinciden"
    }
});