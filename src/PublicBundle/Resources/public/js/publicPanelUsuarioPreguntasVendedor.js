$(document).ready(function () {

    $('.itemPregunta').on('click',function(e){
        e.preventDefault();

        var id=$(this).attr('data-id');

        var selector='#r'+id;

        // if($(selector).text()=="")
        // {
            $('.itemRespuesta').each(function(){
                if(id==$(this).attr('data-id')){
                    $(this).fadeIn();
                }
            });
        //}

        $('.btnCancelarRespuesta').on('click',function(e){
            e.preventDefault();

            $(this).siblings('textarea').val("");
            $(this).parent().hide();
        });
    });


    $('.btnResponder').on('click', function (e) {

        var id=$(this).attr('id');

        id= id.substring(1,id.length);

        var texto=$('#t'+id).val();

        $('#r'+id).text("");

        $('#r'+id).append('<i class="ion-chatboxes"></i>'+' '+texto);

       // $('#r'+id).text(texto);

        $('#r'+id).show();

        var url=$('#urlResponderPreguntas').val();

        wait('#waitPro');

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idPregunta": id,
                "texto":texto
            }
        }).done(function (response) {

            $('#t'+id).val("");
            $('#t'+id).parent().hide();


            endWait('#waitPro');

            alertify.success('La pregunta ha sido respondida satisfactoriamente.');

        }).error(function () {

            endWait('#waitPro');

            alertify.error('Ha ocurrido un error al tratar de responder la pregunta.');
        });

    });

    $('.btnEliminarPregunta').on('click', function (e) {

        var id=$(this).attr('id');

        id=id.substring(1,id.length);

        $('#idPreguntaEliminar').val(id);

    });

    $('#btnConfirmDanger-eliminarPregunta').on('click',function () {

        var id=$('#idPreguntaEliminar').val();

        var url=$('#urlEliminarPreguntas').val();

        $('#tr'+id).remove();

        wait('#waitPro');

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idPregunta": id
            }
        }).done(function (response) {

            if(response.cantPre<=0)
            {
                $('#cont'+response.idProducto).remove();
            }

            $('#dangerMessage-eliminarPregunta').modal('hide');

            endWait('#waitPro');

            alertify.success('La pregunta ha sido eliminada satisfactoriamente.');

        }).error(function () {

            endWait('#waitPro');

            alertify.log('Ha ocurrido un error al tratar de eliminar la pregunta.');
        });


    });


});