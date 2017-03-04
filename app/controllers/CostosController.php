<?php
namespace App\Controllers;

use App\Models\Costos;
use App\Models\CostosDetalle;
use App\Repositories\CostosRepository;
use Core\{Controller};
use Dompdf\Dompdf;
use Dompdf\Exception;

class CostosController extends Controller {
    private $CostosRepo;

    public function __construct() {
        parent::__construct();
        $this->CostosRepo = new CostosRepository();
    }

    public function getIndex() {
        return $this->render('costos/index.twig', [
            'title' => 'Costoss'
        ]);
    }

    public function postGrid() {
        print_r($this->CostosRepo->listar());
    }

    public function getNuevo() {
        return $this->render('Costos/nuevo.twig', [
            'title' => 'Costoss'
        ]);
    }

    public function getDetalle($id) {
        return $this->render('Costos/detalle.twig', [
            'title' => 'Costoss',
            'model' => $this->CostosRepo->obtener($id)
        ]);
    }

    public function postGenerar(){
        $model = new Costos();
        $model->cliente_id = $_POST['cliente_id'];

        $detalle = [];

        foreach($_POST['detalle'] as $d) {
            // Costos_id,producto_id, cantidad, costo, precio
            $d = (object)$d;

            $cd = new CostosDetalle();
            $cd->producto_id = $d->id;
            $cd->cantidad = $d->cantidad;
            $cd->costo = $d->costo;
            $cd->precio = $d->precio;

            $detalle[] = $cd;
        }

        print_r(
            json_encode(
                $this->CostosRepo->generar($model, $detalle)
            )
        );
    }

    public function getPdf($id) {
        $model = $this->CostosRepo->obtener($id);

        if($model->anulado === 1) {
            throw new Exception("Costos anulado");
        }

        $dompdf = new Dompdf();
        $dompdf->loadHtml(
            $this->render('Costos/pdf.twig', [
                'model' => $model
            ])
        );

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('Costos-' . $model->idForView);
    }

    public function postAnular(){
        print_r(
            json_encode(
                $this->CostosRepo->anular($_POST['id'])
            )
        );
    }
}