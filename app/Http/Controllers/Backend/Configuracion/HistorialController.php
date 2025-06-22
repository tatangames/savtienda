<?php

namespace App\Http\Controllers\Backend\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\EntregaProductos;
use App\Models\EntregaProductosDetalle;
use App\Models\Productos;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HistorialController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }


    public function indexHistorialEntradas()
    {
        return view('backend.admin.historial.entradas.vistahistorialentrada');
    }

    public function tablaHistorialEntradas()
    {
        $listado = EntregaProductos::orderBy('fecha', 'ASC')->get();

        foreach ($listado as $fila) {
            $fila->fechaFormat = date("d-m-Y", strtotime($fila->fecha));
        }

        return view('backend.admin.historial.entradas.tablahistorialentrada', compact('listado'));
    }


    public function informacionHistorialEntradas(Request $request)
    {
        $regla = array(
            'id' => 'required', //tabla: entradas
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        // VERIFICAR QUE EXISTA LA ENTRADA
        if($info = EntregaProductos::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 2];
        }
    }


    public function actualizarHistorialEntradas(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'fecha' => 'required',
        );

        // descripcion

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        try {

            EntregaProductos::where('id', $request->id)->update([
                'fecha' => $request->fecha,
                'descripcion' => $request->descripcion,
            ]);

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info("error: " . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }



    public function historialEntradaBorrarLote(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        // VERIFICAR QUE EXISTA LA ENTRADA
        if(EntregaProductos::where('id', $request->id)->first()){

            DB::beginTransaction();

            try {
                // BORRAR
                EntregaProductosDetalle::where('id_entregaproductos', $request->id)->delete();
                EntregaProductos::where('id', $request->id)->delete();

                DB::commit();
                return ['success' => 1];

            } catch (\Throwable $e) {
                Log::info('ee ' . $e);
                DB::rollback();
                return ['success' => 99];
            }
        }else{
            return ['success' => 99];
        }
    }



    public function indexHistorialEntradasDetalle($id)
    {
        $info = EntregaProductos::where('id', $id)->first();

        return view('backend.admin.historial.entradas.detalle.vistaentradadetallebodega', compact('id', 'info'));
    }

    public function tablaHistorialEntradasDetalle($id){


        $listado = EntregaProductosDetalle::where('id_entregaproductos', $id)->get();

        foreach ($listado as $fila) {

            $infoProducto = Productos::where('id', $fila->id_producto)->first();
            $infoUnidad = UnidadMedida::where('id', $infoProducto->id_unidadmedida)->first();

            $fila->nombreProducto = $infoProducto->nombre;
            $fila->nombreUnidad = $infoUnidad->nombre;

            $fila->precioFormat = '$' . number_format((float)$fila->precio_venta, 2, '.', ',');
            $fila->subtotalFormat = '$' . number_format((float)$fila->subtotal, 2, '.', ',');
        }

        return view('backend.admin.historial.entradas.detalle.tablaentradadetallebodega', compact('listado'));
    }


    public function informacionEntradaDetalleHistorialDetalle(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = EntregaProductosDetalle::where('id', $request->id)->first()){

            $arrayProductos = Productos::orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'info' => $lista,
                'arrayProductos' => $arrayProductos];
        }else{
            return ['success' => 2];
        }
    }


    public function actualizarEntradaHistorialDetalle(Request $request)
    {

        Log::info($request->all());

        $regla = array(
            'id' => 'required',
            'idproducto' => 'required',
            'cantidad' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}
        DB::beginTransaction();

        try {
            $infoProducto = Productos::where('id', $request->idproducto)->first();
            $subtotal = $request->cantidad * $infoProducto->precio;

            EntregaProductosDetalle::where('id', $request->id)->update([
                'id_producto' => $request->idproducto,
                'cantidad' => $request->cantidad,
                'precio_venta' => $infoProducto->precio,
                'subtotal' => $subtotal,
            ]);

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info("error: " . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function historialEntradaDetalleBorrarItem(Request $request)
    {
        $regla = array(
            'id' => 'required', //tabla: entradas_detalle
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if($infoEntradaDeta = EntregaProductosDetalle::where('id', $request->id)->first()){

            DB::beginTransaction();

            try {

                // BORRAR ENTRADAS FINALMENTE
                EntregaProductosDetalle::where('id', $infoEntradaDeta->id)->delete();

                // SI YA NO HAY ENTRADAS SE DEBERA BORRAR
                EntregaProductos::whereNotIn('id', EntregaProductosDetalle::pluck('id_entregaproductos'))->delete();

                DB::commit();
                return ['success' => 1];

            } catch (\Throwable $e) {
                Log::info('ee ' . $e);
                DB::rollback();
                return ['success' => 99];
            }
        }else{
            return ['success' => 99];
        }
    }




    public function indexNuevoIngresoEntradaDetalle($id)
    {
        // id: es de entradas
        $info = EntregaProductos::where('id', $id)->first();

        $fechaFormat = date("d-m-Y", strtotime($info->fecha));

        return view('backend.admin.historial.entradas.detalle.vistaingresoextra', compact('id', 'info', 'fechaFormat'));
    }


    public function registrarProductosExtras(Request $request)
    {
        $regla = array(
            'identrada' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            // Obtiene los datos enviados desde el formulario como una cadena JSON y luego decódificala
            $datosContenedor = json_decode($request->contenedorArray, true); // El segundo argumento convierte el resultado en un arreglo

            foreach ($datosContenedor as $filaArray) {

                $infoProducto = Productos::where('id', $filaArray['infoIdProducto'])->first();
                $subtotal = $filaArray['infoCantidad'] * $infoProducto->precio;

                $detalle = new EntregaProductosDetalle();
                $detalle->id_entregaproductos = $request->identrada;
                $detalle->id_producto = $filaArray['infoIdProducto'];
                $detalle->cantidad = $filaArray['infoCantidad'];
                $detalle->precio_venta = $infoProducto->precio;
                $detalle->subtotal = $subtotal; // cantidad * precio unitario
                $detalle->save();
            }

            DB::commit();
            return ['success' => 1];

        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }



}
