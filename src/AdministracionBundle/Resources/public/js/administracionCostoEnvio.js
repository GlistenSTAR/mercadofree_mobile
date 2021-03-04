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

                alert("error");
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

    function BloquearBotones()
    {
        $('#editarCostoEnvio').attr('disabled', true);
        $('#eliminarCostoEnvio').attr('disabled', true);
    }
    function DesBloquearBotones()
    {
        $('#editarCostoEnvio').attr('disabled', false);
        $('#eliminarCostoEnvio').attr('disabled', false);
    }
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

        });

        return ciudades;
    }

    function BuscarProvinciasAdicionarModal()
    {
        var url=$('#urlAdicionar').val();
        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data:{
                'modoAdicionar':3
            }
        }).done(function (response) {

            var provincias= response.provincias;

            $('#nombreProvinciaAdicionarModal option').remove();

            $('#nombreProvinciaAdicionarModal').append('<option value="" >Seleccione Provincia</option>');

            for(var i=0;i<provincias.length;i++)
            {
                $('#nombreProvinciaAdicionarModal').append('<option value="' + provincias[i][0] + '" >' + provincias[i][1] + '</option>');
            }

        });
    }
    function BuscarCostoEnvio(idCostoEnvio)
    {

        var url=$('#urlEditar').val();

        var costoEnvio=null;
        var ciudades=null;
        var provincias=null;

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idCostoEnvio": idCostoEnvio,
                "editarCostoEnvio": false
            }
        }).done(function (response) {

            costoEnvio= response.costoEnvio;

            ciudades= response.ciudades;

            provincias= response.provincias;

            $('#idCostoEnvioEditar').val(costoEnvio[0]);

            $('#nombreProvinciaEditar option').remove();

            $('#nombreProvinciaEditar').append('<option value="" >Seleccione Provincia</option>');

            for(var i=0;i<provincias.length;i++)
            {
                $('#nombreProvinciaEditar').append('<option value="' + provincias[i][0] + '" >' + provincias[i][1] + '</option>');
            }

            $('#nombreProvinciaEditar').val(costoEnvio[1]);

            $('#nombreCiudadEditar option').remove();

            $('#nombreCiudadEditar').append('<option value="" >Seleccione Ciudad</option>');

            for(var i=0;i<ciudades.length;i++)
            {
                $('#nombreCiudadEditar').append('<option value="' + ciudades[i][0] + '" >' + ciudades[i][1] + '</option>');
            }

            $('#nombreCiudadEditar').val (costoEnvio[3]);

            if (costoEnvio[3]!= "")
            {
                $('#nombreCiudadEditar').val(costoEnvio[3]);
            }

            $('#nombreCostoEnvioEditar').val(costoEnvio[5]);

        });


    }

    function LimpiarAdicionar()
    {

        $('#nombreCiudadAdicionar option').remove();
        $('#nombreCiudadAdicionar').attr('disabled', true);
        $('#nombreProvinciaAdicionar').val("");
        $('#nombreCostoEnvioAdicionar').val("");
    }

    function GuardarCostoEnvio()
    {

        $('#nuevaCostoEnvioAdicionar').val('Adicionando Costo de Envío...');

        var $form=$('#formAdicionarCostoEnvio');

        $.ajax($form.attr('action'), {
            type: 'post',
            dataType: 'json',
            data: $form.serialize()
        }).done(function (response) {
            $('#nuevaCostoEnvioAdicionar').val('Guardar');

            alertify.success("El Costo de Envío ha sido adicionado satisfactoriamente.");
        })
            .error(function () {
                alertify.error("Lo sentimos se ha producido un error.");
            });

    }

    function EditarCostoEnvio()
    {

        $('#btnSubmitCostoEnvioEditar').text('Modificando Costo de Envío...');

        var $form=$('#formEditarCostoEnvio');

        $.ajax($form.attr('action'), {
            type: 'post',
            dataType: 'json',
            data: $form.serialize()
        }).done(function (response) {

            $('#btnSubmitCostoEnvioEditar').text('Aceptar');

            $('#modalEditarCostoEnvio').modal('hide');

            table.DataTable().ajax.reload();

            alertify.success('El Costo de Envío ha sido editado satisfactoriamente.');
        })
            .error(function () {
                alertify.error("Lo sentimos se ha producido un error.");
            });

    }

    $('#nombreProvinciaAdicionar').on('change', function () {


        $('#editarCostoEnvio').attr('disabled', true);
        $('#eliminarCostoEnvio').attr('disabled', true);

        if ($(this).val()!="")
        {
            $('#nuevaCostoEnvioAdicionar').attr('disabled', false);
            $('#nuevaCostoEnvioCancelar').attr('disabled', false);
            $('#nombreCiudadAdicionar').attr('disabled', false);
            BuscarCiudades($(this).val(),'#adicionarCostoEnvioTemplate','#nombreCiudadAdicionar');
        }

    });

    $('#nuevaCostoEnvioAdicionar').on('click', function () {

        var validator=null;

        validator=validatorCostoEnvio;

       if(validator.form())
       {
           GuardarCostoEnvio();

           LimpiarAdicionar();
       }

    });

    $('#nuevaCostoEnvioCancelar').on('click', function () {

        LimpiarAdicionar();

    });

    $('#example1 tbody').on('click',".radioDataTable", function (e) {

         BloquearBotones();

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked)
            {
              DesBloquearBotones();
            }

        });
    });

    $('#selectAllCostoEnvio').on('click',function ()
    {
        var select=this.checked;

        $('#example1 tbody :checkbox').each(function () {

            if(this.checked!=select)
            {
                $(this).click();
            }

        });


    });
    $('#editarCostoEnvio').on('click', function () {

        var flag=false;

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked && flag==false)
            {
                flag=true;

                BuscarCostoEnvio($(this).val());

            }

        })

        $('#aModalEditarCostoEnvio').click();
    });

    $('#nombreProvinciaEditar').on('change', function () {

        if ($(this).val()!="")
        {
            BuscarCiudades($(this).val(),'#containerEditarCostoEnvio','#nombreCiudadEditar');
        }

    });

    $('#btnSubmitCostoEnvioEditar').on('click', function ()
    {
        EditarCostoEnvio();
        $('#editarCostoEnvio').attr("disabled",true);
        $('#eliminarCostoEnvio').attr("disabled",true);
    });

    $('#eliminarCostoEnvio').on('click', function () {


        $('#example1 tbody .radioDataTable').each(function (e) {
            var data="";
            if(this.checked)
            {
                data=$('#idCostoEnvioEliminar').val()+':'+this.value;
                $('#idCostoEnvioEliminar').val(data);
            }

        });
        $('#aModalEliminarCostoEnvio').click();
    });
    $('#btnSubmitCostoEnvioEliminar').on('click',function () {

        $('#modalEliminarCostoEnvio').modal('hide');

        var $form=$('#formEliminarCostoEnvio');

        $.ajax($form.attr('action'),{
            type:'post',
            dataType:'json',
            data:$form.serialize()
        }).done(function(response) {

            table.DataTable().ajax.reload();
            $('#editarCostoEnvio').attr("disabled",true);
            $('#eliminarCostoEnvio').attr("disabled",true);
            $('#idCostoEnvioEliminar').val("");

            alertify.success("La accion ha sido realizada satisfactoriamente.");
        })
            .error(function () {
                alertify.error("Lo sentimos se ha producido un error.");
            });

    });

    $('#nuevoCostoEnvio').on('click', function () {

        $('#nombreProvinciaAdicionarModal option').remove();
        $('#nombreCiudadAdicionarModal option').remove();
        $('#nombreCostoEnvioAdicionarModal').val("");

        BuscarProvinciasAdicionarModal();

        $('#aModalAdicionarCostoEnvio').click();

    });


    $('#nombreProvinciaAdicionarModal').on('change', function () {
        BuscarCiudades($(this).val(),'#AdicionarModalCostoEnvioTemplate','#nombreCiudadAdicionarModal');
        $('#nombreCiudadAdicionarModal').attr('disabled',false);
    });
    $('#btnSubmitCostoEnvioAdicionarModal').on('click',function () {

        $('#modalAdicionarCostoEnvio').modal('hide');

        var $form=$('#formAdicionarModalCostoEnvio');

        $.ajax($form.attr('action'),{
            type:'post',
            dataType:'json',
            data:$form.serialize()
        }).done(function(response) {

            table.DataTable().ajax.reload();
            $('#editarCostoEnvio').attr("disabled",true);
            $('#eliminarCostoEnvio').attr("disabled",true);


            alertify.success("La accion ha sido realizada satisfactoriamente.");
        })
            .error(function () {
                alertify.error("Lo sentimos se ha producido un error.");
            });

    });
});
