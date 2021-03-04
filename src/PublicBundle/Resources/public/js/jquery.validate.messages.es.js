jQuery.extend(jQuery.validator.messages, {
    required: "Este campo es requerido",
    remote: "Please fix this field.",
    email: "Debe entrar una dirección de email válida",
    url: "Debe entrar una URL válida",
    date: "Por favor, entre una fecha válida",
    dateISO: "Please enter a valid date (ISO).",
    number: "Este campo debe ser un número",
    digits: "Este campo solo permite números",
    creditcard: "Please enter a valid credit card number.",
    equalTo: "Por favor, el valor debe ser el mismo",
    accept: "Please enter a value with a valid extension.",
    maxlength: jQuery.validator.format("Please enter no more than {0} characters."),
    minlength: jQuery.validator.format("Please enter at least {0} characters."),
    rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
    range: jQuery.validator.format("Please enter a value between {0} and {1}."),
    max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
    min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")
});