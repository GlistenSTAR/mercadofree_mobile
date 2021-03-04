$(document).ready(function () {

    reiniciarStartTotal();

    function enviarStep1() {
        var url = $('#urlStep1').val();

        var idCategoria = $('#categoriaSeleccionada').val();


        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idCategoria": idCategoria,
                "step": 1
            }
        }).done(function (response) {


        });

    }

    function obtenerCategoria(idCategoria) {
        var categorias = null;

        var url = $('#urlObtenerCategoriaId').attr("value");

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idCategoria": idCategoria
            }
        }).done(function (response) {

            categorias = response.categorias;

        });

        return categorias;
    }

    function crearSelect(categorias) {
        var tagOption = "";

        for (var i = 0; i < categorias.length; i++) {
            tagOption += '<option value="' + categorias[i][0] + '">' + categorias[i][1] + '</option>';
        }

        var tagSelect = '<div class="col-md-3 cajon" id="' + categorias[0][2] + '" style="margin-top: 8px">' +
            '<select class="form-control  selectCategorias">' +
            '<option value="">Seleccione categoria</option>' +
            tagOption +
            '</select>' +
            '</div>';

        $('#containerSelect').append(tagSelect);
    }


    function eliminarSelectNivel(nivel) {

        $('#containerSelect .cajon').each(function () {

            if ($(this).attr('id') >= nivel) {
                $(this).remove();
            }

        });
    }

    function crearHilo(nombre, nivel) {
        var hilo = $('#hilo').text();

        var hiloTemp = "";

        if (hilo != "" && nivel != 1) {
            hilo = hilo.split('>');
            for (var i = 0; i < (nivel - 1); i++) {
                if (i == 0) {
                    hiloTemp += hilo[i];
                }
                else {
                    hiloTemp += ' > ' + hilo[i];
                }

            }

            hiloTemp += ' > ' + nombre;
        }
        else {
            hiloTemp = nombre;
        }

        $('#hilo').text(hiloTemp);


    }

    function crearHiloBottom(nombre, nivel) {
        var hilo = $('#hilo-bottom').text();

        var hiloTemp = "";

        if (hilo != "" && nivel != 1) {
            hilo = hilo.split('>');
            for (var i = 0; i < (nivel - 1); i++) {
                if (i == 0) {
                    hiloTemp += hilo[i];
                }
                else {
                    hiloTemp += ' > ' + hilo[i];
                }

            }

            hiloTemp += ' > ' + nombre;
        }
        else {
            hiloTemp = nombre;
        }

        $('#hilo-bottom').text(hiloTemp);


    }

    $('.categoriaPublicar').on('click', function (e) {

        e.preventDefault();

        var idCategoria = $(this).attr('id');

        var categoria = null;

        categoria = obtenerCategoria(idCategoria);

        crearHilo(categoria[1], categoria[2]);
        crearHiloBottom(categoria[1], categoria[2]);

        if (categoria[3].length == 0) {
            if (categoria[2] == 1) {
                eliminarSelectNivel(2);
            }
            else {
                eliminarSelectNivel(categoria[2] - 1);
            }
        }
        else {
            eliminarSelectNivel(categoria[3][0][2]);
            crearSelect(categoria[3]);
        }

    });
    $('#containerSelect').on('change', 'select.selectCategorias', function () {
        $('#nextStep1').attr('disabled', true);

        var idCategoria = $(this).val();

        var categoria = null;

        categoria = obtenerCategoria(idCategoria);

        $('#categoriaSeleccionada').val(categoria[0]);

        crearHilo(categoria[1], categoria[2]);
        crearHiloBottom(categoria[1], categoria[2]);

        if (categoria[3].length == 0) {
            if (categoria[2] == 1) {
                eliminarSelectNivel(2);
            }
            else {
                eliminarSelectNivel(categoria[2] + 1);
            }
            if (categoria[2] >= 3) {
                $('#nextStep1').attr('disabled', false);
            }

        }
        else {
            eliminarSelectNivel(categoria[3][0][2]);
            crearSelect(categoria[3]);
        }


    });

    //Efecto scroll para cuando selecciona una categoria principal vaya hacia el final de la pagina

    $('a.categoriaPublicar').on('click', function () {
        $("html, body").animate({scrollTop: $(document).height()}, 1500);
    });


    $('#nextStep1').on('click', function (e) {
        if ($(this).attr('disabled') != 'disabled') {
            enviarStep1();
        }
        else {
            e.preventDefault();
        }


    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Step 2
    function enviarStep2() {
        var validator = null;

        validator = validatorUsuarioStep2;

        var flag = false;

        if (validator.form()) {
            var $form = $('#formAnuncio2');

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                async: false,
                data: $form.serialize()
            }).done(function (response) {

                flag = true;

            });
        }
        return flag;

    }

    function escapeTags(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    //Uploader de imagenes

    if ($('#btnUploadImg').length > 0) {
        var btn2 = document.getElementById('btnUploadImg'),
            progressBar2 = document.getElementById('progressBarImg'),
            progressOuter2 = document.getElementById('progressOuterImg'),
            //videoContainer = document.getElementById('videoContainer'),
            msgBox2 = document.getElementById('msgBox2');

        //Uploader foto de adicionar Coleccion
        var uploaderFotos = new ss.SimpleUpload({
            button: btn2,
            url: $("#img-upload-path").val(),
            name: 'uploadfile2',
            responseType: 'json',
            allowedExtensions: ['jpg', 'png', 'gif', 'jpeg'],
            maxSize: 536870912, // kilobytes
            hoverClass: 'hover',
            focusClass: 'focus',
            startXHR: function () {
                progressOuter2 = document.getElementById('progressOuterImg');
                progressOuter2.style.display = 'block'; // make progress bar visible
                this.setProgressBar(progressBar2);
            },
            onSubmit: function () {
                btn2.innerHTML = 'Cargando...'; // change button text to "Uploading..."
                this.setProgressBar(progressBar2); // designate as progress bar

            },
            onComplete: function (filename, response) {

                btn2.innerHTML = 'Adicionar Imagen';

                progressOuter2.style.display = 'none'; // hide progress bar when upload is completed

                if (!response) {
                    msgBox2.innerHTML = 'No se ha podido cargar el fichero';
                    return;
                }

                if (response.success === true) {
                    var imagesRoute = $('#img-route').val();

                    var selector = "#containerImages";

                    var img = '<div class="col-md-3">' +
                        '<a href="#" class="thumbnail">' +
                        '<img class="img-responsive" src="' + imagesRoute + escapeTags(response.file) + '" alt="Thumbnail">' +
                        '</a>' +
                        '<input class="imagenes-name" type="hidden" name="imagenes[]" value="' + response.file + '">' +
                        '</div>';

                    /*'<li style="margin-bottom:10px;" class="ui-state-default columnImgContainer" data-img="'+response.file+'" >';
                     img+='<img class="img-responsive" src="'+imagesRoute+escapeTags(response.file)+'" >';
                     img+='<input class="imagenes-name" type="hidden" name="imagenes[]" value="'+response.file+'">';
                     img+='<a data-img="'+response.file+'" class="btn btn-danger btnRemoveImage"><i class="fa fa-trash"></i> Eliminar</a>';
                     img+='</li>';*/

                    $(selector).append($(img));

                } else {
                    if (response.msg) {
                        msgBox2.innerHTML = escapeTags(response.msg);

                    } else {
                        msgBox2.innerHTML = 'Ha ocurrido un error y no se ha podido cargar la imagen';
                    }
                }
            },
            onError: function (filename, response) {
                msgBox2.innerHTML = 'Ha ocurrido un error y no se ha podido cargar la imagen';
            }
        });

        $('#nextStep2').on('click', function (e) {

            var flag = enviarStep2();

            if (flag == false) {
                e.preventDefault();
            }

        });
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Step 3

    function enviarStep3() {
        var validator = null;

        validator = validatorUsuarioStep3;

        var flag = false;

        if (validator.form()) {
            var $form = $('#formAnuncio3');

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                async: false,
                data: $form.serialize()
            }).done(function (response) {

                flag = true;

            });
        }

        return flag;

    }

    $('#nextStep3').on('click', function (e) {

        var flag = enviarStep3();

        if (flag == false) {
            e.preventDefault();
        }


    });


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////Step 4

    function BuscarCiudades(idProvincia, container, select) {
        //wait(container);
        var url = $('#urlObtenerCiudades').val();

        var ciudades = null;

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idProvincia": idProvincia
            }
        }).done(function (response) {

            ciudades = response.ciudades;

            $(select + ' option').remove();//#nombreCiudadAdicionar

            $(select).append('<option value="" >Seleccione Ciudad</option>');
            for (var i = 0; i < ciudades.length; i++) {
                $(select).append('<option value="' + ciudades[i][0] + '" >' + ciudades[i][1] + '</option>');
            }
            //endWait(container);

        });

        return ciudades;
    }

    function buscarDireccion() {
        var direccion = "";
        //$direccion=$direccion->getCalle()." No. ".$direccion->getNumero()." , entre ".$direccion->getEntreCalle().". ".$direccion->getProvincia()->getNombre().", ".$direccion->getCiudad()->getCiudadNombre().".";/\

        var ciudad = $('#ciudad option[value=' + $('#ciudad').val() + ']').text();

        var provincia = $('#provincia option[value=' + $('#provincia').val() + ']').text();

        var numero = $('#numero').val() != "" ? " No. " + $('#numero').val() : "";

        var entre = $('#entreCalles').val() != "" ? " , entre " + $('#entreCalles').val() : "";

        direccion = $('#calle').val() + numero + entre + ". " + provincia + ", " + ciudad + ".";

        return direccion;
    }

    function guardarDireccion() {
        var validator = null;

        validator = validatorDireccionVenta2;

        var flag = false;

        if (validator.form()) {
            var $form = $('#formDireccionVenta2');

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                async: false,
                data: $form.serialize()
            }).done(function (response) {

                flag = true;
            });
        }

        return flag;

    }

    function enviarStep4() {
        var validator = null;

        validator = validatorUsuarioStep4;

        var flag = false;

        if (validator.form()) {
            var $form = $('#formAnuncio4');

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                async: false,
                data: $form.serialize()
            }).done(function (response) {

                flag = true;

            });
        }

        return flag;

    }

    $('#btnSubmitDireccionVenta').on('click', function () {

        var flag = guardarDireccion();

        if (flag == true) {
            $('#modalAdicionarDireccion').modal('hide');

            var direccion = buscarDireccion();

            $('#direccionP').text(direccion);

            $('#addDireccion').hide();
        }


    });

    $('#provincia').on('change', function () {

        BuscarCiudades($(this).val(), "#containerDireccion", "#ciudad");

    });

    $('#nextStep4').on('click', function (e) {

        var flag = enviarStep4();

        if (flag == false) {
            e.preventDefault();
        }
    });

    ////////////////////////////////////////////////////////////////////////////////Listar Productos

    $('#general-search').val($('#valorSearch').val());

    buscarSolo();

    toggleFiltroProductos();

    ////////////////////////////////////////////////////////////////////////////////////////////////////////Filtro Categoria

    $('#containerCategoriaListado').on('click', '#verCatFiltro', function (e) {

        e.preventDefault();

        $('.catFiltro').show("slow");

    });


    $('div.catFiltro').click(function (e) {
        $('#categoriaid').val($(this).data('id'));

        $("#valorSearch").val($('#general-search').val());
        reiniciarStartTotal();

        buscarSolo();
    });


    ////////////////////////////////////////////////////////////////////////////////////////////////////////Filtro Condicion

    $('.condicion').on('change', function (e) {

        var select = $(this).attr('id');
        $('#condicionid').val(select);
        if (this.checked == true) {

            $('.condicion').each(function () {

                if (select != $(this).attr('id')) {
                    if (this.checked == true) {
                        $(this).click();
                    }
                }

            });
        }
        reiniciarStartTotal();
        buscarSolo();

    });

    $('#cleanCondicion').on('click', function (e) {

        e.preventDefault();

        $('input:radio[name="c"]').prop('checked', false);

        $('#condicionid').val("");

        reiniciarStartTotal();
        buscarSolo();

        //document.querySelectorAll('[name=c]').forEach((x) => x.checked=false);
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
        reiniciarStartTotal();

        buscarSolo();

    });

    $('#cleanPrecio').on('click', function (e) {

        e.preventDefault();

        $('input:radio[name="p"]').prop('checked', false);

        $('#precioMax').val("");

        $('#precioOferta').val("");

        $('#precioMin').val("");

        reiniciarStartTotal();

        buscarSolo();

        //document.querySelectorAll('[name=c]').forEach((x) => x.checked=false);
    });

    $('#btnFiltrarProductos').on('click', function () {
        reiniciarStartTotal();

        buscarSolo();
    });


    ////////////////////////////////////////////////////////////////////////////////////////////////////////Filtro Ciudad
    var valor = "";
    var arrayValor = Array();

    $('#containerUbicacionesListado').on('change', '.ubicacion', function () {

        $('#ciudadid').val("");
        if (this.checked) {
            arrayValor.push($(this).attr('id'));

        } else {
            var itemtoRemove = $(this).attr('id');
            arrayValor.splice($.inArray(itemtoRemove, arrayValor), 1);
        }
        $('#ciudadid').val(arrayValor.join(","));
        reiniciarStartTotal();
        buscarSolo();
    });


    $('#containerUbicacionesListado').on('click', '#verCityFiltro', function (e) {

        e.preventDefault();

        $('.verCityFiltro').show("slow");

    });

    ////////////////////////////////////////////////////////////////////////////////////////////////////////Filtro Tipo Envio

    /*$('#containerTipoEnviosListado').on('click','.te', function (e) {

        $('#tipoenvioid').val("");

        $('.te').each(function () {

            if (this.checked==true)
            {
                $('#tipoenvioid').val($(this).attr('id')+", "+$('#tipoenvioid').val());
            }
        });

        reiniciarStartTotal();

        buscarSolo();

    });*/


    ////////////////////////////////////////////////////////////////////////////////////////////////////////Paginacion
    function crearProductosPaginado(response) {
        var productos = response.productos;

        var img = $('#img-route').val();
        var urlDetalles = $('#urlProductoDetalles').val();

        urlDetalles = urlDetalles.substring(0, urlDetalles.length - 1);

        var listaProducto = "";

        var listaProductoTemp = "";

        var cont = 1;

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
            tagProducto += '<br>';
            tagProducto += '<br>';
            tagProducto += '<br>';
            tagProducto += '<br>';
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
                    tagProducto += '<a data-id="' + productos[i].slug + '" title="Producto favorito" class="bookmark_product" data-togle="tooltip"><i class="ion-ios7-heart"></i></a>';
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

        $('#containerProducto').append(listaProducto);

        // Añadir botón de paginado

        var btnPaginado = '<div class="row" id="btnPaginadoContainer">\n' +
            '                            <div class="col-xs-12" style="text-align: center">\n' +
            '                                <button id="btnVerMasProductos" type="button" class="btn btn-primary btn-lg">Ver más productos</button>\n' +
            '                            </div>\n' +
            '                        </div>';

        $('#containerProducto').append(btnPaginado);


        //////////////////////////////////////////////////////////////////////////////Total
        var total = response.total;
        var totalProds = $("#totalProductos").val();
        var tipo = response.tipo;

        var cadena = $('#general-search').val();
        if ((tipo === 'f' || tipo === 'c') && cadena === "") {
            $('#headSmall').text(total > 0 ? (total > 1 ? 'Mostrando ' + totalProds + ' de ' + total + ' resultados.' : 'Mostrando ' + totalProds + ' de ' + total + ' resultado.') : ' No existen resultados');
        } else {
            $('#headSmall').text(total > 0 ? (total > 1 ? 'Mostrando ' + totalProds + ' de ' + total + ' resultados para el término de búsqueda "' + cadena + '"' : 'Mostrando ' + totalProds + ' de ' + total + ' resultado para el término de búsqueda "' + cadena + '"') : ' No existen resultados');
        }

    }

    function buscarPaginado() {
        var datos = $('#general-search').val();

        $('#paginador').val(parseInt($('#paginador').val()) + 1);

        var response = buscarProductosJS(datos, 1);

        crearProductosPaginado(response)
    }
    
    $('#containerProducto').on('click','#btnVerMasProductos',function () {
        if ($('#totalProductos').val() < $('#total').val()) {

            $(this).remove();

            buscarPaginado();
        }
        else{
           $(this).html('No hay más productos');
        }
    });

    //@Deprecated paginado con scroll

   /* $(window).scroll(function () {
        if (jQuery(window).scrollTop() + jQuery(window).height() == jQuery(document).height()) {

            if ($('#totalProductos').val() < $('#total').val()) {
                buscarPaginado();
            }

        }
    });*/

    //Adicionar Producto a favoritos

    $('#containerProducto').on('click', '.bookmark_product', function (e) {
        e.preventDefault();
        var idProducto = $(this).data('id');
        $(this).children('i.ion-ios7-heart-outline').hide();

        $(this).children('.loader').show();

        $.ajax($('#urlAdicionarProductoFavorito').val(), {
            'dataType': 'json',
            'type': 'post',
            'data': {
                'idProducto': idProducto
            }
        }).done(function (response) {
            if (response[0]) {
                $('#containerProducto .shop-product a.bookmark_product').each(function () {
                    if ($(this).attr('data-id') == idProducto) {
                        $(this).attr('title', 'Producto favorito');

                        $(this).children('i.ion-ios7-heart-outline').show();

                        $(this).children('.loader').remove();

                        $(this).children('i').removeClass('ion-ios7-heart-outline');
                        $(this).children('i').addClass('ion-ios7-heart');
                    }
                });
            }
            else {
                $(this).children('i.ion-ios7-heart-outline').show();
                $(this).children('.loader').hide();
                alert(response[1]);
            }


        });

    });


});
