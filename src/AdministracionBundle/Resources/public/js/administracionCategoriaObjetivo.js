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
        $('#editarObjetivo').attr('disabled',true);


        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked)
            {
                $('#editarObjetivo').attr('disabled',false);

            }

        });

    });


    $('#editarCategoriaObjetivo').on('click', function () {

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
                        "idCategoriaObjetivo":this.value,
                        "editarCategoriaObjetivo":false
                    }
                }).done(function (response) {


                    var categoriaObjetivo=response["categoriaObjetivo"];
                    $('#idCategoriaObjetivo').attr('value',categoriaObjetivo[0]);
                    $('#nombreEditarCategoriaObjetivo').attr('value',categoriaObjetivo[1]);

                    $('#modalEditarCategoriaObjetivoA').click();


                });

//
            }

        });

    });

    $('#btnSubmitCategoriaObjetivoEditar').on('click', function () {
        $('#btnSubmitCategoriaObjetivoEditar').text("Modificando Categoria...");
        var $form=$('#formEditarCategoriaObjetivo');
        $.ajax($form.attr('action'),{
            type:'post',
            dataType:'json',
            data:$form.serialize()
        }).done(function(response) {
            $('#btnSubmitCategoriaObjetivoEditar').text("Aceptar");
            $('#modalEditarCategoriaObjetivo').modal('hide');
            table.DataTable().ajax.reload();

            $('#editarCategoriaObjetivo').attr("disabled",true);

            alertify.success("La accion ha sido realizada satisfactoriamente.");
        })
            .error(function () {
                alertify.error("Lo sentimos se ha producido un error.");
            });

    });

    $('#selectAllCategoriaObjetivo').on('click',function ()
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
        $('#editarCategoriaObjetivo').attr('disabled',true);


        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked)
            {
                $('#editarCategoriaObjetivo').attr('disabled',false);

            }

        });

    });

});
