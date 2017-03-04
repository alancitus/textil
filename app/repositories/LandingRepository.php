<?php
namespace App\Repositories;

use App\Models\SqlViews\ReporteVenta;
use Core\{Log};
use App\Models\{Cliente, Producto, Comprobante, ComprobanteDetalle, Usuario};
use Exception;

class LandingRepository {
    private $cliente;
    private $producto;
    private $comprobante;
    private $reporte_venta;
    private $usuario;

    public function __construct(){
        $this->cliente = new Cliente;
        $this->producto = new Producto;
        $this->comprobante = new Comprobante;
        $this->reporte_venta = new ReporteVenta();
        $this->usuario = new Usuario;
    }

    /*
     * stdClass es una clase predinida en PHP, la usamos porque no vamos a crear una clase para el landing page,
     * sino vamos a crear una clase dinámica
    */
    public function obtener() : \stdClass {
        try {
            // Geramos una variable donde retornaremos los datos que necesitamos para el landing page
            $model = [];

            // Este es muy facil, solo hay que contar los modelos
            $model['productos'] = $this->producto->count();
            $model['usuarios'] = $this->usuario->count();
            $model['clientes'] = $this->cliente->count();

            /*
             * Tambíen es facil para contar los comprobantes, solo hay que asignar un WHERE para que
             * traiga los comprobantes que no han sido anulados
             */
            $model['comprobantes'] = $this->comprobante
                                          ->where('anulado', 0)
                                          ->count();

            /*
             * Para obtener la utilidad lo hemos resuelto con una vista SQL que creamos, la de reporte mensual.
             * */

            // La mensual solo necesita filtrar por año y mes
            $model['utilidad_mensual'] = $this->reporte_venta
                                              ->where('anio', date('Y'))
                                              ->where('mes', date('m'))
                                              ->sum('utilidad');

            // La total no filtra nada
            $model['utilidad_total'] = $this->reporte_venta
                                            ->sum('utilidad');

            // Lo convertimos en un objeto
            return (object)$model;
        } catch (Exception $e) {
            Log::error(LandingRepository::class, $e->getMessage());
        }
    }
}