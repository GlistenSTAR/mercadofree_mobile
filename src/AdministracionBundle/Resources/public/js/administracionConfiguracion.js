$(document).ready(function () {

   function GuardarInformacion()
   {
       wait('#containerConfiguracion');
       var $form=$('#formConfiguracion');
       $.ajax($form.attr('action'),{
           type:'post',
           dataType:'json',
           data:$form.serialize()
       }).done(function(response) {

           endWait('#containerConfiguracion');

           alertify.success('Se ha actualizado la Configuración de la plataforma.');
       })
           .error(function () {
               alertify.error("Lo sentimos se ha producido un error.");
           });

   }

    function GuardarInformacionConfHome()
    {
        wait('#containerConfiguracion');
        var $form=$('#formConfiguracionHome');
        $.ajax($form.attr('action'),{
            type:'post',
            dataType:'json',
            data:$form.serialize()
        }).done(function(response) {
            endWait('#containerConfiguracion');
            alertify.success('Se ha actualizado la Configuración de los contenidos de la home.');

        })
            .error(function () {
                endWait('#containerConfiguracion');
                alertify.error('Lo sentimos ha sucedido un error, intentelo de nuevo.');
            });

    }




   // Guardar cambios

   $('#submitBtnConfiguracion').on('click',function (e)
   {
       e.preventDefault();

       GuardarInformacion();
   });

    // Guardar cambios de la configuracion de la home

    $('#submitBtnConfiguracionHome').on('click',function (e)
    {
        e.preventDefault();

        GuardarInformacionConfHome();
    });


    //Uploader de imagenes

    /*var btn2 = document.getElementById('btnUploadImg'),
        progressBar2 = document.getElementById('progressBarImg'),
        progressOuter2 = document.getElementById('progressOuterImg'),*/
    //videoContainer = document.getElementById('videoContainer'),
        //msgBox2 = document.getElementById('msgBox2');

    //Uploader foto de adicionar Coleccion
    /*var uploaderFotos = new ss.SimpleUpload({
        button: btn2,
        url: $("#img-upload-path").val(),
        name: 'uploadfile2',
        responseType: 'json',
        allowedExtensions: ['jpg', 'png', 'gif','jpeg'],
        maxSize: 536870912, // kilobytes
        hoverClass: 'hover',
        focusClass: 'focus',
        startXHR: function() {
            progressOuter2 = document.getElementById('progressOuterImg');
            progressOuter2.style.display = 'block'; // make progress bar visible
            this.setProgressBar( progressBar2 );
        },
        onSubmit: function() {
            btn2.innerHTML = 'Cargando...'; // change button text to "Uploading..."
            this.setProgressBar(progressBar2); // designate as progress bar

        },
        onComplete: function( filename, response ) {

            btn2.innerHTML = 'Adicionar Imagen';

            progressOuter2.style.display = 'none'; // hide progress bar when upload is completed

            if ( !response.success ) {
                msgBox2.innerHTML = 'No se ha podido cargar el fichero';
                return;
            }

            if ( response.success)
            {
                var imagesRoute=$('#img-route').val();

                var selector="#imagenesContainer";

                var img='<li style="margin-bottom:10px;" class="ui-state-default columnImgContainer" data-img="'+response.file+'" >';
                img+='<img class="img-responsive" src="'+imagesRoute+escapeTags(response.file)+'" >';
                img+='<input class="imagenes-name" type="hidden" name="imagenes[]" value="'+response.file+'">';
                img+='<a data-img="'+response.file+'" class="btn btn-danger btnRemoveImage"><i class="fa fa-trash"></i> Eliminar</a>';
                img+='</li>';

                //var img='<div style="margin-bottom:10px;" class="col-xs-12 col-sm-6 col-md-4 columnImgContainer" data-img="'+response.file+'" >';
                //img+='<img class="img-responsive" src="'+imagesRoute+escapeTags(response.file)+'" >';
                //img+='<input class="imagenes-name" type="hidden" name="imagenes[]" value="'+response.file+'">';
                //img+='<a style="margin-top:-58px;" data-img="'+response.file+'" class="btn btn-danger btnRemoveImage"><i class="fa fa-trash"></i> Eliminar</a>';
                //img+='</div>';

                $(selector).append($(img));

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
            btn2.innerHTML = 'Adicionar Imagen';

            progressOuter2.style.display = 'none'; // hide progress bar when upload is completed
            console.log(response);
            if ( !response.success ) {

                msgBox2.innerHTML = 'No se ha podido cargar el fichero';
                return;
            }

            if ( response.success)
            {
                var imagesRoute=$('#img-route').val();

                var selector="#imagenesContainer";

                var img='<li style="margin-bottom:10px;" class="ui-state-default columnImgContainer" data-img="'+response.file+'" >';
                img+='<img class="img-responsive" src="'+imagesRoute+escapeTags(response.file)+'" >';
                img+='<input class="imagenes-name" type="hidden" name="imagenes[]" value="'+response.file+'">';
                img+='<a data-img="'+response.file+'" class="btn btn-danger btnRemoveImage"><i class="fa fa-trash"></i> Eliminar</a>';
                img+='</li>';

                //var img='<div style="margin-bottom:10px;" class="col-xs-12 col-sm-6 col-md-4 columnImgContainer" data-img="'+response.file+'" >';
                //img+='<img class="img-responsive" src="'+imagesRoute+escapeTags(response.file)+'" >';
                //img+='<input class="imagenes-name" type="hidden" name="imagenes[]" value="'+response.file+'">';
                //img+='<a style="margin-top:-58px;" data-img="'+response.file+'" class="btn btn-danger btnRemoveImage"><i class="fa fa-trash"></i> Eliminar</a>';
                //img+='</div>';

                $(selector).append($(img));

            } else {
                if ( response.msg )  {
                    msgBox2.innerHTML = escapeTags( response.msg );

                } else {
                    msgBox2.innerHTML = 'Ha ocurrido un error y no se ha podido cargar la imagen';
                }
            }
        }
    });*/

});
