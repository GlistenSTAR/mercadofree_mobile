$(document).ready(function () {



////////////////////////////////////////////////////////////////////////////////////////////Adicionar Pregunta

$('#btnPregunta').on('click', function ()
{
    var url=$('#adicionarPregunta').val();

    wait('#waitPregunta');

    $.ajax(url, {
        type: 'post',
        dataType: 'json',
        async: false,
        data:
            {
                'pregunta':$('#datosPregunta').val(),
                'idProducto':$('#idProducto').val()
            }
    }).done(function (response) {

        if(!response.error) {
            var tagPregunta='<p><i class="ion-ios7-chatboxes-outline"></i> <b>'+$('#datosPregunta').val()+'</b></p> <hr class="mt10 mb25">';

            $('#containerPreguntas').append(tagPregunta);
            $('#datosPregunta').val("");

            if($('#noPreguntas').length>0)
            {
                $('#noPreguntas').remove();
            }

            if( !$('#errorPregunta').hasClass( "hidden" )) {
                $('#errorPregunta').addClass("hidden");
            }

            endWait('#waitPregunta');

            alertify.log('Operación satisfactoria.');
        } else {
            $('#errorPregunta').removeClass("hidden");
            endWait('#waitPregunta');
        }

    })
        .error(function () {
            endWait('#waitPregunta');
        });

});
////////////////////////////////////////////////////////////////////////////////////////////Adicionar Valoracion

    $('#btnValoracion').on('click', function (e)
    {
        e.preventDefault();
        var $form=$('#comentarioForm');

        var dd=null;

        dd=validatorValoracion;

        if (dd.form())
        {
            wait('#waitValoracion');

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                async: false,
                data:$form.serialize()
            }).done(function (response) {

                if(!response.error) {
                    var listStar="";

                    var punt=parseInt($('#puntuacion').val());

                    for (var i=0; i<punt;i++)
                    {
                        var tagStar='<i class="fa fa-star"></i> ';

                        listStar+=tagStar;
                    }

                    var f=5-punt;

                    for (var j=0; j<f;j++)
                    {
                        var tagNoStar='<i class="fa fa-star-o"></i> ';

                        listStar+=tagNoStar;
                    }



                    var tagValoracion='<div>'+
                            '<div class="media-body">'+
                            '<h4 class="media-heading">'+
                            '<span class="text-muted">'+
                            listStar+
                            '</span>'+
                            '<p class="text-muted">September 8, 2014 - 3:23pm</p>'+
                            '</h4>'+
                            '<p>'+$('#comentario').val()+'</p>'+
                            '</div>'+
                            '</div>'+
                            '<hr class="mt10 mb25">';

                            $('#containerValoracion').append(tagValoracion);
                    $('#nombre').val("");
                    $('#tema').val("");
                    $('#puntuacion').val("");
                    $('#comentario').val("");

                    if($('#noComentario').length>0)
                    {
                        $('#noComentario').remove();
                    }

                    var total=$('#totalValoracion').val();

                    $('#totalValoracion').val(parseInt(total)+1);

                    $('#totalV').text(parseInt(total)+1);
                    
                    if( !$('#errorValoracion').hasClass( "hidden" )) {
                        $('#errorValoracion').addClass("hidden");
                    }

                    endWait('#waitValoracion');

                    alertify.log('Operación satisfactoria.');
                } else {
                    $('#errorValoracion').removeClass("hidden");
                    
                    endWait('#waitValoracion');
                }

            })
                .error(function () {
                    endWait('#waitValoracion');

                    alertify.log('Operación insatisfactoria.');
                });
        }


    });

    $('#verMasOpiniones').on('click', function (e) {
        e.preventDefault();

        var url=$('#urlObtenerValoraciones').val()+"/2";

        if($('#startValoracion').val()!=$('#totalValoracion').val())
        {
            wait('#containerValoracion');
            $.ajax(url, {
                type: 'post',
                dataType: 'json',
                async: false,
                data:{
                    'start':$('#startValoracion').val(),
                    'total':$('#totalValoracion').val(),
                    'idProducto':$('#idProducto').val()
                }

            }).done(function (response) {
                $('#startValoracion').val(response.start);

                $('#totalValoracion').val(response.total);

                var valoraciones=response.valoraciones;

                var listVal="";

                for (var d=0;d<valoraciones.length;d++)
                {
                    var listStar="";

                    var punt=parseInt(valoraciones[d][1]);

                    for (var i=0; i<punt;i++)
                    {
                        var tagStar='<i class="fa fa-star"></i> ';

                        listStar+=tagStar;
                    }

                    var f=5-punt;

                    for (var j=0; j<f;j++)
                    {
                        var tagNoStar='<i class="fa fa-star-o"></i> ';

                        listStar+=tagNoStar;
                    }
                    var tagValoracion='<div>'+
                        '<div class="media-body">'+
                        '<h4 class="media-heading">'+
                        '<span class="text-muted">'+
                        listStar+
                        '</span>'+
                        '<p class="text-muted">September 8, 2014 - 3:23pm</p>'+
                        '</h4>'+
                        '<p>'+valoraciones[d][0]+'</p>'+
                        '</div>'+
                        '</div>'+
                        '<hr class="mt10 mb25">';


                    listVal+=tagValoracion;

                }

                $('#containerValoracion').append(listVal);

                endWait('#containerValoracion');
            })
                .error(function () {
                    endWait('#containerValoracion');
                });
        }

    });

    ////////////////////////////////////////////////////////////////////////////////////////////Adicionar a la cesta

    $('#adicionarACesta').on('click',function(e){
        e.preventDefault();

        wait('#bg-boxed');

        //enviar el form

        var $formAddProd=$('#formAddProdCesta');

        $.ajax($formAddProd.attr('action'),{
            'type':$formAddProd.attr('method'),
            'dataType':'json',
            'data':$formAddProd.serialize()
        }).done(function(response){
            endWait('#bg-boxed');

            if(!response[0]){
                alert(response[1]);
            }
            else{
                //Actualizar el numero de productos de la cesta en el icono del menu

                $('li#cartItem p.cart-qty-preview').empty();

                var cant=response[2];

                $('li#cartItem p.cart-qty-preview').append(cant);

                var prod=response[1];


                //Actualizar listado de productos del preview de la cesta

                var $tablePreview=$('#table-preview-cesta');

                var body='';

                if(cant==1){ //En caso que sea el primer item que se agrega
                    $tablePreview.empty();

                    $tablePreview.append($('<thead><tr><th class="quantity">Cant</th><th class="product">Producto</th><th class="amount">Subtotal</th></tr></thead>'));

                    body=$('<tbody id="table-preview-cesta-body"></tbody>');
                }
                else{ //En caso que ya hayan mas productos en la cesta
                    body=$('#table-preview-cesta-body');
                }

                //A partir de aqui se aplica para los dos casos

                var rutaImagen=$('#urlImagenProducto').val()+prod.imagen;

                var urlProducto=$('#urlDetalleProducto').val();

                var itemProd='<tr id="preview-item-'+prod.id+'"><td class="quantity">'+prod.cantidad+' x</td>';
                itemProd+='<td class="product"><table><tr><td width="30%"><img style="width: 70%" class="img-responsive" src="'+rutaImagen+'"></td>';
                itemProd+='<td><a href="'+urlProducto+'">'+prod.titulo+'</a></td></tr></table></td>';
                if(prod.precioOferta>0){
                    itemProd+='<td class="amount">$ '+(prod.precioOferta*prod.cantidad)+'</td>';
                }
                else{
                    itemProd+='<td class="amount">$ '+(prod.precio*prod.cantidad)+'</td>';
                }

                itemProd+='</tr>';

                body.prepend($(itemProd));

                if(cant==1){ //Agrego la fila de los botones al final

                    var urlCarrito=$('#urlCarrito').val();
                    body.append($('<tr><td class="text-right" colspan="3"><a href="'+urlCarrito+'"><button class="btn btn-rw btn-primary btn-sm">Ver carrito</button></a></td></tr>'));
                }

                $tablePreview.append(body);

                //Llenar el modal con los datos del producto


                $('#modalProductoAnnadido .productImg').attr('src',rutaImagen);

                $('#modalProductoAnnadido #productTitle').empty();

                $('#modalProductoAnnadido #productTitle').append(prod.titulo);

                var precio='$ '+prod.precio;
                var precioOferta='$ '+prod.precioOferta+' &nbsp;';

                $('#modalProductoAnnadido #productPrice').empty();

                if(prod.precioOferta>0){
                    precio='<span class="precio-anterior">$ '+prod.precio+'</span>';

                    $('#modalProductoAnnadido #productPrice').append(precioOferta);
                    $('#modalProductoAnnadido #productPrice').append($(precio));
                }
                else{
                    $('#modalProductoAnnadido #productPrice').append(precio);
                }

                var cantidad='Cantidad: '+prod.cantidad;

                $('#modalProductoAnnadido #productQty').empty();
                $('#modalProductoAnnadido #productQty').append(cantidad);

                //Mostrar el modal

                $('#modalProductoAnnadido').modal('show');


            }
        });

    });

    //////////////////////////////////////////////////////////////////////////////////////////// Comprar Ahora

    $('.comprar-producto-ahora').on('click',function (e) {
        e.preventDefault();

        var idProducto=$('#idProducto').val();
        var cant = $(this).parent().siblings('.cantidad-container').find('.cantidadProd').val();

        var url=$('#urlComprarAhora').val();

        $.ajax(url,{
            type:'post',
            dataType:'json',
            data:{
                'idProducto':idProducto,
                'cant':cant
            }
        }).done(function (response) {
            if(response[0]){
                var idPedido=response[1];
                //redireccionar para conf envio

                var urlEnvio=$('#urlEnvio').val()+'?idProducto='+idProducto+'&buyNow=1';

                location.href=urlEnvio;
            }
        });
    });

});
