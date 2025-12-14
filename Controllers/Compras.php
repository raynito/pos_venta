<?php

class Compras extends Controller {
    public function __construct() {
        session_start();
        parent::__construct();
    }

    public function index() {
        $id_usuario = $_SESSION['id_usuario'];
        $verificar = $this->model->verificarPermisos($id_usuario, 'compras');
        if(!empty($verificar) || $id_usuario == 1){
            $this->views->getView($this, "index");
        } else {
            header('Location:' .base_url. 'Errors/permisos');
        }
    }

    public function buscarCodigoCompras($codigo) {
       $data = $this->model->getProCod($codigo);
       echo json_encode($data, JSON_UNESCAPED_UNICODE);
       die();
    }

    public function ingresar() {
        $id = $_POST['id'];
        $datos = $this->model->getProductoById($id);
        $id_producto = $datos['id'];
        $id_usuario = $_SESSION['id_usuario'];
        $precio = $datos['precio_compra'];
        $precio_bolos = $datos['precio_compra_bolos'];
        $cantidad = $_POST['cantidad'];
        $comprobar = $this->model->consultarDetalleCompra($id_producto, $id_usuario);
        if (empty($comprobar)) {
            $sub_total = $cantidad * $precio;
            $sub_total_bolos = $cantidad * $precio_bolos;
            $data = $this->model->registrarDetalleCompras($id_producto, $id_usuario, $precio, $precio_bolos, $cantidad, $sub_total, $sub_total_bolos);
            if($data == "ok") {
                $msg = array('msg' => 'Producto Ingresado a la Compra', 'icono' => 'success');
            } else {
                $msg = array('msg' => 'Error Ingresando Producto a la Compra', 'icono' => 'error');
            }
        } else {
            $total_cantidad = $comprobar['cantidad'] + $cantidad;
            $sub_total = $total_cantidad * $precio;
            $sub_total_bolos = $total_cantidad * $precio_bolos;
            $data = $this->model->actualizarDetalleCompra($precio, $precio_bolos, $total_cantidad, $sub_total, $sub_total_bolos, $id_producto, $id_usuario);
            if($data == "ok") {
                $msg = array('msg' => 'Producto Actualizado', 'icono' => 'success');
            } else {
                $msg = array('msg' => 'Error Actualizando Producto', 'icono' => 'error');
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function listar() {
        $id_usuario = $_SESSION['id_usuario'];
        $data['detalle'] = $this->model->getDetallesCompra($id_usuario);
        $data['total_pagar'] = $this->model->calcularCompra($id_usuario);
        $data['total_pagar_bolos'] = $this->model->calcularCompra($id_usuario);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function delete($id) {
        $data = $this->model->deleteDetalleCompra($id);
        if ($data == "ok") {
            $msg = array('msg' => 'Detalle Eliminado con éxito', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al registrar el Producto', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function registrarCompra() {
        $id_usuario = $_SESSION['id_usuario'];
        $total = $this->model->calcularCompra($id_usuario)['total'];
        $total_bolos = $this->model->calcularCompra($id_usuario)['total_bolos'];
        $data = $this->model->registrarCompra($total, $total_bolos);
        if ($data == 'ok') {
            $detalles = $this->model->getDetallesCompra($id_usuario);
            $id_compra = $this->model->ultimaCompra();
            foreach ($detalles as $row) {
                $cantidad = $row['cantidad'];
                $precio = $row['precio'];
                $precio_bolos = $row['precio_bolos'];
                $id_producto = $row['id_producto'];
                $sub_total = $cantidad * $precio;
                $sub_total_bolos = $cantidad * $precio_bolos;
                $this->model->registrarDetalleCompra($id_compra['id'], $id_producto, $cantidad, $precio, $precio_bolos, $sub_total, $sub_total_bolos);
                $stock_actual = $this->model->getProductoById($id_producto);
                $stock = $stock_actual['cantidad'] + $cantidad;
                $this->model->actualizarStock($stock, $id_producto);
            }
            $vaciar = $this->model->vaciarDetallesCompra($id_usuario);
            if ($vaciar == "ok") {
                $msg = array('msg' => 'Compra Registrada', 'id_compra' => $id_compra['id'], 'icono' => 'success');
            }
        } else {
            $msg = array('msg' => 'Error al realizar la Venta', 'icono' => 'warning');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function historialC() {
        $id_usuario = $_SESSION['id_usuario'];
        $verificar = $this->model->verificarPermisos($id_usuario, 'historialC');
        if(!empty($verificar) || $id_usuario == 1){
            $this->views->getView($this, "historialC");
        } else {
            header('Location:' .base_url. 'Errors/permisos');
        }
    }

    public function listar_historial() {
        $data = $this->model->getHistorialCompras();
        for ($i=0; $i < count($data); $i++) {
            if ($data[$i]['estado'] == 1) {
                $data[$i]['estado'] = '<span class="badge bg-success">Completada</span>';
                $data[$i]['acciones'] = '<div class="d-flex gap-1">
                <button type="button" class="btn btn-warning btn-sm" onclick="btnAnularCompra('. $data[$i]['id'] .')"><i class="fas fa-ban"></i></button>
                <a class="btn btn-danger btn-sm" href="' . base_url . "Compras/generarPdfCompra/" . $data[$i]['id'] .'" target="_blank"><i class="fas fa-file-pdf"></i></a>
                </div>';
            }else {
                $data[$i]['estado'] = '<span class="badge bg-danger">Anulada</span>';
                $data[$i]['acciones'] = '<div>
                <a class="btn btn-danger btn-sm" href="' . base_url . "Compras/generarPdfCompra/" . $data[$i]['id'] .'" target="_blank"><i class="fas fa-file-pdf"></i></a>
                </div>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function generarPdfCompra($id_compra) {
		$empresa = $this->model->getEmpresa();
		$productos = $this->model->getProCompra($id_compra);
		require('Libraries/fpdf/fpdf.php');

		$pdf = new FPDF('P', 'mm', array(80, 200));
		$pdf->addPage();
		$pdf->setMargins(5, 0, 0);
		
		$pdf->setTitle('Reporte de Compra');
		
		$pdf->setFont('Arial', 'B', 12);
		$pdf->Cell(65, 10, $this->convertirUtf8($empresa['nombre']), 0, 1, 'C');
		$pdf->image('Assets/img/logoempresa.png',5,8,9,8);
		
		$pdf->setFont('Arial', 'B', 7);
		$pdf->Cell(18, 5, 'R.I.F: ', 0, 0, 'L');
		$pdf->setFont('Arial', '', 7);
		$pdf->Cell(20, 5, $empresa['rif'], 0, 1, 'L');

		$pdf->Ln(0.1);

		$pdf->setFont('Arial', 'B', 7);
		$pdf->Cell(18, 5, 'Telefono: ', 0, 0, 'L');
		$pdf->setFont('Arial', '', 7);
		$pdf->Cell(20, 5, $empresa['telefono'], 0, 1, 'L');

		// DIRECCIÓN CON AJUSTE AUTOMÁTICO
		$pdf->setFont('Arial', 'B', 7);
		$pdf->Cell(18, 5, 'Direccion: ', 0, 0, 'L');
		
		$pdf->setFont('Arial', '', 7);
		$direccion = $this->convertirUtf8($empresa['direccion']);
		
		// Configuración para el ajuste de texto
		$anchoMaximo = 55; // Ancho máximo disponible para la dirección (80mm - márgenes)
		$alturaLinea = 3;  // Altura de cada línea
		
		// Dividir la dirección en líneas que quepan en el ancho disponible
		$lineasDireccion = $this->dividirTextoParaPdf($direccion, $anchoMaximo, $pdf);
		
		// Escribir la primera línea junto con la etiqueta "Direccion:"
		if (!empty($lineasDireccion)) {
			$pdf->Cell(55, $alturaLinea, $lineasDireccion[0], 0, 1, 'L');
			
			// Escribir las líneas restantes (si las hay) indentadas
			for ($i = 1; $i < count($lineasDireccion); $i++) {
				$pdf->Cell(18, $alturaLinea, '', 0, 0, 'L'); // Espacio en blanco para alineación
				$pdf->Cell(55, $alturaLinea, $lineasDireccion[$i], 0, 1, 'L');
			}
		}

		$pdf->setFont('Arial', 'B', 7);
		$pdf->Cell(18, 5, 'Compra: ', 0, 0, 'L');
		$pdf->setFont('Arial', '', 7);
		$pdf->Cell(20, 5, $id_compra, 0, 1, 'L');
		
		$pdf->Ln();

		// El resto del código permanece igual...
		//Encabezados de Productos en la Compra
		$pdf->SetFillColor(0, 0, 0);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->setFont('Arial', '', 7);
		$pdf->Cell(10, 5, 'Cant', 0, 0, 'L', true);
		$pdf->Cell(35, 5, 'Descripcion', 0, 0, 'L', true);
		$pdf->Cell(10, 5, 'Precio', 0, 0, 'L', true);
		$pdf->Cell(15, 5, 'SubTotal', 0, 1, 'R', true);
		
		//Productos en la Compra
		$pdf->SetTextColor(0, 0, 0);
		$total = 0.00;
		$alturaLinea = 4; // Altura reducida para mejor ajuste
		
		foreach($productos as $row) {
			$total += $row['sub_total'];
			
			// Dividir la descripción si supera los 30 caracteres
			$descripcion = $this->convertirUtf8($row['descripcion']);
			$lineas = $this->dividirTexto($descripcion, 30);
			$numLineas = count($lineas);
			
			if ($numLineas > 1) {
				// Para descripciones largas (múltiples líneas)
				$alturaTotal = $alturaLinea * $numLineas;
				
				// Primera línea con todos los datos
				$pdf->Cell(10, $alturaTotal, $row['cantidad'], 0, 0, 'C');
				$pdf->Cell(35, $alturaTotal, $lineas[0], 0, 0, 'L');
				$pdf->Cell(10, $alturaTotal, $row['precio'], 0, 0, 'C');
				$pdf->Cell(15, $alturaTotal, number_format($row['sub_total'], 2, ',', '.'), 0, 1, 'R');
				
				// Líneas adicionales (solo descripción)
				for ($i = 1; $i < $numLineas; $i++) {
					$pdf->Cell(10, $alturaLinea, '', 0, 0, 'C');
					$pdf->Cell(35, $alturaLinea, $lineas[$i], 0, 0, 'L');
					$pdf->Cell(10, $alturaLinea, '', 0, 0, 'C');
					$pdf->Cell(15, $alturaLinea, '', 0, 1, 'R');
				}
			} else {
				// Descripción normal (una sola línea)
				$pdf->Cell(10, 5, $row['cantidad'], 0, 0, 'C');
				$pdf->Cell(35, 5, $descripcion, 0, 0, 'L');
				$pdf->Cell(10, 5, $row['precio'], 0, 0, 'C');
				$pdf->Cell(15, 5, number_format($row['sub_total'], 2, ',', '.'), 0, 1, 'R');
			}
		}
		
		// Total de la Compra
		$pdf->Ln();
		$pdf->Cell(70, 5, 'Total a Pagar:', 0, 1, 'R');
		$pdf->Cell(70, 5, number_format($total, 2, ',', '.'), 0, 1, 'R');
		$pdf->Ln();
		$pdf->Cell(0, 0, $this->convertirUtf8($empresa['mensaje']), 0, 0, 'C');
		$pdf->Output();
	}

	// NUEVA FUNCIÓN PARA DIVIDIR TEXTO ESPECÍFICA PARA PDF
	private function dividirTextoParaPdf($texto, $anchoMaximo, $pdf) {
		$lineas = [];
		
		// Si el texto cabe en una línea, retornarlo directamente
		if ($pdf->GetStringWidth($texto) <= $anchoMaximo) {
			$lineas[] = $texto;
			return $lineas;
		}
		
		// Dividir en palabras
		$palabras = explode(' ', $texto);
		$lineaActual = '';
		
		foreach ($palabras as $palabra) {
			// Probar si la palabra cabe en la línea actual
			$lineaPrueba = $lineaActual . ($lineaActual ? ' ' : '') . $palabra;
			
			if ($pdf->GetStringWidth($lineaPrueba) <= $anchoMaximo) {
				$lineaActual = $lineaPrueba;
			} else {
				// Guardar la línea actual y empezar nueva línea
				if (!empty($lineaActual)) {
					$lineas[] = $lineaActual;
				}
				$lineaActual = $palabra;
			}
		}
		
		// Agregar la última línea si queda
		if (!empty($lineaActual)) {
			$lineas[] = $lineaActual;
		}
		
		return $lineas;
	}

	// Mantén tu función dividirTexto existente para los productos
	private function dividirTexto($texto, $maxCaracteres) {
		$lineas = [];
		$palabras = explode(' ', $texto);
		$lineaActual = '';
		
		foreach ($palabras as $palabra) {
			if (strlen($lineaActual . ' ' . $palabra) <= $maxCaracteres) {
				if (!empty($lineaActual)) {
					$lineaActual .= ' ' . $palabra;
				} else {
					$lineaActual = $palabra;
				}
			} else {
				$lineas[] = $lineaActual;
				$lineaActual = $palabra;
			}
		}
		
		if (!empty($lineaActual)) {
			$lineas[] = $lineaActual;
		}
		
		return $lineas;
	}
	
    // Función para reemplazar utf8_encode() obsoleto
    private function convertirUtf8($string) {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
        } else {
            // Fallback para sistemas sin mbstring
            return $string;
        }
    }

    public function anularCompra($id_compra) {
        $data = $this->model->getCompra($id_compra);
        $compra = $this->model->anularCompra($id_compra);
        foreach ($data as $row){
            $stock_actual = $this->model->getProductoById($row['id_producto']);
            $stock = $stock_actual['cantidad'] - $row['cantidad'];
            $this->model->actualizarStock($stock, $row['id_producto']);
        }
        if ($compra == "ok") {
            $msg = array('msg' => 'Compra Anulada', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error Anulando Compra', 'icono' => 'error');
        }

        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
}