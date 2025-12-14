<?php include "Views/Templates/header.php"; ?>

<!-- CSS específico para los iconos de ordenamiento -->
<style>
/* Reset completo para los iconos de DataTables */
#tblClientes thead th.sorting:after,
#tblClientes thead th.sorting_asc:after,
#tblClientes thead th.sorting_desc:after {
    display: none !important;
    opacity: 0 !important;
    content: '' !important;
}

/* Iconos de ordenamiento personalizados */
#tblClientes thead th {
    position: relative;
    padding-right: 25px !important;
    cursor: pointer;
}

#tblClientes thead th.sorting::before {
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

#tblClientes thead th.sorting_asc::before {
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

#tblClientes thead th.sorting_desc::before {
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
#tblClientes thead th:not(.sorting):not(.sorting_asc):not(.sorting_desc)::before {
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

/* Estilos para el textarea en el modal */
#direccion {
    min-height: 100px;
    resize: vertical;
}
</style>

<div class="card mt-4">
    <div class="card-header card-header-primary fw-bold">
        Clientes
    </div>
    <div class="card-body">
        <button class="btn btn-primary mb-2" type="button" onclick="frmCliente();">
            <i class="fas fa-plus me-1"></i> Nuevo Cliente
        </button>
        <div class="table-responsive">
            <table class="table table-light table-bordered table-hover" id="tblClientes">
                <thead class="thead-dark">
                    <tr>
                        <th>Id</th>
                        <th>RIF</th>
                        <th>Nombre</th>
                        <th>Telefono</th>
                        <th>Direccion</th>
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
<div class="modal fade" id="nuevo_cliente" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="title">
                    <i class="fas fa-user me-2"></i><span id="title-text">Nuevo Cliente</span>
                </h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frmCliente">
                    <div class="form-floating mb-3">
                        <input type="hidden" id="id" name="id">
                        <input id="rif" class="form-control" type="text" name="rif" placeholder="RIF">
                        <label for="rif">RIF</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Nombre del Cliente">
                        <label for="nombre">Nombre</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input id="telefono" class="form-control" type="text" name="telefono" placeholder="Telefono del Cliente">
                        <label for="telefono">Telefono</label>
                    </div>
                    <div class="form-floating mb-3">
                      <textarea class="form-control" name="direccion" id="direccion" placeholder="Direccion del Cliente" rows="3"></textarea>
                      <label for="direccion">Direccion</label>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" onclick="registrarCli(event);" id="btnAccion">
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
    $('#tblClientes').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "columnDefs": [
            { 
                "targets": [0, 1, 2, 3, 4, 5], // Columnas ordenables: Id, RIF, Nombre, Telefono, Direccion, Estado
                "orderable": true 
            },
            { 
                "targets": [6], // Columna NO ordenable: Acciones
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
    $('#tblClientes').on('order.dt', function() {
        setTimeout(function() {
            $('th.sorting, th.sorting_asc, th.sorting_desc').css({
                'position': 'relative',
                'padding-right': '25px'
            });
        }, 50);
    });
});

function frmCliente(id = '') {
    // Limpiar formulario
    document.getElementById('frmCliente').reset();
    document.getElementById('id').value = '';
    
    if (id) {
        // Modo edición
        document.getElementById('title-text').textContent = 'Editar Cliente';
        document.getElementById('btn-text').textContent = 'Actualizar';
        // Aquí iría la lógica para cargar los datos del cliente
        // cargarDatosCliente(id);
    } else {
        // Modo nuevo
        document.getElementById('title-text').textContent = 'Nuevo Cliente';
        document.getElementById('btn-text').textContent = 'Registrar';
    }
    
    // Mostrar modal
    var modal = new bootstrap.Modal(document.getElementById('nuevo_cliente'));
    modal.show();
}

function registrarCli(event) {
    event.preventDefault();
    // Aquí va tu lógica para registrar el cliente
    console.log('Registrando cliente...');
    
    // Ejemplo básico de validación
    const rif = document.getElementById('rif').value;
    const nombre = document.getElementById('nombre').value;
    
    if (!rif || !nombre) {
        alert('El RIF y el nombre son obligatorios');
        return;
    }
    
    // Aquí iría tu llamada AJAX para guardar el cliente
    /*
    $.post('<?php echo base_url; ?>Clientes/registrar', 
        $('#frmCliente').serialize(), 
        function(data) {
            if (data.estado) {
                // Recargar la tabla
                $('#tblClientes').DataTable().ajax.reload();
                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('nuevo_cliente')).hide();
                // Mostrar mensaje de éxito
                alert('Cliente guardado correctamente');
            } else {
                alert('Error al guardar el cliente');
            }
        }
    );
    */
}

// Función para formatear el RIF mientras se escribe
document.addEventListener('DOMContentLoaded', function() {
    const rifInput = document.getElementById('rif');
    if (rifInput) {
        rifInput.addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase();
            // Permitir solo letras, números y guiones
            value = value.replace(/[^A-Z0-9-]/g, '');
            e.target.value = value;
        });
    }
});
</script>

<?php include "Views/Templates/footer.php"; ?>