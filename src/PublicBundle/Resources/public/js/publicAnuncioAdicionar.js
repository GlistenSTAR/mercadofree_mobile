$(document).ready(function () {

    function enviarStep1()
    {
        var url= $('#urlStep1').val();

        var idCategoria=$('#categoriaSeleccionada').val();


        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idCategoria": idCategoria,
                "step":1
            }
        }).done(function (response) {



        });

    }

    function obtenerCategoria(idCategoria)
    {
        wait('#containerSelect');
        var categorias=null;

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

            endWait('#containerSelect');

        })
            .error(function () {
                endWait('#containerSelect');
            });

        return categorias;
    }

    function crearSelect(categorias)
    {
        var tagOption="";
        for (var i=0;i<categorias.length;i++)
        {
            tagOption+='<option value="'+categorias[i][0]+'">'+ categorias[i][1]+'</option>';
        }

        var tagSelect='<div class="col-md-3 cajon" id="'+categorias[0][2]+'" style="margin-top: 8px">'+
            '<select class="form-control  selectCategorias">'+
            '<option value="">Seleccione categoria</option>'+
            tagOption+
            '</select>'+
            '</div>';

        $('#containerSelect').append(tagSelect);
    }

    function reiniciarStartTotal() {
        $('#start').val(0);
        $('#total').val("");
    }

    function eliminarSelectNivel(nivel)
    {

        $('#containerSelect .cajon').each(function () {

            if ($(this).attr('id') >= nivel) {
                $(this).remove();
            }

        });
    }

    function crearHilo(nombre,nivel)
    {
        var hilo=$('#hilo').text();

        var hiloTemp="";

        if(hilo!="" && nivel!=1)
        {
            hilo=hilo.split('>');
            for (var i=0;i<(nivel-1);i++)
            {
                if(i==0)
                {
                    hiloTemp+=hilo[i];
                }
                else
                {
                    hiloTemp+=' > '+hilo[i];
                }

            }

            hiloTemp+=' > '+nombre;
        }
        else
        {
            hiloTemp=nombre;
        }

        $('#hilo').text(hiloTemp);


    }

    function crearHiloBottom(nombre,nivel)
    {
        var hilo=$('#hilo-bottom').text();

        var hiloTemp="";

        if(hilo!="" && nivel!=1)
        {
            hilo=hilo.split('>');
            for (var i=0;i<(nivel-1);i++)
            {
                if(i==0)
                {
                    hiloTemp+=hilo[i];
                }
                else
                {
                    hiloTemp+=' > '+hilo[i];
                }

            }

            hiloTemp+=' > '+nombre;
        }
        else
        {
            hiloTemp=nombre;
        }

        $('#hilo-bottom').text(hiloTemp);


    }

    $('.categoriaPublicar').on('click', function (e) {

        e.preventDefault();

        var idCategoria=$(this).attr('id');

        var categoria=null;

        categoria=obtenerCategoria(idCategoria);

        crearHilo(categoria[1],categoria[2]);
        crearHiloBottom(categoria[1],categoria[2]);

        if (categoria[3].length==0)
        {
            if (categoria[2]==1)
            {
                eliminarSelectNivel(2);
            }
            else
            {
                eliminarSelectNivel(categoria[2]-1);
            }
        }
        else
        {
            eliminarSelectNivel(categoria[3][0][2]);
            crearSelect(categoria[3]);
        }

    });
    $('#containerSelect').on('change','select.selectCategorias', function ()
    {


        $('#nextStep1').attr('disabled',true);

        var idCategoria=$(this).val();

        var categoria=null;

        categoria=obtenerCategoria(idCategoria);

        $('#categoriaSeleccionada').val(categoria[0]);

        crearHilo(categoria[1],categoria[2]);
        crearHiloBottom(categoria[1],categoria[2]);

        if (categoria[3].length==0)
        {
            if (categoria[2]==1)
            {
                eliminarSelectNivel(2);
            }
            else
            {
                eliminarSelectNivel(categoria[2]+1);
            }
            if (categoria[2]>=2)
            {
                //$('#nextStep1').attr('disabled',false);

                $('#nextStep1').removeAttr('disabled');
            }

        }
        else
        {
            eliminarSelectNivel(categoria[3][0][2]);
            crearSelect(categoria[3]);
        }


    });

    //Efecto scroll para cuando selecciona una categoria principal vaya hacia el final de la pagina

    $('a.categoriaPublicar').on('click',function(){
        $("html, body").animate({ scrollTop: $(document).height() }, 1500);
    });


    $('#nextStep1').on('click', function (e)
    {
        if($(this).attr('disabled')!='disabled')
        {
            enviarStep1();
        }
        else
        {
            e.preventDefault();
        }



    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Step 2
    function enviarStep2()
    {
        var validator=null;

        validator=validatorUsuarioStep2;

        var flag=false;

        var count=0;
        $('#containerImages input.imagenes-name').each(function(){
            count++;
        });

        if(count==0){
            $('p#msgBox2').append("Debe subir al menos una foto para su anuncio");
        }
        else{
            if(validator.form())
            {
                var $form= $('#formAnuncio2');

                $.ajax($form.attr('action'), {
                    type: 'post',
                    dataType: 'json',
                    async: false,
                    data: $form.serialize()
                }).done(function (response) {

                    flag=true;

                });
            }
        }

        return flag;

    }

    function escapeTags( str ) {
        return String( str )
            .replace( /&/g, '&amp;' )
            .replace( /"/g, '&quot;' )
            .replace( /'/g, '&#39;' )
            .replace( /</g, '&lt;' )
            .replace( />/g, '&gt;' );
    }

    //Uploader de imagenes

    if ($('#btnUploadImg').length>0) {
        var btn2 = document.getElementById('btnUploadImg'),
            progressBar2 = document.getElementById('progressBarImg'),
            progressOuter2 = document.getElementById('progressOuterImg'),
            //videoContainer = document.getElementById('videoContainer'),
            msgBox2 = document.getElementById('msgBox2');

        //Uploader foto de adicionar Coleccion
        var uploaderFotos = new ss.SimpleUpload({
            button: btn2,
            url: $("#img-upload-path").val(),
            data: {'idProducto': $('#idProducto').val()},
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
                    var imagesRoute = $('#img-route-new-image').val();

                    var selector = "#containerImages";

                    var ruta=escapeTags(response.file);

                    var img = '<div class="col-md-3" id="div-' + response.id + '">' +
                        '<div style="max-height:250px;max-width: 265px;min-height:250px;min-width: 265px">' +

                        '<img  height="240" width="260" class="" src="' + imagesRoute + ruta + '" alt="Thumbnail">' +

                        '</div>' +
                        '<div class="caption"><p>Foto de Perfil <input type="radio" class="flat" name="fotoPerfil" value="' + response.id + '"> </p></div>' +
                        '<input class="imagenes-name" type="hidden" name="imagenes[]" value="' + ruta + '">' +
                        // '<a class="btn btn-danger" data-div="div-' + ruta + '"><i class="fa fa-trash"></i> Eliminar</a>'
                        '<a id="e'+response.id+'" data-url="'+ response.file +'"  data-id="'+response.id+'" class="btn btn-danger btn-sm eliminar-imagen" href="#dangerMessage-eliminar-imagen" data-toggle="modal"><i class="fa fa-trash"></i> Eliminar</a>'
                    '</div>';
                    // var img = '<div class="col-md-3">' +
                    //     '<a href="#" class="thumbnail">' +
                    //     '<img class="img-responsive" src="' + imagesRoute + ruta + '" alt="Thumbnail">' +
                    //     '</a>' +
                    //     '<div class="caption"><p>Foto de Perfil <input type="radio" class="flat" name="fotoPerfil" value="'+response.id+'"> </p></div>'+
                    //     '<input class="imagenes-name" type="hidden" name="imagenes[]" value="' + ruta + '">' +
                    //     '<a id="e'+response.id+'" data-url="'+ response.url +'"  data-id="'+response.id+'" class="btn btn-danger btn-sm eliminar-imagen" href="#dangerMessage-eliminar-imagen" data-toggle="modal"><i class="fa fa-trash"></i> Eliminar</a>'
                    //     '</div>';

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

            var flag=enviarStep2();

            if (flag==false)
            {
                e.preventDefault();
            }

        });
    }

    var url_imagen = "";
    var id_imagen = "";
    $('#containerImages').on('click','.eliminar-imagen', function () {
        $('#imgToDelete').val($(this).data('url'));
        $('#idImgToDelete').val($(this).data('id'));
        // id_imagen = $(this).data('id');
        // url_imagen = $(this).data('url');
    });

    $('#btnConfirmDanger-eliminar-imagen').on('click', function (e) {

        var url = $('#img-delete-url').val();
        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                'urlImagen': $('#imgToDelete').val(),
                'tipo': 'productos'
            }
        }).done(function (response) {

            if (response.success == true) {
                var div = $('#div-' + $('#idImgToDelete').val());
                div.remove();
                alertify.success(response.msg);
            } else {
                alertify.error(response.msg);
            }
        });
        $('#dangerMessage-eliminar-imagen').modal('hide');
    });

    $('#no-dimensions-check').on('click',function (){
        if($(this).prop('checked')){
            $('#dimensions-fields').hide();
        }
        else {
            $('#dimensions-fields').fadeIn();
        }
    });
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Step 3

    function enviarStep3()
    {
        var validator=null;

        validator=validatorUsuarioStep3;

        var flag=false;

        if(validator.form())
        {
            var $form= $('#formAnuncio3');

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                async: false,
                data: $form.serialize()
            }).done(function (response) {

                flag=true;

            });
        }

        return flag;

    }

    $('#nextStep3').on('click', function (e) {

        var  flag=enviarStep3();

        if(flag==false)
        {
            e.preventDefault();
        }


    });


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////Step 4

    function BuscarCiudades(idProvincia,container,select)
    {
        wait(container);
        var url=$('#urlObtenerCiudades').val();

        var ciudades=null;

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idProvincia": idProvincia
            }
        }).done(function (response) {

            ciudades= response.ciudades;

            $(select+' option').remove();//#nombreCiudadAdicionar

            $(select).append('<option value="" >Seleccione Ciudad</option>');
            for(var i=0;i<ciudades.length;i++)
            {
                $(select).append('<option value="' + ciudades[i][0] + '" >' + ciudades[i][1] + '</option>');
            }
            endWait(container);

        })
            .error(function () {
                endWait(container);
            });

        return ciudades;
    }

    function buscarDireccion()
    {
        var direccion="";
        //$direccion=$direccion->getCalle()." No. ".$direccion->getNumero()." , entre ".$direccion->getEntreCalle().". ".$direccion->getProvincia()->getNombre().", ".$direccion->getCiudad()->getCiudadNombre().".";/\

        var ciudad=$('#ciudad option[value='+$('#ciudad').val()+']').text();

        var provincia=$('#provincia option[value='+$('#provincia').val()+']').text();

        var numero=$('#numero').val()!=""?" No. "+$('#numero').val():"";

        var entre=$('#entreCalles').val()!=""?" , entre "+$('#entreCalles').val():"";

        direccion= $('#calle').val()+numero+entre+". "+provincia+", "+ciudad+".";

        return direccion;
    }
    function guardarDireccion()
    {
        var validator=null;

        validator=validatorDireccionVenta2;

        var flag=false;

        if(validator.form()) {
            var $form = $('#formDireccionVenta2');

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                async: false,
                data: $form.serialize()
            }).done(function (response) {

                flag=true;
            });
        }

        return flag;

    }

    function enviarStep4()
    {
        var validator=null;

        validator=validatorUsuarioStep4;

        var flag=false;

        if(validator.form())
        {

            //Validar direccion de venta del vendedor

            $.ajax($('#urlValidarDireccionVenta').val(),{
                type: 'get',
                dataType: 'json'                
            }).done(function (response) {
                if(response[0]){
                    var $form= $('#formAnuncio4');

                    $.ajax($form.attr('action'), {
                        type: 'post',
                        dataType: 'json',
                        async: false,
                        data: $form.serialize()
                    }).done(function (response) {

                        //flag = true;

                        location.href=$('#nextStep4').attr('data-href');

                    });

                    return flag;
                }
                else{
                    alert("Debe entrar su dirección de venta para poder publicar el producto");
                }
            });

            return flag;

        }

        return flag;

    }

    $('#btnSubmitDireccionVenta').on('click', function () {

        var flag=guardarDireccion();

        if (flag==true)
        {
            $('#modalAdicionarDireccion').modal('hide');

            var direccion=buscarDireccion();

            $('#direccionP').text(direccion);

            $('#addDireccion').text('Cambiar dirección de venta');
        }



    });

    $('#provincia').on('change', function () {

        BuscarCiudades($(this).val(),"#containerDireccion","#ciudad");

    });

    $('#nextStep4').on('click', function (e) {

        var flag=enviarStep4();

        if (flag==false)
        {
            e.preventDefault();
        }
    });

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////Step 5
    function enviarStep5(idPlan)
    {
        var flag=false;

        var url= $('#urlAdicionarProducto').val();

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                'idPlan':idPlan,
                'idProducto':$('#idProducto').val(),
                'step': 5
            }
        }).done(function (response) {

            flag=true;

        });


        return flag;

    }

    $('.contratarPlan').on('click', function (e) {

        var  flag=enviarStep5($(this).attr('id'));

        if(flag==false)
        {
            e.preventDefault();
        }


    });


});
