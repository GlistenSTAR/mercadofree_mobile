// preloader
Array.prototype.unique = function (a) {
    return function () {
        return this.filter(a)
    }
}(function (a, b, c) {
    return c.indexOf(a, b + 1) < 0
});

Royal_Preloader.config({
    mode: 'number',
    showProgress: false,
    background: '#ffffff'
});

// image zoom
$(document).ready(function () {
    $('.image-zoom-link').magnificPopup({
        type: 'image',
        mainClass: 'mfp-fade',
        fixedContentPos: false
    });
});

// gallery zoom
$('.popup-gallery').each(function () {
    $(this).magnificPopup({
        delegate: '.gallery-zoom',
        type: 'image',
        gallery: {
            enabled: true
        }
    });
});

// video zoom
$(document).ready(function () {
    $('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
        disableOn: 700,
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,
        fixedContentPos: false
    });
});

//responsive video
$(document).ready(function () {
    $(document.body).fitVids();
});

// scroll to top action
$('.scroll-top').on('click', function (event) {
    event.preventDefault();
    $('html, body').animate({
        scrollTop: 0
    }, 'slow');
});

// run mixitup portfolio
$(function () {
    $('#myPortfolio').mixitup({
        targetSelector: '.item',
        transitionSpeed: 600
    });
});

// pagination fix
$(function () {
    $(".dropdown-menu > li > a.trigger").on("click", function (e) {
        var current = $(this).next();
        current.toggle();
        e.stopPropagation();
    });
});

//side-navbar
jQuery("li.list-toggle").bind("click", function () {
    jQuery(this).toggleClass("active");
});

//tooltips and popovers
$(function () {
    $("[data-toggle='tooltip']").tooltip();
});
$(function () {
    $("[data-toggle='popover']").popover();
});

//activate skrollr.js
skrollr.init({
    forceHeight: false,
    smoothScrolling: true,
    smoothScrollingDuration: 1500,
    mobileCheck: function () {
        //hack - forces mobile version to be off
        return false;
    }
});

//carousel subnav slider
$(document).ready(function () {
    var clickEvent = false;
    $('#carouselSubnav').on('click', '.nav a', function () {
        clickEvent = true;
        $('#carouselSubnav .nav li').removeClass('active');
        $(this).parent().addClass('active');
    }).on('slid.bs.carousel', function (e) {
        if (!clickEvent) {
            var count = $('#carouselSubnav .nav').children().length - 1;
            var current = $('#carouselSubnav .nav li.active');
            current.removeClass('active').next().addClass('active');
            var id = parseInt(current.data('slide-to'));
            if (count == id) {
                $('#carouselSubnav .nav li').first().addClass('active');
            }
        }
        clickEvent = false;
    });
});

//owl carousel thumbnail caption slider
$('#owl-carousel-product').owlCarousel({
    loop: true,
    margin: 30,
    nav: true,
    dots: true,
    navText: ["<span><</span>", "<span>></span>"],
    responsive: {
        0: {
            items: 1
        },
        600: {
            items: 1
        },
        1000: {
            items: 1
        }
    }
});

/*
//owl carousel thumbnail caption slider
$('#owl-carousel-historial').owlCarousel({
    loop: true,
    margin: 30,
    nav: true,
    dots: false,
    navContainer: '#customNav-historial',
    navText: ["<span><</span>", "<span>></span>"],
    responsive: {
        0: {
            items: 1
        },
        600: {
            items: 1
        },
        1000: {
            items: 4
        }
    }
})
*/

//owl carousel thumbnail caption slider
$('#owl-carousel-thumb').owlCarousel({
    loop: true,
    margin: 30,
    nav: true,
    dots: false,
    navContainer: '#customNav',
    navText: ["<span><</span>", "<span>></span>"],
    responsive: {
        0: {
            items: 1
        },
        600: {
            items: 2
        },
        1000: {
            items: 4
        }
    }
})

//carousel animation fix
function animateElement(obj, anim_) {
    obj.addClass(anim_ + ' animated').one(
        'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend',
        function () {
            $(this).removeClass();
        });
}

$('#carouselHome, #carouselSubnav').on('slide.bs.carousel', function (e) {
    var current = $('.item').eq(parseInt($(e.relatedTarget).index()));
    $('[data-animation]').removeClass();
    $('[data-animation]', current).each(function () {
        var $this = $(this);
        var anim_ = $this.data('animation');
        animateElement($this, anim_);
    });
});

$('.navbar li a').click(function () {
    $('.navbar li a').removeClass('active');
    $(this).addClass('active');
});

