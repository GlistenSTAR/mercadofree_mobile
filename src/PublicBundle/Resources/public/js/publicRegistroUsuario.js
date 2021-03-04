$(document).ready(function () {

    /**
     * Valida primero el formulario con jQuery Validate y luego si está ok hace el submit del formulario
     */
    function registrarUsuario()
    {
        if(validatorRegistro.form())
        {
            $('#formRegistro').submit();
        }
    }

    /**
     * Verifica si el email en el formulario ya existe en la BD como email de un usuario registrado.
     * Retorna true si existe, false si no existe, y en este ultimo caso ejecuta la función registrarUsuario()
     *
     */
    function checkEmailExist(){
        var email = $('#emailRegistrarUsuario').val();

        $.ajax($('#validarExisteEmailPath').val(),{
            'method':'POST',
            'dataType':'json',
            'data':{
                'email':email
            },
            'async' : true
        }).done(function (response){
         if(response.response === false){
             registrarUsuario();
         }
         else{
             $('label[for="emailRegistrarUsuario"]').append($('<span id="emailExistError" class="error">Ya existe un usuario registrado con este email</span>'));
         }
        });
    }


    /***Action del botón de registrarse****/
    $('#registrarBtn').on('click', function (e)
    {
        e.preventDefault();
        $('#emailExistError').remove();
        checkEmailExist();
    });

    /**Mostrar u ocultar el password***/

    var $passwordRegistroUsuario = $('#passwordRegistrarUsuario');

    var $passwordRepeat = $('#passwordRepeat');

    $passwordRegistroUsuario.hidePassword('focus', {
        toggle: { className: 'btn-toggle-show-password btn btn-default' }
    });

    $passwordRepeat.hidePassword('focus', {
        toggle: { className: 'btn-toggle-show-password-repeat btn btn-default' }
    });

    /**Traducir botón**/

    traducirBotonShowPassword('hidden','btn-toggle-show-password');
    traducirBotonShowPassword('hidden','btn-toggle-show-password-repeat');

    $passwordRegistroUsuario.on('passwordShown', function(){
        traducirBotonShowPassword('show','btn-toggle-show-password');
    });

    $passwordRegistroUsuario.on('passwordHidden', function(){
        traducirBotonShowPassword('hidden','btn-toggle-show-password');
    });

    $passwordRepeat.on('passwordShown', function(){
        traducirBotonShowPassword('show','btn-toggle-show-password-repeat');
    });

    $passwordRepeat.on('passwordHidden', function(){
        traducirBotonShowPassword('hidden','btn-toggle-show-password-repeat');
    });

});
