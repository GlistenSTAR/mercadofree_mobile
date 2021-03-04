$(document).ready(function () {

    $('#btnConfirmDanger-eliminarFavorito').on('click', function (e) {

        var data="";

        var inversion="";

        $('.box').each(function () {

            if(this.checked)
            {
                data=data+','+this.value;

                /*var temp=$('#tr'+this.value+' td:nth-child(4)').text();

                inversion=inversion+','+temp.substring(1,temp.length);*/

                $('#tr'+this.value).remove();

            }
        });

        var url=$('#urlEliminarProductosFavoritos').val();

        wait('#waitPro');

        if (data!="")
        {
            $.ajax(url, {
                type: 'post',
                dataType: 'json',
                async: false,
                data: {
                    "idsProducto": data
                }
            }).done(function (response) {

                endWait('#waitPro');

                $('#dangerMessage-eliminarFavorito').modal('hide');

                $('#btnEliminar').attr('disabled',true);

                alertify.success('El producto ha sido eliminado de los favoritos.');

            }).error(function () {

                endWait('#waitPro');

                $('#dangerMessage-eliminarFavorito').modal('hide');

                alertify.error('Ha ocurrido un error al tratar de eliminar el producto de favoritos.');
            });

        }




    });

    $('#btnEliminar').on('click', function (e) {

        var flag= false;

        var cont=0;

        $('.box').each(function () {

            if(this.checked)
            {
                flag=true;
                cont++;
            }
        });

        if(flag==false)
        {
            e.stopImmediatePropagation();
        }
        if(cont>1)
        {
            $('#textoDanger').text('Si eliminas estos productos de tus favoritos no aparecerán más en el listado de favoritos, aunque puedes adicionarlos como favoritos cuando lo desees.');

            $('#largeModalLabel').text("");

            $('#largeModalLabel').append('<i class="fa fa-warning"></i> ¿Está seguro de eliminar estos productos de tus favoritos?');
        }

        if(cont==1)
        {
            $('#textoDanger').text('Si elimina este producto de tus favoritos no aparecerá más en el listado de favoritos, aunque puedes adicionarlo como favorito cuando lo desees.');

            $('#largeModalLabel').text("");

            $('#largeModalLabel').append('<i class="fa fa-warning"></i> ¿Está seguro de eliminar este producto de tus favoritos?');
        }


    });

    $('.eliminarP').on('click', function (e) {

        var id= $(this).attr('id');

        id= id.substring(1,id.length);

        $('#productoid').val(id);

    });

    $('#btnConfirmDanger-eliminarUnFavorito').on('click', function (e) {

        var id=$('#productoid').val();

        $('#tr'+id).remove();

        var url=$('#urlEliminarProductosFavoritos').val();

        wait('#waitPro');

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idsProducto": id
            }
        }).done(function (response) {

            endWait('#waitPro');

            $('#dangerMessage-eliminarUnFavorito').modal('hide');

            $('#btnEliminar').attr('disabled',true);

            alertify.log('El producto ha sido eliminado de favoritos satisfactoriamente.');


        }).error(function () {

            endWait('#waitPro');

            $('#dangerMessage-eliminarUnFavorito').modal('hide');

            alertify.log('Ha ocurrido un error al tratar de elminar el producto de favoritos.');
        });

    });

    $('#tableBody').on('click','.box',function ()
    {
        $('#btnEliminar').attr('disabled',true);

        $('#tableBody .box').each(function (e) {

            if(this.checked)
            {
                $('#btnEliminar').attr('disabled',false);
            }

        });

    });

    $('.preguntaP').on('click', function (e) {

        var id= $(this).attr('id');

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

            endWait('#waitPro');

            $('#realizarPreguntaProducto').modal('hide');

            $('#btnEliminar').attr('disabled',true);

            alertify.log('La pregunta ha sido adicionada satisfactoriamente.');

        }).error(function () {

            endWait('#waitPro');

            $('#realizarPreguntaProducto').modal('hide');

            alertify.log('Ha ocurrido un error al tratar de adicionar la pregunta.');
        });

    });







});
