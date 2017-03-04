<?php
namespace App\Repositories;

use Core\{Auth, Log};
use App\Helpers\{ResponseHelper,AnexGridHelper};
use App\Models\{Costos};
use Illuminate\Database\Capsule\Manager as DB;
use Exception;

class CostosRepository {
    private $Costos;

    public function __construct(){
        $this->Costos = new Costos;
    }

    public function obtener($id) : Costos {
        $model = new Costos();

        try {
            $model = $this->Costos->find($id);
        } catch (Exception $e) {
            Log::error(CostosRepository::class, $e->getMessage());
        }

        return $model;
    }

    public function generar(Costos $model, array $detalle) : ResponseHelper {
        $rh = new ResponseHelper;

        try {
            DB::beginTransaction();
            //cliente_id, sub_total, iva, total, fecha, anulado
            $model->sub_total = 0;
            $model->iva = 0;
            $model->total = 0;
            $model->fecha = date('Y-m-d');
            $model->anulado = 0;

            // orden, total, cantidad, precio, costo, Costos_id
            foreach($detalle as $k => $d) {
                $d->orden = $k;
                $d->total = $d->cantidad * $d->precio;
                $model->total += $d->total;
            }

            // SubTotal
            $model->sub_total = $model->total / 1.18;
            $model->iva = $model->total - $model->sub_total;

            // Genera el Costos
            $model->save();

            // Guarda el detalle
            $model->detalle()->saveMany($detalle);

            $rh->setResponse(true);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(ProductoRepository::class, $e->getMessage());
        }

        return $rh;
    }

    public function listar() : string {
        $anexgrid = new AnexGridHelper;

        try {
            $result = $this->Costos->orderBy(
                $anexgrid->columna,
                $anexgrid->columna_orden
            )->skip($anexgrid->pagina)
             ->take($anexgrid->limite)
             ->get();

            foreach($result as $r) {
                $r->cliente = $r->cliente;
            }

            return $anexgrid->responde(
                $result,
                $this->Costos->count()
            );
        } catch (Exception $e) {
            Log::error(CostosRepository::class, $e->getMessage());
        }

        return "";
    }

    public function anular(int $id) : ResponseHelper {
        $rh = new ResponseHelper;

        try {
            $this->Costos->id = $id;
            $this->Costos->anulado = 1;
            $this->Costos->exists = true;

            $this->Costos->save();
            $rh->setResponse(true);
        } catch (Exception $e) {
            Log::error(CostosRepository::class, $e->getMessage());
        }

        return $rh;
    }
}