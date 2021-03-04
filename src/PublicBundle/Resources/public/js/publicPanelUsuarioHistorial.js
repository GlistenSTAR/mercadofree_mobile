$(document).ready(function () {
    function buscarProductosJS() {
        var response1 = null;

        var url = $('#urlProductos').val();

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                'indicador': 1
            }
        }).done(function (response) {

            response1 = response;

        }).error(function () {

        });

        return response1;
    }

    function crearProductos(response) {

        var productosPorMes = response.productos;

        var img = $('#img-route').val();

        var urlDetalles = $('#urlProductoDetalles').val();

        urlDetalles = urlDetalles.substring(0, urlDetalles.length - 1);

        var listaProducto = "";

        var listaProductoTemp = "";


        var cont = 1;


        //  var fecha=productos[0][11];
        //
        // var fechaInicial=productos[0][11];

        var flag = true;

        var meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

        $.each(productosPorMes, function (mes, value) {
            var fe = mes.split('/');

            listaProducto += '<div class="col-md-12" style="padding: 5px; font-size: 25px; color: #167d9f;"><strong>' + meses[parseInt(fe[0] - 1)] + '</strong></div>';

            for (var i = 0; i < value.length; i++) {

                var tagProducto = '<div class="col-lg-3 mb30">' +
                    '<div class="view image-hover-1 no-margin">' +
                    '<div class="product-container">' +
                    '<img class="img-responsive full-width" src="' + img + value[i][8] + '" alt="...">';
                //'<span class="badge home-badge">'+
                //'12% OFF'+
                //'</span>'+
                if (value[i][9]) {
                    tagProducto += '<span class="badge home-badge transporte"><i class="fa fa-truck"></i></span>';
                }


                tagProducto += '</div>' +
                    '<div class="mask">' +
                    '<div class="image-hover-content">' +
                    // '<a href="' + img + value[i][8] + '" class="info image-zoom-link">' +
                    // '<div class="image-icon-holder"><span class="ion-ios7-search image-icons"></span></div>' +
                    // '</a>' +
                    //'<a href="' + urlDetalles + value[i][0] + '" class="info">' +
                    '<a href="' + value[i][10] + '" class="info">' +
                    '<div class="image-icon-holder"><span class="ion-link image-icons"></span></div>' +
                    '</a>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="shop-product content-box-shadow">' +
                    '<div class="row">' +
                    '<div class="col-md-10">' +
                    //'<a class="product-title" href="' + urlDetalles + value[i][0] + '"><h2 class="product-title">' + value[i][1] + '</h2></a>' +
                    '<a class="product-title" href="' + value[i][10] + '"><h2 class="product-title">' + value[i][1] + '</h2></a>' +
                    '</div>' +
                    '<div class="col-md-2">';
                if (value[i][11]) {
                    tagProducto += '<a data-id="' + value[i][0] + '" title="Producto favorito" data-togle="tooltip"><i class="ion-ios7-heart"></i></a>';
                }
                else {
                    tagProducto += '<a data-id="' + value[i][0] + '" href="#" class="bookmark_product" title="Marcar como favorito" data-togle="tooltip"><i class="ion-ios7-heart-outline"></i></a>';
                }
                tagProducto += '</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-md-5">' +
                    '<span class="pull-left label label-default shop-product-price"> $ ' + value[i][2] + '</span>' +
                    '</div>' +
                    //'<div class="col-md-7" style="text-align:left;">'+
                    //'<span class="precio-anterior pull-left">$'+productos[i][2]+'</span>'+
                    //'</div>'+
                    '</div>' +
                    '</div>' +
                    '</div>';


                listaProductoTemp += tagProducto;

                if (cont % 4 == 0) {

                    var tagRow = '<div class="row">';

                    tagRow += listaProductoTemp +
                        '</div>';

                    listaProducto += tagRow;

                    listaProductoTemp = "";

                    /*fecha=productos[i][11];*/
                }
                if (value.length < 4) {
                    var tagRow = '<div class="">';

                    tagRow += listaProductoTemp +
                        '</div>';

                    listaProducto += tagRow;

                    listaProductoTemp = "";
                }
                cont++;
            }
        });

        $('#containerProductoHistorial').append(listaProducto);
    }

    function Comienzo() {
        var response = buscarProductosJS();

        if (response.productos.length == 0) {
            var tagDiv = '<div class="alert alert-success" role="alertdialog" style="margin-top: 20px;margin-bottom: 10%;">' +
                'No existen productos en el historial.' +
                '</div>';

            $('#containerProductoHistorial').append(tagDiv);
        }
        else {

            crearProductos(response);
        }

        if (response.productos.length == 0) {
            $('#eliminarHistorial').hide();

        }

    }

    Comienzo();

    //////////////////////////////////////////////////////////////////////////////////////////Eliminar historial
    $('#btnConfirmDanger-eliminarHistorial').on('click', function (e) {

        e.preventDefault();
        $("#dangerMessage-eliminarHistorial").modal('hide');
        wait('#containerProductoHistorial');
        var url = $('#urlProductos').val();
        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                'indicador': 2
            }
        }).done(function (response) {

            $('#containerProductoHistorial div').remove();

            var tagDiv = '<div class="alert alert-success" role="alertdialog" style="margin-top: 20px;margin-bottom: 10%;">' +
                'No existen elementos en el historial.' +
                '</div>';

            $('#containerProductoHistorial').append(tagDiv);
            endWait('#containerProductoHistorial');

            alertify.success("El historial ha sido eliminado satisfactoriamente.")
        }).error(function () {
            endWait('#containerProductoHistorial');
            alertify.success("Ha ocurrido un error al tratar de eliminar el historial.")
        });

    });
});
