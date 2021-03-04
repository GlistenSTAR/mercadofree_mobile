class PublicFinanzasIndex
{
    init(){
        this.page = 0;
        this.end = false;
        this.urldata = 'finanzas/async/get-data';
        this.getData();
        this.bindEvents();
    }

    bindEvents() {
        this.handlerScroll();
    }

    getData() {
        if (this.end) {
            return
        }
        wait('#wait');
        let self = this
        $.ajax(
            self.urldata,
            {
                type: 'post',
                dataType: 'json',
                async: false,
                data: { page: self.page },
                beforeSend: xhr => $('#loading-model').fadeIn(),
            }).done(function (response) {
                self.end = response.end;
                self.agregarData(response.data)
                endWait('#wait');
            }).error(function () {
                endWait('#wait');
            });
    }

    agregarData(data) {
        for (let i = 0; i < data.length; i++) {
            let row = data[i];
            let tr = `<tr>
                <td>${ row.referencia }</td>
                <td>${ row.fecha }</td>
                <td>$ ${ row.monto.toFixed(2) }</td>
                <td>${ row.tipo }</td>
                <td>${ row.concepto }</td>
                <td>
                    <div class="dropdown mb${ row.id }">
                        <button id="dropdownMenu${ row.id }" class="btn btn-rw btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Opciones <span class="caret"></span>
                        </button>
                        <ul id="ul${ row.id }" class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                            <li role="presentation"><a role="menuitem"  href="${ row.urlDetalle }">Ver detalles</a></li>
                        </ul>
                    </div>
                </td>
            </tr>`;

            $('#TransaccionesListado').append($(tr));
        }        
    }

    handlerScroll() {
        let self = this;
        $(window).scroll(function () {
            let position = $('#lista-movimientos').position();
            let alto = $('#lista-movimientos').height();
            let scrollBottom = $(window).height() + $(window).scrollTop();
            let limite = position.top + alto; 
            if (scrollBottom >=  limite) {
                if (!self.end) {
                    self.page++;
                    self.getData();
                }
            }
        });
    }

}

document.addEventListener('DOMContentLoaded', function ()
{
    let obj = new PublicFinanzasIndex();
    obj.init();
});
