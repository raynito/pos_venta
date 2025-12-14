<?php include "Views/Templates/header.php"; ?>

<!-- CSS específico para los iconos de ordenamiento -->
<style>
/* Reset completo para los iconos de DataTables */
#tblMedidas thead th.sorting:after,
#tblMedidas thead th.sorting_asc:after,
#tblMedidas thead th.sorting_desc:after {
    display: none !important;
    opacity: 0 !important;
    content: '' !important;
}

/* Iconos de ordenamiento personalizados */
#tblMedidas thead th {
    position: relative;
    padding-right: 25px !important;
    cursor: pointer;
}

#tblMedidas thead th.sorting::before {
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

#tblMedidas thead th.sorting_asc::before {
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

#tblMedidas thead th.sorting_desc::before {
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
#tblMedidas thead th:not(.sorting):not(.sorting_asc):not(.sorting_desc)::before {
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
        Unidades de Medidas
    </div>
    <div class="card-body">
        <button class="btn btn-primary mb-2" type="button" onclick="frmMedida();">
            <i class="fas fa-plus me-1"></i> Nueva Medida
        </button>
        <div class="table-responsive">
            <table class="table table-light table-bordered table-hover" id="tblMedidas">
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
<div class="modal fade" id="nuevo_medida" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="title">
                    <i class="fas fa-ruler me-2"></i><span id="title-text">Nueva Medida</span>
                </h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frmMedida">
                    <div class="form-floating mb-3">
                        <input type="hidden" id="id" name="id">
                        <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Nombre de la Medida">
                        <label for="nombre">Nombre</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input id="nombre_corto" class="form-control" type="text" name="nombre_corto" placeholder="Nombre Corto">
                        <label for="nombre_corto">Nombre Corto</label>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" onclick="registrarMed(event);" id="btnAccion">
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
$(document).ready(function() {
    // Inicializar DataTable con configuración para los botones de ordenamiento
    $('#tblMedidas').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "columnDefs": [
            { 
                "targets": [0, 1, 2, 3], // Columnas ordenables: Id, Nombre, Nombre Corto, Estado
                "orderable": true 
            },
            { 
                "targets": [4], // Columna NO ordenable: Acciones
                "orderable": false 
            }
        ],
        "order": [[0, "desc"]], // Ordenar por ID descendente por defecto
        "responsive": true,
        "autoWidth": false,
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        "initComplete": function(settings, json) {
            // Forzar la actualización de los estilos después de la inicialización
            setTimeout(function() {
                $('th.sorting, th.sorting_asc, th.sorting_desc').css({
                    'position': 'relative',
                    'padding-right': '25px'
                });
            }, 100);
        }
    });

    // Observar cambios en el ordenamiento para actualizar iconos
    $('#tblMedidas').on('order.dt', function() {
        setTimeout(function() {
            $('th.sorting, th.sorting_asc, th.sorting_desc').css({
                'position': 'relative',
                'padding-right': '25px'
            });
        }, 50);
    });
});

function frmMedida(id = '') {
    // Limpiar formulario
    document.getElementById('frmMedida').reset();
    document.getElementById('id').value = '';
    
    if (id) {
        // Modo edición
        document.getElementById('title-text').textContent = 'Editar Medida';
        document.getElementById('btn-text').textContent = 'Actualizar';
        // Aquí iría la lógica para cargar los datos de la medida
        // cargarDatosMedida(id);
    } else {
        // Modo nuevo
        document.getElementById('title-text').textContent = 'Nueva Medida';
        document.getElementById('btn-text').textContent = 'Registrar';
    }
    
    // Mostrar modal
    var modal = new bootstrap.Modal(document.getElementById('nuevo_medida'));
    modal.show();
}

function registrarMed(event) {
    event.preventDefault();
    // Aquí va tu lógica para registrar la medida
    console.log('Registrando medida...');
    
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
    
    // Aquí iría tu llamada AJAX para guardar la medida
    /*
    $.post('<?php echo base_url; ?>Medidas/registrar', 
        $('#frmMedida').serialize(), 
        function(data) {
            if (data.estado) {
                // Recargar la tabla
                $('#tblMedidas').DataTable().ajax.reload();
                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('nuevo_medida')).hide();
                // Mostrar mensaje de éxito
                alert('Medida guardada correctamente');
            } else {
                alert('Error al guardar la medida');
            }
        }
    );
    */
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