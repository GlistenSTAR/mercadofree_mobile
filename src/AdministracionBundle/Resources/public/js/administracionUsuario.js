$(document).ready(function () {

    var table= $('#example1').dataTable( {
        "language":{
            "decimal": "",
            "emptyTable":"No hay datos",
            "info":           "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty":      "Mostrando 0 a 0 de 0 registros",
            "infoFiltered":   "(Filtro de _MAX_ total registros)",
            "infoPostFix":    "",
            "thousands":      ",",
            "lengthMenu":     "Mostrar _MENU_ registros",
            "loadingRecords": "Cargando...",
            "processing":     "Procesando...",
            "search":         "Buscar:",
            "zeroRecords":    "No se encontraron coincidencias",
            "paginate": {
                "first":      "Primero",
                "last":       "Ultimo",
                "next":       "Próximo",
                "previous":   "Anterior"
            },
            "aria": {
                "sortAscending":  ": Activar orden de columna ascendente",
                "sortDescending": ": Activar orden de columna desendente"
            }
        },
        "bProcessing":true,
        "serverSide":true,
        "ajax": {
            "url":$('#urlListar').value,
            "type":"post",
            "error":function(){

                alertify.error("Lo sentimos se ha producido un error.");
            }

        },
        'columnDefs': [{
            'targets': 0,
            'searchable': false,
            'orderable': false,
            'className': 'dt-body-center',
            'render': function (data, type, full, meta){
                return '<input type="checkbox" name="id" value="' + $('<div/>').text(data).html() + '" class="radioDataTable">';
            }
        }]
    });




    $('#example1 tbody').on('click',".radioDataTable", function (e) {

        $('#editarUsuario').attr('disabled',true);
        $('#eliminarUsuario').attr('disabled',true);
        $('#detallesUsuario').attr('disabled',true);

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked)
            {
                $('#editarUsuario').attr('disabled',false);
                $('#eliminarUsuario').attr('disabled',false);
                $('#detallesUsuario').attr('disabled',false);
            }

        });


    });


    $('#eliminarUsuario').on('click', function () {


        $('#example1 tbody .radioDataTable').each(function (e) {

            var data="";
            if(this.checked)
            {
                data=$('#idUsuarioEliminar').val()+':'+this.value;
                $('#idUsuarioEliminar').val(data);


            }

        });
        $('#modalEliminar').click();
    });
    $('#nuevoUsuario').on('click', function () {
        $('#modalAdicionar').click();
    });
    $('#btnSubmitUsuarioEliminar').on('click', function () {

        $('#modalEliminarUsuario').modal('hide');

        var $form=$('#formEliminarUsuario');

        $.ajax($form.attr('action'),{
            type:'post',
            dataType:'json',
            data:$form.serialize()
        }).done(function(response) {

            table.DataTable().ajax.reload();
            $('#editarUsuario').attr("disabled",true);
            $('#eliminarUsuario').attr("disabled",true);
            $('#detallesUsuario').attr('disabled',true);
            $('#idUsuarioEliminar').val("");
            alertify.success("La accion ha sido realizada satisfactoriamente.");
        })
            .error(function () {
                $('#idUsuarioEliminar').val("");
                alertify.error("Lo sentimos se ha producido un error.");
            });

    });

    $('#editarUsuario').on('click', function () {

        var flag=false;

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked && flag==false)
            {
                flag=true;
                var url=$('#urlEditar').attr("value");

                $('#passwordEditarUsuario').attr('value',"");
                $('#fechaRegistroEditarUsuario').attr('value',"");
                $('#passwordEditarUsuario').attr('value',"");
                $('#idUsuarioEditar').attr('value',"");
                $('#rolEditarUsuario option').remove();
                $('#estadoEditarUsuario option').remove();

                $.ajax(url, {
                    type: 'post',
                    dataType: 'json',
                    async:false,
                    data:{
                        "idUsuario":this.value,
                        "editarUsuario":false
                    }
                }).done(function (response) {


                    var usuario=response["usuario"];
                    var roles=response["roles"];
                    var estados=response.estados;

                    $('#emailEditarUsuario').attr('value',usuario[2]);

                    for(var i=0;i<roles.length;i++)
                    {
                        if(usuario[3]==roles[i][1])
                        {
                            $('#rolEditarUsuario').append('<option value="' + roles[i][0] + '" selected="selected">' + roles[i][1] + '</option>');
                        }
                        else
                            $('#rolEditarUsuario').append('<option value="' + roles[i][0] + '">' + roles[i][1] + '</option>');

                    }
                    for(var i=0;i<estados.length;i++)
                    {
                        if(usuario[5]==estados[i][1])
                        {
                            $('#estadoEditarUsuario').append('<option value="' + estados[i][0] + '" selected="selected">' + estados[i][1] + '</option>');
                        }
                        else
                            $('#estadoEditarUsuario').append('<option value="' + estados[i][0] + '">' + estados[i][1] + '</option>');

                    }

                    $('#fechaRegistroEditarUsuario').attr('value',usuario[1]);
                    $('#passwordEditarUsuario').attr('value',usuario[4]);
                    $('#idUsuarioEditar').attr('value',usuario[0]);

                    $('#modal').click();

                });

//
            }

        })

    });

    $('#btnSubmitUsuarioAdicionar').on('click', function () {

        $('#btnSubmitUsuarioAdicionar').text("Adicionando Usuario...");
        var $form = $('#formAdicionarUsuario');

        var dd=null;

        dd=validator1;

        if (dd.form()) {

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                data: $form.serialize()
            }).done(function (response) {
                $('#btnSubmitUsuarioAdicionar').text("Aceptar");

                if (response == true) {
                    $('#modalAdicionarUsuario').modal('hide');
                    table.DataTable().ajax.reload();
                    $('#editarUsuario').attr("disabled", true);
                    $('#eliminarUsuario').attr("disabled", true);
                    $('#detallesUsuario').attr('disabled',true);

                    alertify.success("La accion ha sido realizada satisfactoriamente.");
                }
                else {
                    alertify.error("La direccion de correo entrada ya existe.");
                }


            })
                .error(function () {
                    alertify.error("Lo sentimos se ha producido un error.");
                });
        }

        });

    $('#rolAdicionarUsuario').on('change',function () {

        if($(this).val()==3)
        {
            $('#labelNombreAdicionarUsuario').text('CUIT*');
            $('#labelApellidosAdicionarUsuario').text('Razon Social*');
        }
        else
        {
            $('#labelNombreAdicionarUsuario').text('Nombre*');
            $('#labelApellidosAdicionarUsuario').text('Apellidos*');
        }
        if ($(this).val()==1)
        {
            $('#nombreAdicionarUsuario').siblings().hide();
            $('#apellidoAdicionarUsuario').siblings().hide();
            $('#nombreAdicionarUsuario').hide();
            $('#apellidoAdicionarUsuario').hide();
        }
        else
        {
            $('#nombreAdicionarUsuario').siblings().show();
            $('#apellidoAdicionarUsuario').siblings().show();
            $('#nombreAdicionarUsuario').show();
            $('#apellidoAdicionarUsuario').show();
        }
    });


    $('#btnSubmitUsuarioEditar').on('click', function () {
        $('#btnSubmitUsuarioEditar').text("Modificando Usuario...");
        var $form=$('#formEditarUsuario');

        $.ajax($form.attr('action'),{
            type:'post',
            dataType:'json',
            data:$form.serialize()
        }).done(function(response) {
            $('#btnSubmitUsuarioEditar').text("Aceptar");
            $('#modalEditarUsuario').modal('hide');
            table.DataTable().ajax.reload();

            $('#editarUsuario').attr("disabled",true);
            $('#eliminarUsuario').attr("disabled",true);
            $('#detallesUsuario').attr('disabled',true);

            alertify.success("La accion ha sido realizada satisfactoriamente.");
        })
            .error(function () {
                alertify.error("Lo sentimos se ha producido un error.");
            });

    });

    $('#selectAllUsuario').on('click',function ()
    {
        var select=this.checked;

        $('#example1 tbody :checkbox').each(function () {

            if(this.checked!=select)
            {
                $(this).click();
            }

        });

    });

