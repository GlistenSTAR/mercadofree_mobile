if($('#urlProductosCol').length>0) {
    var url = $('#urlProductosCol').val();
    var idColeccion = $('#idColeccion').val();
    $('#tableDetalles').dataTable({
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
            "url": url,
            "data": {
                'idColeccion': idColeccion,
                'procedencia': 'coleccion'
            },
            "type": "post",
            "error": function () {

                alert("error");
            }

        },

        'columnDefs': [{
            'targets': 0,
            'searchable': false,
            'orderable': false,
            'className': 'dt-body-centerImage',
            'render': function (data, type, full, meta) {

                return '<img name="img" src="' + $('#urlImagen').attr("value") + $('<div/>').text(data).html() + '" class="imgDataTable" style="width: 100px;">';
            }
        }

        ]
    });

    $('#volverColeccion').on('click',function () {
        var url=$('#urlListar').val();
        window.location.replace(url);

    });
}

