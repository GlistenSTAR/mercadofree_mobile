$(document).ready(function () {

    var desbloquearRequiereGestion = false;
    var desbloquearReintentarPago = false;
    var solicitudesSeleccionadas = [];

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
            "url":$(this).data('url'),
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
                value = ' value="' + $('<div/>').text(data).html() + '" ';
                
                var dataRequiereGestion = "";
                
                if(full['requiere_gestion']) {
                    dataRequiereGestion = ' data-requiere-gestion="1" ';
                }
                
                var dataReintentarPago = "";
                if(full['puede_reintentar_pago']) {
                    dataReintentarPago = ' data-reintentar="1" ';
                }
                
                return '<input type="checkbox" name="id"' + value + dataReintentarPago + dataRequiereGestion + 'class="radioDataTable">';
            }
        }],
        'order': [[ 1, "desc" ]]
    });

    function BloquearBotones()
    {
        $('#aprobarSolicitudRetiro').attr('disabled', true);
        $('#rechazarSolicitudRetiro').attr('disabled', true);
        $('#verSolicitudRetiro').attr('disabled', true);
        $('#reintentarSolicitudRetiro').attr('disabled', true);
    }
    
    function DesBloquearBotones()
    {
        if(desbloquearRequiereGestion) {
            $('#aprobarSolicitudRetiro').attr('disabled', false);
            $('#rechazarSolicitudRetiro').attr('disabled', false);
        }
        
        if(solicitudesSeleccionadas.length == 1) {
            $('#verSolicitudRetiro').attr('disabled', false);
        } else {
            $('#verSolicitudRetiro').attr('disabled', true);
        }
        
        if(desbloquearReintentarPago) {
            $('#reintentarSolicitudRetiro').attr('disabled', false);
        }
    }
    
    function inicializarAlerts() {
        $('#successAlert').hide();
        $('#warningAlert').hide();
    }
    
    $('#aprobarSolicitudRetiro').click(function() {
        inicializarAlerts();
        
        var url = $(this).data('url');
        
        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data:{
                'solicitudesSeleccionadas': solicitudesSeleccionadas,
            }
        }).done(function (response) {

            if(!response.errores) {
                $('#successAlert').text('Las solicitudes fueron correctamente aprobadas');
                $('#successAlert').show();
            } else {
                $('#successAlert').text('Existen solicitudes que no puedieron ser aprobadas');
                $('#warningAlert').show();
            }
            
            recargarTabla();
        });
    });
    
    $('#rechazarSolicitudRetiro').click(function() {
        $('#aModalRechazarSolicitudRetiro').click();
    });
    
    $('#btnSubmitSolicitudRetiroRechazar').click(function() {
        $('#btnSubmitSolicitudRetiroRechazar').text('Rechazando solicitudes de retiro...');

        var $form=$('#formRechazarModalSolicitudRetiro');

        $.ajax($form.attr('action'), {
            type: 'post',
            dataType: 'json',
            data: {
                'solicitudesSeleccionadas': solicitudesSeleccionadas,
                'motivo': $.trim($('#motivoRechazoModal').val())
            }
        }).done(function (response) {

            $('#btnSubmitSolicitudRetiroRechazar').text('Aceptar');
            
            $('#modalRechazarSolicitudRetiro').modal('hide');
            $('#motivoRechazoModal').val('');

            recargarTabla();

            alertify.success('Las solicitudes de retiro han sido rechazadas.');
        })
            .error(function () {
                alertify.error("Lo sentimos se ha producido un error.");
            });
    });
    
    $('#verSolicitudRetiro').click(function() {
        var url = $(this).data('url');
        
        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data:{
                'idSolicitud': solicitudesSeleccionadas[0]
            }
        }).done(function (response) {
            $('#usuarioDetalle').text(response.usuario);
            $('#fechaDetalle').text(response.fecha);
            $('#montoDetalle').text(response.monto);
            $('#cuentaPaypalDetalle').text(response.emailPaypal);
            $('#codigoRespuestaPasarelaDetalle').text(response.codigoRespuestaPasarela);
            $('#mensajeErrorPasarelaDetalle').text(response.mensajeErrorPasarela);
            $('#referenciaPasarelaDetalle').text(response.referenciaPasarela);
            
            $('#aModalDetalleSolicitudRetiro').click();
        });
    });
    
    $('#reintentarSolicitudRetiro').click(function() {
        inicializarAlerts();
        
        var url = $(this).data('url');
        
        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data:{
                'solicitudesSeleccionadas': solicitudesSeleccionadas,
            }
        }).done(function (response) {

            if(!response.errores) {
                $('#successAlert').text('Las solicitudes fueron correctamente re-aprobadas');
                $('#successAlert').show();
            } else {
                $('#successAlert').text('Ha ocurrido un error y no se ha podido reintentar el pago de las solicitudes');
                $('#warningAlert').show();
            }
            
            recargarTabla();
        });
    });
    
    function recargarTabla() {
        table.DataTable().ajax.reload();
        BloquearBotones();
    }

    $('#example1 tbody').on('click',".radioDataTable", function (e) {

        solicitudesSeleccionadas = [];
        BloquearBotones();
        
        desbloquearRequiereGestion = false;
        desbloquearReintentarPago = false;

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked)
            {
                if($(this).data('requiere-gestion')) {
                    desbloquearRequiereGestion = true;
                }
                
                if($(this).data('reintentar')) {
                    desbloquearReintentarPago = true;
                }
                
                solicitudesSeleccionadas.push($(this).val());
                DesBloquearBotones();
            }
        });
    });

    $('#selectAllSolicitudRetiro').on('click',function ()
    {
        var select=this.checked;

        $('#example1 tbody :checkbox').each(function () {

            if(this.checked!=select)
            {
                $(this).click();
            }

        });

    });
});
