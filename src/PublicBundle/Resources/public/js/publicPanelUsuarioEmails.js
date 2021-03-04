$(document).ready(function () {

    $('#btnGuardarEmails').on('click', function () {

        var $form = $('#formEmails');

        wait('#waitPro');

        $.ajax($form.attr('action'), {
            type: 'post',
            dataType: 'json',
            async: false,
            data: $form.serialize()
        }).done(function (response) {

            endWait('#waitPro');

            alertify.log('El Email ha sido guardado satisfactoriamente.');

        });


    });

});
