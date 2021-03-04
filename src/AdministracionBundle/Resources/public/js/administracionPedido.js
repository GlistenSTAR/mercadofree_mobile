$(document).ready(function () {

    var pedidosSeleccionados = [];
    
    var search_fromdate = $('#search_fromdate');
    var search_todate = $('#search_todate');
    var search_text = $('#search_text');
    
    var table= $('#example1').dataTable( {
        "language":{
              "decimal": "",
                "emptyTable": "No hay datos",
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
        "searching":false,
        "bProcessing":true,
        "serverSide":true,
        "ajax": {
            "url":$(this).data('url'),
            "data": function(data){
                var from_date = search_fromdate.val();
                var to_date = search_todate.val();
                var text = search_text.val();
                
                data.searchByFromdate = from_date;
                data.searchByTodate = to_date;
                data.searchByText = text;
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
            'className': 'dt-body-center',
            'render': function (data, type, full, meta){
                var value = ' value="' + $('<div/>').text(data).html() + '" ';
                
                return '<input type="checkbox" name="id"' + value + 'class="radioDataTable">';
            }},
            {
            'targets': 4,
            'className': "text-right"
            }
        ],
        'order': [[ 2, "desc" ]]
    });
    
    search_fromdate.datepicker({
        format: "dd/mm/yyyy",
        autoclose: true
    });
    
    search_todate.datepicker({
        format: "dd/mm/yyyy",
        autoclose: true
    });

    search_fromdate.change( function() { table.DataTable().ajax.reload(); } );
    search_todate.change( function() { table.DataTable().ajax.reload(); } );
    search_text.keyup( function() { table.DataTable().ajax.reload(); } );
    
    function BloquearBotones()
    {
        $('#detallesPedido').attr('disabled', true);
        $('.cambiarEstadoPedido').attr('disabled', true);
        $('.liberarFondosPedido').attr('disabled', true);
    }
    
    function DesBloquearBotones()
    {
        
        if(pedidosSeleccionados.length == 1) {
            $('#detallesPedido').attr('disabled', false);
            $('.cambiarEstadoPedido').attr('disabled', false);
        } else {
            $('#detallesPedido').attr('disabled', true);
            $('.cambiarEstadoPedido').attr('disabled', true);
        }
        
        $('.liberarFondosPedido').attr('disabled', false);
    }
    
    $('#example1 tbody').on('click',".radioDataTable", function (e) {

        pedidosSeleccionados = [];
        BloquearBotones();

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked)
            {
                
                pedidosSeleccionados.push($(this).val());
                DesBloquearBotones();
            }
        });
    });
    
    $('#selectAllPedido').on('click',function ()
    {
        var select=this.checked;

        $('#example1 tbody :checkbox').each(function () {

            if(this.checked!=select)
            {
                $(this).click();
            }

        });

    });

    $('#detallesPedido').click(function() {
        var url = $(this).data('url');
        
        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data:{
                'idPedido': pedidosSeleccionados[0]
            }
        }).done(function (response) {
            
            $('#codigoDetalle').text(response.codigo);
            $('#estadoDetalle').text(response.estado);
            $('#compradorDetalle').text(response.comprador);
            $('#fechaDetalle').text(response.fecha);
            $('#vendedorDetalle').text(response.vendedor);
            $('#metodoPagoDetalle').text(response.metodoPago);
            $('.htmlDireccionEnvio').html(response.htmlDireccionEnvio);
            $('.tableBodyProducto').html(response.tableBodyProducto);
            $('.tableBodySeguimiento').html(response.tableBodySeguimiento);
            $('#detalleValoracion').html(response.tableValoracion);

            $('#aModalDetallePedido').click();
        });
    });
    
    $('.cambiarEstadoPedido').click(function() {
        
        var urlCambiarEstado=$(this).data('url');
        
        $.ajax(urlCambiarEstado,{
            'type':'get',
            'dataType':'json',
            'data':{
                'idPedido':pedidosSeleccionados[0]
            }
        }).done(function (response) {
            if(!response[0]){
                alert(response[1]);
            }
            else{
                var estadoActual=response.estadoActual;
                var next=response.next;

                $('#estadoActualPedido').val(estadoActual.nombre);

                $('#idPedidoModalEstados').val(pedidosSeleccionados[0]);

                $('#nuevoEstadoPedido').empty();

                $('#nuevoEstadoPedido').append($('<option value="">Seleccione el nuevo estado</option>'));

                for(var i=0; i<next.length; i++){
                    var opt='<option value="'+next[i].slug+'">'+next[i].nombre+'</option>';

                    $('#nuevoEstadoPedido').append($(opt));
                }
                
                $('#aModalCambiarEstadoPedido').click();
            }
        });
        
    });
    
    $('#btnOkCambiarEstadoPedido').on('click',function () {
        var $form=$('#cambiarEstadoPedidoForm');
        $('#cambiarEstadoPedidoForm div#error').hide();

        if($('#nuevoEstadoPedido').val()==""){
            $('#cambiarEstadoPedidoForm div#error').show();
        }
        else{
            $.ajax($form.attr('action'),{
                type:$form.attr('method'),
                dataType:'json',
                data:$form.serialize()
            }).done(function (response) {
                if(!response[0]){
                    alert(response[1]);
                }
                else{
                    table.DataTable().ajax.reload();
                }

                $('#modalCambiarEstadoPedido').modal('hide');
                
                checkRefreshModalDetal();
            });
        }
    });
    
    $('.liberarFondosPedido').click(function() {
        if(confirm('Esta seguro de liberar los fondos para los pedidos seleccionados?')) {
            var url = $(this).data('url');

            $.ajax(url, {
                type: 'post',
                dataType: 'json',
                async: false,
                data:{
                    'pedidosSeleccionados': pedidosSeleccionados
                }
            }).done(function (response) {
                table.DataTable().ajax.reload();
                
                checkRefreshModalDetal();
            });
        }
    });

});

function checkRefreshModalDetal() {
    if($('#modalDetallePedido').hasClass('in')) {
        $('button.close').click();
        $('#detallesPedido').click();
    }
}
