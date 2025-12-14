<?php include "Views/Templates/header.php"; ?>
<div class="card mt-4">
  <div class="card-header bg-dark text-white">
    <i class="fas fa-building me-2"></i>Datos de la Empresa
  </div>
  <div class="card-body">
    <form id="frmEmpresa">
        <div class="row">
            <div class="col-md-6">
                <div class="form-floating mb-3">
                    <input type="hidden" name="id" id="id" class="form-control" value="<?php echo $data['empresa']['id']; ?>">
                    <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre de la Empresa" value="<?php echo $data['empresa']['nombre']; ?>">
                    <label for="nombre">Nombre de la Empresa</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-floating mb-3">
                    <input type="text" name="rif" id="rif" class="form-control" placeholder="rif" value="<?php echo $data['empresa']['rif']; ?>">
                    <label for="rif">R.I.F</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-floating mb-3">
                    <input type="text" name="telefono" id="telefono" class="form-control" placeholder="Telefono" value="<?php echo $data['empresa']['telefono']; ?>">
                    <label for="telefono">Telefono</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-floating mb-3">
                    <input type="text" name="direccion" id="direccion" class="form-control" placeholder="Direccion" value="<?php echo $data['empresa']['direccion']; ?>">
                    <label for="direccion">Direccion</label>
                </div>
            </div>
            
            <!-- Sección de Tasas -->
            <div class="col-12">
                <div class="card border-primary mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-dollar-sign text-primary me-2"></i>Tasas de Cambio
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <input type="text" name="tasa" id="tasa" class="form-control" placeholder="Tasa del Dia" value="<?php echo $data['tasa']['factor']; ?>">
                                    <label for="tasa">Tasa del Día (Paralelo)</label>
                                    <div class="rate-info" id="tasa-info">Valor actual: <?php echo $data['tasa']['factor']; ?> BsD/$</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <input type="text" name="tasa_bcv" id="tasa_bcv" class="form-control" placeholder="Tasa del Dia (BCV)" value="<?php echo $data['tasa']['factor_bcv']; ?>">
                                    <label for="tasa_bcv">Tasa BCV (Oficial)</label>
                                    <div class="rate-info" id="tasa-bcv-info">Valor actual: <?php echo $data['tasa']['factor_bcv']; ?> BsD/$</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" name="mensaje" id="mensaje" placeholder="Mensaje" rows="3"><?php echo $data['empresa']['mensaje']; ?></textarea>
                                    <label for="mensaje">Mensaje para Tickets</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="btn-group-custom mt-3">
                            <button type="button" class="btn btn-success" id="btnActualizarTasas" onclick="actualizarTasasAutomatically()">
                                <i class="fas fa-sync-alt me-1"></i> Actualizar Tasas Automáticamente
                            </button>
                            <button type="button" class="btn btn-primary btn-disabled" id="btnModificarEmpresa" onclick="modificarEmpresa()" disabled>
                                <i class="fas fa-save me-1"></i> Guardar Cambios
                            </button>
                        </div>
                        
                        <!-- Estado de la actualización -->
                        <div id="estado-actualizacion" class="mt-3" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
  </div>
</div>

<script>
// Variables para almacenar los valores originales
let valoresOriginales = {
    nombre: '<?php echo $data['empresa']['nombre']; ?>',
    rif: '<?php echo $data['empresa']['rif']; ?>',
    telefono: '<?php echo $data['empresa']['telefono']; ?>',
    direccion: '<?php echo $data['empresa']['direccion']; ?>',
    tasa: '<?php echo $data['tasa']['factor']; ?>',
    tasa_bcv: '<?php echo $data['tasa']['factor_bcv']; ?>',
    mensaje: `<?php echo $data['empresa']['mensaje']; ?>`
};

// Variable para controlar timeouts de mensajes
let timeoutMensaje = null;

// Función para mostrar mensaje temporal
function mostrarMensajeTemporal(mensaje, tipo = 'info', duracion = 1000) {
    const estadoDiv = document.getElementById('estado-actualizacion');
    
    // Cancelar timeout anterior si existe
    if (timeoutMensaje) {
        clearTimeout(timeoutMensaje);
        timeoutMensaje = null;
    }
    
    // Mostrar mensaje
    estadoDiv.innerHTML = `
        <div class="alert alert-${tipo} alert-auto-hide" role="alert">
            <i class="fas fa-${tipo === 'success' ? 'check-circle' : tipo === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${mensaje}
        </div>
    `;
    estadoDiv.style.display = 'block';
    
    // Configurar timeout para ocultar después de la duración especificada
    timeoutMensaje = setTimeout(() => {
        estadoDiv.style.display = 'none';
        estadoDiv.innerHTML = '';
        timeoutMensaje = null;
    }, duracion);
}

