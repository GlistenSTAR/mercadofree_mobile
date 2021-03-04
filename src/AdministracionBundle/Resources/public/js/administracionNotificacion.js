$(document).ready(function () {

    var table = $('#example1').dataTable({
        "language": {
            "decimal": "",
            "emptyTable": "No hay datos",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(Filtro de _MAX_ total registros)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron coincidencias",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Próximo",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": Activar orden de columna ascendente",
                "sortDescending": ": Activar orden de columna desendente"
            }
        },
        "bProcessing": true,
        "serverSide": true,
        "ajax": {
            "url": $('#urlListar').value,
            "type": "post",
            "error": function () {

                alert("error");
            }

        },
        'columnDefs': [{
            'targets': 0,
            'searchable': false,
            'orderable': false,
            'className': 'dt-body-center',
            'render': function (data, type, full, meta) {

                return '<input type="checkbox" name="id" value="' + $('<div/>').text(data).html() + '" class="radioDataTable">';
            }
        }]
    });

    $('#adicionarNotificacion').on('click', function ()
    {
        $('#modalAdicionarNotificacionId').click();
    });

    $('#btnSubmitNotificacionAdicionar').on('click', function () {

        $('#btnSubmitNotificacionAdicionar').text('Adicionando Notificación...');

        var $form=$('#formAdicionarNotificacion');

        $.ajax($form.attr('action'), {
            type: 'post',
            dataType: 'json',
            data: $form.serialize()
        }).done(function (response) {

            $('#nombreNotificacion').val("");
            $('#iconoNotificacion').val("");

            $('#btnSubmitColecciondicionar').text('Aceptar');

            $('#modalAdicionarNotificacion').modal('hide');

            table.DataTable().ajax.reload();

            alert('La Notificación ha sido adicionada satisfactoriamente.');

        });

    });

    $('#selectAllNotificacion').on('click',function ()
    {
        var select=this.checked;

        $('#example1 tbody :checkbox').each(function () {

            if(this.checked!=select)
            {
                $(this).click();
            }

        });


    });

    $('#example1 tbody').on('click',".radioDataTable", function (e) {
        $('#editarNotificacion').attr('disabled',true);
        $('#eliminarNotificacion').attr('disabled',true);

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked)
            {
                $('#editarNotificacion').attr('disabled',false);
                $('#eliminarNotificacion').attr('disabled',false);
            }

        });

    });

    $('#eliminarNotificacion').on('click', function () {


        $('#example1 tbody .radioDataTable').each(function (e) {
            var data="";
            if(this.checked)
            {
                data=$('#idNotificacionEliminar').val()+':'+this.value;
                $('#idNotificacionEliminar').val(data);
            }

        });
        $('#modalEliminar').click();
    });

    $('#btnSubmitNotificacionEliminar').on('click', function () {

        var $form=$('#formEliminarNotificacion');

        $.ajax($form.attr('action'), {
            type: 'post',
            dataType: 'json',
            data: $form.serialize()
        }).done(function (response) {

            $('#idNotificacionEliminar').val("");

            $('#modalEliminarNotificacion').modal('hide');

            table.DataTable().ajax.reload();

            alert('La accion ha sido realizada satisfactoriamente.');

        });

    });
    $('#editarNotificacion').on('click', function () {

        var flag=false;

        $('#example1 tbody .radioDataTable').each(function (e) {

            if (this.checked && flag == false) {
                flag = true;
                var url = $('#urlEditar').attr("value");

                $.ajax(url, {
                    type: 'post',
                    dataType: 'json',
                    async: false,
                    data: {
                        "idNotificacion": this.value,
                        "editarNotificacion": false
                    }
                }).done(function (response) {
                    var notificacion=response.notificacion;
                    $('#nombreNotificacionEditar').val(notificacion.titulo);
                    $('#iconoNotificacionEditar').val(notificacion.icono);
                    $('#idNotificacion').val(notificacion.id);
                    $('#mensajeNotificacionEditar').val(notificacion.mensaje);
                    $('#enabledNotificacionEditar').val(notificacion.enabled);
                    if(notificacion.enabled == true){
                        $('#enabledNotificacionEditar').attr('checked','checked');
                    }
                    else{
                        $('#enabledNotificacionEditar').removeAttr('checked');
                    }
                    $('#modalEditar').click();
                });
            }
        });

        $('#btnSubmitNotificacionEditar').on('click', function () {

            $('#btnSubmitNotificacionEditar').text('Modificando Notificación...');
            var $form=$('#formEditarNotificacion');

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                data: $form.serialize()
            }).done(function (response) {

                $('#iconoNotificacionEditar').val("");

                $('#nombreNotificacionEditar').val("");

                $('#modalEditarNotificacion').modal('hide');

                $('#editarNotificacion').attr('disabled',true);

                table.DataTable().ajax.reload();


                alertify.success("La acción ha sido realizada satisfactoriamente.");


            });

        });
    });

    /***Action para seleccionar los iconos desde el listado****/

    $('#iconoNotificacionEditar').on('click',function(){
        $('#modalIconosIionic').modal('show');
    });

    $('#modalIconosIionic i').on('click',function(){

        $('#iconoNotificacionEditar').val($(this).attr('class'));

        $('#iconoContainerNotificacionEditar').addClass($(this).attr('class'));

        $('#modalIconosIionic').modal('hide');
        //alert($(this).attr('class'));
    });



});

