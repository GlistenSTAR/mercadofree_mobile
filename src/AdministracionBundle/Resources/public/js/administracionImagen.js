$(document).ready(function () {

    //Eliminar Imagen

    $('#imagenesContainer').on('click','.btnRemoveImage',function(e){
        e.preventDefault();

        var urlImagen=$(this).attr('data-img');

        $('#formEliminarImagen #urlImagen').val(urlImagen);

        $('#modalEliminarImagen').modal('show');
    });

    $('#btnSubmitEliminarImagen').on('click',function(e){
        e.preventDefault();

        var form=$('#formEliminarImagen');

        var urlImagen=$('#formEliminarImagen #urlImagen').val();

        //alert(path);

        $.ajax(form.attr('action'),{
            dataType:'json',
            type:'post',
            data:form.serialize()
        }).done(function(response){

            if(response[0]){
                //eliminar la imagen del listado

                $('#imagenesContainer li.columnImgContainer').each(function(){
                    if(urlImagen==$(this).attr('data-img')){
                        $(this).remove();
                    }
                });
            }

            //ocultar el modal

            $('#modalEliminarImagen').modal('hide');
        });
    });

});
