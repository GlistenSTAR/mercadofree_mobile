var urlImprimirEtiqueta = '';

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
        
        //Si existen elementos para ver calificaciones previas removemos los eventos onclick
        //para no ejecutar dos o mas veces el mismo script de asignacion de datos
        $(".detalle-calificacion-button").prop("onclick", null).off("click");
        
        var pedidos=response['pedidos'];

        for(var i=0;i<pedidos.length;i++){
            var pedido=pedidos[i];
            
            var valoracion = null;
            if(pedido.tiene_valoracion) {
                valoracion = pedido.valoracion_pedido;
            }
            
            var tr='<tr id="tr'+pedido.id+'">';
            tr+='<td><input class="box" type="checkbox" value="'+pedido.id+'"/></td>';
            tr+='<td>'+pedido.codigo+'</td>';
            tr+='<td>'+pedido.fecha+'</td>';
            tr+='<td>$ '+pedido.monto+'</td>';
            tr+='<td style="text-transform: capitalize;" class="estadoTexto">'+pedido.estado+'</td>';
            tr+='<td><div class="dropdown mb'+pedido.id+'"><button id="dropdownMenu1" class="btn btn-rw btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">';
            tr+='Opciones <span class="caret"></span></button>';
            tr+='<ul id="ul'+pedido.id+'" class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">';
            tr+='<li role="presentation"><a role="menuitem" data-id="'+pedido.id+'" class="btnCambiarEstado" href="#">Cambiar estado</a></li>';
            tr+='<li role="presentation"><a role="menuitem"  href="'+pedido.url+'">Ver detalles</a></li>';
            tr+='<li role="presentation"><a role="menuitem" class="btnEliminarPedido" data-id="'+pedido.id+'" href="#">Eliminar</a></li>';
            tr+='</ul></div>';
            if(valoracion) {
                tr+='<br/><a class="btn btn-rw btn-info detalle-calificacion-button" href="#modalDetalleCalificacionPedido" data-toggle="modal" data-codigo-pedido="'+pedido.codigo+'" data-tiempo-entrega="'+valoracion.detalle_tiempo_entrega+'" data-calidad-producto="'+valoracion.detalle_calidad_producto+'" data-compra-aceptada="'+valoracion.compra_aceptada+'" data-motivo-rechazo="'+valoracion.motivo_rechazo+'"> Calificación </a>';
            }
            if(pedido.puede_imprimir_pdf) {
                tr+='<br/><a class="btn btn-rw btn-info imprimir-etiqueta-button" href="#modalImprimirPDFPedido" data-toggle="modal" data-codigo-pedido="'+pedido.codigo+'">Imprimir Etiqueta</a>';
            }
            tr+='</td></tr>';

            $('#pedidosListado').append($(tr));
        }
        
        $('.detalle-calificacion-button').click(function () {
            $('.label-codigo-pedido').html($(this).data('codigo-pedido'));
            $('.label-tiempo-entrega').html($(this).data('tiempo-entrega'));
            $('.label-calidad-producto').html($(this).data('calidad-producto'));
            var compraAceptada = $(this).data('compra-aceptada');
            if(compraAceptada) {
                $('.label-compra-aceptada').html('La compra fue aceptada');
                $('.motivo-rechazo-row').hide();
            } else {
                $('.label-compra-aceptada').html('La compra fue rechazada');
                $('.label-motivo-rechazo').html($(this).data('motivo-rechazo'));
                $('.motivo-rechazo-row').show();
            }
        });
        
        $('.imprimir-etiqueta-button').click(function () {
            console.log('imprimir etiqueta');
            
            $('.label-codigo-pedido').html($(this).data('codigo-pedido'));
            urlImprimirEtiqueta = $('#urlImprimirEtiqueta').val()+'/'+ $(this).data('codigo-pedido');
            $('.continuarImprimirEtiquetaButton').attr("href",urlImprimirEtiqueta + '/1');
            $('.noNotificarCompradorImprimirEtiquetaButton').attr("href",urlImprimirEtiqueta + '/0');
        });
        
        $('.continuarImprimirEtiquetaButton').click( function() {
            $('.continuarImprimirEtiquetaButton').attr('disabled', true);
            $('.noNotificarCompradorImprimirEtiquetaButton').attr('disabled', true);
            setTimeout( function() {
                $('.continuarImprimirEtiquetaButton').attr('disabled', false);
                $('.noNotificarCompradorImprimirEtiquetaButton').attr('disabled', false);
                $('#modalImprimirPDFPedido').modal("hide");
            }, 1000);
                    
        });
        
        $('.noNotificarCompradorImprimirEtiquetaButton').click(function() {
            $('.continuarImprimirEtiquetaButton').attr('disabled', true);
            $('.noNotificarCompradorImprimirEtiquetaButton').attr('disabled', true);
            setTimeout( function() {
                $('.continuarImprimirEtiquetaButton').attr('disabled', false);
                $('.noNotificarCompradorImprimirEtiquetaButton').attr('disabled', false);
                $('#modalImprimirPDFPedido').modal("hide");
            }, 1000);
        });
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

    ///////////////////////////////////////////////////////////////////////////////// Cambiar estado

    $('#pedidosListado').on('click','.btnCambiarEstado',function (e) {
        e.preventDefault();

        var id=$(this).attr('data-id');
        var urlCambiarEstado=$('#urlPedidoCambiarEstado').val();
        $('#nuevoEstadoPedido').removeAttr('disabled');

        $.ajax(urlCambiarEstado,{
            'type':'get',
            'dataType':'json',
            'data':{
                'idPedido':id
            }
        }).done(function (response) {
            if(!response[0]){
                alert(response[1]);
            }
            else{
                var estadoActual=response.estadoActual;
                var next=response.next;

                $('#estadoActualPedido').val(estadoActual.nombre);

                $('#idPedidoModalEstados').val(id);

                $('#nuevoEstadoPedido').empty();

                if(next.length > 0){
                    $('#nuevoEstadoPedido').append($('<option value="">Seleccione el nuevo estado</option>'));

                    for(var i=0; i<next.length; i++){
                        var opt='<option value="'+next[i].slug+'">'+next[i].nombre+'</option>';

                        $('#nuevoEstadoPedido').append($(opt));
                    }
                }
                else{
                    $('#nuevoEstadoPedido').append($('<option value="">Sin estados</option>'));
                    $('#nuevoEstadoPedido').attr('disabled','disabled');
                }


                $('#modalCambiarEstadoPedido').modal('show');
            }
        })

    });

    $('#btnOkCambiarEstadoPedido').on('click',function () {
        var $form=$('#cambiarEstadoPedidoForm');
        $('#cambiarEstadoPedidoForm div#error').hide();

        if($('#nuevoEstadoPedido').val()==""){
            $('#cambiarEstadoPedidoForm div#error').show();
        }
        else{
            $.ajax($form.attr('action'),{
                type:$form.attr('method'),
                dataType:'json',
                data:$form.serialize()
            }).done(function (response) {
                if(!response[0]){
                    alertify.error(response[1]);
                }
                else{
                    var selector="#tr"+$('#idPedidoModalEstados').val()+' .estadoTexto';

                    var nombreEstado=response.nombre_estado;

                    $(selector).empty();
                    $(selector).append(nombreEstado);

                    alertify.success('El estado del pedido se ha cambiado satisfactoriamente.');
                }

                $('#modalCambiarEstadoPedido').modal('hide');
            });
        }
    });


    ///////////////////////////////////////////////////////////////////////////////////////////////Eliminar Pedido

    $('#pedidosListado').on('click','.btnEliminarPedido',function (e) {
        e.preventDefault();

        var id=$(this).attr('data-id');

        $('#idPedido').val(id);

        $('#dangerMessage-eliminarPedido').modal('show');
    });

    $('#btnConfirmDanger-eliminarPedido').on('click', function (e) {

        var id=$('#idPedido').val();

        var url=$('#urlEliminarPedido').val();

        wait('#wait');

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idPedido": id
            }
        }).done(function (response) {

            endWait('#wait');

            if(!response[0]){
                alertify.error(response[1]);
            }
            else{
                var selector="#tr"+id;

                $(selector).remove();
                alertify.success("El pedido ha  sido eliminado satisfactoriamente.");
            }

            $('#dangerMessage-eliminarPedido').modal('hide');

        });


    });


});
