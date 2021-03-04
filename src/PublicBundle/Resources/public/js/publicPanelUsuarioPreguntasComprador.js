$(document).ready(function () {

    $('.preguntaP').on('click', function (e) {

        var id=$(this).attr('id');

        id= id.substring(1,id.length);

        $('#productoid').val(id);

    });

    $('#btnHacerPregunta').on('click', function (e) {

        var id=$('#productoid').val();

        var url=$('#urlAdicionarPreguntas').val();

        wait('#waitPro');

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idProducto": id,
                "pregunta": $('#pregunta').val()
            }
        }).done(function (response) {

            $('#realizarPreguntaProducto').modal('hide');

            $('#btnEliminar').attr('disabled',true);

            var url1=$('#urlObtenerPreguntas').val();

            $.ajax(url1, {
                type: 'post',
                dataType: 'json',
                async: false,
                data: {
                    "idProducto": id
                }
            }).done(function (response) {


                var pregunta=response.pregunta;

               var respuesta= pregunta[2]!=null?'<p><i class="ion-chatboxes"></i> '+pregunta[2]+'</p>':"";

                var tagTr='<tr id="tr'+pregunta[0]+'">'+
                 '<td>'+
                 '<p><i class="ion-chatbox"></i> '+pregunta[1]+'</p>'+
                    respuesta+
                 '</td>'+
                 '<td>'+
                 '<a id="e'+pregunta[0]+'" class="btn btn-danger eliminarPre" data-toggle="modal" href="#dangerMessage-eliminarPregunta"><i class="fa fa-trash"></i> Eliminar</a>'+
                 '</td>'+
                 '</tr>';

                $('#tableBody'+id).append(tagTr);


                endWait('#waitPro');

                alertify.success('La pregunta ha sido guardada satisfactoriamente.');

            }).error(function () {

                endWait('#waitPro');

                alertify.error('Ha ocurrido un error al tratar de guardar la pregunta.');

            });



        }).error(function () {

            endWait('#waitPro');
            $('#realizarPreguntaProducto').modal('hide');
        });

    });

    $('.tableD').on('click','.eliminarPre', function (e) {

        id=$(this).attr('id');

        id= id.substring(1,id.length);

        $('#preguntaid').val(id);
    });

    $('#btnConfirmDanger-eliminarPregunta').on('click', function (e) {

        var id=$('#preguntaid').val();

        var url=$('#urlEliminarPreguntas').val();

        wait('#waitPro');


        $('.tableD #tr'+id).remove();

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idPregunta": id
            }
        }).done(function (response) {

            endWait('#waitPro');

            $('#dangerMessage-eliminarPregunta').modal('hide');

            alertify.success('La pregunta ha sido guardada satisfactoriamente.');

        }).error(function () {

            endWait('#waitPro');

            $('#dangerMessage-eliminarPregunta').modal('hide');

            alertify.error('Ha ocurrido un error al tratar de eliminar la pregunta.');
        });


    });






});

