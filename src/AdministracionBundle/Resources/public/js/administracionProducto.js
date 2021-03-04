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
    $('#volverProducto').on('click',function () {
        var url=$('#urlListar').val();
        window.location.replace(url);

    });

    $('#example1 tbody').on('click',".radioDataTable", function (e) {
        $('#editarProducto').attr('disabled',true);
        $('#eliminarProducto').attr('disabled',true);
        $('#detallesProducto').attr('disabled',true);

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked)
            {
                $('#editarProducto').attr('disabled',false);
                $('#eliminarProducto').attr('disabled',false);
                $('#detallesProducto').attr('disabled',false);
            }

        });

    });

    $('#editarProducto').on('click', function () {

        var flag=false;

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked && flag==false)
            {
                flag=true;
                var url=$('#urlEditar').attr("value");

                $.ajax(url, {
                    type: 'post',
                    dataType: 'json',
                    async:false,
                    data:{
                        "idProducto":this.value,
                        "editarProducto":false
                    }
                }).done(function (response) {

                    var productos=response["producto"];
                    var estados=response["estados"];

                    $('#idProducto').attr('value',productos[0]);
                    $('#nombreEditar').attr('value',productos[1]);
                    $('#descripcionEditar').html(productos[2]);
                    $('#precioEditar').attr('value',productos[3]);
                    $('#cuotasEditar').attr('value',productos[4]);
                    $('#cantidadEditar').attr('value',productos[5]);
                    $('#categoriaEditar').attr('value',productos[6]);
                    $('#usuarioEditar').attr('value',productos[7]);
                    $('#imgEditarProductoContainer').attr('src',$('#urlImagen').attr("value")+productos[9]);



                    for(var i=0;i<estados.length;i++)
                    {
                        if(productos[8]==estados[i][1])
                        {
                            $('#estadoEditar').append('<option value="' + estados[i][0] + '" selected="selected">' + estados[i][1] + '</option>');
                        }
                        else
                            $('#estadoEditar').append('<option value="' + estados[i][0] + '">' + estados[i][1] + '</option>');

                    }

                    $('#modal').click();
                });

//
            }

        });

    });

    $('#btnSubmitProductoEditar').on('click', function () {
        $('#btnSubmitProductoEditar').text("Modificando Producto...");
        var $form=$('#formEditarProducto');
        $.ajax($form.attr('action'),{
            type:'post',
            dataType:'json',
            data:$form.serialize()
        }).done(function(response) {
            $('#btnSubmitProductoEditar').text("Aceptar");
            $('#modalEditarProducto').modal('hide');
            table.DataTable().ajax.reload();

            $('#editarProducto').attr("disabled",true);
            $('#eliminarProducto').attr("disabled",true);
            $('#detallesProducto').attr('disabled',true);

            alertify.success("La accion ha sido realizada satisfactoriamente.");
        })
            .error(function () {
                alertify.error("Lo sentimos se ha producido un error.");
            });

    });

    $('#eliminarProducto').on('click', function () {


        $('#example1 tbody .radioDataTable').each(function (e) {
            var data="";
            if(this.checked)
            {
                data=$('#idProductoEliminar').val()+':'+this.value;
                $('#idProductoEliminar').val(data);
            }

        });
        $('#modalEliminar').click();
    });
    $('#selectAllProducto').on('click',function ()
    {
        var select=this.checked;

        $('#example1 tbody :checkbox').each(function () {

            if(this.checked!=select)
            {
                $(this).click();
            }

        });


    });
    $('#btnSubmitProductoEliminar').on('click', function () {

        $('#modalEliminarProducto').modal('hide');

        var $form=$('#formEliminarProducto');

        $.ajax($form.attr('action'),{
            type:'post',
            dataType:'json',
            data:$form.serialize()
        }).done(function(response) {

            table.DataTable().ajax.reload();
            $('#editarProducto').attr("disabled",true);
            $('#eliminarProducto').attr("disabled",true);
            $('#detallesProducto').attr('disabled',true);

            alertify.success("La accion ha sido realizada satisfactoriamente.");
        })
            .error(function () {
                alertify.error("Lo sentimos se ha producido un error.");
            });

    });
    $('#detallesProducto').on('click', function () {

        var flag=false;

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked && flag==false)
            {
                flag=true;
                var url=$('#urlDetalles').attr("value")+'?idProducto='+this.value;
                window.location.replace(url);
            }

        })


    });



});
