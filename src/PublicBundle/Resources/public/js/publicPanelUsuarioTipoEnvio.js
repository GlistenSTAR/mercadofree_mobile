$(document).ready(function () {

    $('#edv').on('click',function () {

        if($('#edv').prop('checked')==true)
        {
            $('#regiones-envio-container').css('display','inline-block');
        }
        else
        {
            $('#regiones-envio-container').css('display','none');
        }
    });
    $('#costoFijoPaisCheck').on('click',function () {

        if($('#costoFijoPaisCheck').prop('checked')==true)
        {
            $('#costoFijoPais').css('display','inline');

            // $('#gruposCostoPais').css('display','none');
        }
        else
        {
            $('#costoFijoPais').css('display','none');
            // $('#gruposCostoPais').css('display','inline-block');
        }
    });

    function BuscarCiudades(idProvincia,container,select)
    {
        //wait(container);
        var url=$('#urlObtenerCiudades').val();

        var ciudades=null;

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idProvincia": idProvincia
            }
        }).done(function (response) {

            ciudades= response.ciudades;

            $(select+' option').remove();//#nombreCiudadAdicionar

            $(select).append('<option value="" >Seleccione Ciudad</option>');
            for(var i=0;i<ciudades.length;i++)
            {
                $(select).append('<option value="' + ciudades[i][0] + '" >' + ciudades[i][1] + '</option>');
            }
            //endWait(container);

        });

        return ciudades;
    }
    $('#provinciaAdicionar').on('change', function () {

        BuscarCiudades($(this).val(),"#containerDireccion","#ciudadAdicionar");

    });

    $('#costoNumero').on('change',function(){
        var msg="El envío tendrá un costo de $"+$(this).val()+" para:";
        $('#tituloGruposEnvio').html(msg);
    });

    $('#esGratis').on('click',function () {

        if($('#esGratis').prop('checked')==true)
        {
            $('#costoNumero').attr('disabled',true);
            $('#tituloGruposEnvio').html("El envío será gratis para:")
        }
        else {
            $('#costoNumero').attr('disabled',false);
            var costo= $('#costoNumero').val();
            if(costo=="" || costo==null){
                costo=0.00;
            }
            var msg="El envío tendrá un costo de $"+costo+" para:";
            $('#tituloGruposEnvio').html(msg);
        }

    });

    // Action para el boton Adicionar del Modal de Crear Grupo de Costos de Envio

    // $('#adicionarCosto').on('click', function () {
    //
    //    var provincia= $('#provinciaAdicionar').val();
    //
    //     var ciudad= $('#ciudadAdicionar').val();
    //
    //     var costo= $('#costoNumero').val();
    //
    //     var esGratis= $('#esGratis').prop('checked');
    //
    //     var url=$('#urlAgregarCostoEnvio').val();
    //
    //     var flag=false;
    //
    //     if(provincia)
    //     {
    //         if(esGratis)
    //         {
    //             flag=true;
    //         }
    //         else if (costo)
    //         {
    //             flag=true;
    //         }
    //
    //     }
    //
    //
    //     if(flag)
    //     {
    //         // $.ajax(url, {
    //         //     type: 'post',
    //         //     dataType: 'json',
    //         //     async: false,
    //         //     data: {
    //         //         'idCiudad':ciudad,
    //         //         'idProvincia':provincia,
    //         //         'costo':costo,
    //         //         'esGratis':esGratis
    //         //
    //         //     }
    //         // }).done(function (response) {
    //         //
    //         //     var g="<p>"+$('#provinciaAdicionar option:selected').text()+(($('#ciudadAdicionar option:selected').text()!="" && $('#ciudadAdicionar').val()!="" )?" > "+$('#ciudadAdicionar option:selected').text():"")+"</p>";
    //         //
    //         //     $('#gruposC').append(g);
    //         //
    //         //     var v="<p>"+$('#provinciaAdicionar option:selected').text()+(($('#ciudadAdicionar option:selected').text()!="" && $('#ciudadAdicionar').val()!="" )?" > "+$('#ciudadAdicionar option:selected').text():"")+"- <b>"+ ($('#esGratis').prop('checked')==true?"Gratis":"$"+$('#costoNumero').val())+"</b>"+'<a id='+response.id+' style="padding: 0px 7px;margin-left: 10px;" class="btn btn-danger eliminarCosto"><i class="fa fa-trash"></i></a>'+"</p>";
    //         //
    //         //     $('.costoE').append('<section>'+v+'</section>');
    //         //
    //         // });
    //
    //         if(esGratis){
    //             $('#tituloGruposEnvio').html("El envío será gratis para:")
    //         }
    //         else{
    //             var msg="El envío tendrá un costo de $"+costo+" para:";
    //             $('#tituloGruposEnvio').html(msg);
    //         }
    //
    //         var g="<p>"+$('#provinciaAdicionar option:selected').text()+(($('#ciudadAdicionar option:selected').text()!="" && $('#ciudadAdicionar').val()!="" )?" > "+$('#ciudadAdicionar option:selected').text():"")+"</p>";
    //
    //         $('#gruposC').append(g);
    //
    //         var inputs='<input type="hidden" name="grupos[]" value="'+$('#provinciaAdicionar').val()+','+$('#ciudadAdicionar').val()+'">';
    //
    //             $('.costoE').append('<section>'+v+'</section>');
    //
    //             alertify.log('Operación satisfactoria.');
    //
    //
    //         $('#gruposC').append($(inputs));
    //     }
    //     else {
    //         $('#error').show();
    //     }
    // });


    // Funcion para limpiar los campos del modal de adicionar grupo de costos de envio

    function resetModal() {
        $('#costoNumero').val("");
        $('#costoNumero').removeAttrs('disabled');
        $('#esGratis').removeAttrs('checked');
        $('#provinciaAdicionar').val("");
        $('#ciudadAdicionar').val("");
        $('#tituloGruposEnvio').html("");
        $('#gruposC p').each(function () {
           $(this).remove();
        });

        $('#gruposC input').each(function () {
            $(this).remove();
        });
    }

    //Action para guardar en BD la nueva conf de costo de envio creada en el modal

    $('#btnGuardarCambiosConfCostoEnvio').on('click',function (e) {
        e.preventDefault();

        var $form=$('#addConfCostoEnvio');

        $.ajax($form.attr('action'),{
            dataType:'json',
            type:$form.attr('method'),
            data:$form.serialize()
        }).done(function (response) {
            if(response[0]){

                //actualizar la vista con las nuevas conf

                var items=response[1];

                for(var i=0;i<items.length;i++){
                    var item='<section><p>';

                    if(items[i].provincia!=''){
                        item += items[i].provincia;
                    }
                    else{
                        item += 'Todo el país';
                    }

                    if(items[i].ciudad!=""){
                        item+=' > '+items[i].ciudad;
                    }

                    var precio='';

                    if(items[i].gratis){
                        precio=' - <b>GRATIS</b>';
                    }
                    else{
                        precio=' - <b>$'+items[i].costo+'</b>';
                    }

                    item+=precio;

                    var btnEliminar='<a id="'+items[i].id+'" class="btn btn-danger eliminarCosto" style="padding: 0px 7px;margin-left: 10px;">';
                    btnEliminar+='<i class="fa fa-trash"></i></a>';

                    item+=btnEliminar;

                    item+='</p>';

                    item+='<p class="dimensiones-envio" style="font-size: 13px;">';

                    if(items[i].peso!=''){
                        item+='Hasta '+items[i].peso+' Kg';
                    }

                    if(items[i].peso!='' && items[i].ancho!=''){
                        item+=', Ancho: '+items[i].ancho+' cm';
                    }
                    else if(items[i].ancho!=''){
                        item+='Ancho: '+items[i].ancho+' cm';
                    }


                    if((items[i].peso!='' || items[i].ancho!='') && items[i].alto!=''){
                        item+=', Alto: '+items[i].alto+' cm';
                    }
                    else if(items[i].alto!=''){
                        item+='Alto: '+items[i].alto+' cm';
                    }

                    if((items[i].peso!='' || items[i].ancho!='' || items[i].alto!='') && items[i].profundidad!=''){
                        item+=', Profundidad: '+items[i].profundidad+' cm';
                    }
                    else if(items[i].profundidad!=''){
                        item+='Profundidad: '+items[i].profundidad+' cm';
                    }


                    item+='</p>';

                    item+='</section>';

                    console.log(item);

                    $('.costoE').append($(item));
                }

                $('#modalCrearGrupoCostoEnvio').modal('hide');
                resetModal();
            }
            else{
                var msg=response[1];

                $('#addConfCostoEnvio #error').html(msg);

                $('#addConfCostoEnvio #error').removeAttrs('hidden');
            }
        });
    });


    $('a.eliminarCosto','.costoE').on('click', function () {

        var idCosto=$(this).attr('id');

        $(this).parent().parent().remove();

        var url=$('#urlEliminarCostoEnvio').val();

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                'idCosto':idCosto
            }
        }).done(function (response) {

            alertify.log('Operación satisfactoria.');
        });

    });

    $('.guardar').on('click', function (e) {

       e.preventDefault();

       var form=$('#formGrupoCosto');

        $.ajax(form.action, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: form.serialize()
        }).done(function (response) {

            alertify.log('Tu configuración de envío se ha guardado correctamente.');

        });

    });

    //Habilitar o deshabilitar checks de otros tipos de envio si se marca el envio gratis
    $('#envio-gratis').on('click',function () {
       if($(this).prop('checked')){
           $('#envio-mercadofree').attr('disabled','disabled');
           $('#edv').attr('disabled','disabled');
           $('#recogida-domicilio-vendedor').attr('disabled','disabled');

           $('#envio-mercadofree').removeAttrs('checked');
           $('#edv').removeAttrs('checked');
           $('#recogida-domicilio-vendedor').removeAttrs('checked');

           $('#regiones-envio-container').hide();
       }
       else{
           $('#envio-mercadofree').removeAttrs('disabled');
           $('#edv').removeAttrs('disabled');
           $('#recogida-domicilio-vendedor').removeAttrs('disabled');
       }
    });

    //Desmarcar el check de envio a domicilio por el vendedor, en caso que se marque el envio por mercadofree

    $('#envio-mercadofree').on('click',function () {
        if($(this).prop('checked')){
            $('#edv').removeAttrs('checked');

            $('#regiones-envio-container').hide();
        }
    });

});
