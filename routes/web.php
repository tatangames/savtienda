<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\Login\LoginController;
use App\Http\Controllers\Controles\ControlController;
use App\Http\Controllers\Backend\Roles\RolesController;
use App\Http\Controllers\Backend\Perfil\PerfilController;
use App\Http\Controllers\Backend\Roles\PermisoController;
use App\Http\Controllers\Backend\Configuracion\ConfiguracionController;
use App\Http\Controllers\Backend\Productos\ProductosController;
use App\Http\Controllers\Backend\Configuracion\HistorialController;


// --- LOGIN ---

Route::get('/', [LoginController::class,'index'])->name('login');

Route::post('/admin/login', [LoginController::class, 'login']);
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

// --- CONTROL WEB ---

Route::get('/panel', [ControlController::class,'indexRedireccionamiento'])->name('admin.panel');

// --- ROLES ---

Route::get('/admin/roles/index', [RolesController::class,'index'])->name('admin.roles.index');
Route::get('/admin/roles/tabla', [RolesController::class,'tablaRoles']);
Route::get('/admin/roles/lista/permisos/{id}', [RolesController::class,'vistaPermisos']);
Route::get('/admin/roles/permisos/tabla/{id}', [RolesController::class,'tablaRolesPermisos']);
Route::post('/admin/roles/permiso/borrar', [RolesController::class, 'borrarPermiso']);
Route::post('/admin/roles/permiso/agregar', [RolesController::class, 'agregarPermiso']);
Route::get('/admin/roles/permisos/lista', [RolesController::class,'listaTodosPermisos']);
Route::get('/admin/roles/permisos-todos/tabla', [RolesController::class,'tablaTodosPermisos']);
Route::post('/admin/roles/borrar-global', [RolesController::class, 'borrarRolGlobal']);

// --- PERMISOS A USUARIOS ---

Route::get('/admin/permisos/index', [PermisoController::class,'index'])->name('admin.permisos.index');
Route::get('/admin/permisos/tabla', [PermisoController::class,'tablaUsuarios']);
Route::post('/admin/permisos/nuevo-usuario', [PermisoController::class, 'nuevoUsuario']);
Route::post('/admin/permisos/info-usuario', [PermisoController::class, 'infoUsuario']);
Route::post('/admin/permisos/editar-usuario', [PermisoController::class, 'editarUsuario']);
Route::post('/admin/permisos/nuevo-rol', [PermisoController::class, 'nuevoRol']);
Route::post('/admin/permisos/extra-nuevo', [PermisoController::class, 'nuevoPermisoExtra']);
Route::post('/admin/permisos/extra-borrar', [PermisoController::class, 'borrarPermisoGlobal']);

// --- PERFIL DE USUARIO ---
Route::get('/admin/editar-perfil/index', [PerfilController::class,'indexEditarPerfil'])->name('admin.perfil');
Route::post('/admin/editar-perfil/actualizar', [PerfilController::class, 'editarUsuario']);

// --- SIN PERMISOS VISTA 403 ---
Route::get('sin-permisos', [ControlController::class,'indexSinPermiso'])->name('no.permisos.index');


// DASHBOARD
Route::get('/admin/dashboard/index', [ConfiguracionController::class,'vistaDashboard'])->name('admin.dashboard.index');

// UNIDAD DE MEDIDA
Route::get('/admin/unidadmedida/index', [ConfiguracionController::class,'vistaUnidadMedida'])->name('admin.unidadmedida.index');
Route::get('/admin/unidadmedida/tabla', [ConfiguracionController::class,'tablaUnidadMedida']);
Route::post('/admin/unidadmedida/nuevo', [ConfiguracionController::class,'nuevoUnidadMedida']);
Route::post('/admin/unidadmedida/informacion', [ConfiguracionController::class,'infoUnidadMedida']);
Route::post('/admin/unidadmedida/editar', [ConfiguracionController::class,'actualizarUnidadMedida']);

// PRODUCTOS
Route::get('/admin/productos/index', [ProductosController::class,'vistaProductos'])->name('admin.productos.index');
Route::get('/admin/productos/tabla', [ProductosController::class,'tablaProductos']);
Route::post('/admin/productos/nuevo', [ProductosController::class,'nuevoProductos']);
Route::post('/admin/productos/informacion', [ProductosController::class,'infoProductos']);
Route::post('/admin/productos/editar', [ProductosController::class,'actualizarProductos']);


// ENTRADAS DE PRODUCTOS
Route::get('/admin/entradas/productos/index', [ProductosController::class,'vistaEntradasProductos'])->name('admin.entradas.productos.index');
Route::post('/admin/buscar/producto',  [ProductosController::class,'buscadorProductoGlobal']);
Route::post('/admin/entradas/guardar',  [ProductosController::class,'guardarNuevaEntrada']);





// HISTORIAL - ENTRADAS
Route::get('/admin/historial/entrada/index', [HistorialController::class,'indexHistorialEntradas'])->name('sidebar.historial.entradas');
Route::get('/admin/historial/entrada/tabla', [HistorialController::class,'tablaHistorialEntradas']);
Route::post('/admin/historial/entrada/informacion', [HistorialController::class,'informacionHistorialEntradas']);
Route::post('/admin/historial/entrada/actualizar', [HistorialController::class,'actualizarHistorialEntradas']);


// - Detalle
Route::get('/admin/historial/entradadetalle/index/{id}', [HistorialController::class,'indexHistorialEntradasDetalle']);
Route::get('/admin/historial/entradadetalle/tabla/{id}', [HistorialController::class,'tablaHistorialEntradasDetalle']);
Route::post('/admin/historial/entradadetalle/informacion', [HistorialController::class,'informacionEntradaDetalleHistorialDetalle']);
Route::post('/admin/historial/entradadetalle/actualizar', [HistorialController::class,'actualizarEntradaHistorialDetalle']);


// vista para ingresar nuevo producto al lote existente
Route::get('/admin/historial/nuevoingresoentradadetalle/index/{id}', [HistorialController::class,'indexNuevoIngresoEntradaDetalle']);
Route::post('/admin/registrar/productosextras',  [HistorialController::class,'registrarProductosExtras']);

// BORRAR ENTRADA COMPLETA DE PRODUCTOS -> ELIMINARA SALIDAS SI HUBIERON
Route::post('/admin/historial/entrada/borrarlote', [HistorialController::class, 'historialEntradaBorrarLote']);
Route::post('/admin/historial/entradadetalle/borraritem', [HistorialController::class, 'historialEntradaDetalleBorrarItem']);





