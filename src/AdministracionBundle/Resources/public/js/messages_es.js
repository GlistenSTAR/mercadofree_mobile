/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: ES (Spanish; Español)
 */
(function($) {
	$.extend($.validator.messages, {
		required: "Campo requerido.",
		remote: "Por favor, rellena este campo.",
		email: "Email no válido.",
		url: "URL no válida.",
		date: "Fecha inválida.",
		dateISO: "Fecha inválida.",
		number: "Número inválido.",
		digits: "Escribe sólo dígitos.",
		creditcard: "Por favor, escribe un número de tarjeta válido.",
		equalTo: "Por favor, escribe el mismo valor de nuevo.",
		extension: "Por favor, escribe un valor con una extensión aceptada.",
		maxlength: $.validator.format("no escribas más de {0} caracteres."),
		minlength: $.validator.format("no escribas menos de {0} caracteres."),
		rangelength: $.validator.format("escribe un valor entre {0} y {1} caracteres."),
		range: $.validator.format("escribe un valor entre {0} y {1}."),
		max: $.validator.format("escribe un valor menor o igual a {0}."),
		min: $.validator.format("escribe un valor mayor o igual a {0}."),
		nifES: "Por favor, escribe un NIF válido.",
		nieES: "Por favor, escribe un NIE válido.",
		cifES: "Por favor, escribe un CIF válido.",
        beforecall: "El valor entrado no es válido"
	});
}(jQuery));
