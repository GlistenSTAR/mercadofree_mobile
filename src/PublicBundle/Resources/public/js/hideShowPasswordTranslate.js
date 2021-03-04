/**
 * Traduce el texto del botón de mostrar u ocultar contraseña, según la acción correspondiente
 * @param action
 * @param btnClass
 */
function traducirBotonShowPassword(action,btnClass){
    if(action === 'show'){
        $('.hideShowPassword-wrapper .'+btnClass).text('Ocultar');
    }
    else{
        $('.hideShowPassword-wrapper .'+btnClass).text('Mostrar');
    }
}