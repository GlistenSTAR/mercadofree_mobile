$(document).ready(function () {

    function reiniciarStartTotal() {

        $('#start').val(0);
        $('#total').val(1);

    }

    function buscarProductosJS() {
        var response1 = null;

        var url = $('#urlBuscarProductosPaginado').val();

        wait('#wait');

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "valorSearch": "",
                "precioMin": $('#precioMin').val(),
                "precioMax": $('#precioMax').val(),
                "categoriaid": $('#categoriaid').val(),
                "condicionid": $('#condicionid').val(),
                "ciudadid": $('#ciudadid').val(),
                "tipoenvioid": $('#tipoenvioid').val(),
                "start": $('#start').val(),
                "total": $('#total').val(),
                "usuarioid": $('#usuarioid').val()

            }
        }).done(function (response) {

            response1 = response;
            endWait('#wait');

        }).error(function () {
            endWait('#wait');
        });

        return response1;
    }

    function crearProductos(response) {
        var productos = response.productos;

        var img = $('#img-route').val();

        var urlDetalles = $('#urlProductoDetalles').val();

        urlDetalles = urlDetalles.substring(0, urlDetalles.length - 1);

        var listaProducto = "";

        var listaProductoTemp = "";

        var cont = 1;

        if (response.total > 0) {
            for (var i = 0; i < productos.length; i++) {
                var tagProducto = '<div class="col-lg-4 col-md-4 col-sm-6 mb30">' +
                    '<div class="view image-hover-1 no-margin">' +
                    '<div class="product-container">' +
                    '<img class="img-responsive full-width" src="' + img + productos[i].imagen + '" alt="...">';
                //'<span class="badge home-badge">'+
                //'12% OFF'+
                //'</span>'+
                if (productos[i].envio_domicilio) {
                    tagProducto += '<span class="badge home-badge transporte"><i class="fa fa-truck"></i></span>';
                }
                tagProducto += '</div>' +
                    '<div class="mask">' +
                    '<div class="image-hover-content">' +
                    '<a href="' + img + productos[i].imagen + '" class="info image-zoom-link">' +
                    '<div class="image-icon-holder"><span class="ion-ios7-search image-icons"></span></div>' +
                    '</a>' +
                    '<a href="' + urlDetalles + productos[i].slug + '" class="info">' +
                    '<div class="image-icon-holder"><span class="ion-link image-icons"></span></div>' +
                    '</a>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="shop-product content-box-shadow">' +
                    '<div class="row">' +
                    '<div class="col-md-10">' +
                    '<a class="product-title" href="' + urlDetalles + productos[i].slug + '"><h2 class="product-title">' + productos[i].nombre + '</h2></a>' +
                    '</div>' +
                    '<div class="col-md-2">';
                if (productos[i].favorito) {
                    tagProducto += '<a data-id="' + productos[i].id + '" title="Producto favorito" data-togle="tooltip"><i class="ion-ios7-heart"></i></a>';
                }
                else {
                    tagProducto += '<a data-id="' + productos[i].id + '" href="#" class="bookmark_product" title="Marcar como favorito" data-togle="tooltip"><i class="ion-ios7-heart-outline"></i></a>';
                }
                tagProducto += '</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-md-5">' +
                    '<span class="pull-left label label-default shop-product-price"> $ ' + productos[i].precio + '</span>' +
                    '</div>' +
                    //'<div class="col-md-7" style="text-align:left;">'+
                    //'<span class="precio-anterior pull-left">$'+productos[i][2]+'</span>'+
                    //'</div>'+
                    '</div>' +
                    '</div>' +
                    '</div>';

                listaProductoTemp += tagProducto;

                if (cont % 3 == 0 || cont == productos.length) {
                    var tagRow = '<div class="row">' +
                        listaProductoTemp +
                        '</div>';

                    listaProducto += tagRow;
                    listaProductoTemp = "";
                }

                cont++;

            }
        } else {
            listaProducto =  '<div class="alert alert-success" role="alertdialog" style="margin-top: 20px;margin-bottom: 10%;">No existen productos en esta tienda.</div>';

        }

        $('#containerProducto').append(listaProducto);

        //////////////////////////////////////////////////////////////////////////////Total

        $('#total').val(response.total);

        $('#start').val(response.start);


        var listaCategorias = response.listaCategoriasFiltro;

        /*if (listaCategorias.length>0)
        {
            var categoriaid=response.categoriaid;

            $('#categoriaid').val(categoriaid);

            var categorias="";

            for (var i=0;i<listaCategorias.length;i++)
            {
                if(i<=10)
                {
                    var tagCategoria='<p class="catFiltro" ><a href="'+$('#urlBuscarProductos').val()+'?categoriaid='+listaCategorias[i][1]+'">'+listaCategorias[i][2]+'</a></p>';
                }
                else
                {
                    var tagCategoria='<p class="catFiltro" hidden ><a href="'+$('#urlBuscarProductos').val()+'?categoriaid='+listaCategorias[i][1]+'">'+listaCategorias[i][2]+' </a></p>';
                }

                categorias+=tagCategoria;
            }
            if(listaCategorias.length>10)
            {
                categorias+='<p ><a id="verCatFiltro" class="default" href="#">Ver todos</a></p>';
            }
            $('#containerCategoriaListado p').remove();
            $('#containerCategoriaListado').append(categorias);
        }


        //////////////////////////////////////////////////////////////////////////////Total

        $('#total').val(response.total);


        $('#start').val(parseInt(response.start)+parseInt($('#start').val()));


        //////////////////////////////////////////////////////////////////////////////Ubicacion

        var ciudades=response.ciudadesProduct;

        if (ciudades!="")
        {
            var categoriaid=response.categoriaid;

            $('#categoriaid').val(categoriaid);

            var ubicaciones="";

            for (var i=0;i<ciudades.length;i++)
            {
                if(i<=7)
                {
                    var tagUbicacion='<p class="city" id="'+ciudades[i].id+'" ><input type="checkbox" class="ubicacion" id="'+ciudades[i].id+'"> '+ ciudades[i].ciudadNombre +'</p>';
                }
                else if ($('#ciudadid').val()!=ciudades[i].id)
                {
                    var tagUbicacion='<p class="city" hidden id="'+ciudades[i].id+'" ><input type="checkbox" class="ubicacion" id="'+ciudades[i].id+'"> '+ ciudades[i].ciudadNombre+'</p>';
                }

                ubicaciones+=tagUbicacion;
            }
            if(ciudades.length>7)
            {
                ubicaciones+='<p ><a id="verCatFiltro" class="default" href="#">Ver todos</a></p>';
            }
            $('#containerUbicacionesListado p').remove();
            $('#containerUbicacionesListado').append(ubicaciones);        }


        ////////////////////////////////////////////////////////////////////////////////////////////////////////Filtro Envio

        var tipoenvio=response.tipoenvio;

        if (tipoenvio!="")
        {
            var categoriaid=response.categoriaid;

            $('#categoriaid').val(categoriaid);

            var tiposenvios="";

            for (var i=0;i<tipoenvio.length;i++)
            {

                var tagTipoEnvio='<p class="tipoenvio"  ><input type="checkbox" class="te" id="'+tipoenvio[i].id+'"> '+tipoenvio[i].nombre+'</p>';

                tiposenvios+=tagTipoEnvio;
            }

            $('#containerTipoEnviosListado p').remove();
            $('#containerTipoEnviosListado').append(tiposenvios);
        }

        var catBread=response.categoriasBread;

        if (catBread.length>0)
        {
            var categoriaid=response.categoriaid;

            $('#categoriaid').val(categoriaid);

            var cat="";



            $('#containerBreadCrumb li').remove();
            $('#containerBreadCrumb').append('<li><span class="ion-home breadcrumb-home"></span><a href="'+$('#urlPublicHome')+'">Inicio</a></li>');

            var breads="";

            var lastCat="";

            for (var i=0;i<catBread.length;i++)
            {
                var tagli='<li><a href="'+$('#urlBuscarProductos').val()+'?categoriaid='+catBread[i][0]+'">'+catBread[i][1]+'</a></li>';

                breads+=tagli;

                lastCat=catBread[i][1];
            }


            $('#containerBreadCrumb').append(breads);
        }

        $('#headSmall').text(response.total>0?(response.total>1?response.total+' resultados':response.total+' resultado'):' No existen resultados');

        $('#categoriaName').text(lastCat);*/
    }

    function comienzo() {

        var response = buscarProductosJS();

        crearProductos(response);
    }

    reiniciarStartTotal();

    comienzo();

    /// Toggle filtro mobile

    toggleFiltroProductos();

    ////////////////////////////////////////////////////////////////////////////////////////////////////////Filtro Categoria

    $('.categoria').on('click', function (e) {

        e.preventDefault();

        $('#categoriaid').val($(this).attr('id'));

        $('#containerProducto div').remove();

        reiniciarStartTotal();

        comienzo();

    });


    ////////////////////////////////////////////////////////////////////////////////////////////////////////Filtro Tipo Envio

    $('#containerTipoEnviosListado').on('click', '.te', function (e) {

        $('#tipoenvioid').val("");

        $('.te').each(function () {

            if (this.checked == true) {
                $('#tipoenvioid').val($(this).attr('id') + ", " + $('#tipoenvioid').val());
            }
        });

        $('#containerProducto div').remove();

        reiniciarStartTotal();

        comienzo();

    });

    ////////////////////////////////////////////////////////////////////////////////////////////////////////Filtro Precio definido

    $('.precioFijo').on('change', function () {

        var select = $(this).attr('id');

        if (this.checked == true) {

            if (select == 'precio1') {
                $('#precioMin').val(1000);

                $('#precioMax').val(3000);

            }
            else if (select == 'precio2') {
                $('#precioMin').val("");

                $('#precioMax').val(1000);

            }
            else if (select == 'precio3') {
                $('#precioMax').val("");

                $('#precioMin').val(3000);

            }

            $('.precioFijo').each(function () {

                if ($(this).attr('id') != select) {
                    if (this.checked == true) {
                        $(this).click();
                    }

                }

            });

        }

        $('#containerProducto div').remove();

        reiniciarStartTotal();

        comienzo();

    });


    $('#cleanPrecio').on('click', function (e) {

        e.preventDefault();

        $('input:radio[name="p"]').prop('checked', false);

        $('#precioMax').val("");

        $('#precioMin').val("");

        $('#containerProducto div').remove();

        reiniciarStartTotal();

        comienzo();


    });

    $('#btnFiltrarProductos').on('click', function (e) {
        e.preventDefault();

        $('#containerProducto div').remove();

        reiniciarStartTotal();

        comienzo();

    });

    ////////////////////////////////////////////////////////////////Condicion

    $('.condicion').on('change', function (e) {

        var select = $(this).attr('id');

        if (this.checked == true) {

            if (select == 'condicionNueva') {
                $('#condicionid').val(1);
            }
            if (select == 'condicionUso') {
                $('#condicionid').val(2);
            }

            $('.condicion').each(function () {

                if (select != $(this).attr('id')) {
                    if (this.checked == true) {
                        $(this).click();
                    }
                }

            });
        }

        $('#containerProducto div').remove();

        reiniciarStartTotal();

        comienzo();

    });

    $('#cleanCondicion').on('click', function (e) {

        e.preventDefault();

        $('input:radio[name="c"]').prop('checked', false);

        $('#condicionid').val("");

        $('#containerProducto div').remove();

        reiniciarStartTotal();

        comienzo();


    });


    /////////////////////////////////////////////////////////////////Scroll
    $(window).scroll(function () {
        if (jQuery(window).scrollTop() + jQuery(window).height() == jQuery(document).height()) {
            if ($('#start').val() != $('#total').val()) {
                comienzo();
            }

        }
    });


});
