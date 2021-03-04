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
    $('#selectAllObjetivo').on('click',function ()
    {
        var select=this.checked;

        $('#example1 tbody :checkbox').each(function () {

            if(this.checked!=select)
            {
                $(this).click();
            }

        });


    });

    $('#editarObjetivo').on('click', function () {

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
                        "idObjetivo":this.value,
                        "editarObjetivo":false
                    }
                }).done(function (response) {

                    $('#visibleObjetivo option').remove();
                    var objetivo=response["objetivo"];

                    $('#idObjetivoEditar').attr('value',objetivo[0]);
                    $('#nombreObjetivo').attr('value',objetivo[1]);
                    $('#iconoObjetivo').attr('value',objetivo[2]);
                    $('#puntosObjetivo').attr('value',objetivo[3]);
                    $('#descripcionObjetivo').attr('value', objetivo[6]);

                    $('#iconoContainerObjetivoEditar').attr('class','');

                    $('#categoriaObjetivo').attr('value',objetivo[5]);

                    $('#visibleObjetivo').append('<option value="1">Sí</option>');
                    $('#visibleObjetivo').append('<option value="0">No</option>');
                    $('#visibleObjetivo').val(objetivo[4]);

                    $('#modalEditar').click();


                });

//
            }

        });

    });

    $('#btnSubmitObjetivoEditar').on('click', function () {
        $('#btnSubmitObjetivoEditar').text("Modificando Objetivo...");
        var $form=$('#formEditarObjetivo');
        $.ajax($form.attr('action'),{
            type:'post',
            dataType:'json',
            data:$form.serialize()
        }).done(function(response) {
            $('#btnSubmitObjetivoEditar').text("Aceptar");
            $('#modalEditarObjetivo').modal('hide');
            table.DataTable().ajax.reload();

            $('#editarObjetivo').attr("disabled",true);



            alertify.success("La accion ha sido realizada satisfactoriamente.");
        })
            .error(function () {
                alertify.error("Lo sentimos se ha producido un error.");
            });

    });

    /***Action para seleccionar los iconos desde el listado****/

    $('#iconoObjetivo').on('click',function(){
        $('#modalIconosIionic').modal('show');
    });

    $('#modalIconosIionic i').on('click',function(){

        $('#iconoObjetivo').val($(this).attr('class'));

        $('#iconoContainerObjetivoEditar').addClass($(this).attr('class'));

        $('#modalIconosIionic').modal('hide');
        //alert($(this).attr('class'));
    });

});
