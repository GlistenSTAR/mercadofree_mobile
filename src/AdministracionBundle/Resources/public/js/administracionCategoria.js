$(document).ready(function () {

    if($("#container").length>0)
    {
        wait('#panelCategoria');
        $.ajax($('#urlListar').val(), {
            'type': 'post',
            'dataType': 'json',
            'data': {
                'nivel': 1
            }
        }).done(function (response) {
            var categorias = response.categorias;
            var items = "";
            for (var i = 0; i < categorias.length; i++) {

                items += '<li id="' + categorias[i].id + ':' + categorias[i].nivel + '" class="selectCategoria nivel' + categorias[i].nivel + '"><i style="margin-right:5px;" class="'+categorias[i].icono+'"></i>' + categorias[i].nombre + '<a id="' + categorias[i].id + ':' + categorias[i].nivel + '" class="editar"  hidden style="margin-right:5px; margin-left:5px;" href="#" data-toggle="modal"><i class="fa fa-edit"></i></a><a class="eliminar" hidden id="' + categorias[i].id + ':' + categorias[i].nivel + '" href="#"><i class="fa fa-trash-o"></i></a></li>';
            }

            var categoriaHTML = '<div class="cajon" id="' + categorias[0].nivel + '">' +
                '<div class="panel panel-default ">' +
                '<div class="panel-heading " ><h4 style="margin-left: 25%;">' +
                'Nivel ' + categorias[0].nivel +
                '</h4></div>' +
                '<div class="panel-body">' +
                '<ul class="list-unstyled" id="ul' + categorias[0].nivel + '">' +
                items +
                '</ul>' +
                '</div>' +
                '</div>' +
                '</div>';

            $('#container').append(categoriaHTML);

        });
        endWait('#panelCategoria');
    }
    $("#container").on("mouseover",".selectCategoria", function () {

        var li=$(this).attr('id');

        $('#container a').each(function () {
            if($(this).attr('id')==li)
            {
                $(this).show();
            }
        });
    });

    $("#container").on("mouseout","a.editar", function () {

        var li=$(this).attr('id');

        $('#container a').each(function () {
            if($(this).attr('id')==li)
            {
                $(this).hide();
            }
        });
    });


    $("#container").on("mouseout",".selectCategoria", function () {

        var li=$(this).attr('id');

        $('#container a').each(function () {
            if($(this).attr('id')==li)
            {
                $(this).hide();
            }
        });
    });

    $("#container").on("click", ".selectCategoria", function () {

        wait('#panelCategoria');
        $('#editarCategoria').attr('disabled',false);
        $('#eliminarCategoria').attr('disabled',false);

        $(this).css('background-color', '#d6e1ff');
        var id = $(this).attr("id");
        var idNivel = id.split(':');
        /*wait('#container');*/

        if( $('#ultimaCategoriaSelected').attr('value')!=id)
        {
            $('#ultimaCategoriaSelected').attr('value', id);

            if (idNivel[1] >= 3) {
                $('#rowCaracteristicas').show();
            }
            else
            {
                $('#rowCaracteristicas').hide();
            }

            $(".nivel" + idNivel[1]).each(function () {

                if ($(this).attr("id") != id) {
                    $(this).css('background-color', '#ffffff');
                }

            });

            var nivel = idNivel[1];


            $('#container .cajon').each(function () {

                if ($(this).attr('id') > nivel) {
                    $(this).remove();
                }

            });

            nivel++;

            $.ajax($('#urlListar').val(), {
                'type': 'post',
                'dataType': 'json',
                'data': {
                    'nivel': nivel,
                    'idCategoriaPadre': idNivel[0]

                }
            }).done(function (response) {
                var categorias = response.categorias;

                var categoriaPadre = response.categoriaPadre;

                if (categoriaPadre[3] != null) {
                    $('#containerCaracteristica div').remove();
                    for (var i = 0; i < categoriaPadre[3].length; i++) {
                        var tagCaracteristica = '<div class="col-sm-6 m-t-sm caracteristica" style="margin-right: -15px;margin-left: 1px;">' +
                            '<div class="filter-tag alert alert-info alert-dismissible bg-shadow" role="alert" >' +
                            '<input type="hidden" class="form-control" name="caracteristicas[]" value="' + categoriaPadre[3][i] + '">' +
                            '<button type="button" class="close" id="buttonFiltroPais"  data-dismiss="alert" ><span aria-hidden="true" >×</span><span class="sr-only">Close</span></button>' +
                            '<b>' + categoriaPadre[3][i] + '</b>' +
                            '</div>' +
                            '</div>';
                        $('#containerCaracteristica').append(tagCaracteristica);
                    }

                }

                if (categorias.length != 0) {
                    var items = "";
                    for (var i = 0; i < categorias.length; i++) {
                        items += '<li id="' + categorias[i].id + ':' + categorias[i].nivel + '" class="selectCategoria nivel' + categorias[i].nivel + '">' + categorias[i].nombre + '<a id="' + categorias[i].id + ':' + categorias[i].nivel + '" class="editar"  hidden style="margin-right:5px; margin-left:5px;" href="#" data-toggle="modal"><i class="fa fa-edit"></i></a><a class="eliminar" hidden id="' + categorias[i].id + ':' + categorias[i].nivel + '" href="#"><i class="fa fa-trash-o"></i></a></li>';
                    }

                    var categoriaHTML = '<div class=" cajon" id="' + categorias[0].nivel + '" >' +
                        '<div class="panel panel-default ">' +
                        '<div class="panel-heading " ><h4 style="margin-left: 25%;">' +
                        'Nivel ' + categorias[0].nivel +
                        '</h4></div>' +
                        '<div class="panel-body">' +
                        '<ul class="list-unstyled" id="ul' + categorias[0].nivel + '">' +
                        items +
                        '</ul>' +
                        '</div>' +
                        '</div>' +
                        '</div>';

                    $('#container').append(categoriaHTML);

                }
                endWait('#panelCategoria');
            });
        }
        else
        {
            $(this).css('background-color', '#ffffff');

            $('#container .cajon').each(function () {

                if ($(this).attr('id') > idNivel[1]) {
                    $(this).remove();
                }

            });

            //$('#ultimaCategoriaSelected').attr('value','');

            endWait('#panelCategoria');
        }
    });

    $('#nuevaCategoria').on('click', function () {

        wait("#panelCategoria");

        var data = $('#ultimaCategoriaSelected').val();

        var nivel = 1;

        var idCategoria = 0;

        var nombreCategoria = $('#nombreCategoria').val();

        var iconoCategoria = $('#iconoCategoria').val();
        
        if (data != "") {
            data = data.split(':');

            nivel = data[1];

            idCategoria = data[0];
        }

        if(nombreCategoria!="" && iconoCategoria!="")
        {
            $.ajax($('#urlAdicionar').val(), {
                'type': 'post',
                'dataType': 'json',
                'data': {
                    'nivel': nivel,
                    'idCategoria': idCategoria,
                    'nombreCategoria':nombreCategoria,
                    'iconoCategoria':iconoCategoria,
                    'modoAdicionar':1
                }
            }).done(function (response) {

                if (response!=false)
                {
                    $('#nombreCategoria').val("");
                    alertify.success("Su CATEGORIA ha sido adicionada correctamente.");

                    var nivelTemp = parseInt(nivel) + 1;
                    $('#container .cajon').each(function () {

                        if (data == "") {
                            $(this).remove();
                        }
                        else if ($(this).attr('id') == nivelTemp) {
                            $(this).remove();
                        }

                    });

                    if (data != "") {
                        nivel++;
                    }

                    $.ajax($('#urlListar').val(), {
                        'type': 'post',
                        'dataType': 'json',
                        'data': {
                            'nivel': nivel,
                            'idCategoriaPadre': idCategoria


                        }
                    }).done(function (response) {
                        var categorias = response.categorias;

                        if (categorias.length != 0) {
                            var items = "";
                            for (var i = 0; i < categorias.length; i++) {
                                items += '<li id="' + categorias[i].id + ':' + categorias[i].nivel + '" class="selectCategoria nivel' + categorias[i].nivel + '"><i class="'+categorias[i].icono+'"></i> ' + categorias[i].nombre +'<a id="' + categorias[i].id + ':' + categorias[i].nivel + '" class="editar"  hidden style="margin-right:5px; margin-left:5px;" href="#" data-toggle="modal"><i class="fa fa-edit"></i></a><a class="eliminar" hidden id="' + categorias[i].id + ':' + categorias[i].nivel + '" href="#"><i class="fa fa-trash-o"></i></a> '+'</li>';
                            }

                            var categoriaHTML = '<div class=" cajon" id="' + categorias[0].nivel + '" >' +
                                '<div class="panel panel-default ">' +
                                '<div class="panel-heading " ><h4 style="margin-left: 25%;">' +
                                'Nivel ' + categorias[0].nivel +
                                '</h4></div>' +
                                '<div class="panel-body">' +
                                '<ul class="list-unstyled" id="ul' + categorias[0].nivel + '">' +
                                items +
                                '</ul>' +
                                '</div>' +
                                '</div>' +
                                '</div>';

                            $('#container').append(categoriaHTML);
                            endWait("#panelCategoria");


                        }

                    });
                }
                else
                {
                    endWait("#panelCategoria");
                    alertify.error('El nombre de la CATEGORIA ya esta en el sistema.');
                }


            });
        }
        else
        {
            endWait("#panelCategoria");
            alertify.error('Debe insertar el nombre y el ícono de la CATEGORIA.');
        }

    });

    //Adicionar Caracteristicas visualmente
    $('#nuevaCaracteristica').on('click', function () {

        var nombreCaracteristica = $('#nombreCaracteristica').val();
        if (nombreCaracteristica!=null && nombreCaracteristica!="" )
        {
            var tagCaracteristica = '<div class="col-sm-6 m-t-sm caracteristica" style="margin-right: -15px;margin-left: 1px;">' +
                '<div class="filter-tag alert alert-info alert-dismissible bg-shadow" role="alert" >' +
                '<input type="hidden" class="form-control" name="caracteristicas[]" value="' + nombreCaracteristica + '">' +
                '<button type="button" class="close" id="buttonFiltroPais"  data-dismiss="alert" ><span aria-hidden="true" >×</span><span class="sr-only">Close</span></button>' +
                '<b>' + nombreCaracteristica + '</b>' +
                '</div>' +
                '</div>';
            $('#containerCaracteristica').append(tagCaracteristica);
            $('#nombreCaracteristica').val("");

        }
       alertify.error('Debe de llenar el campo nombre.');

    });

    //Adicionar Caracteristicas a la Base de Datos
    $('#guardarCaracteristica').on('click', function () {
        wait('#panelCategoria');
        $('#containerCaracteristica button').each(function () {
            if ($(this).attr('type')=="hidden")
            {
                $(this).remove();
            }
        });

        var $form = $('#formCaracteristicas');

        $.ajax($form.attr('action'), {
            type: 'post',
            dataType: 'json',
            data: $form.serialize()
        }).done(function (response) {

            endWait('#panelCategoria');

            alertify.success("Las caracteristicas editadas han sido guardadas correctamente");

        });

    });

    //Editar Categoria
    $("#container").on("click","a.editar",  function () {

        var data = $(this).attr('id');

        data = data.split(':');

        $.ajax($('#urlEditar').val(), {
            'type': 'post',
            'dataType': 'json',
            'data': {
                'nivelCategoria': data[1],
                'idCategoria': data[0],
                'editarCategoria': false
            }
        }).done(function (response) {
            var categoria = response.categoria;
            $('#idCategoria').val(categoria.id);

            var categoriasPadre = response.categoriasPadre;
            console.log(categoriasPadre);
            var nivelMax = response.nivel;

            var idCategoriaPadre = response.idCategoriaPadre;

            $('#idCategoriaPadre').val(idCategoriaPadre);

            var nivelCategoriaPadre = response.nivelCategoriaPadre;

            $('#nombreCategoriaEditar').val(categoria.nombre);

            $('#iconoCategoriaEditar').val(categoria.icono);

            $('#iconoContainerCategoriaEditar').addClass(categoria.icono);
            
            $('#tiempoExpiracionEditar').val(categoria.tiempoExpiracion);

            $('#nivelAntesEditar').val(categoria.nivel);

            if(categoria.nivel<3)
            {
                $('#nombreCaracteristicaAdicionarEditar').attr('disabled',true);
                $('#nuevaCaracteristicaEditar').attr('disabled',true);
            }
            else
            {
                $('#nombreCaracteristicaAdicionarEditar').attr('disabled',false);
                $('#nuevaCaracteristicaEditar').attr('disabled',false);
            }
            if (categoria.nivel==1)
            {
                $('#nombreCategoriaHija option').remove();
                $('#rowHija').show();
                $('#rowPadre').hide();
                var nivelHija = 2;

                $.ajax($('#urlObtener').val(), {
                    'type': 'post',
                    'dataType': 'json',
                    'data': {
                        'nivel': nivelHija
                    }
                }).done(function (response) {
                    var categorias = response.categorias;
                    var nombreTag = "";
                    console.log(categorias);
                    $('#nombreCategoriaHija').append('<option value="0" class="">Seleccione Categoria Hija</option>');
                    for (var i = 0; i < categorias.length; i++) {
                        nombreTag = '<option value="' + categorias[i].id + '" class="">' + categorias[i].nombre + '</option>';
                        $('#nombreCategoriaHija').append(nombreTag);
                    }


                    $('#nivelCategoriaHija').val(categorias[0].nivel);
                });
            }
            /*else
            {
                $('#nombreCategoriaPadre option').remove();
                $('#rowHija').hide();
                $('#rowPadre').show();
                var nivelPadre= parseInt(categoria[1])+1;

                $.ajax($('#urlObtener').val(), {
                    'type': 'post',
                    'dataType': 'json',
                    'data': {
                        'nivel': nivelPadre
                    }
                }).done(function (response) {
                    var categorias = response.categorias;
                    var nombreTag = "";
                    $('#nombreCategoriaPadre').append('<option value="0" class="">Seleccione Categoria Hija</option>');
                    for (var i = 0; i < categorias.length; i++) {
                        nombreTag = '<option value="' + categorias[i][2] + '" class="">' + categorias[i][0] + '</option>';
                        $('#nombreCategoriaPadre').append(nombreTag);
                    }


                    $('#nivelCategoriaPadre').val(categorias[0][1]);
                });
            }*/

            var nivelTag = "";
            $('#nivelCategoriaEditar option').remove();
            for (var i = 1; i < (parseInt(nivelMax) + 1); i++) {
                if (i == categoria.nivel) {
                    nivelTag = '<option selected id="' + i + '" class="">' + i + '</option>';
                }
                else
                    nivelTag = '<option id="' + i + '" class="">' + i + '</option>';
                $('#nivelCategoriaEditar').append(nivelTag);
            }
            var cat = categoria[0];
            if (cat.length > 0) {
                $('#containerCaracteristicaEditar div').remove();
                for (var i = 0; i < cat.length; i++) {
                    var tagCaracteristica = '<div class="col-sm-6 m-t-sm caracteristica" style="margin-right: -15px;margin-left: 1px;">' +
                        '<div class="filter-tag alert alert-info alert-dismissible bg-shadow" role="alert" >' +
                        '<input type="hidden" class="form-control" name="caracteristicasEditar[]" value="' + cat[i] + '">' +
                        '<button type="button" class="close" id="buttonFiltroPais"  data-dismiss="alert" ><span aria-hidden="true" >×</span><span class="sr-only">Close</span></button>' +
                        '<b>' + cat[i] + '</b>' +
                        '</div>' +
                        '</div>';
                    $('#containerCaracteristicaEditar').append(tagCaracteristica);
                }
            }
            if (categoria.nivel>1)
            {

                var categoriaTag = "";
                for (var i = 0; i < categoriasPadre.length; i++) {
                    if (categoriasPadre[i].id == idCategoriaPadre) {
                        categoriaTag = '<option selected='+(categoria.idPadre == categoriasPadre[i].id ? "selected" :"")+' value="' + categoriasPadre[i].id + '" class="">' + categoriasPadre[i].nombre + '</option>';

                        if (categoriasPadre[i][0].length != 0) {
                            $('#containerCaracteristicaPadreEditar div').remove();
                            for (var j = 0; j < categoriasPadre[i][0].length; j++) {
                                var tagCaracteristica = '<div class="col-sm-4 m-t-sm caracteristica" style="margin-right: -15px;margin-left: 1px;">' +
                                    '<div class="filter-tag alert alert-info alert-dismissible bg-shadow" role="alert" >' +
                                    '<input type="hidden" class="form-control" name="caracteristicasPadre[]" value="' + categoriasPadre[i][0][j] + '">' +
                                    '<button type="button" class="close" id="buttonFiltroPais"  data-dismiss="alert" ><span aria-hidden="true" >×</span><span class="sr-only">Close</span></button>' +
                                    '<b>' + categoriasPadre[i][0][j] + '</b>' +
                                    '</div>' +
                                    '</div>';
                                $('#containerCaracteristicaPadreEditar').append(tagCaracteristica);
                            }
                        }
                    }
                    else
                        categoriaTag = '<option value="' + categoriasPadre[i].id + '" class="">' + categoriasPadre[i].nombre + '</option>';
                    $('#nombreCategoriaPadre').append(categoriaTag);
                }
            }


            if (categoriasPadre.length>0)
            {
                $('#nivelCategoriaPadre').val(categoriasPadre[0].nivel);
                $("#nombreCategoriaPadre[option='"+categoria.idPadre+"']").attr("selected","selected");
                $("#nombreCategoriaPadre").val(categoria.idPadre)
            }
            else
            {
                $('#nivelCategoriaPadre').val("0");
            }



            $('#modalEditar').click();
        })
            .error(function () {
                alertify.error('Lo sentimos se ha producido un error.');
            });

    });

    $('#nivelCategoriaEditar').on('change', function ()
    {

        wait('#containerEditarCategoria');

        $('#nombreCategoriaPadre option').remove();

        $('#containerCaracteristicaPadreEditar div').remove();
        if ($(this).val() != 1)
        {
            $('#rowPadre').show();
            $('#rowHija').hide();
            if ($(this).val() >= 3) {
                var idCategoria = $('#ultimaCategoriaSelected').val();
                idCategoria = idCategoria.split(':');
                $.ajax($('#urlObtenerCaracteristicas').val(), {
                    'type': 'post',
                    'dataType': 'json',
                    'data': {
                        'idCategoria': idCategoria[0]
                    }
                }).done(function (response) {
                    $('#containerCaracteristicaEditar div').remove();

                    var caracteristicas = response.caracteristicas;

                    for (var j = 0; j < caracteristicas.length; j++) {
                        var tagCaracteristica = '<div class="col-sm-4 m-t-sm caracteristica" style="margin-right: -15px;margin-left: 1px;">' +
                            '<div class="filter-tag alert alert-info alert-dismissible bg-shadow" role="alert" >' +
                            '<input type="hidden" class="form-control" name="caracteristicasEditar[]" value="' + caracteristicas[j] + '">' +
                            '<button type="button" class="close" id="buttonFiltroPais"  data-dismiss="alert" ><span aria-hidden="true" >×</span><span class="sr-only">Close</span></button>' +
                            '<b>' + caracteristicas[j] + '</b>' +
                            '</div>' +
                            '</div>';
                        $('#containerCaracteristicaEditar').append(tagCaracteristica);
                    }

                    $('#nuevaCaracteristicaEditar').attr("disabled", false);
                    $('#nombreCaracteristicaAdicionarEditar').attr('disabled', false);

                    endWait('#containerEditarCategoria');

                });

            }
            else
            {
                $('#containerCaracteristicaEditar div').remove();
                $('#nuevaCaracteristicaEditar').attr("disabled", true);
                $('#nombreCaracteristicaAdicionarEditar').attr('disabled', true);


            }
            var nivelPadre = parseInt($(this).val()) - 1;

            $.ajax($('#urlObtener').val(), {
                'type': 'post',
                'dataType': 'json',
                'data': {
                    'nivel': nivelPadre
                }
            }).done(function (response) {
                var categorias = response.categorias;
                var nombreTag = "";
                $('#nombreCategoriaPadre').append('<option value="0" class="">Seleccione Categoria Padre</option>');
                for (var i = 0; i < categorias.length; i++) {
                    nombreTag = '<option value="' + categorias[i].id + '" class="">' + categorias[i].nombre + '</option>';
                    $('#nombreCategoriaPadre').append(nombreTag);
                }


                $('#nivelCategoriaPadre').val(categorias[0].nivel);
                endWait('#containerEditarCategoria');
            });




        }
        else {
            /*$('#nombreCategoriaPadre').attr("disabled",true);
             $('#nivelCategoriaPadre').attr('disabled',true);
             $('#containerCaracteristicaPadreEditar div').remove();*/
            $('#rowHija').show();
            $('#rowPadre').hide();

            $('#containerCaracteristicaEditar div').remove();

            $('#nuevaCaracteristicaEditar').attr("disabled", true);
            $('#nombreCaracteristicaAdicionarEditar').attr('disabled', true);

            var nivelHija = parseInt($(this).val()) + 1;

            $.ajax($('#urlObtener').val(), {
                'type': 'post',
                'dataType': 'json',
                'data': {
                    'nivel': nivelHija
                }
            }).done(function (response) {
                var categorias = response.categorias;
                var nombreTag = "";
                $('#nombreCategoriaHija option').remove();
                $('#nombreCategoriaHija').append('<option value="0" class="">Seleccione Categoria Hija</option>');
                for (var i = 0; i < categorias.length; i++) {
                    nombreTag = '<option value="' + categorias[i][2] + '" class="">' + categorias[i][0] + '</option>';
                    $('#nombreCategoriaHija').append(nombreTag);
                }


                $('#nivelCategoriaHija').val(categorias[0].nivel);
                endWait('#containerEditarCategoria');
            });

            $('#nivelCategoriaHija').val(2);

        }

    });

    $('#nombreCategoriaPadre').on('change', function () {

        var idCategoria = $(this).val();
        $.ajax($('#urlObtenerCaracteristicas').val(), {
            'type': 'post',
            'dataType': 'json',
            'data': {
                'idCategoria': idCategoria
            }
        }).done(function (response) {
            $('#containerCaracteristicaPadreEditar div').remove();

            var caracteristicas = response.caracteristicas;

            for (var j = 0; j < caracteristicas.length; j++) {
                var tagCaracteristica = '<div class="col-sm-4 m-t-sm caracteristica" style="margin-right: -15px;margin-left: 1px;">' +
                    '<div class="filter-tag alert alert-info alert-dismissible bg-shadow" role="alert" >' +
                    '<input type="hidden" class="form-control" name="caracteristicasPadre[]" value="' + caracteristicas[j] + '">' +
                    '<button type="button" class="close" id="buttonFiltroPais"  data-dismiss="alert" ><span aria-hidden="true" >×</span><span class="sr-only">Close</span></button>' +
                    '<b>' + caracteristicas[j] + '</b>' +
                    '</div>' +
                    '</div>';
                $('#containerCaracteristicaPadreEditar').append(tagCaracteristica);
            }

        });
    });

    $('#nuevaCaracteristicaEditar').on('click', function () {

        var nombreCaracteristica = $('#nombreCaracteristicaAdicionarEditar').val();
        var tagCaracteristica = '<div class="col-sm-4 m-t-sm caracteristica" style="margin-right: -15px;margin-left: 1px;">' +
            '<div class="filter-tag alert alert-info alert-dismissible bg-shadow" role="alert" >' +
            '<input type="hidden" class="form-control" name="caracteristicasEditar[]" value="' + nombreCaracteristica + '">' +
            '<button type="button" class="close" id="buttonFiltroPais"  data-dismiss="alert" ><span aria-hidden="true" >×</span><span class="sr-only">Close</span></button>' +
            '<b>' + nombreCaracteristica + '</b>' +
            '</div>' +
            '</div>';
        $('#containerCaracteristicaEditar').append(tagCaracteristica);

        $('#nombreCaracteristicaAdicionarEditar').val("");
    });

    $('#btnSubmitCategoriaEditar').on('click', function () {
        $('#btnSubmitCategoriaEditar').text('Modificando Categoria...');

        var dd=$('#idCategoria').val();

        var $form = $('#formEditarCategoria');

        var nivelBeforeEdit=$('#nivelAntesEditar').val();

        var nivelCategoriaActual = $('#nivelCategoriaEditar').val();

        if($('#nivelCategoriaEditar').val()!="1" && $('#nombreCategoriaPadre').val()=="0")
        {
            alertify.error('Debe entrar la CATEGORIA PADRE.');
        }
        else
        {
            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                data: $form.serialize()
            }).done(function (response) {

                var categoria=response.categoria;

                console.log(nivelBeforeEdit);
                console.log(categoria);
                console.log('nivelBEforeEdit '+nivelBeforeEdit + ' categoria.nivel ' + categoria.nivel)
                if (nivelBeforeEdit== categoria.nivel)
                {
                   console.log('Primer if nivelBeforeEdit==categoria.id');
                    var id=categoria.nivel+":"+categoria.id;

                    $('#container .cajon li').each(function () {

                        if($(this).attr('id')==id)
                        {
                            $(this).empty();
                            var content='<i style="margin-right:5px;" class="'+categoria.icono+'"></i> ';
                            $(this).append($(content));
                            $(this).append(categoria.nombre);
                            $(this).append('<a id="' + categoria.nivel + ':' + categoria.id + '" class="editar"  hidden style="margin-right:5px; margin-left:5px;" href="#" data-toggle="modal"><i class="fa fa-edit"></i></a><a class="eliminar" hidden id="' + categoria.nivel + ':' + categoria.id + '" href="#"><i class="fa fa-trash-o"></i></a> ');
                        }
                    });
                }
                else if (nivelBeforeEdit=="1" || categoria.nivel=="1")
                {
                    console.log('Primer else if nivelBeforeEdit '+ nivelBeforeEdit +' == 1 || categoria[1]==1 ' + categoria[1]);
                    $('#container .cajon').each(function ()
                    {
                        $(this).remove();

                    });
                    $.ajax($('#urlListar').val(), {
                        'type': 'post',
                        'dataType': 'json',
                        'data': {
                            'nivel': 1
                        }
                    }).done(function (response) {
                        var categorias = response.categorias;

                        var items = "";
                        for (var i = 0; i < categorias.length; i++) {
                            items += '<li id="' + categorias[i].id + ':' + categorias[i].nivel + '" class="selectCategoria nivel' + categorias[i].nivel + '"><i class="'+categorias[i].icono+'"></i> ' + categorias[i].nombre + '<a id="' + categorias[i].id + ':' + categorias[i].nivel + '" class="editar"  hidden style="margin-right:5px; margin-left:5px;" href="#" data-toggle="modal"><i class="fa fa-edit"></i></a><a class="eliminar" hidden id="' + categorias[i].id + ':' + categorias[i].nivel + '" href="#"><i class="fa fa-trash-o"></i></a></li>';
                        }

                        var categoriaHTML = '<div class=" cajon" id="' + categorias[0].nivel + '">' +
                            '<div class="panel panel-default ">' +
                            '<div class="panel-heading " ><h4 style="margin-left: 25%;">' +
                            'Nivel ' + categorias[0].nivel +
                            '</h4></div>' +
                            '<div class="panel-body">' +
                            '<ul class="list-unstyled" id="ul' + categorias[0].nivel + '">' +
                            items +
                            '</ul>' +
                            '</div>' +
                            '</div>' +
                            '</div>';

                        $('#container').append(categoriaHTML);

                    });
                }
                else if (nivelBeforeEdit>categoria.nivel)
                {
                    console.log('Segundo else if nivelBeforeEdit '+nivelBeforeEdit+' > categoria[1] '+ categoria.nivel);
                    $('#container .cajon li').each(function(){
                        var id = $(this).attr('id');
                        id = id.split(':');
                        if(id[0] == parseInt(categoria.id)){
                            item = '<li id="' + categoria.id + ':' + categoria.nivel + '" class="selectCategoria nivel' + categoria.nivel + '"><i class="'+categoria.icono+'"></i> ' + categoria.nombre + '<a id="' + categoria.id + ':' + categoria.nivel + '" class="editar"  hidden style="margin-right:5px; margin-left:5px;" href="#" data-toggle="modal"><i class="fa fa-edit"></i></a><a class="eliminar" hidden id="' + categoria.id + ':' + categoria.nivel + '" href="#"><i class="fa fa-trash-o"></i></a></li>';
                            $('#ul'+categoria.nivel).append(item);
                            $(this).remove();
                            $('#'+nivelBeforeEdit).remove();
                        }
                    });
                }
                else if (nivelBeforeEdit<categoria.nivel)
                {
                    $.ajax($('#urlListar').val(), {
                        'type': 'post',
                        'dataType': 'json',
                        'data': {
                            'nivel': categoria.nivel,
                            'idCategoriaPadre': categoria.idPadre
                        }
                    }).done(function (response) {
                        var categorias = response.categorias;
                        console.log(response);
                        var items = "";
                        for (var i = 0; i < categorias.length; i++) {
                            items += '<li id="' + categorias[i].id + ':' + categorias[i].nivel + '" class="selectCategoria nivel' + categorias[i].nivel + '"><i class="'+categorias[i].icono+'"></i> ' + categorias[i].nombre + '<a id="' + categorias[i].id + ':' + categorias[i].nivel + '" class="editar"  hidden style="margin-right:5px; margin-left:5px;" href="#" data-toggle="modal"><i class="fa fa-edit"></i></a><a class="eliminar" hidden id="' + categorias[i].id + ':' + categorias[i].nivel + '" href="#"><i class="fa fa-trash-o"></i></a></li>';
                        }
                        $('#container .cajon li').each(function(){
                            var id = $(this).attr('id');
                            id = id.split(':');
                            if(id[0] == parseInt(categoria.id)){
                                $(this).remove();
                            }
                        });
                        var categoriaHTML = '<div class=" cajon" id="' + categoria.nivel + '">' +
                            '<div class="panel panel-default ">' +
                            '<div class="panel-heading " ><h4 style="margin-left: 25%;">' +
                            'Nivel ' + categoria.nivel +
                            '</h4></div>' +
                            '<div class="panel-body">' +
                            '<ul class="list-unstyled" id="ul' + categoria.nivel + '">' +
                            items +
                            '</ul>' +
                            '</div>' +
                            '</div>' +
                            '</div>';

                        $('#container').append(categoriaHTML);

                    });
                }
                $('#btnSubmitCategoriaEditar').text('Aceptar');
                $('#modalEditarCategoria').modal('hide');


            });
        }

    });

    $("#container").on("click","a.eliminar", function (e) {
        e.preventDefault();
        var data = $(this).attr('id');
        data = data.split(':');

        $.ajax($('#urlEliminar').val(), {
            'type': 'post',
            'dataType': 'json',
            'data': {
                'idCategoriaEliminar': data[0],
                'nivel': data[1],
                'eliminarCategoria': false
            }
        }).done(function (response) {
            var categoria = response.categoria;

            var categoriaNivel = response.categoriaNivel;

            $('#nombreCategoriaEliminar').val(categoria[0]);

            $('#nivelCategoriaEliminar').val(categoria[1]);

            $('#idCategoriaEliminar').val(data[0]);

            var nombreTag = "";

            $('#nombreCategoriaVecino option').remove();

            $('#nombreCategoriaVecino').append('<option value="0" class="">Seleccione Categoria</option>');
            for (var i = 0; i < categoriaNivel.length; i++) {
                nombreTag = '<option data-nivel="'+categoriaNivel[i].nivel+'" value="' + categoriaNivel[i].id + '" class="">' + categoriaNivel[i].nombre + '</option>';
                $('#nombreCategoriaVecino').append(nombreTag);
            }

            $('#nivelCategoriaVecino').val(categoriaNivel[0].nivel);

            $('#modalEliminar').click();

        });
    });

    $('#btnSubmitCategoriaEliminar').on('click', function () {

        $('#btnSubmitCategoriaEliminar').text('Eliminando Categoria...');

        var $form = $('#formEliminarCategoria');

        var data = $('#ultimaCategoriaSelected').val();
        data = data.split(':');

        $('#idCategoriaEliminar').val(data[0]);
        $('#idCategoriaVecinaEliminar').val(data[1]);

        $.ajax($form.attr('action'), {
            type: 'post',
            dataType: 'json',
            data: $form.serialize()
        }).done(function (response) {

            $('#btnSubmitCategoriaEliminar').text('Aceptar');

            if(response.success){
                $('#modalEliminarCategoria').modal('hide');

                var dd=$('#ultimaCategoriaSelected').attr('value');

                d=dd.split(':');

                $("#container .selectCategoria").each(function () {

                    if($(this).attr('id')==dd)
                    {
                        $(this).remove();
                    }

                });

                $('#container .cajon').each(function () {

                    if ($(this).attr('id') > d[1]) {
                        $(this).remove();
                    }

                });
            }
            else{
                alert(response.message);
            }

        });


    });

    ////////////////////////////////////////////////////////////////Aki comienza el adicionar de template

   if($('#formAdicionarCategoria').length>0)
   {
       $('#nuevaCaracteristicaAdicionar2').attr("disabled", true);
       $('#nombreCaracteristicaAdicionar2').attr('disabled', true);
       $.ajax($('#urlAdicionar').val(), {
           'type': 'post',
           'dataType': 'json',
           'data': {
               'modoAdicionar': 2,
               'nivel':1
           }
       }).done(function (response) {
           $('#nivelCategoriaAdicionar option').remove();
           var nivelTag = "";
           for (var i = 1; i < (parseInt(response) + 2); i++) {
               nivelTag = '<option value="' + i + '" class="">' + i + '</option>';
               $('#nivelCategoriaAdicionar').append(nivelTag);
           }
       });
   }
    $('#nombreCategoriaAdicionar').on('keyup', function () {

        if($(this).val()=="")
        {
            $('#nuevaCategoriaAdicionar').attr('disabled',true);
            $('#nuevaCategoriaCancelar').attr('disabled',true);
        }
        else
        {
            $('#nuevaCategoriaAdicionar').attr('disabled',false);
            $('#nuevaCategoriaCancelar').attr('disabled',false);
        }
    });
    
    $('#nivelCategoriaAdicionar').on('change', function ()
    {

        wait('#adicionarCategoriaTemplate');

        $('#nombreCategoriaPadreAdicionar option').remove();

        $('#containerCaracteristicaPadreAdicionar div').remove();
        if ($(this).val() != 1) {


            $('#nuevaCaracteristicaAdicionar2').attr("disabled", true);
            $('#nombreCaracteristicaAdicionar2').attr('disabled', true);
            $('#nombreCategoriaPadreAdicionar').attr("disabled", false);

            if ($(this).val() >= 3)
            {
                $('#nuevaCaracteristicaAdicionar2').attr("disabled", false);
                $('#nombreCaracteristicaAdicionar2').attr('disabled', false);
            }
            var nivelPadre = parseInt($(this).val()) - 1;

            $.ajax($('#urlObtener').val(), {
                'type': 'post',
                'dataType': 'json',
                'data': {
                    'nivel': nivelPadre
                }
            }).done(function (response) {
                var categorias = response.categorias;
                var nombreTag = "";
                $('#nombreCategoriaPadreAdicionar option').remove();
                $('#nombreCategoriaPadreAdicionar').append('<option value="0" class="">Seleccione Categoria Padre</option>');
                for (var i = 0; i < categorias.length; i++) {
                    nombreTag = '<option value="' + categorias[i][2] + '" class="">' + categorias[i][0] + '</option>';
                    $('#nombreCategoriaPadreAdicionar').append(nombreTag);
                }


                $('#nivelCategoriaPadreAdicionar').val(categorias[0][1]);
                endWait('#adicionarCategoriaTemplate');
            });




        }
        else
        {
            $('#nivelCategoriaPadreAdicionar').val("");
            $('#nombreCategoriaPadreAdicionar').attr("disabled", true);
            $('#nuevaCaracteristicaAdicionar').attr("disabled", true);
            $('#nombreCaracteristicaAdicionarAdicionar').attr('disabled', true);
            endWait('#adicionarCategoriaTemplate');
        }


    });

    $('#nuevaCaracteristicaAdicionar2').on('click', function () {

        var nombreCaracteristica = $('#nombreCaracteristicaAdicionar2').val();
        if(nombreCaracteristica!="")
        {
            var tagCaracteristica = '<div class="col-sm-4 m-t-sm caracteristica" style="margin-right: -15px;margin-left: 1px;">' +
                '<div class="filter-tag alert alert-info alert-dismissible bg-shadow" role="alert" >' +
                '<input type="hidden" class="form-control" name="caracteristicasAdicionar[]" value="' + nombreCaracteristica + '">' +
                '<button type="button" class="close" id="buttonFiltroPais"  data-dismiss="alert" ><span aria-hidden="true" >×</span><span class="sr-only">Close</span></button>' +
                '<b>' + nombreCaracteristica + '</b>' +
                '</div>' +
                '</div>';
            $('#containerCaracteristicaAdicionar').append(tagCaracteristica);

            $('#nombreCaracteristicaAdicionar2').val("");
        }
        else
        {
            alertify.error('Debe entrar el nombre de la caracteristica.');
        }

    });

    $('#nombreCategoriaPadreAdicionar').on('change', function () {



        var idCategoria = $(this).val();
        $.ajax($('#urlObtenerCaracteristicas').val(), {
            'type': 'post',
            'dataType': 'json',
            'data': {
                'idCategoria': idCategoria
            }
        }).done(function (response) {
            $('#containerCaracteristicaPadreAdicionar div').remove();

            var caracteristicas = response.caracteristicas;

            for (var j = 0; j < caracteristicas.length; j++) {
                var tagCaracteristica = '<div class="col-sm-4 m-t-sm caracteristica" style="margin-right: -15px;margin-left: 1px;">' +
                    '<div class="filter-tag alert alert-info alert-dismissible bg-shadow" role="alert" >' +
                    '<input type="hidden" class="form-control" name="caracteristicasPadre[]" value="' + caracteristicas[j] + '">' +
                    '<button type="button" class="close" id="buttonFiltroPais"  data-dismiss="alert" ><span aria-hidden="true" >×</span><span class="sr-only">Close</span></button>' +
                    '<b>' + caracteristicas[j] + '</b>' +
                    '</div>' +
                    '</div>';
                $('#containerCaracteristicaPadreAdicionar').append(tagCaracteristica);
            }

        });
    });


    $('#nuevaCategoriaAdicionar').on('click', function () {

        wait('#adicionarCategoriaTemplate');

        if($('#nombreCategoriaAdicionar').val()!="")
        {
            if($('#nombreCategoriaPadreAdicionar').val()=="0" && parseInt($('#nivelCategoriaAdicionar').val())>1)
            {
                endWait('#adicionarCategoriaTemplate');
                alertify.error('Debe de seleccionar una categoría padre.')
            }
            else
            {
                $('#modoAdicionar').val(3);

                var $form = $('#formAdicionarCategoria');

                $.ajax($form.attr('action'), {
                    type: 'post',
                    dataType: 'json',
                    data: $form.serialize()
                }).done(function (response) {
                    endWait('#adicionarCategoriaTemplate');
                    alertify.success("Su categoria ha sido guardada correctamente");
                    $('#nuevaCategoriaCancelar').click();

                });
            }

        }
        else
            {
                endWait('#adicionarCategoriaTemplate');
                alertify.error('Debe al menos llenar el campo del nombre de la categoria. ');
            }


    });

    $('#nuevaCategoriaCancelar').on('click', function () {
        $('#nivelCategoriaPadreAdicionar').val("");
        $('#nombreCategoriaPadreAdicionar option').remove();
        $('#nombreCategoriaPadreAdicionar').attr("disabled", true);
        $('#nombreCategoriaAdicionar').val("");
        $('#nuevaCaracteristicaAdicionar').attr("disabled", true);
        $('#containerCaracteristicaAdicionar div').remove();
        $('#nombreCaracteristicaAdicionarAdicionar').val("");
        $('#nombreCaracteristicaAdicionarAdicionar').attr('disabled', true);
        $('#containerCaracteristicaPadreAdicionar div').remove();

        $('#iconoCategoriaAdicionar').val("");
        $('#iconoContainerCategoriaAdicionar').attr('class','');
        
        $('#tiempoExpiracionCategoriaAdicionar').val("");
        $('#tiempoExpiracionContainerCategoriaAdicionar').attr('class','');

        $('#nuevaCategoriaAdicionar').attr('disabled', true);
        $('#nuevaCategoriaCancelar').attr('disabled', true);

        $('#nivelCategoriaAdicionar option').each(function ()
        {
           if($(this).val()==1)
           {
               $('#nivelCategoriaAdicionar').val(1);
           }

        });
    });


    /***Action para seleccionar los iconos desde el listado****/

    $('#iconoCategoria').on('click',function(){
        $('#trigger-icon-select').val('addCategoriaListado');
        $('#modalIconosIionic').modal('show');
    });

    $('#modalIconosIionic i').on('click',function(){

        var trigger=$('#trigger-icon-select').val();

        if(trigger=='addCategoriaListado'){
            $('#iconoCategoria').val($(this).attr('class'));
        }
        else if(trigger=='editCategoria'){
            $('#iconoCategoriaEditar').val($(this).attr('class'));

            $('#iconoContainerCategoriaEditar').addClass($(this).attr('class'));
        }
        else if(trigger=='addCategoria'){
            $('#iconoCategoriaAdicionar').val($(this).attr('class'));

            $('#iconoContainerCategoriaAdicionar').addClass($(this).attr('class'));
        }


        $('#modalIconosIionic').modal('hide');
        //alert($(this).attr('class'));
    });

    /***Action para seleccionar los iconos desde el editar****/

    $('#iconoCategoriaEditar').on('click',function(){
        $('#trigger-icon-select').val('editCategoria');
        $('#modalIconosIionic').modal('show');
    });

    /***Action para seleccionar los iconos desde el adicionar****/

    $('#iconoCategoriaAdicionar').on('click',function(){
        $('#trigger-icon-select').val('addCategoria');
        $('#modalIconosIionic').modal('show');
    });
});


