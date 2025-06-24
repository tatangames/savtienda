<?php

namespace App\Http\Controllers\Backend\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Pagos;
use App\Models\Trabajadores;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ConfiguracionController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }


    public function vistaDashboard()
    {
        return view('backend.admin.dashboard.vistadashboard');
    }

    public function vistaUnidadMedida()
    {
        return view('backend.admin.config.unidadmedida.vistaunidadmedida');
    }

    public function tablaUnidadMedida()
    {
        $lista = UnidadMedida::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.config.unidadmedida.tablaunidadmedida', compact('lista'));
    }


    public function nuevoUnidadMedida(Request $request)
    {
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {
            $dato = new UnidadMedida();
            $dato->nombre = $request->nombre;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function infoUnidadMedida(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $info = UnidadMedida::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function actualizarUnidadMedida(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        UnidadMedida::where('id', $request->id)->update([
            'nombre' => $request->nombre
        ]);

        return ['success' => 1];
    }


    //*****************************************************************************




    public function vistaPagos()
    {
        $arrayTrabajador = Trabajadores::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.config.pagos.vistapagos', compact('arrayTrabajador'));
    }



    public function tablaPagos()
    {
        $listado = Pagos::orderBy('fecha', 'ASC')->get();

        foreach ($listado as $item) {

            $infoTrabajador = Trabajadores::where('id', $item->id_trabajador)->first();
            $item->nombreTrabajador = $infoTrabajador->nombre;

            $item->fechaFormat = date("d-m-Y", strtotime($item->fecha));
            $item->montoFormat = '$' . number_format((float)$item->monto, 2, '.', ',');
        }

        return view('backend.admin.config.pagos.tablapagos', compact('listado'));
    }


    public function registrarPago(Request $request)
    {
        $regla = array(
            'fecha' => 'required',
            'monto' => 'required',
            'idtrabajador' => 'required',
        );

        // descripcion

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }
        DB::beginTransaction();

        try {
            $dato = new Pagos();
            $dato->fecha = $request->fecha;
            $dato->monto = $request->monto;
            $dato->descripcion = $request->descripcion;
            $dato->id_trabajador = $request->idtrabajador;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function informacionPago(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if($info = Pagos::where('id', $request->id)->first()){

            $arrayTrabajador = Trabajadores::orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'arrayTrabajador' => $arrayTrabajador, 'info' => $info];
        }else{
            return ['success' => 2];
        }
    }


    public function editarPago(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'fecha' => 'required',
            'monto' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        Pagos::where('id', $request->id)->update([
            'fecha' => $request->fecha,
            'monto' => $request->monto,
            'descripcion' => $request->descripcion,
            'id_trabajador' => $request->idtrabajador,
        ]);

        return ['success' => 1];
    }







}
