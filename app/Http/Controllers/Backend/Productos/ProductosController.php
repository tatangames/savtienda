<?php

namespace App\Http\Controllers\Backend\Productos;

use App\Http\Controllers\Controller;
use App\Models\EntregaProductos;
use App\Models\EntregaProductosDetalle;
use App\Models\Productos;
use App\Models\Trabajadores;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductosController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    //*****************  REGISTRO DE MATERIALES   **********************************


    public function vistaProductos(){
        $arrayUnidades = UnidadMedida::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.productos.vistaproductos', compact('arrayUnidades'));
    }

    public function tablaProductos(){

        $listado = Productos::orderBy('nombre', 'ASC')->get();

        foreach ($listado as $fila) {

            $fila->precioFormat = "$" . number_format($fila->precio, 2, '.', ',');
        }

        return view('backend.admin.productos.tablaproductos', compact('listado'));
    }

    public function nuevoProductos(Request $request){

        $regla = array(
            'nombre' => 'required',
            'unidad' => 'required',
            'precio' => 'required',
        );

        // codigo

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            $registro = new Productos();
            $registro->id_unidadmedida = $request->unidad;
            $registro->nombre = $request->nombre;
            $registro->precio = $request->precio;
            $registro->codigo = $request->codigo;
            $registro->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function infoProductos(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Productos::where('id', $request->id)->first()){

            $arrayUnidad = UnidadMedida::orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'info' => $lista, 'arrayUnidad' => $arrayUnidad];
        }else{
            return ['success' => 2];
        }
    }

    public function actualizarProductos(Request $request){

        $regla = array(
            'nombre' => 'required',
            'unidad' => 'required',
            'precio' => 'required',
        );

        // codigo

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        Productos::where('id', $request->id)->update([
            'id_unidadmedida' => $request->unidad,
            'nombre' => $request->nombre,
            'precio' => $request->precio,
            'codigo' => $request->codigo,
        ]);

        return ['success' => 1];
    }


    //*****************************************************************


    public function vistaEntradasProductos()
    {
        $arrayTrabajadores = Trabajadores::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.registro.entrada.vistaentrada', compact('arrayTrabajadores'));
    }


    public function buscadorProductoGlobal(Request $request)
    {
        if($request->get('query')){
            $query = $request->get('query');
            $arrayMateriales = Productos::where('nombre', 'LIKE', "%{$query}%")
                ->orWhere('codigo', 'LIKE', "%{$query}%")
                ->get();

            $output = '<ul class="dropdown-menu" style="display:block; position:relative; overflow: auto; ">';
            $tiene = true;
            foreach($arrayMateriales as $row){


                $infoMedida = UnidadMedida::where('id', $row->id_unidadmedida)->first();
                $medida = "(" . $infoMedida->nombre . ")";



                $nombreCompleto = $row->nombre . '  ' .$medida;


                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if(count($arrayMateriales) == 1){
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li class="cursor-pointer" onclick="modificarValor(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px; color: black">'.$nombreCompleto .'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li class="cursor-pointer" onclick="modificarValor(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px; color: black">'.$nombreCompleto .'</a></li>
                   <hr>
                ';
                    }
                }
            }
            $output .= '</ul>';
            if($tiene){
                $output = '';
            }
            echo $output;
        }
    }



    public function guardarNuevaEntrada(Request $request)
    {
        $rules = array(
            'fecha' => 'required',
            'idTrabajador' =>  'required',
         );

        // descripcion

        // idMaterial, infoCantidad

        $validator = Validator::make($request->all(), $rules);
        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $datosContenedor = json_decode($request->contenedorArray, true);

            $registro = new EntregaProductos();
            $registro->fecha = $request->fecha;
            $registro->descripcion = $request->descripcion;
            $registro->id_trabajador = $request->idTrabajador;
            $registro->save();

            // idMaterial    infoCantidad

            // SUMAR CANTIDAD
            foreach ($datosContenedor as $filaArray) {

                $infoProducto = Productos::where('id', $filaArray['idMaterial'])->first();

                $subtotal = $filaArray['infoCantidad'] * $infoProducto->precio;

                $detalle = new EntregaProductosDetalle();
                $detalle->id_entregaproductos = $registro->id;
                $detalle->id_producto = $filaArray['idMaterial'];
                $detalle->cantidad = $filaArray['infoCantidad'];
                $detalle->precio_venta = $infoProducto->precio;
                $detalle->subtotal = $subtotal; // cantidad * precio unitario
                $detalle->save();
            }

            // ENTRADA COMPLETADA

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info("error: " . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }














}
