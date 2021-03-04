$(document).ready(function () {

    $('#start').val(0);

    function buscarPedidosJS()
    {
        var response1=null;

        var url=$('#urlPedidos').val();

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

    function crearPedido(response) {
        var pedidos=response['pedidos'];

        for(var i=0;i<pedidos.length;i++){
            var pedido=pedidos[i];
            var tr='<tr id="tr'+pedido.id+'">';
            tr+='<td>'+pedido.codigo+'</td>';
            tr+='<td>'+pedido.fecha+'</td>';
            tr+='<td>'+pedido.monto+'</td>';
            tr+='<td>'+pedido.estado+'</td>';
            tr+='<td><div class="dropdown mb'+pedido.id+'"><button id="dropdownMenu1" class="btn btn-rw btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">';
            tr+='Opciones<span class="caret"></span></button>';
            tr+='<ul id="ul'+pedido.id+'" class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">';
            tr+='<li role="presentation"><a role="menuitem"  href="'+pedido.url+'">Ver detalles</a></li>';
            tr+='</ul></div></td></tr>';

            $('#pedidosListado').append($(tr));
        }
    }



     var response=buscarPedidosJS();

     crearPedido(response);

    $(window).scroll(function () {
        if (jQuery(window).scrollTop() + jQuery(window).height() == jQuery(document).height())
        {
            if ($('#start').val()!=$('#total').val())
            {
                var response=buscarPedidosJS();

                crearPedido(response);

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

    //////////////////////////////////////////////////////Ver Anuncio

    $('#tableBody').on('click','.detalles', function (e) {

        e.preventDefault();

        var urlDetalles=$('#urlProductoDetalles').val();

        var id=$(this).attr('id');

        id=id.substring(1,id.length);

        var tab=window.open(urlDetalles+id,'_blank');

        tab.focus();

    });





});