//progress bar
$(document).ready(function () {
    var percent = 0, bar = $('.transition-timer-carousel-progress-bar'), crsl = $('#carouselHome');

    function progressBarCarousel() {
        bar.css({width: percent + '%'});
        percent = percent + 0.5;
        if (percent > 100) {
            percent = 0;
            crsl.carousel('next');
        }
    }

    crsl.carousel({
        interval: false,
        pause: true
    }).on('slide.bs.carousel', function () {
        percent = 0;
    });
    var barInterval = setInterval(progressBarCarousel, 30);
    crsl.hover(
        function () {
            clearInterval(barInterval);
        },
        function () {
            barInterval = setInterval(progressBarCarousel, 30);
        })
});

$('#fixed-navbar').affix({
    offset: {
        top: $('.top-bar').height()
    }
});

function clearAll() {
    $('input:radio[name="c"]').prop('checked', false);

    $('#condicionid').val("");

    $('input:radio[name="p"]').prop('checked', false);

    $('#precioMax').val(0);

    $('#precioOferta').val("");

    $('#precioMin').val(0);

}

// Mover de sitio el buscador general para los móviles

$(document).ready(function($){
    var ancho = $(window).width();

    if(ancho < 768){
        $('#fixed-navbar .container').append($('<div id="buscador-mobile-container" class="row"></div>'));
        $('#buscador-container').appendTo('#fixed-navbar .container #buscador-mobile-container');
    }
});

// Buscador general

let timeout = null;

$(document).ready(function () {

    $('a.categoria-menu').on('click', function (e) {
        $("#categoria_slug").val($(this).data('slug'));
    });


    var urlActual = $('#urlActual').val();

    var campoBusqueda = $('#general-search');

    var btnBuscarProducto = $('#btnBuscarProductos');

    btnBuscarProducto.attr("disabled", true);
    if (campoBusqueda.val() !== "") {
        btnBuscarProducto.attr("disabled", false);
    }
    campoBusqueda.on('keyup', function (e) {
        e.preventDefault();
        
        clearTimeout(timeout);

        var generalSearchResultsElement = $('#general-search-results');
        
        var cadena = $(this).val();

        clearAll();
        
        if (e.keyCode === 13) {
            if (cadena.length > 0) {


                if (urlActual == 'public_anuncio_listar') {
                    $("#valorSearch").val($('#general-search').val());
                    $('#categoriaid').length > 0 ? $('#categoriaid').val("") : "";


                    reiniciarStartTotal();

                    buscarSolo();
                }
                else {
                    reiniciarStartTotal();
                    var url = $('#urlBuscarProductos').val() + '?valorSearch=' + $('#general-search').val();
                    window.location.replace(url);
                    // }
                }
            } else {
                alertify.error("Debe escribir la palabra que desea buscar");
            }
        }
        
        if (cadena.length > 0 && cadena !== "") {
            
            $('#btnBuscarProductos').attr("disabled", false);
            
            if(cadena.length > 3) {
                timeout = setTimeout(function () {
                    var urlBusquedaGeneralProducto = generalSearchResultsElement.attr('data-url-busqueda-general-productos');

                    $.ajax(urlBusquedaGeneralProducto, {
                        type: 'post',
                        dataType: 'json',
                        async: false,
                        data: {
                            "valorSearch": cadena
                        }
                    }).done(function (response) {
                        generalSearchResultsElement.empty();

                        var productos = response.productos;
                        var listadoHtml = "";

                        if(productos.length) {

                            for( var i = 0; i < productos.length; i++ )
                            {
                                var producto = productos[i];
                                listadoHtml += "<li><a href='"+producto.detalle_url+"' >";
                                if(producto.imagen_destacada) {
                                    listadoHtml += "<img src='"+producto.imagen_destacada+"' />";
                                }
                                listadoHtml += producto.nombre;
                                listadoHtml += "</a></li>";
                            }
                            generalSearchResultsElement.append("<ul>"+listadoHtml+"</ul>");
                            generalSearchResultsElement.removeClass("hidden");

                        } else {
                            generalSearchResultsElement.addClass("hidden");
                        }

                    }).error(function () {
                        generalSearchResultsElement.empty();
                    });
                }, 1000);
                
            }
            
        } else {
            $('#btnBuscarProductos').attr("disabled", true);
            generalSearchResultsElement.addClass("hidden");
            generalSearchResultsElement.empty();
            
            return false;
        }


    });


});

