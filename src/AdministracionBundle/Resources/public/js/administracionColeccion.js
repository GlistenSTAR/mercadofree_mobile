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

    $('#example1 tbody').on('click',".radioDataTable", function (e) {
        $('#editarColeccion').attr('disabled',true);
        $('#eliminarColeccion').attr('disabled',true);
        $('#detallesColeccion').attr('disabled',true);

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked)
            {
                $('#editarColeccion').attr('disabled',false);
                $('#eliminarColeccion').attr('disabled',false);
                $('#detallesColeccion').attr('disabled',false);
            }

        });
    });
    $('#adicionarColeccion').on('click',function () {

        $('#imgColeccionContainerAdicionar').attr('src',$('#urlImagen').val()+'no_image.jpg');

        $('#nombreColeccion').val("");

        $('#modalAdicionar').click();
    });


    var btn2 = document.getElementById('uploadColeccionImg'),
        progressBar2 = document.getElementById('progressBarColeccion'),
        progressOuter2 = document.getElementById('progressOuterColeccion'),
        //videoContainer = document.getElementById('videoContainer'),
        msgBox2 = document.getElementById('msgBoxColeccion');

    //Uploader foto de adicionar Coleccion
    var uploaderFotos = new ss.SimpleUpload({
        button: btn2,
        url: $("#img-upload-path").val(),
        name: 'uploadfile2',
        responseType: 'json',
        allowedExtensions: ['jpg', 'png', 'gif','jpeg'],
        maxSize: 536870912, // kilobytes
        hoverClass: 'hover',
        focusClass: 'focus',
        startXHR: function() {
            progressOuter2 = document.getElementById('progressOuterColeccion');
            progressOuter2.style.display = 'block'; // make progress bar visible
            this.setProgressBar( progressBar2 );
        },
        onSubmit: function() {
            btn2.innerHTML = 'Cargando...'; // change button text to "Uploading..."
            this.setProgressBar(progressBar2); // designate as progress bar

        },
        onComplete: function( filename, response ) {

            btn2.innerHTML = 'Cambiar imagen';

            progressOuter2.style.display = 'none'; // hide progress bar when upload is completed

            if ( !response ) {
                msgBox2.innerHTML = 'No se ha podido cargar el fichero';
                return;
            }

            if ( response.success === true )
            {
                var imagesRoute=$('#img-route').val();
                $('#imgColeccion').val(response.file);

                $('#imgColeccionContainerAdicionar').attr('src',imagesRoute+response.file);

            } else {
                if ( response.msg )  {
                    msgBox2.innerHTML = escapeTags( response.msg );

                } else {
                    msgBox2.innerHTML = 'Ha ocurrido un error y no se ha podido cargar la imagen';
                }
            }
        },
        onError: function(filename, response)
        {
            msgBox2.innerHTML = 'Ha ocurrido un error y no se ha podido cargar la imagen';
        }
    });

    $('#btnSubmitColecciondicionar').on('click', function () {

        $('#btnSubmitColecciondicionar').text('Adicionando Coleccion...');

        var $form=$('#formAdicionarColeccion');

        $.ajax($form.attr('action'), {
            type: 'post',
            dataType: 'json',
            data: $form.serialize()
        }).done(function (response) {

            $('#nombreColeccion').val("");

            $('#btnSubmitColecciondicionar').text('Aceptar');

            $('#modalAdicionarColeccion').modal('hide');

            table.DataTable().ajax.reload();

            alertify.success("La accion ha sido realizada satisfactoriamente.");
        })
            .error(function () {
                alertify.error("Lo sentimos se ha producido un error.");
            });

    });



    $('#editarColeccion').on('click', function () {

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
                        "idColeccion": this.value,
                        "editarColeccion": false
                    }
                }).done(function (response) {
                    var imagesRoute=$('#urlImagen').val();
                    var coleccion=response.coleccion;

                    $('#idColeccion').val(coleccion[0]);
                    $('#imgColeccionEditar').val(coleccion[1]);
                    $('#imgColeccionContainerEditar').attr('src',imagesRoute+coleccion[1])
                    $('#nombreColeccionEditar').val(coleccion[2]);
                    $('#modalEditar').click();
                });
            }
        });
    });

    $('#btnSubmitColeccionEditar').on('click',function () {

        $('#btnSubmitColeccionEditar').text("Modificando Coleccion...");
        var $form=$('#formEditarColeccion');
        $.ajax($form.attr('action'),{
            type:'post',
            dataType:'json',
            data:$form.serialize()
        }).done(function(response) {
            $('#btnSubmitColeccionEditar').text("Aceptar");
            $('#modalEditarColeccion').modal('hide');
            table.DataTable().ajax.reload();

            $('#editarColeccion').attr("disabled",true);
            $('#eliminarColeccion').attr("disabled",true);
            $('#detallesColeccion').attr("disabled",true);

            alertify.success("La accion ha sido realizada satisfactoriamente.");
        })
            .error(function () {
                alertify.error("Lo sentimos se ha producido un error.");
            });
    });
    var btn3 = document.getElementById('uploadColeccionImgEditar'),
        progressBar3 = document.getElementById('progressBarColeccionEditar'),
        progressOuter3 = document.getElementById('progressOuterColeccionEditar'),
        //videoContainer = document.getElementById('videoContainer'),
        msgBox3 = document.getElementById('msgBoxColeccionEditar');

    //Uploader foto de editar Coleccion
    var uploaderFotoEditar = new ss.SimpleUpload({
        button: btn3,
        url: $("#img-upload-path").val(),
        name: 'uploadfile3',
        responseType: 'json',
        allowedExtensions: ['jpg', 'png', 'gif','jpeg'],
        maxSize: 536870912, // kilobytes
        hoverClass: 'hover',
        focusClass: 'focus',
        startXHR: function() {
            progressOuter3 = document.getElementById('progressOuterColeccion');
            progressOuter3.style.display = 'block'; // make progress bar visible
            this.setProgressBar( progressBar3 );
        },
        onSubmit: function() {
            btn3.innerHTML = 'Cargando...'; // change button text to "Uploading..."
            this.setProgressBar(progressBar3); // designate as progress bar

        },
        onComplete: function( filename, response ) {

            btn3.innerHTML = 'Cambiar imagen';

            progressOuter2.style.display = 'none'; // hide progress bar when upload is completed

            if ( !response ) {
                msgBox3.innerHTML = 'No se ha podido cargar el fichero';
                return;
            }

            if ( response.success === true )
            {
                var imagesRoute=$('#img-route').val();
                $('#imgColeccionEditar').val(response.file);

                $('#imgColeccionContainerEditar').attr('src',imagesRoute+response.file);

            } else {
                if ( response.msg )  {
                    msgBox3.innerHTML = escapeTags( response.msg );

                } else {
                    msgBox3.innerHTML = 'Ha ocurrido un error y no se ha podido cargar la imagen';
                }
            }
        },
        onError: function(filename, response)
        {
            msgBox3.innerHTML = 'Ha ocurrido un error y no se ha podido cargar la imagen';
        }
    });

    $('#eliminarColeccion').on('click', function () {


        $('#example1 tbody .radioDataTable').each(function (e) {
            var data="";
            if(this.checked)
            {
                data=$('#idColeccionEliminar').val()+':'+this.value;
                $('#idColeccionEliminar').val(data);
            }

        });
        $('#modalEliminar').click();
    });
    $('#btnSubmitColeccionEliminar').on('click',function () {

        $('#modalEliminarColeccion').modal('hide');

        var $form=$('#formEliminarColeccion');

        $.ajax($form.attr('action'),{
            type:'post',
            dataType:'json',
            data:$form.serialize()
        }).done(function(response) {

            table.DataTable().ajax.reload();
            $('#editarColeccion').attr("disabled",true);
            $('#eliminarColeccion').attr("disabled",true);
            $('#detallesColeccion').attr("disabled",true);

            $('#idColeccionEliminar').val("");

            alertify.success("La accion ha sido realizada satisfactoriamente.");
        })
            .error(function () {
                $('#idColeccionEliminar').val("");
                alertify.error("Lo sentimos se ha producido un error.");
            });

    });
    $('#selectAllColeccion').on('click',function ()
    {
        var select=this.checked;

        $('#example1 tbody :checkbox').each(function () {

            if(this.checked!=select)
            {
                $(this).click();
            }

        });


    });

    $('#detallesColeccion').on('click', function () {

        var flag=false;

        $('#example1 tbody .radioDataTable').each(function (e) {

            if(this.checked && flag==false)
            {
                flag=true;
                var url=$('#urlDetalles').attr("value")+'?idColeccion='+this.value;
                window.location.replace(url);
            }

        })


    });

});
