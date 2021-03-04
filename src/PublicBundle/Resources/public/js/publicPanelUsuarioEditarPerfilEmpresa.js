$(document).ready(function () {

    function BuscarCiudades(idProvincia,container,select)
    {
        //wait(container);
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
            //endWait(container);

        });

        return ciudades;
    }

    function buscarDireccion(id)
    {
        var direccion="";
        //$direccion=$direccion->getCalle()." No. ".$direccion->getNumero()." , entre ".$direccion->getEntreCalle().". ".$direccion->getProvincia()->getNombre().", ".$direccion->getCiudad()->getCiudadNombre().".";/\

        var ciudad=$('#ciudadAdicionar option[value='+$('#ciudadAdicionar').val()+']').text();

        var provincia=$('#provinciaAdicionar option[value='+$('#provinciaAdicionar').val()+']').text();

        var numero=$('#numero').val()!=""?" No. "+$('#numero').val():"";

        var entre=$('#entreCalles').val()!=""?" , entre "+$('#entreCalles').val():"";

        //direccion= $('#calle').val()+numero+entre+". "+provincia+", "+ciudad+".";

        //return direccion;


    }
    function guardarDireccion()
    {
        var validator=null;

        validator=validatorDireccionPanelUsuario;

        var flag=false;

        if(validator.form()) {
            var $form = $('#formDireccionAdicionar');

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                async: false,
                data: $form.serialize()
            }).done(function (response) {

                var tagDir="";

                if($('#venta').prop('checked'))
                {
                    var ciudad=$('#ciudadAdicionar option[value='+$('#ciudadAdicionar').val()+']').text();

                    var provincia=$('#provinciaAdicionar option[value='+$('#provinciaAdicionar').val()+']').text();

                    tagDir='<div class="form-group">'+
                        '<label>'+$('#calleAdicionar').val()+' ('+$('#numeroAdicionar').val()+') '+'</label>'+
                        ' '+ciudad+', '+provincia+' - '+
                        '<span class="label label-default">Venta</span><br/><br/>'+
                        '<a id="m'+response.id+'" class="btn btn-primary btn-sm modificarDirec" href="#modalEditarDireccion" data-toggle="modal"><i class="fa fa-edit"></i> Modificar</a>'+
                        ' <a id="e'+response.id+'" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Eliminar</a>'+
                        '<hr>'+
                        '</div>';

                    $('#direccionVenta div').remove();

                    $('#direccionVenta').append(tagDir);

                    $('#modalAdicionarDireccion').modal('hide');
                }
                else
                {
                    var ciudad=$('#ciudadAdicionar option[value='+$('#ciudadAdicionar').val()+']').text();

                    var provincia=$('#provinciaAdicionar option[value='+$('#provinciaAdicionar').val()+']').text();

                    tagDir='<div class="form-group">'+
                        '<label>'+$('#calleAdicionar').val()+' ('+$('#numeroAdicionar').val()+') '+'</label>'+
                        ' '+ciudad+', '+provincia+' - '+
                        '<span class="label label-default">Compra</span><br/><br/>'+
                        '<a id="m'+response.id+'" class="btn btn-primary btn-sm modificarDirec" href="#modalEditarDireccion" data-toggle="modal"><i class="fa fa-edit"></i> Modificar</a>'+
                        ' <a id="e'+response.id+'" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Eliminar</a>'+
                        '<hr>'+
                        '</div>';



                    $('#direccionCompra').append(tagDir);

                    $('#modalAdicionarDireccion').modal('hide');

                    alertify.success('La dirección ha sido guardada satisfactoriamente.');

                }
            });
        }

        return flag;

    }

    $('#btnSubmitDireccionAdicionar').on('click', function () {

        var flag=guardarDireccion();

        /*if (flag==true)
        {
            $('#modalAdicionarDireccion').modal('hide');

            var direccion=buscarDireccion();

            $('#direccionP').text(direccion);

            $('#addDireccion').hide();
        }*/



    });

    $('#provinciaAdicionar').on('change', function () {

        BuscarCiudades($(this).val(),"#containerDireccion","#ciudadAdicionar");

    });

    //////////////////////////////////////////////////////////////////////////////////////////Modificar Cuenta

    $('#btnSubmitEditarCuentaEmpresa').on('click',function (e) {

        var validator=null;

        validator=validatorPersonalesEmpresaPanelUsuario;

        var flag=false;

        if(validator.form())
        {
            if($('#passwordE2').val()==$('#passwordE').val())
            {
                var $form = $('#formEmpresaCuenta');

                $.ajax($form.attr('action'), {
                    type: 'post',
                    dataType: 'json',
                    async: false,
                    data: $form.serialize()
                }).done(function (response) {

                    $('#emailP').text($('#emailE').val());
                    $('#cuitP').text($('#cuitE').val());
                    $('#telefonoP').text($('#telefonoE').val());
                    $('#razonsocialP').text($('#razonsocialE').val());

                    $('#modalEditarDatosCuentaEmpresa').modal('hide');

                    alertify.success('La cuenta ha sido editada satisfactoriamente.');
                });
            }
            else
            {
                $('#dul2').show();
            }
        }


    });

    //////////////////////////////////////////////////////////////////////////////////////////Eliminar Direccion

    $('#containerDireccion').on('click','.eliminarDirec', function (e) {

        e.preventDefault();

        var id=$(this).attr('id');

        id= id.substring(1,id.length);

        $('#idDireccion').val(id);

        $('#btnConfirmDanger-eliminarDireccion').modal('show');
    });

    $('#btnConfirmDanger-eliminarDireccion').on('click',function () {

        var id=$('#idDireccion').val();

        var url=$('#urlEliminarDireccion').val();

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                'idDireccion':id
            }
        }).done(function (response) {

            $('#e'+id).parent().remove();

            $('#dangerMessage-eliminarDireccion').modal('hide');

            alertify.log('La dirección ha sido eliminada satisfactoriamente.');
        });

    });

    //////////////////////////////////////////////////////////////////////////////////////////Modificar Direccion
    $('#containerDireccion').on('click','.modificarDirec', function (e) {

        e.preventDefault();

        var id=$(this).attr('id');

        id= id.substring(1,id.length);

        $('#idDireccionEditar').val(id);

        var url=$('#urlObtenerDireccionId').val();

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                'idDireccion':id
            }
        }).done(function (response) {

            var ciudades=response.ciudades;

            var direccion=response.direccion;

            $('#ciudadEditar option').remove();//#nombreCiudadAdicionar

            $('#ciudadEditar').append('<option value="" >Seleccione Ciudad</option>');

            for(var i=0;i<ciudades.length;i++)
            {
                $('#ciudadEditar').append('<option value="' + ciudades[i][0] + '" >' + ciudades[i][1] + '</option>');
            }

            $('#ciudadEditar').val(direccion[1]);

            $('#provinciaEditar').val(direccion[2]);

            $('#calleEditar').val(direccion[0]);

            $('#entreCallesEditar').val(direccion[4]);

            $('#numeroEditar').val(direccion[3]);

            $('#datosAdicionalesEditar').val(direccion[5]);

            if (direccion[6]=="1")
            {
                $('#ventaEditar').prop('checked',true)
            }
            else
            {
                $('#ventaEditar').prop('checked',false);
            }



        });


    });

    $('#btnSubmitEditarDireccion').on('click', function (e) {

        var validator=null;

        validator=validatorDireccionPanelUsuarioEditar;

        var flag=false;

        if(validator.form()) {
            var $form = $('#formDireccionEditar');

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                async: false,
                data: $form.serialize()
            }).done(function (response) {


                if($('#ventaEditar').prop('checked')==true)
                {
                    var id=$('#idDireccionEditar').val();

                    var idVenta=$('#direccionVenta a.modificarDirec').attr('id');

                    if(idVenta)
                    {
                        idVenta= idVenta.substring(1,idVenta.length);
                    }

                    if(id!=idVenta )
                    {
                        $('#direccionCompra #e'+response.id).parent().remove();

                        var ciudad=$('#ciudadEditar option[value='+$('#ciudadEditar').val()+']').text();

                        var provincia=$('#provinciaEditar option[value='+$('#provinciaEditar').val()+']').text();

                        var tagDir='<div class="form-group">'+
                            '<label>'+$('#calleEditar').val()+' ('+$('#numeroEditar').val()+') '+'</label>'+
                            ' '+ciudad+', '+provincia+' - '+
                            '<span class="label label-default">Venta</span><br/><br/>'+
                            '<a id="m'+response.id+'" class="btn btn-primary btn-sm modificarDirec" href="#modalEditarDireccion" data-toggle="modal"><i class="fa fa-edit"></i> Modificar</a>'+
                            ' <a id="e'+response.id+'" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Eliminar</a>'+
                            '<hr>'+
                            '</div>';

                        $('#direccionVenta div').remove();

                        $('#direccionVenta').append(tagDir);
                    }
                    else
                    {
                        var ciudad=$('#ciudadEditar option[value='+$('#ciudadEditar').val()+']').text();

                        var provincia=$('#provinciaEditar option[value='+$('#provinciaEditar').val()+']').text();

                        var tagDir='<div class="form-group">'+
                            '<label>'+$('#calleEditar').val()+' ('+$('#numeroEditar').val()+') '+'</label>'+
                            ' '+ciudad+', '+provincia+' - '+
                            '<span class="label label-default">Venta</span><br/><br/>'+
                            '<a id="m'+response.id+'" class="btn btn-primary btn-sm modificarDirec" href="#modalEditarDireccion" data-toggle="modal"><i class="fa fa-edit"></i> Modificar</a>'+
                            ' <a id="e'+response.id+'" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Eliminar</a>'+
                            '<hr>'+
                            '</div>';

                        $('#direccionVenta div').remove();

                        $('#direccionVenta').append(tagDir);
                    }
                }
                else
                {
                    var id=$('#idDireccionEditar').val();

                    var idVenta=$('#direccionVenta a.modificarDirec').attr('id');

                    idVenta= idVenta.substring(1,idVenta.length);

                    if(id!=idVenta)
                    {
                        var $padre= $('#direccionCompra a#m'+id).parent();

                        var ciudad=$('#ciudadEditar option[value='+$('#ciudadEditar').val()+']').text();

                        var provincia=$('#provinciaEditar option[value='+$('#provinciaEditar').val()+']').text();

                        tagDir='<div class="form-group">'+
                            '<label>'+$('#calleEditar').val()+' ('+$('#numeroEditar').val()+') '+'</label>'+
                            ' '+ciudad+', '+provincia+' - '+
                            '<span class="label label-default">Compra</span><br/><br/>'+
                            '<a id="m'+response.id+'" class="btn btn-primary btn-sm modificarDirec" href="#modalEditarDireccion" data-toggle="modal"><i class="fa fa-edit"></i> Modificar</a>'+
                            ' <a id="e'+response.id+'" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Eliminar</a>'+
                            '<hr>'+
                            '</div>';


                        $padre.text("");

                        $padre.append(tagDir);

                        $('#modalAdicionarDireccion').modal('hide');
                    }
                    else
                    {
                        $('#direccionVenta div').remove();

                        var $padre= $('#direccionCompra a#m12').parent();

                        var ciudad=$('#ciudadEditar option[value='+$('#ciudadEditar').val()+']').text();

                        var provincia=$('#provinciaEditar option[value='+$('#provinciaEditar').val()+']').text();

                        tagDir='<div class="form-group">'+
                            '<label>'+$('#calleEditar').val()+' ('+$('#numeroEditar').val()+') '+'</label>'+
                            ' '+ciudad+', '+provincia+' - '+
                            '<span class="label label-default">Compra</span><br/><br/>'+
                            '<a id="m'+response.id+'" class="btn btn-primary btn-sm modificarDirec" href="#modalEditarDireccion" data-toggle="modal"><i class="fa fa-edit"></i> Modificar</a>'+
                            ' <a id="e'+response.id+'" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Eliminar</a>'+
                            '<hr>'+
                            '</div>';


                        $('#direccionCompra').append(tagDir);

                        $('#modalAdicionarDireccion').modal('hide');
                    }
                }

                $('#modalEditarDireccion').modal('hide');

                alertify.success('La dirección ha sido guardada satisfactoriamente.');
            });
        }
    });


});
