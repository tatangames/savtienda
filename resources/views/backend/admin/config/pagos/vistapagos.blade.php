@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <button type="button" style="font-weight: bold; background-color: #2156af; color: white !important;" onclick="modalAgregar()"
                        class="button button-3d button-rounded button-pill button-small">
                    <i class="fas fa-pencil-alt"></i>
                    Nuevo Pago
                </button>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Pago</li>
                    <li class="breadcrumb-item active">Listado de Pagos</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content" style="margin-top: 15px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Listado de Pagos</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="tablaDatatable">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nueva Medida</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Trabajador Entrega:</label>
                                        <br>
                                        <select class="form-control" id="select-trabajador-nuevo">
                                            @foreach($arrayTrabajador as $sel)
                                                <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha</label>
                                        <input type="date" class="form-control" id="fecha-nuevo" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Monto</label>
                                        <input type="number" class="form-control" id="monto-nuevo" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <input type="text" class="form-control" id="descripcion-nuevo" autocomplete="off">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #2156af; color: white !important;" class="button button-rounded button-pill button-small" onclick="nuevo()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-editar">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" id="id-editar">
                                    </div>

                                    <div class="form-group">
                                        <label>Trabajador Entrega:</label>
                                        <br>
                                        <select class="form-control" id="select-trabajador-editar">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha</label>
                                        <input type="date" class="form-control" id="fecha-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Monto</label>
                                        <input type="number" class="form-control" id="monto-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <input type="text" class="form-control" id="descripcion-editar" autocomplete="off">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="editar()">Actualizar</button>
                </div>
            </div>
        </div>
    </div>

</div>


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function(){


            openLoading()
            var ruta = "{{ URL::to('/admin/pagos/tabla') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('/admin/pagos/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal({backdrop: 'static', keyboard: false})
        }

        function nuevo(){

            var fecha = document.getElementById('fecha-nuevo').value;
            var monto = document.getElementById('monto-nuevo').value;
            var descripcion = document.getElementById('descripcion-nuevo').value;
            var idtrabajador = document.getElementById('select-trabajador-nuevo').value;

            if(fecha === ''){
                toastr.error('Fecha es requerido');
                return;
            }

            var reglaNumeroDiesDecimal = /^([0-9]+\.?[0-9]{0,10})$/;


            if(monto === ''){
                toastr.error('Monto es requerida');
                return;
            }

            if(!monto.match(reglaNumeroDiesDecimal)) {
                toastr.error('Monto debe ser número decimal y no Negativo');
                return;
            }

            if(monto < 0){
                toastr.error('Monto no debe ser negativo o menor a cero');
                return;
            }

            if(monto > 1000000){
                toastr.error('Monto máximo 1 millón');
                return;
            }


            openLoading();
            var formData = new FormData();
            formData.append('fecha', fecha);
            formData.append('monto', monto);
            formData.append('descripcion', descripcion);
            formData.append('idtrabajador', idtrabajador);

            axios.post(url+'/pagos/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Registrado correctamente');
                        $('#modalAgregar').modal('hide');
                        recargar();
                    }
                    else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        function informacion(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url+'/pagos/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal({backdrop: 'static', keyboard: false})

                        $('#id-editar').val(id);
                        $('#fecha-editar').val(response.data.info.fecha);
                        $('#monto-editar').val(response.data.info.monto);
                        $('#descripcion-editar').val(response.data.info.descripcion);

                        document.getElementById("select-trabajador-editar").options.length = 0;

                        // unidad de medida
                        $.each(response.data.arrayTrabajador, function( key, val ){
                            if(response.data.info.id_unidadmedida == val.id){
                                $('#select-trabajador-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-trabajador-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });


                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }


        function editar(){

            var id = document.getElementById('id-editar').value;
            var fecha = document.getElementById('fecha-editar').value;
            var idtrabajador = document.getElementById('select-trabajador-editar').value;
            var monto = document.getElementById('monto-editar').value;
            var descripcion = document.getElementById('descripcion-editar').value;

            if(fecha === ''){
                toastr.error('Fecha es requerido');
                return;
            }

            var reglaNumeroDiesDecimal = /^([0-9]+\.?[0-9]{0,10})$/;


            if(monto === ''){
                toastr.error('Monto es requerida');
                return;
            }

            if(!monto.match(reglaNumeroDiesDecimal)) {
                toastr.error('Monto debe ser número decimal y no Negativo');
                return;
            }

            if(monto < 0){
                toastr.error('Monto no debe ser negativo o menor a cero');
                return;
            }

            if(monto > 1000000){
                toastr.error('Monto máximo 1 millón');
                return;
            }


            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('fecha', fecha);
            formData.append('monto', monto);
            formData.append('descripcion', descripcion);
            formData.append('idtrabajador', idtrabajador);

            axios.post(url+'/pagos/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
                    }
                    else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }


    </script>


@endsection
