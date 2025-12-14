<?php
class Cajas extends Controller{
    public function __construct() {
        session_start();
        if (empty($_SESSION['activo'])) {
            header("location: ".base_url);
        }
        parent::__construct();
    }
    
    public function index(){
        $id_usuario = $_SESSION['id_usuario'];
        $verificar = $this->model->verificarPermisos($id_usuario, 'cajas');
        if(!empty($verificar) || $id_usuario == 1){
            $this->views->getView($this, "index");
        } else {
            header('Location:' .base_url. 'Errors/permisos');
        }
    }

    public function arqueo(){
        $this->views->getView($this, "arqueo");
    }

    public function listar(){
        $data = $this->model->getCajas('caja');
        for ($i=0; $i < count($data); $i++) { 
            if ($data[$i]['estado'] == 1) {
                $data[$i]['estado'] = '<span class="badge bg-success">Activo</span>';
                $data[$i]['acciones'] = '<div>
                <button class="btn btn-primary" type="button" onclick="btnEditarCaja(' . $data[$i]['id'] . ');"><i class="fas fa-edit"></i></button>
                <button class="btn btn-danger" type="button" onclick="btnEliminarCaja(' . $data[$i]['id'] . ');"><i class="fas fa-ban"></i></button>
                <div/>';
            }else{
                $data[$i]['estado'] = '<span class="badge bg-danger">Inactivo</span>';
                $data[$i]['acciones'] = '<div>
                <button class="btn btn-success" type="button" onclick="btnReingresarCaja(' . $data[$i]['id'] . ');"><i class="fas fa-check"></i></button>
                <div/>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function registrar(){
        $caja = $_POST['nombre'];
        $id = $_POST['id'];
        if (empty($caja)) {
            $msg = array('msg' => 'Todo los campos son obligatorios', 'icono' => 'warning');
        }else{
            if ($id == "") {
                    $data = $this->model->registrarCaja($caja);
                    if ($data == "ok") {
                        $msg = array('msg' => 'Caja registrado con éxito', 'icono' => 'success');
                    }else if($data == "existe"){
                        $msg = array('msg' => 'La caja ya existe', 'icono' => 'warning');
                    }else{
                        $msg = array('msg' => 'Error al registrar la caja', 'icono' => 'error');
                    }
            }else{
                $data = $this->model->modificarCaja($caja, $id);
                if ($data == "modificado") {
                        $msg = array('msg' => 'Caja Modificado con éxito', 'icono' => 'success');
                }else {
                        $msg = array('msg' => 'Error al modificar la caja', 'icono' => 'error');
                }
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    
    public function editar(int $id){
        $data = $this->model->editarCaja($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function eliminar(int $id){
        $data = $this->model->accionCaja(0, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Caja dado de baja', 'icono' => 'success');

        }else{
            $msg = array('msg' => 'Error al aliminar la caja', 'icono' => 'error');

        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function reingresar(int $id){
        $data = $this->model->accionCaja(1, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Caja reingresado', 'icono' => 'success');

        } else {
            $msg = array('msg' => 'Error al reingresar la caja', 'icono' => 'error');

        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function abrirArqueo(){
    $monto_inicial = $_POST['monto_inicial'];
    $monto_inicial_bolos = $_SESSION['tasa'] * $monto_inicial;
    $fecha_apertura = date('Y-m-d');
    $id_usuario = $_SESSION['id_usuario'];
    $id = $_POST['id'];
    
    if (empty($monto_inicial)) {
        $msg = array('msg' => 'Todo los campos son obligatorios', 'icono' => 'warning');
    } else {
        if ($id == '') {
            $data = $this->model->registrarArqueo($id_usuario, $monto_inicial, $monto_inicial_bolos, $fecha_apertura);
            if ($data == "ok") {
                $msg = array('msg' => 'Caja Abierta', 'icono' => 'success');
            } else if($data == "existe") {
                $msg = array('msg' => 'La caja ya esta abierta', 'icono' => 'warning');
            } else {
                $msg = array('msg' => 'Error al Aperturar la caja', 'icono' => 'error');
            }
        } else {
            // VERIFICAR SI LOS DATOS EXISTEN ANTES DE ACCEDER
            $data['totales'] = $this->model->getVentas($id_usuario);
            $data['montos_iniciales'] = $this->model->getMontoInicial($id_usuario);
            
            // Verificar si los datos existen y son arrays
            if (!$data['totales'] || !$data['montos_iniciales']) {
                $msg = array('msg' => 'Error: No se encontraron datos para cerrar la caja', 'icono' => 'error');
                echo json_encode($msg, JSON_UNESCAPED_UNICODE);
                die();
            }
            
            // Usar valores por defecto si no existen
            $monto_total = isset($data['totales']['monto_total']) ? $data['totales']['monto_total'] : 0;
            $monto_total_bolos = isset($data['totales']['monto_total_bolos']) ? $data['totales']['monto_total_bolos'] : 0;
            $total_ventas = isset($data['totales']['total_ventas']) ? $data['totales']['total_ventas'] : 0;
            $monto_inicial_db = isset($data['montos_iniciales']['monto_inicial']) ? $data['montos_iniciales']['monto_inicial'] : 0;
            $monto_inicial_bolos_db = isset($data['montos_iniciales']['monto_inicial_bolos']) ? $data['montos_iniciales']['monto_inicial_bolos'] : 0;
            $id_arqueo = isset($data['montos_iniciales']['id']) ? $data['montos_iniciales']['id'] : 0;
            
            $general = $monto_total + $monto_inicial_db;
            $general_bolos = $monto_total_bolos + $monto_inicial_bolos_db;
            
            $data = $this->model->actualizarArqueo($monto_total, $monto_total_bolos, $fecha_apertura, $total_ventas, $general, $general_bolos, $id_arqueo);
            
            if ($data == "ok") {
                $data = $this->model->actualizarApertura($id_usuario);
                if ($data == "ok") {
                    $msg = array('msg' => 'Caja Cerrada', 'icono' => 'success');
                } else {
                    $msg = array('msg' => 'Error al Cerrar la caja', 'icono' => 'error');
                }
            } else {
                $msg = array('msg' => 'Error al Cerrar la caja', 'icono' => 'error');
            }
        }
    } 
    echo json_encode($msg, JSON_UNESCAPED_UNICODE);
    die();
}

    public function listarArqueos(){
        $id_usuario = $_SESSION['id_usuario'];
        $verificar = $this->model->verificarPermisos($id_usuario, 'cajas');
        if(!empty($verificar) || $id_usuario == 1){
            $data = $this->model->getUserCajas('cierre_caja', $id_usuario);
            for ($i=0; $i < count($data); $i++) { 
                if ($data[$i]['estado'] == 1) {
                    $data[$i]['estado'] = '<span class="badge bg-success">Abierta</span>';
                }else{
                    $data[$i]['estado'] = '<span class="badge bg-danger">Cerrada</span>';
                }
            }
        } else {
            header('Location:' .base_url. 'Errors/permisos');
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getVentas() {
		$id_usuario = $_SESSION['id_usuario'];
		$data['totales'] = $this->model->getVentas($id_usuario);
		$data['montos_iniciales'] = $this->model->getMontoInicial($id_usuario);
		
		// Verificar y asignar valores por defecto
		$data['montos_totales'] = (isset($data['totales']['monto_total']) ? $data['totales']['monto_total'] : 0) + 
								 (isset($data['montos_iniciales']['monto_inicial']) ? $data['montos_iniciales']['monto_inicial'] : 0);
		
		$data['montos_totales_bolos'] = (isset($data['totales']['monto_total_bolos']) ? $data['totales']['monto_total_bolos'] : 0) + 
									   (isset($data['montos_iniciales']['monto_inicial_bolos']) ? $data['montos_iniciales']['monto_inicial_bolos'] : 0);
		
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
		die();
	}
}
