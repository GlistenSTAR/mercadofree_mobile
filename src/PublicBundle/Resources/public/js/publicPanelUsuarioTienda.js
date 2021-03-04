$(document).ready(function () {


    function escapeTags( str ) {
        return String( str )
            .replace( /&/g, '&amp;' )
            .replace( /"/g, '&quot;' )
            .replace( /'/g, '&#39;' )
            .replace( /</g, '&lt;' )
            .replace( />/g, '&gt;' );
    }


    /////////////////////////////////////////////////////Subir logo
    //Uploader de imagenes
        var btn2 = document.getElementById('btnUploadImg'),
            progressBar2 = document.getElementById('progressBarImg'),
            progressOuter2 = document.getElementById('progressOuterImg'),
            //videoContainer = document.getElementById('videoContainer'),
            msgBox2 = document.getElementById('msgBox2');

        //Uploader foto de adicionar Coleccion
        var uploaderFotos = new ss.SimpleUpload({
            button: btn2,
            url: $("#img-upload-path").val(),
            name: 'uploadfile2',
            responseType: 'json',
            allowedExtensions: ['jpg', 'png', 'gif', 'jpeg'],
            maxSize: 536870912, // kilobytes
            hoverClass: 'hover',
            focusClass: 'focus',
            startXHR: function () {
                progressOuter2 = document.getElementById('progressOuterImg');
                progressOuter2.style.display = 'block'; // make progress bar visible
                this.setProgressBar(progressBar2);
            },
            onSubmit: function () {
                btn2.innerHTML = 'Cargando...'; // change button text to "Uploading..."
                this.setProgressBar(progressBar2); // designate as progress bar

            },
            onComplete: function (filename, response) {

                btn2.innerHTML = 'Cambiar Imagen';

                progressOuter2.style.display = 'none'; // hide progress bar when upload is completed

                if (!response) {
                    msgBox2.innerHTML = 'No se ha podido cargar el fichero';
                    return;
                }

                if (response.success === true) {
                    var imagesRoute = $('#img-route').val();

                    var selector = "#containerImages";

                    var ruta = escapeTags(response.file);

                    $('#imgLogo').attr('src',imagesRoute+ruta);

                    $('#logo').val(ruta);


                    /*'<li style="margin-bottom:10px;" class="ui-state-default columnImgContainer" data-img="'+response.file+'" >';
                     img+='<img class="img-responsive" src="'+imagesRoute+escapeTags(response.file)+'" >';
                     img+='<input class="imagenes-name" type="hidden" name="imagenes[]" value="'+response.file+'">';
                     img+='<a data-img="'+response.file+'" class="btn btn-danger btnRemoveImage"><i class="fa fa-trash"></i> Eliminar</a>';
                     img+='</li>';*/

                    //$(selector).append($(img));

                } else {
                    if (response.msg) {
                        msgBox2.innerHTML = escapeTags(response.msg);

                    } else {
                        msgBox2.innerHTML = 'Ha ocurrido un error y no se ha podido cargar la imagen';
                    }
                }
            },
            onError: function (filename, response) {
                msgBox2.innerHTML = 'Ha ocurrido un error y no se ha podido cargar la imagen';
            }
        });

////////////////////////////////////////////////////////////////////////////////////Subir banner


    //Uploader de imagenes
    var btn3 = document.getElementById('btnUploadImg2'),
        progressBar3 = document.getElementById('progressBarImg2'),
        progressOuter3 = document.getElementById('progressOuterImg2'),
        //videoContainer = document.getElementById('videoContainer'),
        msgBox3 = document.getElementById('msgBox3');

    //Uploader foto de adicionar Coleccion
    var uploaderBanner = new ss.SimpleUpload({
        button: btn3,
        url: $("#img-upload-path").val(),
        name: 'uploadfile3',
        responseType: 'json',
        allowedExtensions: ['jpg', 'png', 'gif', 'jpeg'],
        maxSize: 536870912, // kilobytes
        hoverClass: 'hover',
        focusClass: 'focus',
        startXHR: function () {
            progressOuter2 = document.getElementById('progressOuterImg2');
            progressOuter2.style.display = 'block'; // make progress bar visible
            this.setProgressBar(progressBar3);
        },
        onSubmit: function () {
            btn3.innerHTML = 'Cargando...'; // change button text to "Uploading..."
            this.setProgressBar(progressBar3); // designate as progress bar

        },
        onComplete: function (filename, response) {

            btn3.innerHTML = 'Cambiar Imagen';

            progressOuter3.style.display = 'none'; // hide progress bar when upload is completed

            if (!response) {
                msgBox3.innerHTML = 'No se ha podido cargar el fichero';
                return;
            }

            if (response.success === true) {
                var imagesRoute = $('#img-route').val();

                var ruta = escapeTags(response.file);

                $('#imgBanner').attr('src',imagesRoute+ruta);

                $('#banner').val(ruta);

            } else {
                if (response.msg) {
                    msgBox3.innerHTML = escapeTags(response.msg);

                } else {
                    msgBox3.innerHTML = 'Ha ocurrido un error y no se ha podido cargar la imagen';
                }
            }
        },
        onError: function (filename, response) {
            msgBox3.innerHTML = 'Ha ocurrido un error y no se ha podido cargar la imagen';
        }
    });

    $('.gd').on('click',function (e) {

        var validator=null;

        validator=validatorTiendaPanelUsuario;

        if(validator.form()) {

            var $form= $('#formPanelUsuarioTienda');

            var id=$(this).attr('id');

            var urlDetalles=$('#urlDetalles').val();

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                async: false,
                data: $form.serialize()
            }).done(function (response) {

                switch (id) {
                    case 'guardar': {
                        alertify.success('Operación satisfactoria.');
                        break;
                    }
                    case 'guardarVer': {
                        alertify.success('Operación satisfactoria.');
                        window.open(urlDetalles, '_blank');
                        break;
                    }

                }


            });
        }

    });


});