function buscarProductosJS(datos, paginado) {
    var response1 = null;
    var url = $('#urlBuscarProductos').val();

    /*if (paginado==1)
     {
     url=$('#urlBuscarProductosPaginado').val()+'/'+$('#paginador').val();
     }*/

    wait('#wait');
    var ciudad = $('#ciudadid').val();
    ciudad = ciudad.substr(0, ciudad.length);
    $.ajax(url, {
        type: 'post',
        dataType: 'json',
        async: false,
        data: {
            "valorSearch": datos,
            "precioMin": $('#precioMin').val(),
            "precioMax": $('#precioMax').val(),
            "precioOferta": $("#precioOferta").val(),
            "categoriaid": $('#categoriaid').val(),
            "condicionid": $('#condicionid').val(),
            "ciudadid": ciudad,
            "tipoenvioid": $('#tipoenvioid').val(),
            "start": $('#start').val(),
            "total": $('#total').val(),
            "offset": $('#offset').val(),
            "totalProductos": $('#totalProductos').val(),
            "tipo": $('#tipo_categoria').val(),
            "slug": $('#categoria_slug').val()

        }
    }).done(function (response) {

        response1 = response;
        $("#totalProductos").val(parseInt($("#totalProductos").val()) + response.totalProductos);
        $("#total").val(response.total);
        $("#start").val(response.start);
        $("#categoriaid").val(response.categoriaid);
        $("#valorSearch").val(response.valorSearch);
        endWait('#wait');

    }).error(function () {
        endWait('#wait');
    });

    if (paginado == 0) {
        $('#paginador').val(1);
    }

    return response1;
}


