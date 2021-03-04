$(document).ready(function(){
    ////////////////////////////////////////////Validando Fechas




    //handlers para ocultar y mostrar los campos segun el tipo de contribuyente

    $('#tipoContribuyente').on('change',function(){
        if($(this).val()=='3'){
            $('#exclusionIVAQuestion').hide();

            toggleCertExclusionIva(true);
            toggleRegimenIngresosBrutos(false);
        }
        else{
            $('#exclusionIVAQuestion').fadeIn();
            toggleCertExclusionIva(false);
        }

        if($(this).val()==$('#tipoContribuyenteHidden').val())
        {
            if($(this).val()=='3')
            {
                $("#fechaInicio").val($("#fechaInicioHidden").val());
                $("#fechaFin").val($("#fechaFinHidden").val());
                $("#certificadoExclusionIva").val($("#certificadoExclusionHidden").val());
                $("#urlFichero").text($("#certificadoExclusionHidden").val());
                $("#exclusionIVA").val($("#exclusionIVAHidden").val());
                $("#btnUpload").text("Subir Archivo");
            }
            else
            {
                $("#fechaInicio").val($("#fechaInicioHidden").val());
                $("#fechaFin").val($("#fechaFinHidden").val());

                if($("#exclusionIVAHidden").val()=="1")
                {
                    $("#certificadoExclusionIva").val($("#certificadoExclusionHidden").val());
                    $("#urlFichero").text($("#certificadoExclusionHidden").val());

                    $("#exclusionIVA").val($("#exclusionIVAHidden").val());

                    $("#btnUpload").text("Subir Archivo");

                    toggleCertExclusionIva(true);
                    toggleRegimenIngresosBrutos(false);
                }
                else if($("#exclusionIVAHidden").val()=="0")
                {
                    $("#regimenIngresosBrutos").val($("#regimenIngresosBrutosHidden").val());
                    $("#urlFicheroIngresosBrutos").text($("#formInscripcionIngresosBrutosHidden").val());

                    $("#certificadoIngresosBrutos").val($("#formInscripcionIngresosBrutosHidden").val());

                    $("#btnIngresosBrutos").text("Subir Archivo");

                    toggleCertExclusionIva(false);

                    toggleRegimenIngresosBrutos(true);

                }

            }

        }
        else
        {
            $("#fechaInicio").val("");
            $("#fechaFin").val("");
            $("#certificadoExclusionIva").val("");
            $("#urlFichero").text("");
            $("#exclusionIVA").val("");
            $("#btnUpload").text("Subir Archivo");
            $("#regimenIngresosBrutos").val("");
            $("#urlFicheroIngresosBrutos").text("");
            $("#certificadoIngresosBrutos").val("");
            $("#btnIngresosBrutos").text("Subir Archivo");

        }

    });

    $('#exclusionIVA').on('change',function () {
        if($(this).val()=='1'){
            toggleCertExclusionIva(true);
            toggleRegimenIngresosBrutos(false);
        }
        else{
            toggleCertExclusionIva(false);

            toggleRegimenIngresosBrutos(true);
        }

    });

    /////////////////////////////////////////////////////////////Agregar exclusion IVA
    var btn2 = document.getElementById('btnUpload'),
        progressBar2 = document.getElementById('progressBar'),
        progressOuter2 = document.getElementById('progressOuter'),
        //videoContainer = document.getElementById('videoContainer'),
        msgBox2 = document.getElementById('msgBox2');

    //Uploader foto de adicionar Coleccion
    var uploaderFotos = new ss.SimpleUpload({
        button: btn2,
        url: $("#img-upload-path").val(),
        name: 'uploadfile2',
        responseType: 'json',
        allowedExtensions: ['jpg', 'pdf'],
        maxSize: 536870912, // kilobytes
        hoverClass: 'hover',
        focusClass: 'focus',
        startXHR: function () {
            progressOuter2 = document.getElementById('progressOuter');
            progressOuter2.style.display = 'block'; // make progress bar visible
            this.setProgressBar(progressBar2);
        },
        onSubmit: function () {
            btn2.innerHTML = 'Cargando...'; // change button text to "Uploading..."
            this.setProgressBar(progressBar2); // designate as progress bar

        },
        onComplete: function (filename, response) {

            btn2.innerHTML = 'Cambiar Fichero';

            progressOuter2.style.display = 'none'; // hide progress bar when upload is completed

            if (!response) {
                msgBox2.innerHTML = 'No se ha podido cargar el fichero';
                return;
            }

            if (response.success === true) {

                var ruta=response.file;

                $('#certificadoIVA').val(ruta);

                $('#urlFichero').text(filename);

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

    /////////////////////////////////////////////////////////////Agregar exclusion IVA
    var btn2 = document.getElementById('btnIngresosBrutos'),
        progressBar2 = document.getElementById('progressBarIngresosBrutos'),
        progressOuter2 = document.getElementById('progressOuterIngresosBrutos'),
        //videoContainer = document.getElementById('videoContainer'),
        msgBox2 = document.getElementById('msgBox2IngresosBrutos');

    //Uploader foto de adicionar Coleccion
    var uploaderFotos = new ss.SimpleUpload({
        button: btn2,
        url: $("#img-upload-path").val(),
        name: 'uploadfile2',
        responseType: 'json',
        allowedExtensions: ['jpg', 'pdf'],
        maxSize: 536870912, // kilobytes
        hoverClass: 'hover',
        focusClass: 'focus',
        startXHR: function () {
            progressOuter2 = document.getElementById('progressOuterIngresosBrutos');
            progressOuter2.style.display = 'block'; // make progress bar visible
            this.setProgressBar(progressBar2);
        },
        onSubmit: function () {
            btn2.innerHTML = 'Cargando...'; // change button text to "Uploading..."
            this.setProgressBar(progressBar2); // designate as progress bar

        },
        onComplete: function (filename, response) {

            btn2.innerHTML = 'Cambiar Fichero';

            progressOuter2.style.display = 'none'; // hide progress bar when upload is completed

            if (!response) {
                msgBox2.innerHTML = 'No se ha podido cargar el fichero';
                return;
            }

            if (response.success === true) {

                var ruta=response.file;

                $('#certificadoIngresosBrutos').val(ruta);

                $('#urlFicheroIngresosBrutos').text(filename);

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

    //////////////////////////////Enviar Datos
    $('#btnSubmitDatosFiscales').on('click', function (e) {

        e.preventDefault();

        var flag=false;

        if($('#tipoContribuyente').val()=="1" || $('#tipoContribuyente').val()=="2")
        {
            if($('#exclusionIVA').val()=="1")
            {
                if($('#certificadoIVA').val()!="")
                {
                    if ($('#fechaInicio').val() != "" && $('#fechafin').val() != "")
                    {
                        var fi=new Date($('#fechaInicio').val());

                        var ff=new Date($('#fechaFin').val());

                        if(ff.getTime()>fi.getTime())
                        {
                            flag=true;
                        }
                        else
                        {
                            flag=false;
                        }

                    }
                }
            }
            else if($('#exclusionIVA').val()=="0")
            {
               if($('#regimenIngresosBrutos').val()!="")
               {
                   if($('#certificadoIngresosBrutos').val()!="")
                   {
                       flag=true;
                   }
               }
            }


        }
        else if($('#tipoContribuyente').val()=="3")
        {
            if($('#certificadoIVA').val()!="")
            {
                if ($('#fechaInicio').val() != "" && $('#fechafin').val() != "")
                {
                    flag = true;
                }
            }
        }


        if(flag)
        {
            var $form=$('#formDatosFiscales');

            $.ajax($form.attr('action'), {
                type: 'post',
                dataType: 'json',
                async: false,
                data:$form.serialize()
            }).done(function (response) {

                var url=$('#urlDatosEmpresa').attr("value");
                window.location.replace(url);

            });
        }
        else
        {
            $('#alertError').show();
            e.stopImmediatePropagation();
        }

    });


});

