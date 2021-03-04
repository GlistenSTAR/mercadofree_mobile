$(document).ready(function () {

    function buscarProductosJS()
    {
        var response1=null;

        var url=$('#urlBuscarProductosPaginado').val();

        wait('#waitPro');

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "usuarioid": $('#usuarioid').val(),
                "campannaid":$('#campannaid').val(),
                "start":$('#start').val(),
                "total":$('#total').val()

            }
        }).done(function (response) {

            response1=response;
            endWait('#waitPro');

        }).error(function () {
            endWait('#waitPro');
        });

        return response1;
    }

    function crearProductos(response) {

        var productos=response.productos;

        var urlDetalles=$('#urlProductoDetalles').val();

        urlDetalles=urlDetalles.substring(0,urlDetalles.length-1);

        var img=$('#img-route').val();

        for (var i=0;i<productos.length;i++)
        {
            var  tagEstado="";

            if(productos[i][11]==1)
            {
                 tagEstado='<span class="label label-success">Activo</span>'+
                '<p style="margin-top: 5px;text-align: center"><a style="color:red" href="#" class="pausar">Pausar</a></p>'+
                '</td>'+
                '<td>';
            }
            else
            {
                 tagEstado='<span class="label label-warning">Pausado</span>'+
                '<p style="margin-top: 5px;text-align: center"><a style="color:red" href="#" class="activar">Activar</a></p>'+
                '</td>';
            }

            var tagTr='<tr id="tr'+productos[i][0]+'">'+
            '<td><input type="checkbox" class="box" value="'+productos[i][0]+'"></td>'+
            '<td>'+
            '<table>'+
            '<tr>'+
            '<td width="17%" style="padding-right: 5px"><img class="img-responsive" src="'+img+productos[i][8]+'"></td>'+
            '<td>'+
            '<a href="'+urlDetalles+productos[i][0]+'">'+productos[i][1]+'</a>'+
            '<p>$ '+productos[i][2]+'</p>'+
            '</td>'+
            '</tr>'+
            '</table>'+
            '</td>'+
            '<td>'+productos[i][9]+'</td>'+
            '<td>$ '+productos[i][10]+'</td>'+
            '<td id="td'+productos[i][0]+'">'+
            tagEstado+
            '</tr>';

            $('#tableBody').append(tagTr);

        }


    }


    $('#btnConfirmDanger-eliminarAnuncioPublicidad').on('click', function (e) {

        var data="";

        var inversion="";

        $('.box').each(function () {

            if(this.checked)
            {
                data=data+','+this.value;

                var temp=$('#tr'+this.value+' td:nth-child(4)').text();

                inversion=inversion+','+temp.substring(1,temp.length);

                $('#tr'+this.value).remove();

            }
        });

        var url=$('#urlEliminarProductos').val();

        wait('#waitPro');

        if (inversion!="")
        {
            var inversionTotal = $('#inversion').text();

            inversionTotal=inversionTotal.substring(1,inversionTotal.length);

            inversion = inversion.split(',');

            var suma = 0;

            for (var i = 0; i < inversion.length; i++)
            {
                if(inversion[i]!="")
                {
                    suma = parseInt(suma) + parseInt(inversion[i]);
                }

            }

            inversionTotal=parseInt(inversionTotal)-parseInt(suma);

            $('#inversion').text('$ '+inversionTotal);

        }

        if (data!="")
        {
            $.ajax(url, {
                type: 'post',
                dataType: 'json',
                async: false,
                data: {
                    "idsProducto": data
                }
            }).done(function (response) {

                endWait('#waitPro');

                $('#dangerMessage-eliminarAnuncioPublicidad').modal('hide');

                $('#btnEliminar').attr('disabled',true);
                $('#btnPausar').attr('disabled',true);

            }).error(function () {

                endWait('#waitPro');
                $('#dangerMessage-eliminarAnuncioPublicidad').modal('hide');
            });

        }




    });

    $('#btnConfirmWarning-pausarAnuncio').on('click', function (e) {

        var data="";

        $('.box').each(function () {

            if(this.checked)
            {
                data=data+','+this.value;

                $('#td'+this.value).remove();


                $('#tr'+this.value).append('<td id="td'+this.value+'"><span class="label label-warning">Pausado</span>'+
                    '<p style="margin-top: 5px;text-align: center"><a style="color:red" href="#" class="activar">Activar</a></p>'+
                    '</td>');

                $(this).click();
            }
        });

        var url=$('#urlPausarProductos').val();

        wait('#waitPro');

        if (data!="")
        {
            $.ajax(url, {
                type: 'post',
                dataType: 'json',
                async: false,
                data: {
                    "idsProducto": data,
                    "estado":0,
                    "procedencia":1
                }
            }).done(function (response) {

                endWait('#waitPro');

                $('#warningMessage-pausarAnuncio').modal('hide');

            }).error(function () {

                endWait('#waitPro');
                $('#warningMessage-pausarAnuncio').modal('hide');
            });

        }




    });


    $('#tableBody').on('click', '.activar', function (e) {

        e.preventDefault();

        var tr=$(this).parent().parent().parent().attr('id');

        var idP=$('#'+tr+' .box').val();

        //$('a[href="#warningMessage-pausarAnuncio"]').click();

        var url=$('#urlPausarProductos').val();

        wait('#waitPro');

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idsProducto": idP,
                "estado":1
            }
        }).done(function (response) {

            $('#td'+idP).remove();

            if($('#tr'+idP+' td:nth-child(4)').length>0)
            {
                $('#tr'+idP+' td:nth-child(5)').remove();
            }

            $('#tr'+idP).append('<td id="td'+idP+'"><span class="label label-success">Activo</span>'+
                '<p style="margin-top: 5px;text-align: center"><a style="color:red" href="#" class="pausar">Pausar</a></p>'+
                '</td>');


            endWait('#waitPro');

            $('#warningMessage-pausarAnuncio').modal('hide');

        }).error(function () {

            endWait('#waitPro');
            $('#warningMessage-pausarAnuncio').modal('hide');
        });

    });

    $('#tableBody').on('click', '.pausar', function (e) {

        e.preventDefault();

        var tr=$(this).parent().parent().parent().attr('id');

        var idP=$('#'+tr+' .box').val();

        //$('a[href="#warningMessage-pausarAnuncio"]').click();

        var url=$('#urlPausarProductos').val();

        wait('#waitPro');

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idsProducto": idP,
                "estado":0
            }
        }).done(function (response) {

            $('#td'+idP).remove();

            if($('#tr'+idP+' td:nth-child(4)').length>0)
            {
                $('#tr'+idP+' td:nth-child(5)').remove();
            }

            $('#tr'+idP).append('<td id="td'+idP+'"><span class="label label-warning">Pausado</span>'+
                '<p style="margin-top: 5px;text-align: center"><a style="color:red" href="#" class="activar">Activar</a></p>'+
                '</td>');


            endWait('#waitPro');

            $('#warningMessage-pausarAnuncio').modal('hide');

        }).error(function () {

            endWait('#waitPro');
            $('#warningMessage-pausarAnuncio').modal('hide');
        });

    });

    $('#contCampannaEstado').on('click','.estadoCampanna', function (e) {

        e.preventDefault();

        var  url=$('#urlModificarCampanna').val();

        wait('#waitPro');

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "campannaid": $('#campannaid').val(),
                "usuarioid":$('#usuarioid').val()
            }
        }).done(function (response) {

           var estado= response.estado;

           var tagDiv="";

           if (estado==1)
           {
                tagDiv='<div class="col-xs-4" id="estadoCamp">'+
                   '<p>Campaña Activada</p>'+
                   '<a href="#" class="btn btn-warning btn-sm estadoCampanna"><i class="fa fa-play"></i> Pausar</a>'+
                   '</div>';
           }
           else
           {
                tagDiv='<div class="col-xs-4" id="estadoCamp">'+
                   '<p>Campaña Pausada</p>'+
                   '<a href="#" class="btn btn-success btn-sm estadoCampanna"><i class="fa fa-play"></i> Activar</a>'+
                   '</div>';
           }
           $('#estadoCamp').remove();

           $('#primeraColumna').after(tagDiv);

            endWait('#waitPro');

            $('#warningMessage-pausarAnuncio').modal('hide');

        }).error(function () {

            endWait('#waitPro');
            $('#warningMessage-pausarAnuncio').modal('hide');
        });



    });
    $('#btnEliminar').on('click', function (e) {

        var flag = false;

        var cont = 0;

        $('.box').each(function () {

            if (this.checked) {
                flag = true;
                cont++;
            }
        });

        if (flag == false) {
            e.stopImmediatePropagation();
        }
        });

    $('#btnPausar').on('click', function (e) {

        var flag = false;

        var cont = 0;

        $('.box').each(function () {

            if (this.checked) {
                flag = true;
                cont++;
            }
        });

        if (flag == false) {
            e.stopImmediatePropagation();
        }
    });

    $('#tableBody').on('click','.box',function ()
    {
        $('#btnPausar').attr('disabled',true);

        $('#btnEliminar').attr('disabled',true);

        $('#tableBody .box').each(function (e) {

                if(this.checked)
                {
                    $('#btnPausar').attr('disabled',false);

                    $('#btnEliminar').attr('disabled',false);
                }

            });

    });

    $('#btnAgregarProductos').on('click',function () {

        var data="";

        $('.modalBox').each(function ()
        {
            if(this.checked)
            {
                data = data + ',' + this.value;

                $('#tr'+this.value).remove();
            }

        });

        var  url=$('#urlAgregarProductos').val();

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idsProducto": data,
                "campannaid": $('#campannaid').val()
            }
        }).done(function (response) {




            /*$('#tableBody tr').remove();

            var response= buscarProductosJS();*/

            crearProductos(response);

            $('#modalBuscarProductoPublicidad').modal('hide');

        }).error(function () {

            endWait('#waitPro');
            $('#modalBuscarProductoPublicidad').modal('hide');
        });



    });


    $('.cambiarPlan').on('click', function (e) {

        var campannaid=$(this).attr('id');
        var campannaidAnterior=$('#idCampanna').val();

        var  url=$('#urlAgregarProductos').val();

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "campannaid": campannaid,
                'campannaidAnterior':campannaidAnterior
            }
        }).done(function (response) {



        }).error(function () {


        });
    });

    $('#btnMostrarProductos').on('click',function (e) {

        //e.preventDefault();

        var url=$('#urlMostrarProductos').val();

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false
        }).done(function (response) {

            var productos=response.productos;

            var img=$('#img-route').val();

            $('#containerProductosSinCampanna tr').remove();

            if(productos.length>0)
            {
                for (var i=0;i<productos.length;i++)
                {
                    var tagTr='<tr id="'+productos[i][0]+'">'+
                        '<td><input type="checkbox" class="modalBox" value="'+productos[i][0]+'"></td>'+
                        '<td width="17%">'+
                        '<img class="img-responsive" src="'+img+productos[i][8]+'">'+
                        '</td>'+
                        '<td>'+
                        '<p>'+productos[i][1]+'</p>'+
                        '<p>$'+productos[i][2]+'</p>'+
                        '</td>'+
                        '</tr>';

                    $('#containerProductosSinCampanna').append(tagTr);
                }

            }
            else
            {
                $('#containerProductosSinCampanna').append('<tr><td><h4 style="color: red">No existen Productos sin campaña publicitaria.</h4></td></tr>');
            }


            }).error(function () {


        });



    });

    $('.adicionarPlan').on('click', function (e) {

        var idCampanna=$(this).attr('id');


        if($('#'+idCampanna).attr('disabled')=='disabled')
        {
            e.preventDefault();

            e.stopImmediatePropagation();
            }
        else
        {
        var url=$('#urlAdicionarCampanna').val();

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data:{
                'idCampanna':idCampanna
            }
        }).done(function (response) {

        });

        }


        });


});
