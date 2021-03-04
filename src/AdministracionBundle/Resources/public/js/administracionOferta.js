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
            }}

        ]
    });

    $('#example1 tbody').on('click',".radioDataTable", function (e) {
        $('#bloquearOferta').attr('disabled',true);
        $('#eliminarOferta').attr('disabled',true);
        $('#detallesOferta').attr('disabled',true);

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked)
            {
                $('#bloquearOferta').attr('disabled',false);
                $('#eliminarOferta').attr('disabled',false);
                $('#detallesOferta').attr('disabled',false);
            }

        });

    });

    $('#volverOferta').on('click',function () {

        var url=$('#urlListar').val();
        window.location.replace(url);

    });

    $('#selectAllOferta').on('click',function ()
    {
        var select=this.checked;

        $('#example1 tbody :checkbox').each(function () {

            if(this.checked!=select)
            {
                $(this).click();
            }

        });


    });
    $('#eliminarOferta').on('click', function () {


        $('#example1 tbody .radioDataTable').each(function (e) {

            var data="";
            if(this.checked)
            {
                data=$('#idOfertaEliminar').val()+':'+this.value;
                $('#idOfertaEliminar').val(data);
            }
        });
        $('#modalEliminar').click();
    });
    $('#btnSubmitOfertaEliminar').on('click', function () {

        $('#modalEliminarOferta').modal('hide');

        var $form=$('#formEliminarOferta');

        $.ajax($form.attr('action'),{
            type:'post',
            dataType:'json',
            data:$form.serialize()
        }).done(function(response) {
            $('#idOfertaEliminar').val("");
            table.DataTable().ajax.reload();

            alertify.success("La accion ha sido realizada satisfactoriamente.");
            $('#bloquearOferta').attr('disabled',true);
            $('#eliminarOferta').attr('disabled',true);
            $('#detallesOferta').attr('disabled',true);
        })
            .error(function () {
                $('#idOfertaEliminar').val("");
                alertify.error("Lo sentimos se ha producido un error.");
            });

    });
    $('#bloquearOferta').on('click', function () {

        var flag=false;

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked && flag==false)
            {
                flag=true;
                $('#idOfertaBloquear').val(this.value);
            }
        });

        var urlBloquear=$('#urlBloquear').val();
        $.ajax(urlBloquear,{
            type:'post',
            async:false,
            dataType:'json',
            data:{
                'idOferta':$('#idOfertaBloquear').val(),
                'bloquearOferta':false
            }
        }).done(function(response) {

           var oferta= response.oferta;
            var estados= response.estados;
            $('#nombreOfertaDetalles').val("");
            $('#fechaFinOfertaDetalles').val("");
            $('#descuentoOfertaDetalles').val("");
            $('#fechaInicioOfertaDetalles').val("");
            $('#estadoOfertaDetalles option').remove();

            $('#nombreOfertaDetalles').val(oferta[0]);
            $('#fechaFinOfertaDetalles').val(oferta[4]);
            $('#descuentoOfertaDetalles').val(oferta[1]);
            $('#fechaInicioOfertaDetalles').val(oferta[3]);

            $('#estadoOfertaDetalles').append('<option value="">Seleccione Estado</option>');
            for(var i=0;i<estados.length;i++)
            {
                if(oferta[2]==estados[i][1])
                {
                    $('#estadoOfertaDetalles').append('<option value="' + estados[i][0] + '" selected="selected">' + estados[i][1] + '</option>');
                }
                else
                    $('#estadoOfertaDetalles').append('<option value="' + estados[i][0] + '">' + estados[i][1] + '</option>');

            }
        });
        $('#modalBloquear').click();
    });
    $('#btnSubmitOfertaBloquear').on('click', function () {

        $('#modalBloquearOferta').modal('hide');

        var $form=$('#formBloquearOferta');

        $.ajax($form.attr('action'),{
            type:'post',
            dataType:'json',
            data:$form.serialize()
        }).done(function(response) {
            $('#bloquearOferta').attr('disabled',true);
            $('#eliminarOferta').attr('disabled',true);
            $('#detallesOferta').attr('disabled',true);
            table.DataTable().ajax.reload();
            alertify.success("La accion ha sido realizada satisfactoriamente.");
        })
            .error(function () {
                alertify.error("Lo sentimos se ha producido un error.");
            });

    });
    $('#detallesOferta').on('click', function () {

        var flag=false;

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked && flag==false)
            {
                flag=true;
                var url=$('#urlDetalles').attr("value")+'?idOferta='+this.value;
                window.location.replace(url);
            }

        })


    });
    if($('#urlProductos').length>0)
    {
        var url=$('#urlProductos').val();
        var idOferta=$('#idOferta').val();
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
                    'idOferta':idOferta,
                    'procedencia':'oferta'
                },
                "type":"post",
                "error":function(){

                    alert("error");
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