// Función para verificar si hay cambios en los campos
function verificarCambios() {
    const btnGuardar = document.getElementById('btnModificarEmpresa');
    let hayCambios = false;
    
    // Verificar cada campo
    const campos = ['nombre', 'rif', 'telefono', 'direccion', 'tasa', 'tasa_bcv', 'mensaje'];
    
    campos.forEach(campo => {
        const elemento = document.getElementById(campo);
        if (elemento) {
            const valorActual = elemento.value;
            const valorOriginal = valoresOriginales[campo];
            
            if (valorActual !== valorOriginal) {
                hayCambios = true;
                elemento.classList.add('field-changed');
            } else {
                elemento.classList.remove('field-changed');
            }
        }
    });
    
    // Habilitar o deshabilitar el botón según si hay cambios
    if (hayCambios) {
        btnGuardar.disabled = false;
        btnGuardar.classList.remove('btn-disabled');
    } else {
        btnGuardar.disabled = true;
        btnGuardar.classList.add('btn-disabled');
    }
    
    return hayCambios;
}

// Función para actualizar las tasas automáticamente desde la API
function actualizarTasasAutomatically() {
    const btnActualizar = document.getElementById('btnActualizarTasas');
    
    // Mostrar estado de carga
    btnActualizar.disabled = true;
    btnActualizar.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Actualizando...';
    btnActualizar.classList.add('btn-loading');
    
    // Hacer la petición a la API
    fetch('https://ve.dolarapi.com/v1/dolares')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta de la API');
            }
            return response.json();
        })
        .then(data => {
            let tasaOficial = null;
            let tasaParalelo = null;
            
            // Buscar las tasas en la respuesta
            data.forEach(item => {
                if (item.nombre === 'Oficial') {
                    tasaOficial = item.promedio;
                } else if (item.nombre === 'Paralelo') {
                    tasaParalelo = item.promedio;
                }
            });
            
            // Actualizar los campos si se encontraron las tasas
            if (tasaOficial && tasaParalelo) {
                const nuevaTasaBcv = parseFloat(tasaOficial).toFixed(2);
                const nuevaTasa = parseFloat(tasaParalelo).toFixed(2);
                
                // Solo actualizar si los valores son diferentes
                if (nuevaTasaBcv !== valoresOriginales.tasa_bcv || nuevaTasa !== valoresOriginales.tasa) {
                    document.getElementById('tasa_bcv').value = nuevaTasaBcv;
                    document.getElementById('tasa').value = nuevaTasa;
                    
                    // Actualizar la información mostrada
                    document.getElementById('tasa-bcv-info').textContent = `Nuevo valor: ${nuevaTasaBcv} BsD/$`;
                    document.getElementById('tasa-info').textContent = `Nuevo valor: ${nuevaTasa} BsD/$`;
                    
                    // Añadir efectos visuales
                    document.getElementById('tasa_bcv').classList.add('rate-updated');
                    document.getElementById('tasa').classList.add('rate-updated');
                    
                    // Verificar cambios después de actualizar
                    setTimeout(verificarCambios, 100);
                    
                    // Mostrar mensaje de éxito por 1 segundo
                    mostrarMensajeTemporal('Tasas actualizadas correctamente. No olvide guardar los cambios.', 'success', 1000);
                    
                } else {
                    // Mostrar mensaje informativo por 1 segundo
                    mostrarMensajeTemporal('Las tasas ya están actualizadas. No hay cambios que guardar.', 'info', 1000);
                }
                
            } else {
                throw new Error('No se encontraron las tasas Oficial y Paralelo en la respuesta');
            }
        })
        .catch(error => {
            console.error('Error al obtener las tasas:', error);
            
            // Mostrar mensaje de error por 1 segundo
            mostrarMensajeTemporal(`Error al obtener las tasas: ${error.message}. Por favor, intente nuevamente.`, 'danger', 1000);
            
            // Añadir efecto de error a los campos
            document.getElementById('tasa_bcv').classList.add('rate-error');
            document.getElementById('tasa').classList.add('rate-error');
        })
        .finally(() => {
            // Restaurar el botón
            btnActualizar.disabled = false;
            btnActualizar.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Actualizar Tasas Automáticamente';
            btnActualizar.classList.remove('btn-loading');
            
            // Remover efectos después de 1 segundo
            setTimeout(() => {
                document.getElementById('tasa_bcv').classList.remove('rate-updated', 'rate-error');
                document.getElementById('tasa').classList.remove('rate-updated', 'rate-error');
            }, 1000);
        });
}

