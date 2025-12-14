<?php
class Productos extends Controller{
    public function __construct() {
        session_start();
        parent::__construct();
    }

    public function index(){
        $id_usuario = $_SESSION['id_usuario'];
        $verificar = $this->model->verificarPermisos($id_usuario, 'productos');
        if(!empty($verificar) || $id_usuario == 1){
            if (empty($_SESSION['activo'])) {
                header("location: " . base_url);
            }
    
            $data['medidas'] = $this->model->getMedidas();
            $data['categorias'] = $this->model->getCategorias();
            $this->views->getView($this, "index", $data);
        } else {
            header('Location:' .base_url. 'Errors/permisos');
        }
    }
    
    public function listar(){
        $data = $this->model->getProductos();
        for ($i=0; $i < count($data); $i++) { 
            // Ruta CORREGIDA: debe coincidir con donde se guardan las imágenes
            $ruta_fisica = "C:/wamp64/www/uploads/productos/" . $data[$i]['foto'];
            
            error_log("Buscando imagen: {$ruta_fisica}");
            
            $src = '';
            
            // Verificar si la imagen existe físicamente
            if (file_exists($ruta_fisica) && $data[$i]['foto'] != 'default.jpg' && filesize($ruta_fisica) > 0) {
                try {
                    // Convertir a base64 para mostrar directamente
                    $image_data = file_get_contents($ruta_fisica);
                    if ($image_data !== false) {
                        $image_info = getimagesize($ruta_fisica);
                        if ($image_info !== false) {
                            $mime_type = $image_info['mime'];
                            $base64_data = base64_encode($image_data);
                            $src = 'data:' . $mime_type . ';base64,' . $base64_data;
                            error_log("✅ Imagen convertida a base64: {$data[$i]['foto']}");
                        } else {
                            error_log("❌ No se pudo obtener info de la imagen: {$ruta_fisica}");
                        }
                    } else {
                        error_log("❌ No se pudo leer el archivo: {$ruta_fisica}");
                    }
                } catch (Exception $e) {
                    error_log("❌ Error procesando imagen {$ruta_fisica}: " . $e->getMessage());
                }
            } else {
                error_log("❌ Imagen no encontrada: {$ruta_fisica}");
            }
            
            // Si no se pudo cargar la imagen, usar por defecto
            if (empty($src)) {
                error_log("⚠️ Usando imagen por defecto para producto {$data[$i]['id']}");
                $default_path = "C:/wamp64/www/pos_venta/Assets/img/default.jpg";
                if (file_exists($default_path)) {
                    try {
                        $image_data = file_get_contents($default_path);
                        $base64_data = base64_encode($image_data);
                        $src = 'data:image/jpeg;base64,' . $base64_data;
                    } catch (Exception $e) {
                        $src = base_url . 'Assets/img/default.jpg';
                    }
                } else {
                    $src = base_url . 'Assets/img/default.jpg';
                }
            }
            
            $data[$i]['imagen'] = '<img class="img-thumbnail" src="'. $src .'" width="100" height="100">';
            
            if ($data[$i]['estado'] == 1) {
                $data[$i]['estado'] = '<span class="badge bg-success">Activo</span>';
                $data[$i]['acciones'] = '<div class="d-flex gap-1">
					<button class="btn btn-primary btn-sm" type="button" onclick="btnEditarPro(' . $data[$i]['id'] . ');"><i class="fas fa-edit"></i></button>
					<button class="btn btn-danger btn-sm" type="button" onclick="btnEliminarPro(' . $data[$i]['id'] . ');"><i class="fas fa-ban"></i></button>
					</div>';
            } else {
                $data[$i]['estado'] = '<span class="badge bg-danger">Inactivo</span>';
                $data[$i]['acciones'] = '<div>
                <button class="btn btn-success" type="button" onclick="btnReingresarPro(' . $data[$i]['id'] . ');"><i class="fas fa-check"></i></button>
                <div/>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    
    public function registrar(){
        $request_id = uniqid('req_', true);
        error_log("=== INICIANDO REGISTRO [$request_id] ===");
        
        $id_usuario = $_SESSION['id_usuario'];
        $verificar = $this->model->verificarPermisos($id_usuario, 'registrar_producto');
        
        if(!empty($verificar) || $id_usuario == 1){
            $codigo = $_POST['codigo'];
            $descripcion = $_POST['descripcion'];
            $precio_compra = $_POST['precio_compra'];
            $precio_venta = $_POST['precio_venta'];
            $medida = $_POST['medida'];
            $categoria = $_POST['categoria'];
            $id = $_POST['id'];
            $tasa = $_SESSION['tasa'];
            
            error_log("[$request_id] ID recibido: '" . $id . "'");
            
            if (empty($codigo) || empty($descripcion) || empty($precio_compra) || empty($precio_venta)) {
                $msg = array('msg' => 'Todos los campos son obligatorios', 'icono' => 'warning');
            } else {
                // Manejo de la imagen
                $name = "default.jpg";
                $imagen_procesada = false;
                
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                    error_log("[$request_id] Iniciando procesamiento de imagen...");
                    $img = $_FILES['imagen'];
                    
                    // Verificar si ya fue procesada (archivo temporal existe)
                    if (file_exists($img['tmp_name']) && is_uploaded_file($img['tmp_name'])) {
                        $name = $this->procesarImagen($img, $request_id);
                        if ($name === false) {
                            $msg = array('msg' => 'Error al subir la imagen. Verifique el formato (JPEG, PNG, GIF) y tamaño (máx 2MB).', 'icono' => 'error');
                            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
                            die();
                        }
                        $imagen_procesada = true;
                        error_log("[$request_id] Imagen procesada exitosamente: " . $name);
                    } else {
                        error_log("[$request_id] ADVERTENCIA: Archivo temporal ya no existe, usando default.jpg");
                        $name = "default.jpg";
                    }
                }

                // VERIFICACIÓN CORREGIDA: Si $id está vacío o es 0, es NUEVO producto
                if (empty($id) || $id == 0 || $id == "0" || $id == "") {
                    // REGISTRAR NUEVO PRODUCTO
                    error_log("[$request_id] Registrando NUEVO producto...");
                    $data = $this->model->registrarProducto($codigo, $descripcion, $precio_compra, $precio_venta, $medida, $categoria, $name, $tasa);
                    if ($data == "ok") {
                        $msg = array('msg' => 'Producto registrado con éxito', 'icono' => 'success');
                        error_log("[$request_id] ✅ Producto NUEVO registrado exitosamente");
                    } else if($data == "existe") {
                        $msg = array('msg' => 'El Producto ya existe', 'icono' => 'warning');
                        error_log("[$request_id] ❌ Producto ya existe");
                    } else {
                        $msg = array('msg' => 'Error al registrar el Producto', 'icono' => 'error');
                        error_log("[$request_id] ❌ Error al registrar producto NUEVO");
                    }
                } else {
                    // MODIFICAR PRODUCTO EXISTENTE
                    error_log("[$request_id] Modificando producto EXISTENTE ID: " . $id);
                    $producto_actual = $this->model->editarPro($id);
                    $img_actual = $producto_actual['foto'];
                    
                    // Si se subió nueva imagen Y no ha sido procesada aún
                    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0 && !$imagen_procesada) {
                        $img = $_FILES['imagen'];
                        if (file_exists($img['tmp_name']) && is_uploaded_file($img['tmp_name'])) {
                            $name = $this->procesarImagen($img, $request_id);
                            if ($name === false) {
                                $msg = array('msg' => 'Error al subir la imagen', 'icono' => 'error');
                                echo json_encode($msg, JSON_UNESCAPED_UNICODE);
                                die();
                            }
                            // Eliminar imagen anterior si no es default
                            if ($img_actual != "default.jpg" && $img_actual != "") {
                                $ruta_anterior = "C:/wamp64/www/uploads/productos/" . $img_actual;
                                if (file_exists($ruta_anterior)){
                                    unlink($ruta_anterior);
                                    error_log("[$request_id] Imagen anterior eliminada: " . $img_actual);
                                }
                            }
                        } else {
                            // Mantener imagen actual si el temporal ya no existe
                            $name = $img_actual;
                            error_log("[$request_id] Usando imagen actual: " . $img_actual);
                        }
                    } else if ($imagen_procesada) {
                        // Si ya se procesó una imagen nueva, usarla y eliminar la anterior
                        error_log("[$request_id] Usando imagen ya procesada: " . $name);
                        if ($img_actual != "default.jpg" && $img_actual != "") {
                            $ruta_anterior = "C:/wamp64/www/uploads/productos/" . $img_actual;
                            if (file_exists($ruta_anterior)){
                                unlink($ruta_anterior);
                                error_log("[$request_id] Imagen anterior eliminada: " . $img_actual);
                            }
                        }
                    } else {
                        // Mantener imagen actual
                        $name = $img_actual;
                        error_log("[$request_id] Manteniendo imagen actual: " . $img_actual);
                    }
                    
                    $data = $this->model->modificarProducto($codigo, $descripcion, $precio_compra, $precio_venta, $medida, $categoria, $name, $tasa, $id);
                    if ($data == "modificado") {
                        $msg = array('msg' => 'Producto modificado con éxito', 'icono' => 'success');
                        error_log("[$request_id] ✅ Producto EXISTENTE modificado exitosamente");
                    } else {
                        $msg = array('msg' => 'Error al modificar el Producto', 'icono' => 'error');
                        error_log("[$request_id] ❌ Error al modificar producto EXISTENTE");
                    }
                }
            }
        } else {
            $msg = array('msg' => 'No tienes Permisos para Registrar Productos', 'icono' => 'warning');
        }
        
        error_log("[$request_id] === FINALIZANDO REGISTRO ===");
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    private function procesarImagen($img, $request_id = '') {
        error_log("[$request_id] === PROCESANDO IMAGEN ===");
        error_log("[$request_id] Archivo: " . $img['name']);
        error_log("[$request_id] Temporal: " . $img['tmp_name']);
        
        // Verificar que el archivo temporal existe
        if (!file_exists($img['tmp_name'])) {
            error_log("[$request_id] ❌ ERROR: Archivo temporal no existe");
            return false;
        }
        
        if (!is_uploaded_file($img['tmp_name'])) {
            error_log("[$request_id] ❌ ERROR: No es un archivo subido válido");
            return false;
        }
        
        $permitidos = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
        
        // Verificar tipo de archivo
        if (!in_array($img['type'], $permitidos)) {
            error_log("[$request_id] Tipo de archivo no permitido: " . $img['type']);
            return false;
        }
        
        // Verificar tamaño (máximo 2MB)
        if ($img['size'] > 2 * 1024 * 1024) {
            error_log("[$request_id] Archivo demasiado grande: " . $img['size']);
            return false;
        }
        
        // RUTA CONSISTENTE: usar la misma en guardar y buscar
        $directorio = "C:/wamp64/www/uploads/productos/";
        
        // Crear directorio si no existe
        if (!file_exists($directorio)) {
            if (!mkdir($directorio, 0755, true)) {
                error_log("[$request_id] No se pudo crear el directorio");
                return false;
            }
            error_log("[$request_id] Directorio creado: " . $directorio);
        }
        
        // Verificar permisos de escritura
        if (!is_writable($directorio)) {
            error_log("[$request_id] Directorio sin permisos de escritura");
            return false;
        }
        
        // Verificar que es una imagen válida
        $tipo_imagen = @exif_imagetype($img['tmp_name']);
        if ($tipo_imagen === false) {
            error_log("[$request_id] No se pudo determinar el tipo de imagen");
            return false;
        }
        
        $tipos_permitidos = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);
        if (!in_array($tipo_imagen, $tipos_permitidos)) {
            error_log("[$request_id] Tipo de imagen no permitido: " . $tipo_imagen);
            return false;
        }
        
        // Generar nombre único
        $extension = pathinfo($img['name'], PATHINFO_EXTENSION);
        $name = 'producto_' . time() . '_' . uniqid() . '.' . strtolower($extension);
        $destino = $directorio . $name;
        
        error_log("[$request_id] Moviendo a: " . $destino);
        
        // Mover archivo
        if (move_uploaded_file($img['tmp_name'], $destino)) {
            error_log("[$request_id] ✅ Imagen procesada: " . $name);
            
            // Verificar que realmente se guardó
            if (file_exists($destino)) {
                error_log("[$request_id] ✅ Archivo guardado correctamente en: " . $destino);
            } else {
                error_log("[$request_id] ❌ ERROR: Archivo no se guardó en destino");
            }
            
            return $name;
        } else {
            error_log("[$request_id] ❌ Error moviendo archivo");
            return false;
        }
    }

    // Función para verificar todas las imágenes guardadas
    public function verificarImagenesGuardadas() {
        $directorio = "C:/wamp64/www/uploads/productos/";
        
        echo "<h3>Verificando imágenes en: " . $directorio . "</h3>";
        
        if (!file_exists($directorio)) {
            echo "❌ El directorio NO existe<br>";
            return;
        }
        
        $archivos = scandir($directorio);
        $total_archivos = count($archivos) - 2;
        
        echo "Total de archivos: " . $total_archivos . "<br><br>";
        
        foreach ($archivos as $archivo) {
            if ($archivo != "." && $archivo != "..") {
                $ruta_completa = $directorio . $archivo;
                $tamaño = filesize($ruta_completa);
                $fecha = date("Y-m-d H:i:s", filemtime($ruta_completa));
                
                echo "<div style='border:1px solid #ccc; margin:10px; padding:10px;'>";
                echo "<strong>Archivo:</strong> " . $archivo . "<br>";
                echo "<strong>Tamaño:</strong> " . $tamaño . " bytes<br>";
                echo "<strong>Fecha:</strong> " . $fecha . "<br>";
                
                // Mostrar imagen
                try {
                    $image_data = file_get_contents($ruta_completa);
                    $base64_data = base64_encode($image_data);
                    $image_info = getimagesize($ruta_completa);
                    $mime_type = $image_info['mime'];
                    $src = 'data:' . $mime_type . ';base64,' . $base64_data;
                    echo "<img src='{$src}' width='150' style='margin-top:10px;'><br>";
                } catch (Exception $e) {
                    echo "❌ Error mostrando imagen: " . $e->getMessage() . "<br>";
                }
                
                echo "</div>";
            }
        }
        
        if ($total_archivos == 0) {
            echo "⚠️ No hay archivos en el directorio<br>";
        }
        
        die();
    }

    public function editar(int $id){
        $data = $this->model->editarPro($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function eliminar(int $id){
        $id_usuario = $_SESSION['id_usuario'];
        $verificar = $this->model->verificarPermisos($id_usuario, 'eliminar_producto');
        if(!empty($verificar) || $id_usuario == 1){
            $data = $this->model->accionProducto(0, $id);
            if ($data == 1) {
                $msg = array('msg' => 'Producto dado de baja', 'icono' => 'success');
            }else{
                $msg = array('msg' => 'Error al Inactivar el Producto', 'icono' => 'error');
            }
        } else {
            $msg = array('msg' => 'No tienes Permisos para Inactivar Productos', 'icono' => 'warning');
        }
        
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function reingresar(int $id){
        $data = $this->model->accionProducto(1, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Producto Activado con éxito', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al Activar el Producto', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
}