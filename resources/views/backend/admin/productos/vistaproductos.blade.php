@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }
    .select2-container{
        height: 30px !important;
    }

    .dataTables_wrapper .dataTables_info {
        float: left !important;
        text-align: left;
    }

    .dataTables_wrapper .dataTables_paginate {
        float: left !important;
        text-align: left;
        padding-left: 10px;
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row">
            <h1 style="margin-left: 5px">Productos</h1>

            <button type="button" style="margin-left: 15px" onclick="modalAgregar()" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-square"></i>
                Registrar
            </button>

        </div>
    </section>

    <section class="content" style="margin-top: 15px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Listado de Productos</h3>
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Producto</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">

                            <div class="form-group">
                                <label>Nombre del Producto:</label>
                                <input type="text" class="form-control" autocomplete="off" maxlength="200" id="nombre-nuevo">
                            </div>

                            <div class="form-group col-md-5">
                                <label>Unidad de Medida:</label>
                                <br>
                                <select width="70%"  class="form-control" id="select-unidad-nuevo">
                                    @foreach($arrayUnidades as $sel)
                                        <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Precio:</label>
                                <input type="text" class="form-control col-md-3" autocomplete="off" id="precio-nuevo">
                            </div>


                            <div class="form-group">
                                <label>Código (Opcional):</label>
                                <input type="text" class="form-control col-md-3" autocomplete="off" id="codigo-nuevo" maxlength="100">
                            </div>


                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="nuevo()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal editar -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Material</h4>
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
                                        <label>Nombre del Producto:</label>
                                        <input type="text" class="form-control" autocomplete="off" maxlength="200" id="nombre-editar">
                                    </div>

                                    <div class="form-group col-md-5">
                                        <label>Unidad de Medida:</label>
                                        <br>
                                        <select width="70%"  class="form-control" id="select-unidad-editar">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Precio:</label>
                                        <input type="text" class="form-control col-md-3" autocomplete="off" id="precio-editar">
                                    </div>


                                    <div class="form-group">
                                        <label>Código (Opcional):</label>
                                        <input type="text" class="form-control col-md-3" autocomplete="off" id="codigo-editar" maxlength="100">
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

            var ruta = "{{ URL::to('/admin/productos/tabla') }}";
            $('#tablaDatatable').load(ruta);

            $('#select-unidad-nuevo').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-unidad-editar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/productos/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#select-unidad-nuevo').prop('selectedIndex', 0).change();
            $('#modalAgregar').modal({backdrop: 'static', keyboard: false})
        }

        function nuevo(){

            var nombre = document.getElementById('nombre-nuevo').value;
            var unidad = document.getElementById('select-unidad-nuevo').value;
            var precio = document.getElementById('precio-nuevo').value;
            var codigo = document.getElementById('codigo-nuevo').value;

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            if(unidad === ''){
                toastr.error('Unidad Medida es requerido');
                return
            }

            var reglaNumeroDiesDecimal = /^([0-9]+\.?[0-9]{0,10})$/;


            if(precio === ''){
                toastr.error('Precio es requerida');
                return;
            }

            if(!precio.match(reglaNumeroDiesDecimal)) {
                toastr.error('Precio debe ser número decimal y no Negativo');
                return;
            }

            if(precio < 0){
                toastr.error('Precio no debe ser negativo o menor a cero');
                return;
            }

            if(precio > 1000000){
                toastr.error('Precio máximo 1 millón');
                return;
            }


            openLoading();
            var formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('unidad', unidad);
            formData.append('precio', precio);
            formData.append('codigo', codigo);

            axios.post(url+'/productos/nuevo', formData, {
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

            axios.post(url+'/productos/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal({backdrop: 'static', keyboard: false})

                        $('#id-editar').val(id);
                        $('#nombre-editar').val(response.data.info.nombre);
                        $('#precio-editar').val(response.data.info.precio);
                        $('#codigo-editar').val(response.data.info.codigo);

                        document.getElementById("select-unidad-editar").options.length = 0;

                        // unidad de medida
                        $.each(response.data.arrayUnidad, function( key, val ){
                            if(response.data.info.id_unidadmedida == val.id){
                                $('#select-unidad-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-unidad-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
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
            var nombre = document.getElementById('nombre-editar').value;
            var unidad = document.getElementById('select-unidad-editar').value;
            var precio = document.getElementById('precio-editar').value;
            var codigo = document.getElementById('codigo-editar').value;

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            if(unidad === ''){
                toastr.error('Unidad Medida es requerido');
                return
            }

            var reglaNumeroDiesDecimal = /^([0-9]+\.?[0-9]{0,10})$/;


            if(precio === ''){
                toastr.error('Precio es requerida');
                return;
            }

            if(!precio.match(reglaNumeroDiesDecimal)) {
                toastr.error('Precio debe ser número decimal y no Negativo');
                return;
            }

            if(precio < 0){
                toastr.error('Precio no debe ser negativo o menor a cero');
                return;
            }

            if(precio > 1000000){
                toastr.error('Precio máximo 1 millón');
                return;
            }


            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('unidad', unidad);
            formData.append('precio', precio);
            formData.append('codigo', codigo);

            axios.post(url+'/productos/editar', formData, {
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
