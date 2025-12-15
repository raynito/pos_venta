<?php include "Views/Templates/header.php"; ?>

<!-- CSS específico para los iconos de ordenamiento -->
<style>
/* Reset completo para los iconos de DataTables */
#tblCajas thead th.sorting:after,
#tblCajas thead th.sorting_asc:after,
#tblCajas thead th.sorting_desc:after {
    display: none !important;
    opacity: 0 !important;
    content: '' !important;
}

/* Iconos de ordenamiento personalizados */
#tblCajas thead th {
    position: relative;
    padding-right: 25px !important;
    cursor: pointer;
}

#tblCajas thead th.sorting::before {
    content: "↕";
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 14px;
    opacity: 0.4;
    font-weight: normal;
    font-family: Arial, sans-serif;
}

#tblCajas thead th.sorting_asc::before {
    content: "▴";
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 16px;
    opacity: 1;
    color: #0d6efd;
    font-weight: bold;
    font-family: Arial, sans-serif;
}

#tblCajas thead th.sorting_desc::before {
    content: "▾";
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 16px;
    opacity: 1;
    color: #0d6efd;
    font-weight: bold;
    font-family: Arial, sans-serif;
}

/* Asegurar que las columnas no ordenables no tengan iconos */
#tblCajas thead th:not(.sorting):not(.sorting_asc):not(.sorting_desc)::before {
    display: none !important;
}

/* Mejorar la apariencia de DataTables */
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
}

.dataTables_wrapper .dataTables_length select {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.375rem 2rem 0.375rem 0.75rem;
}

/* Estilos para el modal */
#nuevoCaja .modal-dialog {
    max-width: 500px;
}

/* Estilos para inputs en el modal */
.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    color: #0d6efd;
    font-weight: 500;
}
</style>

<div class="card mt-4">
    <div class="card-header card-header-primary fw-bold">
        Cajas
    </div>
    <div class="card-body">
        <button class="btn btn-primary mb-2" type="button" onclick="frmCaja();">
            <i class="fas fa-plus me-1"></i> Nueva Caja
        </button>
        <div class="table-responsive">
            <table class="table table-light table-bordered table-hover" id="tblCajas">
                <thead class="thead-dark">
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="nuevoCaja" tabindex="-1" aria-labelledby="my_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="title">
                    <i class="fas fa-cash-register me-2"></i><span id="title-text">Nueva Caja</span>
                </h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" id="frmCaja">
                    <div class="form-floating mb-3">
                        <input type="hidden" id="id" name="id">
                        <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Nombre de la caja">
                        <label for="nombre">Nombre de la Caja</label>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" onclick="registrarCaja(event);" id="btnAccion">
                            <i class="fas fa-save me-1"></i> <span id="btn-text">Registrar</span>
                        </button>
                        <button class="btn btn-danger" type="button" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script para inicializar DataTable -->
<script>
function frmCaja(id = '') {
    // Limpiar formulario
    document.getElementById('frmCaja').reset();
    document.getElementById('id').value = '';
    
    if (id) {
        // Modo edición
        document.getElementById('title-text').textContent = 'Editar Caja';
        document.getElementById('btn-text').textContent = 'Actualizar';
        // Aquí iría la lógica para cargar los datos de la caja
        // cargarDatosCaja(id);
    } else {
        // Modo nuevo
        document.getElementById('title-text').textContent = 'Nueva Caja';
        document.getElementById('btn-text').textContent = 'Registrar';
    }
    
    // Mostrar modal
    var modal = new bootstrap.Modal(document.getElementById('nuevoCaja'));
    modal.show();
}

function registrarCaja(event) {
    event.preventDefault();
    // Aquí va tu lógica para registrar la caja
    console.log('Registrando caja...');
    
    // Ejemplo básico de validación
    const nombre = document.getElementById('nombre').value;
    
    if (!nombre) {
        alert('El nombre de la caja es obligatorio');
        return;
    }
    
    // Validar longitud del nombre
    if (nombre.length > 50) {
        alert('El nombre de la caja no debe exceder los 50 caracteres');
        return;
    }
    
    // Aquí iría tu llamada AJAX para guardar la caja
    /*
    $.post('<?php echo base_url; ?>Cajas/registrar', 
        $('#frmCaja').serialize(), 
        function(data) {
            if (data.estado) {
                // Recargar la tabla
                $('#tblCajas').DataTable().ajax.reload();
                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('nuevoCaja')).hide();
                // Mostrar mensaje de éxito
                alert('Caja guardada correctamente');
            } else {
                alert('Error al guardar la caja');
            }
        }
    );
    */
}

// Función para formatear el nombre de la caja (primera letra mayúscula)
document.addEventListener('DOMContentLoaded', function() {
    const nombreInput = document.getElementById('nombre');
    if (nombreInput) {
        nombreInput.addEventListener('blur', function(e) {
            // Capitalizar la primera letra de cada palabra
            if (e.target.value) {
                e.target.value = e.target.value.replace(/\w\S*/g, function(txt) {
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
            }
        });
        
        // Mostrar contador de caracteres
        nombreInput.addEventListener('focus', function() {
            if (!document.getElementById('char-counter-caja')) {
                const counter = document.createElement('small');
                counter.id = 'char-counter-caja';
                counter.className = 'form-text text-muted mt-1';
                counter.textContent = 'Máximo 50 caracteres';
                nombreInput.parentNode.appendChild(counter);
            }
        });
        
        // Actualizar contador en tiempo real
        nombreInput.addEventListener('input', function(e) {
            const counter = document.getElementById('char-counter-caja');
            if (counter) {
                const remaining = 50 - e.target.value.length;
                counter.textContent = `${e.target.value.length}/50 caracteres`;
                counter.style.color = remaining < 10 ? '#dc3545' : '#6c757d';
            }
        });
    }
});
</script>

<?php include "Views/Templates/footer.php"; ?>