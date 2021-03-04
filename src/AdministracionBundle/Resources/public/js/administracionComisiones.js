const MODE_LOADING = 0;
const MODE_WAITING = 1;
const MODE_LIST    = 2;
const MODE_FORM    = 3;

const dict = {
    en: {
        custom: {
            tipo: {
                required: 'Campo Requerido'
            },
            precio_minimo: {
                required_if: 'Campo Requerido',
                between: 'Valor incorrecto',
            },
            precio_maximo: {
                required_if: 'Campo Requerido',
                min_value: 'Valor incorrecto',
            },
            categoria:{
                required_if: 'Categoría es requerido'
            },
            comision:{
                required: 'Comisión es requerido',
                numeric: 'No Válido',
                between: 'No Válido',
            },
        }
    }
}

// add Validator
Vue.use( VeeValidate, { locale: 'en', dictionary: dict } );

// Register filter currncy
Vue.filter('currency', function (value) {
    return (value != '' && value != null && value != undefined) ?
        '$ ' + parseFloat(value).toFixed(2) :
        '';
});

const app = new Vue({
    el: '#app',
    data: {
        mode: MODE_LOADING,
        list: [],
        categorias: [],
        checkall: false,
        form: {
            id: null,
            tipo: 1,
            precio_minimo: null,
            precio_maximo: null,
            categoria: null,
            comision: 0,
        }
    },
    methods: {
        eliminar: function() {
            let self = this;
            Swal.fire({
                title: 'Esta seguro de eliminar la(s) comisión(es) seleccionada(s)?',
                icon: 'warning',
                showCancelButton: true,
            }).then((result) => {
                if (result.value) {
                    let objEliminar = self.list.filter( e => e.selected );
                    let idEliminar = objEliminar.map( e => e.id );
                    $.ajax(URL_ELIMINAR, {
                        dataType: 'json',
                        type: 'post',
                        data: JSON.stringify({ids: idEliminar}),
                        beforeSend: xhr => self.showLoading(),
                    }).done(function (response) {
                        self.list = self.list.filter( e => !e.selected );
                        alertify.success("La accion ha sido realizada satisfactoriamente.");
                    }).fail( response => {
                        let msj = 'Ocurrió un error en el proceso, intente de nuevo mas tarde';
                        Swal.fire(
                            'Atención!',
                            msj,
                            'error'
                        );
                    }).always((jqXHR, textStatus) => self.hideLoading());
                }
            });
        },
        editar: function() {
            let first = false;
            let i = 0;
            while (!first && i < this.list.length) {
                first = this.list[i].selected;
                if (first) {
                    this.form = this.list[i];
                    this.mode = MODE_FORM;
                }
                i++;
            }
        },
        crear: function() {
            this.clearForm()
            this.mode = MODE_FORM;
        },
        salvar(item) {
            let self = this;
            if ((this.form.tipo == 2) && (this.form.categoria == null || this.form.categoria == 0 || this.form.categoria == '')) {
                this.$validator.errors.add({
                    field: 'categoria',
                    msg: 'Debe escoger una categoría'
                });
                return;
            } 
            this.$validator.validateAll().then( val => {
                if (!val) {
                    Swal.fire(
                        'Atención!',
                        'Datos errados en el formulario, por favor verifique',
                        'warning'
                    )
                    return;
                }
                let url = URL_CREAR;
                if (item.id != null && item.id != undefined && item.id != '') {
                    url = item.url_editar;
                }
                $.ajax(url, {
                    dataType: 'json',
                    type: 'post',
                    data: JSON.stringify(item),
                    beforeSend: xhr => self.showLoading(),
                }).done(function (response) {
                    if (item.id == null) {
                        response.selected = false;
                        self.list.push(response)
                    } else {
                        for (let i = 0; i < self.list.length; i++) {
                            if (self.list[i].id == item.id) {
                                self.list[i] = item
                            }
                        }
                    }
                    self.mode = MODE_LIST;
                    alertify.success("La accion ha sido realizada satisfactoriamente.");
                }).fail( response => {
                    let msj = 'Ocurrió un error en el proceso, intente de nuevo mas tarde';
                    if (response.status == 400) {
                        msj = 'Existen errores en los datos enviado, verifique e intenete de nuevo';
                        for (campo in response.responseJSON) {
                            self.$validator.errors.add({field: campo, msg: response.responseJSON[campo][0]});
                         }
                    }
                    Swal.fire(
                        'Atención!',
                        msj,
                        'error'
                    );
                }).always((jqXHR, textStatus) => self.hideLoading());
            });
        },
        clearForm: function() {
            this.form = {
                id: null,
                tipo: 1,
                precio_minimo: null,
                precio_maximo: null,
                categoria: null,
                comision: 0,
            };
        },
        showLoading: function() {
            $('#loading-model').fadeIn();
        },
        hideLoading: function() {
            $('#loading-model').fadeOut();
        },
        getCategoria: function(id) {
            let response = '';
            for (let i = 0; i < this.categorias.length; i++) {
                if (this.categorias[i].id === id) {
                    response = this.categorias[i].nombre;
                }
            }
            return response;
        },
        cambioTipo: function() {
            if (this.form.tipo == 1) {
                this.form.categoria = null;
            } else if (this.form.tipo == 2)  {
                this.form.precio_minimo = null;
                this.form.precio_maximo = null;
            }
        },
        toogleAll: function() {
            for (let i = 0; i < this.list.length; i++) {
                this.list[i].selected = this.checkall;
            }
        }
    },
    mounted: function() {
        let self = this;
        $(function() {
            $.ajax(URL_CONTROL, {
                dataType: 'json',
                type: 'post',
                beforeSend: xhr => self.showLoading(),
            }).done(function (response) {
                self.list = response.list.map( e => {
                    e.selected = false;
                    return e;
                });
                self.categorias = response.categorias;
            }).fail( response => {
                console.log(response);
                Swal.fire(
                    'Atención!',
                    'Problemas de comunicación con el servidor',
                    'error'
                );
            }).always((jqXHR, textStatus) => self.hideLoading());
            self.mode = MODE_LIST;
        });
    },
    computed: {
        hasSelected: function() {
            let hasSelected = false;
            let i = 0;
            while (!hasSelected && i < this.list.length) {
                hasSelected = this.list[i].selected;
                i++;
            }
            return hasSelected;
        }
    },
    delimiters: ["{[{","}]}"]
});