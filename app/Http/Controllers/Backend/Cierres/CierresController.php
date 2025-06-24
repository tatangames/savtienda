<?php

namespace App\Http\Controllers\Backend\Cierres;

use App\Http\Controllers\Controller;
use App\Models\EntregaProductosDetalle;
use App\Models\Pagos;
use App\Models\Productos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CierresController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }


    public function indexCierres()
    {
        return view('backend.admin.cierres.vistacierres');
    }


    public function ejemplo(Request $request){

        DB::beginTransaction();

        try {

            $fechaInicio = '2025-06-01';
            $fechaFin = '2025-06-02';

            // Simulamos los productos que quedaron físicamente
            $quedaron = [
                1 => 10, // producto_id => cantidad sobrante
                2 => 20,
            ];

            // 1. Obtener productos entregados directamente con join
            $entregados = DB::table('entrega_productos_detalle as d')
                ->join('entrega_productos as e', 'd.id_entregaproductos', '=', 'e.id')
                ->whereBetween('e.fecha', [$fechaInicio, $fechaFin])
                ->select(
                    'd.id_producto',
                    DB::raw('SUM(d.cantidad) as total_entregado'),
                    DB::raw('MAX(d.precio_venta) as precio_unitario')
                )
                ->groupBy('d.id_producto')
                ->get();

            $totalEsperado = 0;
            $detalle = [];

            foreach ($entregados as $entrega) {
                $productoId = $entrega->id_producto;
                $producto = DB::table('productos')->where('id', $productoId)->first();

                $entregado = $entrega->total_entregado;
                $quedo = $quedaron[$productoId] ?? 0;
                $vendido = $entregado - $quedo;
                $precio = $entrega->precio_unitario;
                $total = $vendido * $precio;

                $totalEsperado += $total;

                $detalle[] = [
                    'producto' => $producto->nombre,
                    'entregado' => $entregado,
                    'quedo' => $quedo,
                    'vendido' => $vendido,
                    'precio_unitario' => $precio,
                    'total_esperado' => round($total, 2),
                ];
            }


            // Fondos iniciales entregados al trabajador en ese mismo período
            $fondo = DB::table('fondo_iniciales')
                ->where('id_trabajador', 1)
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->sum('monto');


            // 2. Obtener pagos realizados
            $totalRecibido = DB::table('pagos')
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->sum('monto');

            $diferencia = round($totalEsperado - ($totalRecibido - $fondo), 2);

            $descripcion = '';

            if ($diferencia > 0) {
                $descripcion = "Faltó dinero: el trabajador entregó menos de lo esperado.";
            } elseif ($diferencia < 0) {
                $descripcion = "Sobrante: el trabajador entregó más dinero de lo esperado.";
            } else {
                $descripcion = "Todo cuadró correctamente. No hay diferencia.";
            }

            return response()->json([
                'detalle' => $detalle,
                'total_esperado' => round($totalEsperado, 2),
                'total_recibido' => round($totalRecibido, 2),
                'diferencia' => $diferencia,
                'descripcion' => $descripcion,
            ]);

        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }


    }

}
