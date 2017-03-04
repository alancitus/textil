<?php
namespace App\Repositories;

use Core\{Auth, Log};
use App\Helpers\{ResponseHelper,AnexGridHelper};
use App\Models\{Proveedor};
use Exception;

class ProveedorRepository {
    private $Proveedor;

    public function __construct(){
        $this->Proveedor = new Proveedor;
    }

    public function listar() : string {
        $anexgrid = new AnexGridHelper;

        try {
            $result = $this->Proveedor->orderBy(
                $anexgrid->columna,
                $anexgrid->columna_orden
            )->skip($anexgrid->pagina)
             ->take($anexgrid->limite)
             ->get();

            return $anexgrid->responde(
                $result,
                $this->Proveedor->count()
            );
        } catch (Exception $e) {
            Log::error(ProveedorRepository::class, $e->getMessage());
        }

        return "";
    }

    public function guardar(Proveedor $model) : ResponseHelper {
        $rh = new ResponseHelper;

        try {
            $this->Proveedor->id = $model->id;
            $this->Proveedor->nombre = $model->nombre;
            $this->Proveedor->direccion = $model->direccion;

            if(!empty($model->id)){
                $this->Proveedor->exists = true;
            }

            $this->Proveedor->save();
            $rh->setResponse(true);
        } catch (Exception $e) {
            Log::error(ProveedorRepository::class, $e->getMessage());
        }

        return $rh;
    }

    public function obtener($id) : Proveedor {
        $Proveedor = new Proveedor;

        try {
            $Proveedor = $this->Proveedor->find($id);
        } catch (Exception $e) {
            Log::error(ProveedorRepository::class, $e->getMessage());
        }

        return $Proveedor;
    }

    public function eliminar(int $id) : ResponseHelper {
        $rh = new ResponseHelper;

        try {
            $this->Proveedor->destroy($id);
            $rh->setResponse(true);
        } catch (Exception $e) {
            Log::error(ProveedorRepository::class, $e->getMessage());
        }

        return $rh;
    }

    public function buscar(string $q) : array {
        $result = [];

        try {
            $result = $this->Proveedor
                           ->where('nombre', 'like', "%$q%")
                           ->orderBy('nombre')
                           ->get()
                           ->toArray();
        } catch (Exception $e) {
            Log::error(ProveedorRepository::class, $e->getMessage());
        }

        return $result;
    }
}