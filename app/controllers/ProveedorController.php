<?php
namespace App\Controllers;

use App\Repositories\ProveedorRepository;
use Core\{Controller};
use App\Models\Proveedor;
use App\Validations\ProveedorValidation;

class ProveedorController extends Controller {
    private $ProveedorRepo;

    public function __construct() {
        parent::__construct();
        $this->ProveedorRepo = new ProveedorRepository();
    }

    public function getIndex() {
        return $this->render('proveedor/index.twig', [
            'title' => 'Proveedores'
        ]);
    }

    public function postGrid() {
        print_r($this->ProveedorRepo->listar());
    }

    public function getCrud($id = 0) {
        $model = (
        $id === 0
            ? new Proveedor
            : $this->ProveedorRepo->obtener($id)
        );

        return $this->render('Proveedor/crud.twig', [
            'title' => 'Proveedors',
            'model' => $model
        ]);
    }

    public function postGuardar() {
        ProveedorValidation::validate($_POST);

        $model = new Proveedor;
        $model->id = $_POST['id'];
        $model->nombre = $_POST['nombre'];
        $model->direccion = $_POST['direccion'];

        $rh = $this->ProveedorRepo->guardar($model);

        if($rh->response) {
            $rh->href = 'proveedor';
        }

        print_r(
            json_encode($rh)
        );
    }

    public function postEliminar() {
        print_r(
            json_encode(
                $this->ProveedorRepo->eliminar($_POST['id'])
            )
        );
    }

    public function getBuscar($q) {
        print_r(
            json_encode(
                $this->ProveedorRepo->buscar($q)
            )
        );
    }
}