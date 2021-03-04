$(document).ready(function () {

    function reiniciarStartTotal() {

        $('#start').val(0);
        $('#total').val(1);

    }
    function buscarProductosJS()
    {
        var response1=null;

        var url=$('#urlBuscarProductos').val();

        wait('#wait');

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "valorSearch": "",
                "precioMin":$('#precioMin').val(),
                "precioMax":$('#precioMax').val(),
                "categoriaid":$('#categoriaid').val(),
                "condicionid":$('#condicionid').val(),
                "ciudadid": $('#ciudadid').val(),
                "porcientoid": $('#porcientoid').val(),
                "tipoenvioid":$('#tipoenvioid').val(),
                "start":$('#start').val(),
                "total":$('#total').val(),
                "usuarioid":$('#usuarioid').val()

            }
        }).done(function (response) {

            response1=response;
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


        for (var i = 0; i < productos.length; i++)
        {
            var tagProducto ='<div class="col-lg-4 col-md-4 col-sm-6">'+
                '<div class="thumbnail">'+
                '<div class="view image-hover-1 no-margin">'+
                '<div class="product-container">'+
                '<img class="img-responsive full-width" src="'+img+productos[i][8]+'" alt="...">'+
                '<span class="badge home-badge">'+
                productos[i][11]+'% OFF'+
                '</span>'+
                '<span class="badge home-badge transporte">'+
                '<i class="fa fa-truck"></i>'+
                '</span>'+
                '</div>'+
                '<div class="mask">'+
                '<div class="image-hover-content">'+
                '<a href="'+img+productos[i][8]+'" class="info image-zoom-link">'+
                '<div class="image-icon-holder"><span class="ion-ios7-search image-icons"></span></div>'+
                '</a>'+
                '<a href="'+urlDetalles+productos[i][0]+'" class="info">'+
                '<div class="image-icon-holder"><span class="ion-link image-icons"></span></div>'+
                '</a>'+
                '</div>'+
                '</div>'+
                '</div>'+
                '<div class="shop-product content-box-shadow">'+
                '<a class="product-title" href="'+urlDetalles+productos[i][0]+'"><h2 class="product-title">'+productos[i][1]+'</h2></a>'+
                '<div class="row">'+
                '<div class="col-md-5">'+
                '<span class="pull-left label label-default shop-product-price"> $ '+productos[i][12]+'</span>'+
                '</div>'+
                '<div class="col-md-7" style="text-align:left;">'+
                '<span class="precio-anterior pull-left" style="text-decoration:line-through;">$ '+productos[i][2]+'</span>'+
                '</div>'+
                '</div>'+
                '</div>'+
                '</div>'+
                '</div>';

            listaProductoTemp += tagProducto;

            /*if (cont % 3 == 0 || cont == productos.length) {
                var tagRow = '<div class="row">' +
                    listaProductoTemp +
                    '</div>';

                listaProducto += tagRow;
                listaProductoTemp = "";
            }

            cont++;
*/
        }

        $('#containerProducto').append(listaProductoTemp);

        //////////////////////////////////////////////////////////////////////////////Total

        $('#total').val(response.total);

        $('#start').val(response.start);

        $('#headSmall').text(response.total+" resultados");
    }

    function comienzo() {

        var response=buscarProductosJS();

        crearProductos(response);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////// Toggle filtro Mobile

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

    $('#containerTipoEnviosListado').on('click','.te', function (e) {

        $('#tipoenvioid').val("");

        $('.te').each(function () {

            if (this.checked==true)
            {
                $('#tipoenvioid').val($(this).attr('id')+", "+$('#tipoenvioid').val());
            }
        });

        $('#containerProducto div').remove();

        reiniciarStartTotal();

        comienzo();

    });
    ////////////////////////////////////////////////////////////////////////////////////////////////////////Filtro Porciento

        $('#containerPorcientoListado').on('click','.porciento', function (e) {

            $('#porcientoid').val("");

            $('.porciento').each(function () {

                if (this.checked==true)
                {
                    $('#porcientoid').val($(this).attr('id')+", "+$('#porcientoid').val());
                }
            });

            $('#containerProducto div').remove();

            reiniciarStartTotal();

            comienzo();

        });
    ////////////////////////////////////////////////////////////////////////////////////////////////////////Filtro Ubicacion

        $('#containerUbicacionesListado').on('click','.ubicacion', function (e) {

            $('#ciudadid').val("");

            $('.ubicacion').each(function () {

                if (this.checked==true)
                {
                    $('#ciudadid').val($(this).attr('id')+", "+$('#ciudadid').val());
                }
            });

            $('#containerProducto div').remove();

            reiniciarStartTotal();

            comienzo();

        });

    ////////////////////////////////////////////////////////////////////////////////////////////////////////Filtro Precio definido

    $('.precioFijo').on('change',function () {

        var select=$(this).attr('id');

        if (this.checked==true)
        {

            if (select=='precio1')
            {
                $('#precioMin').val(1000);

                $('#precioMax').val(3000);

            }
            else if (select=='precio2')
            {
                $('#precioMin').val("");

                $('#precioMax').val(1000);

            }
            else if (select=='precio3')
            {
                $('#precioMax').val("");

                $('#precioMin').val(3000);

            }

            $('.precioFijo').each(function () {

                if($(this).attr('id')!=select)
                {
                    if (this.checked==true)
                    {
                        $(this).click();
                    }

                }

            });

        }

        $('#containerProducto div').remove();

        reiniciarStartTotal();

        comienzo();

    });


    $('#cleanPrecio').on('click',function (e) {

        e.preventDefault();

        $('input:radio[name="p"]').prop('checked', false);

        $('#precioMax').val("");

        $('#precioMin').val("");

        $('#containerProducto div').remove();

        reiniciarStartTotal();

        comienzo();


    });

    $('#btnFiltrarProductos').on('click', function (e)
    {
        e.preventDefault();

        $('#containerProducto div').remove();

        reiniciarStartTotal();

        comienzo();

    });

    ////////////////////////////////////////////////////////////////Condicion

    $('.condicion').on('change', function (e) {

        var select=$(this).attr('id');

        alert(select);

        if (this.checked==true)
        {
            alert(this.checked);
            if (select=='condicionNueva')
            {
                $('#condicionid').val(1);
            }
            if (select=='condicionUso')
            {
                alert(2);
                $('#condicionid').val(2);
            }

            $('.condicion').each(function () {

                if (select!=$(this).attr('id'))
                {
                    if (this.checked==true)
                    {
                        $(this).click();
                    }
                }

            });
        }

        $('#containerProducto div').remove();

        reiniciarStartTotal();

        comienzo();

    });

    $('#cleanCondicion').on('click',function (e) {

        e.preventDefault();

        $('input:radio[name="c"]').prop('checked', false);

        $('#condicionid').val("");

        $('#containerProducto div').remove();

        reiniciarStartTotal();

        comienzo();


    });





    /////////////////////////////////////////////////////////////////Scroll
    $(window).scroll(function () {
        if (jQuery(window).scrollTop() + jQuery(window).height() == jQuery(document).height())
        {
            if ($('#start').val()!=$('#total').val())
            {
                comienzo();
            }

        }
    });


});
