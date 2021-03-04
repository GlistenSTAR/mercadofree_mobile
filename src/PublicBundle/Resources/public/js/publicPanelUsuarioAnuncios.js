$(document).ready(function () {

    $('#start').val(0);

    function buscarProductosJS()
    {
        var response1=null;

        var url=$('#urlProductos').val();

        wait('#wait');

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "usuarioid":$('#usuarioid').val(),
                "start":$('#start').val(),
                "total":$('#total').val()

            }
        }).done(function (response) {

            response1=response;

            endWait('#wait');

        }).error(function () {
            endWait('#wait');
        });

        return response1;
    }

    function crearProducto(response)
    {
        if(response==null)
        {
            if($('#tableBody').children('tr').length==0)
            {

                var alerto='<div class="alert alert-info-rw " role="alert" style="margin: 5px" >'+
                    'No tiene publicaciones.'+
                    '</div>';
                $('#bodyPublicaciones').append(alerto);
                return;
            }
            return;
        }
        var productos=response.productos;



        $('#start').val(response.start);

        $('#total').val(response.total);

        var img=$('#img-route').val();

        var urlDetalles=$('#urlProductoDetalles').val();

        var urlPublicar=$('#urlProductoPublicar').val();

        var urlModificar=$('#urlProductoModificar').val();

        urlDetalles=urlDetalles.substring(0,urlDetalles.length-1);


        for (var i=0;i<productos.length;i++)
        {
            var tagSpan="";

            var tagLis="";

            if (productos[i][7]=='Publicado')
            {
                tagSpan='<span class="label label-success">'+productos[i][7].toUpperCase()+'</span>';

                tagLis='<li role="presentation"><a id="v'+productos[i][0]+'" class="pausarP" role="menuitem" tabindex="-1" href="#">Pausar</a></li>'+
                    '<li role="presentation"><a data-slug="'+productos[i][13]+'" id="d'+productos[i][0]+'" class="detalles" role="menuitem" tabindex="-1" href="#">Ver anuncio</a></li>'+
                    '<li role="presentation"><a class="preguntaP" role="menuitem" tabindex="-1" data-toggle="modal" href="'+urlModificar+'?idProducto='+productos[i][0]+'">Modificar</a></li>';

            }
            else if(productos[i][7]=='Pendiente')
            {
                tagSpan='<span class="label label-warning">'+productos[i][7].toUpperCase()+'</span>';

                tagLis='<li role="presentation"><a role="menuitem" tabindex="-1" href="'+urlPublicar+'">Publicar</a></li>' +
                '<li role="presentation"><a id="e'+productos[i][0]+'" class="eliminarA" role="menuitem" tabindex="-1" data-toggle="modal" href="#dangerMessage-eliminarProducto">Eliminar</a></li>' ;

            }
            else if(productos[i][7]=='Bloqueado')
            {
                tagSpan='<span class="label label-danger">'+productos[i][7].toUpperCase()+'</span>';

                tagLis='<li role="presentation"><a role="menuitem" tabindex="-1" href="#">¿Por qué bloqueado?</a></li>';

            }
            else if(productos[i][7]=='Finalizado')
            {
                tagSpan='<span class="label label-default">'+productos[i][7].toUpperCase()+'</span>';

                tagLis= '<li role="presentation"><a data-slug="'+productos[i][13]+'" id="d'+productos[i][0]+'" class="detalles" role="menuitem" tabindex="-1" href="#">Ver anuncio</a></li>'+
                        '<li role="presentation"><a id="e'+productos[i][0]+'" class="eliminarA" role="menuitem" tabindex="-1" data-toggle="modal" href="#dangerMessage-eliminarProducto">Eliminar</a></li>';

            }
            
            else if(productos[i][7]=='Pausado')
            {
                tagSpan='<span class="label label-default">'+productos[i][7].toUpperCase()+'</span>';

                tagLis=' <li role="presentation"><a id="v'+productos[i][0]+'" class="pausarP" role="menuitem" tabindex="-1" href="#">Publicar</a></li>'+
                    '<li role="presentation"><a data-slug="'+productos[i][13]+'" id="d'+productos[i][0]+'" class="detalles" role="menuitem" tabindex="-1" href="#">Ver anuncio</a></li>'+
                    '<li role="presentation"><a class="preguntaP" role="menuitem" tabindex="-1" data-toggle="modal" href="'+urlModificar+'?idProducto='+productos[i][0]+'">Modificar</a></li>'+
                    '<li role="presentation"><a id="e'+productos[i][0]+'" class="eliminarA" role="menuitem" tabindex="-1" data-toggle="modal" href="#dangerMessage-eliminarProducto">Eliminar</a></li>';

            }

            var tagTr = '<tr id="tr'+productos[i][0]+'">' +
                '<td><input type="checkbox" class="box" value="'+productos[i][13]+'" data-id="'+productos[i][0]+'"></td>' +
                '<td width="40%">' +
                '<table>' +
                '<tr>' +
                '<td width="17%" style="padding-right: 5px"><img class="img-responsive" src="'+img+productos[i][8]+'"></td>' +
                '<td>' +
                '<a id="d'+productos[i][0]+'" data-slug="'+productos[i][13]+'" class="detalles" href="#">'+productos[i][1]+'</a>' +
                '<p>$ '+productos[i][2]+'</p>' +
                '</td>' +
                '</tr>' +
                '</table>' +
                '</td>' +
                '<td>'+productos[i][12]+' visitas</td>' +
                '<td><table><tr><td id="td'+productos[i][0]+'">'+tagSpan+'</td></tr><tr><td style="font-family: sans-serif; font-size: 0.8em; padding-top: 10px;">'+productos[i][14]+'</td></tr></table></td>' +
                '<td>' +
                '<div class="dropdown mb15">' +
                '<button class="btn btn-rw btn-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">' +
                'Opciones' +
                '<span class="caret"></span>' +
                '</button>' +
                '<ul id="u'+productos[i][0]+'" class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">' +
                    tagLis+
                '</ul>' +
                '</div>' +
                '</td>' +
                '</tr>';

            $('#tableBody').append(tagTr);
        }


    }

     var response=buscarProductosJS();

    crearProducto(response);

    $(window).scroll(function () {
        if (jQuery(window).scrollTop() + jQuery(window).height() == jQuery(document).height())
        {
            if ($('#start').val()!=$('#total').val())
            {
                var response=buscarProductosJS();

                crearProducto(response);

            }

        }
    });
    /////////////////////////////////////////////////////////////////////////////////Eliminar Anuncio
    $('#tableBody').on('click','.box',function ()
    {
        $('#btnEliminarProductos').attr('disabled',true);

        $('#tableBody .box').each(function (e) {

            if(this.checked)
            {
                $('#btnEliminarProductos').attr('disabled',false);
            }

        });

    });


    $('#tableBody').on('click','.eliminarA', function () {

        var idProducto=$(this).attr('id');

        idProducto=idProducto.substring(1,idProducto.length);

        $('#productoid').val(idProducto);

        $('#indicador').val(0);

    });

    $('#btnEliminarProductos').on('click', function (e) {

        var data="";

        $('.box').each(function () {

            if(this.checked)
            {
                data=data+','+ $(this).data('id');

                /*var temp=$('#tr'+this.value+' td:nth-child(4)').text();

                 inversion=inversion+','+temp.substring(1,temp.length);*/

            }
        });

        if(data!="")
        {
            $('#productoid').val(data);

            $('#indicador').val(1);
        }
        else
        {
            e.stopImmediatePropagation();
        }



    });

    $('#btnConfirmDanger-eliminarProducto').on('click', function (e) {

        var id=$('#productoid').val();

        var indicador= $('#indicador').val();

        var url=$('#urlProductosEliminar').val();

        wait('#wait');

        if(indicador==1)
        {
            var ids=id.split(',');

            for(var i=0;i<id.length;i++)
            {
                if(ids[i]!="")
                {
                    $('#tableBody #tr'+ids[i]).remove();
                }
            }

        }
        else if(indicador==0)
        {
            $('#tableBody #tr'+id).remove();
        }


        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idProducto": id,
                "indicador": indicador
            }
        }).done(function (response) {

            endWait('#wait');

            $('#dangerMessage-eliminarProducto').modal('hide');

            alertify.success('El producto ha sido eliminado satisfactoriamente');

        }).error(function () {

            endWait('#wait');

            $('#dangerMessage-eliminarProducto').modal('hide');

            alertify.error('Ha ocurrido un error al tratar de eliminar el producto.');
        });


    });
    /////////////////////////////////////////////////////////////////Cambiar estado
    $('#tableBody').on('click','.pausarP', function (e) {

        e.preventDefault();

        var urlDetalles=$('#urlProductoDetalles').val();

        var urlModificar=$('#urlProductoModificar').val();

        var id=$(this).attr('id');

        id=id.substring(1,id.length);

        var url=$('#urlProductosCambiarEstado').val();

        wait('#wait');

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idProducto": id
            }
        }).done(function (response) {
            var estado=response;

            var tagLis="";

            var tagSpan="";

            if(estado==3)
            {
                tagSpan='<span class="label label-default">PAUSADO</span>';

                tagLis=' <li role="presentation"><a id="v'+id+'" class="pausarP" role="menuitem" tabindex="-1" href="#">Publicar</a></li>'+
                    '<li role="presentation"><a id="d'+id+'" class="detalles" role="menuitem" tabindex="-1" href="#">Ver anuncio</a></li>'+
                    '<li role="presentation"><a class="preguntaP" role="menuitem" tabindex="-1" data-toggle="modal" href="'+urlModificar+'">Modificar</a></li>'+
                    '<li role="presentation"><a id="e'+id+'" class="eliminarA" role="menuitem" tabindex="-1" data-toggle="modal" href="#dangerMessage-eliminarProducto">Eliminar</a></li>';

            }
            if(estado==4)
            {
                tagSpan='<span class="label label-success">PUBLICADO</span>';

                tagLis='<li role="presentation"><a id="v'+id+'" class="pausarP" role="menuitem" tabindex="-1" href="#">Pausar</a></li>'+
                    '<li role="presentation"><a id="d'+id+'" class="detalles" role="menuitem" tabindex="-1" href="#">Ver anuncio</a></li>'+
                    '<li role="presentation"><a class="preguntaP" role="menuitem" tabindex="-1" data-toggle="modal" href="'+urlModificar+'">Modificar</a></li>'+
                    '<li role="presentation"><a id="e'+id+'" class="eliminarA" role="menuitem" tabindex="-1" data-toggle="modal" href="#dangerMessage-eliminarProducto">Eliminar</a></li>';

            }

            $('#tableBody #u'+id+' li').remove();

            $('#tableBody #u'+id).append(tagLis);

            $('#tableBody #td'+id+' span').remove();

            $('#tableBody #td'+id).append(tagSpan);


            endWait('#wait');

            alertify.success('El producto ha sido pausado satisfactoriamente.');

        }).error(function () {

            endWait('#wait');

            alertify.log('Ha ocurrido un error al tratar de pausar el producto');


        });



    });
    //////////////////////////////////////////////////////Ver Anuncio

    $('#tableBody').on('click','.detalles', function (e) {

        e.preventDefault();

        var urlDetalles=$('#urlProductoDetalles').val();

        var id=$(this).data('slug');

        urlDetalles = urlDetalles.replace(urlDetalles.substr(urlDetalles.length-1), id);

        var tab=window.open(urlDetalles,'_blank');

        tab.focus();

    });





});
