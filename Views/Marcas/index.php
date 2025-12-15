<?php include "Views/Templates/header.php"; ?>

<!-- CSS específico para los iconos de ordenamiento -->
<style>
/* Reset completo para los iconos de DataTables */
#tblMarcas thead th.sorting:after,
#tblMarcas thead th.sorting_asc:after,
#tblMarcas thead th.sorting_desc:after {
    display: none !important;
    opacity: 0 !important;
    content: '' !important;
}

/* Iconos de ordenamiento personalizados */
#tblMarcas thead th {
    position: relative;
    padding-right: 25px !important;
    cursor: pointer;
}

#tblMarcas thead th.sorting::before {
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

#tblMarcas thead th.sorting_asc::before {
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

#tblMarcas thead th.sorting_desc::before {
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
#tblMarcas thead th:not(.sorting):not(.sorting_asc):not(.sorting_desc)::before {
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

/* Estilos para inputs en el modal */
.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    color: #0d6efd;
    font-weight: 500;
}
</style>

<div class="card mt-4">
    <div class="card-header card-header-primary fw-bold">
        Maestro de Marcas
    </div>
    <div class="card-body">
        <button class="btn btn-primary mb-2" type="button" onclick="frmMarca();">
            <i class="fas fa-plus me-1"></i> Nueva Marca
        </button>
        <div class="table-responsive">
            <table class="table table-light table-bordered table-hover" id="tblMarcas">
                <thead class="thead-dark">
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Nombre Corto</th>
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
<div class="modal fade" id="nuevo_marca" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="title">
                    <i class="fas fa-ruler me-2"></i><span id="title-text">Nueva Marca</span>
                </h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frmMarca">
                    <div class="form-floating mb-3">
                        <input type="hidden" id="id" name="id">
                        <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Nombre de la Marca">
                        <label for="nombre">Nombre</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input id="nombre_corto" class="form-control" type="text" name="nombre_corto" placeholder="Nombre Corto">
                        <label for="nombre_corto">Nombre Corto</label>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" onclick="registrarMar(event);" id="btnAccion">
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

<script>
function frmMarca(id = '') {
    // Limpiar formulario
    document.getElementById('frmMarca').reset();
    document.getElementById('id').value = '';
    
    if (id) {
        // Modo edición
        document.getElementById('title-text').textContent = 'Editar Marca';
        document.getElementById('btn-text').textContent = 'Actualizar';
        // Aquí iría la lógica para cargar los datos de la marca
        // cargarDatosMarca(id);
    } else {
        // Modo nuevo
        document.getElementById('title-text').textContent = 'Nueva Marca';
        document.getElementById('btn-text').textContent = 'Registrar';
    }
    
    // Mostrar modal
    var modal = new bootstrap.Modal(document.getElementById('nuevo_marca'));
    modal.show();
}

function registrarMar(event) {
    event.preventDefault();
    // Aquí va tu lógica para registrar la marca
    console.log('Registrando marca...');
    
    // Ejemplo básico de validación
    const nombre = document.getElementById('nombre').value;
    const nombreCorto = document.getElementById('nombre_corto').value;
    
    if (!nombre || !nombreCorto) {
        alert('El nombre y el nombre corto son obligatorios');
        return;
    }
    
    // Validar longitud del nombre corto
    if (nombreCorto.length > 10) {
        alert('El nombre corto no debe exceder los 10 caracteres');
        return;
    }
}

// Función para formatear el nombre corto (solo mayúsculas)
document.addEventListener('DOMContentLoaded', function() {
    const nombreCortoInput = document.getElementById('nombre_corto');
    if (nombreCortoInput) {
        nombreCortoInput.addEventListener('input', function(e) {
            // Convertir a mayúsculas automáticamente
            e.target.value = e.target.value.toUpperCase();
            
            // Limitar a 10 caracteres
            if (e.target.value.length > 10) {
                e.target.value = e.target.value.substring(0, 10);
            }
        });
        
        // Mostrar contador de caracteres
        nombreCortoInput.addEventListener('focus', function() {
            if (!document.getElementById('char-counter')) {
                const counter = document.createElement('small');
                counter.id = 'char-counter';
                counter.className = 'form-text text-muted';
                counter.textContent = 'Máximo 10 caracteres';
                nombreCortoInput.parentNode.appendChild(counter);
            }
        });
    }
});
</script>

<?php include "Views/Templates/footer.php"; ?>