// Función para modificar la empresa
function modificarEmpresa() {
    if (!verificarCambios()) {
        mostrarMensajeTemporal('No hay cambios que guardar.', 'info', 1000);
        return;
    }
    
    // Aquí va tu lógica existente para guardar los datos de la empresa
    console.log('Guardando datos de la empresa...');
    
    // Ejemplo de implementación:
    const formData = new FormData(document.getElementById('frmEmpresa'));
    
    // Mostrar indicador de guardado
    const btnGuardar = document.getElementById('btnModificarEmpresa');
    const textoOriginal = btnGuardar.innerHTML;
    btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Guardando...';
    btnGuardar.disabled = true;
    
    // Aquí iría tu llamada AJAX para guardar los datos
    /*
    fetch('<?php echo base_url; ?>Administracion/actualizar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito
            mostrarMensajeTemporal('Datos actualizados correctamente', 'success', 1000);
            
            // Actualizar los valores originales con los nuevos valores
            actualizarValoresOriginales();
            
            // Actualizar la información mostrada
            document.getElementById('tasa-bcv-info').textContent = `Valor guardado: ${document.getElementById('tasa_bcv').value} BsD/$`;
            document.getElementById('tasa-info').textContent = `Valor guardado: ${document.getElementById('tasa').value} BsD/$`;
            
        } else {
            mostrarMensajeTemporal('Error al guardar los datos', 'danger', 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensajeTemporal('Error de conexión', 'danger', 1000);
    })
    .finally(() => {
        btnGuardar.innerHTML = textoOriginal;
        verificarCambios();
    });
    */
    
    // Simulación de guardado exitoso (eliminar esto cuando implementes la lógica real)
    setTimeout(() => {
        // Actualizar los valores originales
        actualizarValoresOriginales();
        
        // Mostrar mensaje de éxito
        mostrarMensajeTemporal('Datos actualizados correctamente', 'success', 1000);
        
        // Actualizar información mostrada
        document.getElementById('tasa-bcv-info').textContent = `Valor guardado: ${document.getElementById('tasa_bcv').value} BsD/$`;
        document.getElementById('tasa-info').textContent = `Valor guardado: ${document.getElementById('tasa').value} BsD/$`;
        
        // Restaurar botón
        btnGuardar.innerHTML = textoOriginal;
        verificarCambios();
        
    }, 1000);
}

// Función para actualizar los valores originales después de guardar
function actualizarValoresOriginales() {
    valoresOriginales = {
        nombre: document.getElementById('nombre').value,
        rif: document.getElementById('rif').value,
        telefono: document.getElementById('telefono').value,
        direccion: document.getElementById('direccion').value,
        tasa: document.getElementById('tasa').value,
        tasa_bcv: document.getElementById('tasa_bcv').value,
        mensaje: document.getElementById('mensaje').value
    };
    
    // Remover estilos de campos cambiados
    const campos = ['nombre', 'rif', 'telefono', 'direccion', 'tasa', 'tasa_bcv', 'mensaje'];
    campos.forEach(campo => {
        const elemento = document.getElementById(campo);
        if (elemento) {
            elemento.classList.remove('field-changed');
        }
    });
}

// Inicialización cuando el documento está listo
document.addEventListener('DOMContentLoaded', function() {
    // Validación de campos numéricos para las tasas
    const tasaInput = document.getElementById('tasa');
    const tasaBcvInput = document.getElementById('tasa_bcv');
    
    function validarNumero(input) {
        input.addEventListener('input', function(e) {
            // Permitir solo números y punto decimal
            e.target.value = e.target.value.replace(/[^0-9.]/g, '');
            
            // Validar que solo haya un punto decimal
            const puntos = e.target.value.split('.').length - 1;
            if (puntos > 1) {
                e.target.value = e.target.value.substring(0, e.target.value.lastIndexOf('.'));
            }
            
            // Limitar a 2 decimales
            if (e.target.value.includes('.')) {
                const partes = e.target.value.split('.');
                if (partes[1].length > 2) {
                    e.target.value = partes[0] + '.' + partes[1].substring(0, 2);
                }
            }
            
            // Verificar cambios después de cada entrada
            verificarCambios();
        });
    }
    
    if (tasaInput) validarNumero(tasaInput);
    if (tasaBcvInput) validarNumero(tasaBcvInput);
    
    // Agregar event listeners para verificar cambios en todos los campos
    const campos = ['nombre', 'rif', 'telefono', 'direccion', 'tasa', 'tasa_bcv', 'mensaje'];
    campos.forEach(campo => {
        const elemento = document.getElementById(campo);
        if (elemento) {
            elemento.addEventListener('input', verificarCambios);
            elemento.addEventListener('change', verificarCambios);
        }
    });
    
    // Mostrar información de la última actualización
    const lastUpdate = new Date().toLocaleDateString('es-VE');
    document.getElementById('tasa-info').textContent += ` | Última actualización: ${lastUpdate}`;
    document.getElementById('tasa-bcv-info').textContent += ` | Última actualización: ${lastUpdate}`;
    
    // Verificar estado inicial
    verificarCambios();
});
</script>

<?php include "Views/Templates/footer.php"; ?>