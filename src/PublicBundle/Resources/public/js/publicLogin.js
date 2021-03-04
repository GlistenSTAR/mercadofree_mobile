$(document).ready(function () {


    /**Mostrar u ocultar el password***/

    var $password = $('#password');

    $password.hidePassword('focus', {
        toggle: { className: 'btn-toggle-show-password btn btn-default' }
    });

    /**Traducir botón**/

    traducirBotonShowPassword('hidden','btn-toggle-show-password');


    $password.on('passwordShown', function(){
        traducirBotonShowPassword('show','btn-toggle-show-password');
    });

    $password.on('passwordHidden', function(){
        traducirBotonShowPassword('hidden','btn-toggle-show-password');
    });


});
