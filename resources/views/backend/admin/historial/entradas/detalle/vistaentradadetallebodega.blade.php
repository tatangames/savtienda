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

            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Bodega</li>
                    <li class="breadcrumb-item active">Detalle - Historial Entradas</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Listado De Entradas</h3>
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


    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Producto</h4>
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


                                    <div class="form-group col-md-5">
                                        <label>Producto:</label>
                                        <br>
                                        <select width="70%"  class="form-control" id="select-producto-editar">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Cantidad:</label>
                                        <input type="number" class="form-control col-md-3" autocomplete="off" id="cantidad-editar">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="actualizar()">Actualizar</button>
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
            let id = {{ $id }};
            var ruta = "{{ URL::to('/admin/historial/entradadetalle/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);


            $('#select-producto-editar').select2({
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
            let id = {{ $id }};
            var ruta = "{{ URL::to('/admin/historial/entradadetalle/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }

        function infoBorrar(id){
            Swal.fire({
                title: 'ADVERTENCIA',
                text: "Esto eliminará todo el ingreso de este producto",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Borrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarRegistro(id)
                }
            })
        }

        function borrarRegistro(id){
            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/historial/entradadetalle/borraritem', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Borrado correctamente');
                        recargar();
                    }
                    else {
                        toastr.error('Error al borrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al borrar');
                    closeLoading();
                });
        }


        function infoEditar(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url+'/historial/entradadetalle/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal({backdrop: 'static', keyboard: false})

                        $('#id-editar').val(id);
                        $('#cantidad-editar').val(response.data.info.cantidad);

                        document.getElementById("select-producto-editar").options.length = 0;

                        $.each(response.data.arrayProductos, function( key, val ){
                            if(response.data.info.id_producto == val.id){
                                $('#select-producto-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-producto-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
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



        function actualizar(){
            var id = document.getElementById('id-editar').value;
            var producto = document.getElementById('select-producto-editar').value;
            var cantidad = document.getElementById('cantidad-editar').value;

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(cantidad === ''){
                toastr.error('Cantidad es requerida');
                return;
            }

            if(!cantidad.match(reglaNumeroEntero)) {
                toastr.error('Cantidad debe ser número entero y no Negativo');
                return;
            }

            if(cantidad < 0){
                toastr.error('Cantidad no debe ser menor a cero');
                return;
            }

            if(cantidad > 1000000){
                toastr.error('Precio máximo 1 millón');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('idproducto', producto);
            formData.append('cantidad', cantidad);

            axios.post(url+'/historial/entradadetalle/actualizar', formData, {
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