//////////////////////////////////////////////////////////////////////////////////Agregando usuario desde el template


    $('#nuevoUsuarioAdicionar').on('click', function () {


        var dd=null;

        dd=validator1;

        if (dd.form()) {
            $('#nuevoUsuarioAdicionar').val("Guardando Usuario...");
            var $form = $('#formAdicionarUsuario');

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                data: $form.serialize()
            }).done(function (response) {
                $('#nuevoUsuarioAdicionar').val("Guardar");

                if (response == true) {
                    $('#nombreAdicionarUsuario').val("");
                    $('#apellidoAdicionarUsuario').val("");
                    $('#rolAdicionarUsuario').val("");
                    $('#emailAdicionarUsuario').val("");
                    $('#passwordAdicionarUsuario').val("");

                    alertify.success("La accion ha sido realizada satisfactoriamente.");
                }
                else {
                    alertify.error("La direccion de correo entrada ya existe.");
                }


            })
                .error(function () {
                    alertify.error("Lo sentimos se ha producido un error.");
                });
        }

    });

    $('#nuevoUsuarioCancelar').on('click', function () {
        $('#nombreAdicionarUsuario').val("");
        $('#apellidoAdicionarUsuario').val("");
        $('#rolAdicionarUsuario').val(0);
        $('#emailAdicionarUsuario').val("");
        $('#passwordAdicionarUsuario').val("");

        alertify.error('La accion fue cancelada.');
    });
    ////////////////////////////////////////////////////////////////////////////////

    $('#volverUsuario').on('click',function () {
        var url=$('#urlListar').val();
        window.location.replace(url);

    });

    $('#detallesUsuario').on('click', function () {

        var flag=false;

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked && flag==false)
            {
                flag=true;
                var url=$('#urlDetalles').attr("value")+'?idUsuario='+this.value;
                window.location.replace(url);
            }

        })


    });

    if($('#urlProductos').length>0)
    {
        var url=$('#urlProductos').val();
        var idUsuario=$('#idUsuario').val();
        $('#tableDetalles').dataTable( {
            "language":{
                "decimal": "",
                "emptyTable":"No hay datos",
                "info":           "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty":      "Mostrando 0 a 0 de 0 registros",
                "infoFiltered":   "(Filtro de _MAX_ total registros)",
                "infoPostFix":    "",
                "thousands":      ",",
                "lengthMenu":     "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing":     "Procesando...",
                "search":         "Buscar:",
                "zeroRecords":    "No se encontraron coincidencias",
                "paginate": {
                    "first":      "Primero",
                    "last":       "Ultimo",
                    "next":       "Próximo",
                    "previous":   "Anterior"
                },
                "aria": {
                    "sortAscending":  ": Activar orden de columna ascendente",
                    "sortDescending": ": Activar orden de columna desendente"
                }
            },
            "bProcessing":true,
            "serverSide":true,
            "ajax": {
                "url":url,
                "data":{
                    'idUsuario':idUsuario,
                    'procedencia':'usuario'
                },
                "type":"post",
                "error":function(){

                   alertify.error('Lo sentimos ha ocurrido un error.');
                }

            },

            'columnDefs': [{
                'targets': 0,
                'searchable': false,
                'orderable': false,
                'className': 'dt-body-centerImage',
                'render': function (data, type, full, meta){

                    return '<img name="img" src="' + $('#urlImagen').attr("value")+$('<div/>').text(data).html() + '" class="imgDataTable" style="width: 100px;">';
                }}

            ]
        });
    }
});
