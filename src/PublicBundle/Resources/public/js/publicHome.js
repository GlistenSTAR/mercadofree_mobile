$(document).ready(function () {

    function obtenerCategoria(idColeccion)
    {
        var colecciones=null;

        var url = $('#urlObtenerColeccion').attr("value");

        $.ajax(url, {
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                "idColeccion": idColeccion
            }
        }).done(function (response) {

            colecciones = response.colecciones;

        });

        return colecciones;
    }


    function construirColecciones(idColeccion)
    {
        $('#coleccionHome div').remove();

        var coleccion=null;
           coleccion =obtenerCategoria(idColeccion);

        var imgColeccion=$('#imgColeccion').val();

        var imgProducto=$('#imgProducto').val();

        var control=coleccion[3].length>4?4:coleccion[3].length;

        var productos1="";

        for(var i=0;i<control;i++)
        {
            var tagProducto='<div class="col-xs-3">'+
                '<div class="white-bg-shadow coleccion-item">'+
                '<a href="#">'+
                '<img src="'+imgProducto+coleccion[3][i][2]+'" class="img-responsive">'+
                '</a>'+
                '</div>'+
                '</div>';

            productos1+=tagProducto;
        }

        var productos2="";
        if (coleccion[3].length>4)
        {
            control=coleccion[3].length>8?9:coleccion[3].length;
            for(var i=5;i<control;i++)
            {
                var tagProducto='<div class="col-xs-3">'+
                    '<div class="white-bg-shadow coleccion-item">'+
                    '<a href="#">'+
                    '<img src="'+imgProducto+coleccion[3][i][2]+'" class="img-responsive">'+
                    '</a>'+
                    '</div>'+
                    '</div>';

                productos2+=tagProducto;
            }
        }

        var tagColeccion='<div class="row">'+
            '<div class="col-sm-4 white-bg-shadow coleccion-img-container" data-sr="enter left">'+
            '<div class="row">'+
            '<div class="col-xs-12">'+
            '<img src="'+imgColeccion+coleccion[2]+'" class="img-responsive" style="margin-top: 15px">'+
            '</div>'+
            '<div class="col-xs-12 featured-product-container">'+
            '<div class="row">'+
            '<div class="col-xs-5">'+
            '<h3>'+ coleccion[1] +'</h3>'+
            '</div>'+
            '<div class="col-xs-7">'        +
            '<a href="#" class="btn btn-rw btn-primary">'+
            'Ver más'+
            '</a>'+
            '</div>'+
            '</div>'+
            '</div>'+
            '</div>'+
            '</div>'+
            '<div class="col-sm-8" data-sr="enter left">'+
            '<div class="row mb10">'+
            productos1+
            '</div>'+
            '<div class="row">'  +
            productos2+
            '</div>'+
            '</div>'+
            '</div>';

        $('#coleccionHome').append(tagColeccion);
    }

    $('#selectColeccion').on('change', function () {

        var idColeccion=$(this).val();

        construirColecciones(idColeccion);
    });

});