function crearProductos(response, paginado) {

    var productos = response.productos;
    $('#general-search').val($('#valorSearch').val());
    var img = $('#img-route').val();

    var urlDetalles = $('#urlProductoDetalles').val();

    urlDetalles = urlDetalles.substring(0, urlDetalles.length - 1);

    var listaProducto = "";

    var listaProductoTemp = "";

    var cont = 1;

    if (paginado == 0) {
        $('#containerProducto div').remove();
    }

    if (productos.length > 0) {
        for (var i = 0; i < productos.length; i++) {
            var tagProducto = '<div class="col-lg-4 col-md-4 col-sm-6 mb30">' +
                '<div class="view image-hover-1 no-margin">' +
                '<div class="product-container">' +
                '<img class="img-responsive full-width" src="' + img + productos[i].imagen + '" alt="...">';
            if (productos[i].en_oferta) {
                tagProducto += '<span class="badge home-badge" style="text-align: right">' +
                    productos[i].descuento + '% OFF' +
                    '</span>';
            }

            if (productos[i].envio_domicilio) {
                tagProducto += '<span class="badge home-badge transporte" style="text-align: left"><i class="fa fa-truck"></i></span>';
            }
            var nombreProd = productos[i].nombre;
            var cadena = nombreProd;
            if (cadena.length > 20) {
                cadena = nombreProd.slice(0, 18) + '...';
            }
            tagProducto += '</div>' +
                '<div class="mask">' +
                '<div class="image-hover-content">' +
                '<a href="' + productos[i].url_detalle + '" class="info">' +
                '<div class="image-icon-holder"><span class="ion-link image-icons"></span></div>' +
                '</a>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="shop-product content-box-shadow">' +
                '<div class="row">' +
                '<div class="col-md-10">' +
                '<a class="product-title" href="' + urlDetalles + productos[i].slug + '"><h2 title="' + nombreProd + '" class="product-title">' + cadena + '</h2></a>' +
                '</div>' +
                '<div class="col-md-2">';
            var authenticated = $('#status-user').val();
            if (authenticated === "1") {
                if (productos[i].favorito) {
                    tagProducto += '<a data-id="' + productos[i].slug + '" class="bookmark_product" title="Producto favorito" data-togle="tooltip"><i class="ion-ios7-heart"></i></a>';
                } else {
                    tagProducto += '<a data-id="' + productos[i].slug + '" href="#" class="bookmark_product" title="Marcar como favorito" data-togle="tooltip"><div style="display: none;" class="loader"></div><i class="ion-ios7-heart-outline"></i></a>';
                }
            }

            if (productos[i].en_oferta) {
                tagProducto += '</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-md-5">' +
                    '<span class="pull-left label label-default shop-product-price"> $ ' + productos[i].precio_oferta + '</span>' +
                    '</div>' +
                    '<div class="col-md-7" style="text-align:left;">' +
                    '<span class="precio-anterior pull-right">$' + productos[i].precio + '</span>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            } else {
                tagProducto += '</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-md-5">' +
                    '<span class="pull-left label label-default shop-product-price"> $ ' + productos[i].precio + '</span>' +
                    '</div>' +

                    '</div>' +
                    '</div>' +
                    '</div>';
            }

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
        listaProducto = '<div class="alert alert-success" role="alertdialog" style="margin-top: 20px;margin-bottom: 10%;">' +
            'No existen productos para la búsqueda realizada.' +
            '</div>';
    }

    $('#containerProducto').append(listaProducto);

    // Añadir botón de paginado

    if(productos.length > 0){
        var btnPaginado = '<div class="row" id="btnPaginadoContainer">\n' +
            '                            <div class="col-xs-12" style="text-align: center">\n' +
            '                                <button id="btnVerMasProductos" type="button" class="btn btn-primary btn-lg">Ver más productos</button>\n' +
            '                            </div>\n' +
            '                        </div>';

        $('#containerProducto').append(btnPaginado);
    }

    var listaCategorias = response.listaCategoriasFiltro;
    if (listaCategorias.length > 0) {
        categoriaid = response.categoriaid;

        // $('#categoriaid').val(categoriaid);
        var categorias = "";
        for (var i = 0; i < listaCategorias.length; i++) {
            if (i <= 10) {
                var tagCategoria = '<p class="catFiltro" ><a class="seccion-categoria" data-id="' + listaCategorias[i].id + '" data-slug="' + listaCategorias[i].slug + '" href="' + listaCategorias[i].url + '">' + listaCategorias[i].nombre + '</a></p>';
            }
            else {
                var tagCategoria = '<p class="catFiltro" hidden ><a class="seccion-categoria" data-id="' + listaCategorias[i].id + '" data-slug="' + listaCategorias[i].slug + '" href="' + listaCategorias[i].url + '">' + listaCategorias[i].nombre + ' </a></p>';
            }

            categorias += tagCategoria;
        }
        if (listaCategorias.length > 10) {
            categorias += '<div ><a id="verCatFiltro" class="default" href="#">Ver todos</a></div>';
        }
        $('#containerCategoriaListado p').remove();
        $('#containerCategoriaListado').append(categorias);
    }

    //////////////////////////////////////////////////////////////////////////////Ubicacion


    var ciudades = response.ciudadesProduct;
    console.log(ciudades);
    if (ciudades != "") {

        var ubicaciones = "";


        for (var a = 0; a < ciudades.length; a++) {

            if (a <= 7) {

                var tagUbicacion = '<p class="city" ><input type="checkbox"  class="ubicacion" id="' + ciudades[a].id + '"> ' + ciudades[a].ciudadNombre + '</p>';
            }
            else if ($('#ciudadid').val() != ciudades[a].id) {
                var tagUbicacion = '<p class="city" hidden ><input type="checkbox"  class="ubicacion" id="' + ciudades[a].id + '"> ' + ciudades[a].ciudadNombre + '</p>';
            }

            ubicaciones += tagUbicacion;
        }
        if (ciudades.length > 7) {
            ubicaciones += '<p ><a id="verCatFiltro" class="default" href="#">Ver todos</a></p>';
        }

        $('#containerUbicacionesListado p').remove();
        $('#containerUbicacionesListado').append(ubicaciones);
    }

    var seleccionadas = response.ciudadid;
    if (seleccionadas !== "") {

        //seleccionadas = seleccionadas.substr(0, seleccionadas.length);
        seleccionadas = seleccionadas.split(',');
        seleccionadas = seleccionadas.unique();
        $.each(seleccionadas, function (index, obj) {
            $("#" + obj).prop('checked', true);
        });
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////Filtro uso


    var catBread = response.categoriasBread;
    $('#categoriaid').val(response.categoriaid);

    if (catBread.length > 0) {


        $('#containerBreadCrumb li').remove();
        $('#containerBreadCrumb').append('<li><span class="ion-home breadcrumb-home"></span><a href="' + $('#urlPublicHome').val() + '">Inicio</a></li>');

        var breads = "";

        var lastCat = "";
        var busqueda = $('#general-search').val();
        if (busqueda !== "") {
            breads = '<li>Buscando "' + busqueda + '"</li>';
            for (var i = 0; i < catBread.length; i++) {
                var tagli = '<li><a href="' + catBread[i]['url'] + '">' + catBread[i]['nombre'] + '</a></li>';

                breads += tagli;

                lastCat = catBread[i]['nombre'];
            }
        } else {
            for (var i = 0; i < catBread.length; i++) {
                var tagli = '<li><a href="' + catBread[i]['url'] + '">' + catBread[i]['nombre'] + '</a></li>';

                breads += tagli;

                lastCat = catBread[i]['nombre'];
            }
        }
        $('#containerBreadCrumb').append(breads);
    } else {
        $('#containerBreadCrumb li').remove();
        $('#containerBreadCrumb').append('<li><span class="ion-home breadcrumb-home"></span><a href="' + $('#urlPublicHome').val() + '">Inicio</a></li>');
        var busqueda = $('#general-search').val();
        if (busqueda !== "") {
            breads = '<li>Buscando "' + busqueda + '"</li>';
            $('#containerBreadCrumb').append(breads);
        }
    }

    var total = response.total;
    var tipo = response.tipo;

    var totalProds = $('#totalProductos').val();
    console.log(total);
    console.log($('#totalProductos').val());

    var cadena = $('#general-search').val();
    if ((tipo === 'c' || tipo === 'f') && cadena === "") {
        $('#headSmall').text(total > 0 ? (total > 1 ? 'Mostrando ' + totalProds + ' de ' + total + ' resultados.' : 'Mostrando ' + totalProds + ' de ' + total + ' resultado.') : ' No existen resultados');
        $('#categoriaName').text(lastCat);
    } else {

        $('#headSmall').text(total > 0 ? (total > 1 ? 'Mostrando ' + totalProds + ' de ' + total + ' resultados para el término de búsqueda "' + cadena + '"' : 'Mostrando ' + totalProds + ' de ' + total + ' resultado para el término de búsqueda "' + cadena + '"') : ' No existen resultados');
        $('#categoriaName').text("");
    }


}

function reiniciarStartTotal() {
    $('#start').val(0);
    $('#total').val("");
    $('#totalProductos').val(0);
}

function buscarSolo() {
    var datos = $('#general-search').val();

    var response = buscarProductosJS(datos, 0);

    crearProductos(response, 0)
}

// Toggle Filtro productos en listados mobiles

function toggleFiltroProductos(){
    $('#btn-toggle-filter').on('click',function (e){
        e.preventDefault();
        var filterContainer = $('div.row.filter-container');

        if(filterContainer.is(':hidden')){
            filterContainer.slideDown();
        }
        else{
            filterContainer.slideUp();
        }

    });
}

$('#btnBuscarProductos').on('click', function (e) {
    e.preventDefault();

    var urlActual = $('#urlActual').val();

    if (urlActual == 'public_anuncio_listar') {

        $('#categoriaid').length > 0 ? $('#categoriaid').val("") : "";

        reiniciarStartTotal();

        buscarSolo();
    }
    else {
        var url = $('#urlBuscarProductos').val() + '?valorSearch=' + $('#general-search').val();
        window.location.replace(url);
    }

});

// Ordenando menu de categorias según tamaño de pantalla

$(document).ready(function (){
    let width = $( window ).width();
    let col = 1;
    let index = 1;
    if(width >= 768 && width < 1024){
        col = 2;
    }
    else if(width >= 1024 && width < 1366){
        col = 4;
    }
    else if(width >= 1366){
        col = 6;
    }

    $("ul#containerCategoria > li").each(function (){
        if(index > 1 && index === col){
           $(this).after($('<li class="clearfix"></li>'));
           index = 1;
        }
        else{
            index ++;
        }
    });
});

// Listado de Notificaciones

$(document).ready(function (){

    // Marcar las notificaciones como leidas, cuando se despliega el listado
    $('#btnOpenNotifications').on('click',function (){
        // Marcar todas las notificaciones como leidas
        var url = $('#urlSetNotificacionesLeidas').val();

        $.ajax(url, {
            'type' : 'post',
            'dataType' : 'json'
        }).done(function (response){
            if(response.success){

                // Marcar las notificaciones no leidas, como leidas
                $('#notificaciones-menu li').each(function (){
                    if(!$(this).hasClass('divider') && !$(this).hasClass('del-notificaciones') && !$(this).hasClass('read')){
                        $(this).addClass('read');
                    }
                });

                // Quitar el indicador de la cantidad de notificaciones nuevas del botón de la campana
                $('#btnOpenNotifications').empty();
                $('#btnOpenNotifications').append($('<span class="fa fa-bell"></span>'));

            }
        });
    });

    //Eliminar notificaciones

    $('#deleteAllNotifications').on('click',function (e){
        e.preventDefault();

        $.ajax($(this).attr('data-url'),{
            'type' : 'post',
            'dataType' : 'json'
        }).done(function (response){
            if(response.success){
                $('#notificaciones-menu').empty();

                var noNotifMessage = '<li style="padding: 25px;">\n' +
                    '                <p style="text-align: center"><i class="fa fa-thumbs-o-up" style="font-size: 26px;"></i></p>\n' +
                    '                <p class="notificaciones-title" style="text-align: center;color: #424141;">Por el momento no tienes notificaciones</p>\n' +
                    '            </li>';

                $('#notificaciones-menu').append($(noNotifMessage));
            }
        });
    });
});