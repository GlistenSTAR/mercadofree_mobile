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
            }},{
            'targets': 1,
            'searchable': false,
            'orderable': false,
            'className': 'dt-body-centerImage',
            'render': function (data, type, full, meta){

                return '<img name="img" src="' + $('#urlImagen').attr("value")+$('<div/>').text(data).html() + '" class="imgDataTable" style="width: 100px;">';
            }}

        ]
    });


 $('#editarTienda').on('click', function ()
 {
     $('#estadoEditar option').remove();

     $('#example1 tbody .radioDataTable').each(function (e) {

         if (this.checked)
         {
             var url = $('#urlEditar').attr("value");

             $.ajax(url, {
                 type: 'post',
                 dataType: 'json',
                 async: false,
                 data: {
                     "slug": this.value,
                     "editarTienda": false
                 }
             }).done(function (response) {

                 $('#nombreEditar').attr('value', "");
                 var tienda = response["tienda"];
                 var estados = response.estados;

                 $('#nombreEditar').attr('value', tienda[1]);
                 $('#usuarioEditar').attr('value', tienda[3]);
                 $('#marcaEditar').attr('value', tienda[2]);
                 $('#idTienda').attr('value', tienda[0]);
                 $('#imgEditarTiendaContainer').attr('src', $('#urlImagen').attr("value") + tienda[4]);


                 for(var i=0;i<estados.length;i++)
                 {
                      if(tienda[4]==estados[i].value)
                      {
                        $('#estadoEditar').append('<option value="' + estados[i].key + '" selected="selected">' + estados[i].value + '</option>');
                      }
                      else
                        $('#estadoEditar').append('<option value="' + estados[i].key + '">' + estados[i].value + '</option>');

                 }



                 $('#modal').click();

             });
         }
     });

 });

    $('#selectAllTienda').on('click',function ()
    {
        var select=this.checked;

        $('#example1 tbody :checkbox').each(function () {

            if(this.checked!=select)
            {
                $(this).click();
            }

        });


    });


    $('#btnSubmiTiendaEditar').on('click', function () {
        $('#btnSubmiTiendaEditar').text("Modificando Tienda...");
        var $form=$('#formEditarTienda');
        $.ajax($form.attr('action'),{
            type:'post',
            dataType:'json',
            data:$form.serialize()
        }).done(function(response) {
            $('#btnSubmiTiendaEditar').text("Aceptar");
            $('#modalEditarTienda').modal('hide');
            table.DataTable().ajax.reload();

            $('#editarTienda').attr("disabled",true);
            $('#eliminarTienda').attr("disabled",true);
            $('#detallesTienda').attr('disabled',true);

            alertify.success("La accion ha sido realizada satisfactoriamente.");
        })
            .error(function () {
                alertify.error("Lo sentimos se ha producido un error.");
            });


    });

//

    $('#volverTienda').on('click',function () {
        var url=$('#urlListar').val();
        window.location.replace(url);

    });

    $('#example1 tbody').on('click',".radioDataTable", function (e) {
        $('#editarTienda').attr('disabled',true);
        $('#eliminarTienda').attr('disabled',true);
        $('#detallesTienda').attr('disabled',true);

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked)
            {
                $('#editarTienda').attr('disabled',false);
                $('#eliminarTienda').attr('disabled',false);
                $('#detallesTienda').attr('disabled',false);
            }

        });
    });

    $('#eliminarTienda').on('click', function () {


        $('#example1 tbody .radioDataTable').each(function (e) {
            var data="";
            if(this.checked)
            {
                data=$('#idTiendaEliminar').val()+':'+this.value;
                $('#idTiendaEliminar').val(data);

            }

        });
        $('#modalEliminar').click();
    });
    $('#btnSubmitTiendaEliminar').on('click', function () {

        $('#modalEliminarTienda').modal('hide');

        var $form=$('#formEliminarTienda');

        $.ajax($form.attr('action'),{
            type:'post',
            dataType:'json',
            data:$form.serialize()
        }).done(function(response) {
            $('#idTiendaEliminar').val("");
            table.DataTable().ajax.reload();
            $('#editarTienda').attr("disabled",true);
            $('#eliminarTienda').attr("disabled",true);
            $('#detallesTienda').attr('disabled',true);
            alertify.success("La accion ha sido realizada satisfactoriamente.");
        })
            .error(function () {
                $('#idTiendaEliminar').val("");
                alertify.error("Lo sentimos se ha producido un error.");
            });

    });

    $('#detallesTienda').on('click', function () {

        var flag=false;

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked && flag==false)
            {
                flag=true;
                var data = $('#urlDetalles').attr("value");
                var url_temp = data.split('/');
                url_temp[url_temp.length-1] = $(this).val();
                var url = url_temp.join('/');

                window.location.replace(url);
            }

        })


    });
    if($('#urlProductos').length>0)
    {
        var url=$('#urlProductos').val();
        var idUsuarioTienda=$('#idUsuarioTienda').val();
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
                    'idUsuarioTienda':idUsuarioTienda,
                    'procedencia':'tienda'
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

                    return '<img name="img" src="' + $('#urlImagen').attr("value")+$('<div>').text(data).html() + '" class="imgDataTable" style="width: 100px;">';
                }}

            ]
        });
    }

});



