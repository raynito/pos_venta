<?php include "Views/Templates/header.php"; ?>

<!-- CSS específico para los iconos de ordenamiento -->
<style>
/* Reset completo para los iconos de DataTables */
#tblUsuarios thead th.sorting:after,
#tblUsuarios thead th.sorting_asc:after,
#tblUsuarios thead th.sorting_desc:after {
    display: none !important;
    opacity: 0 !important;
    content: '' !important;
}

/* Iconos de ordenamiento personalizados */
#tblUsuarios thead th {
    position: relative;
    padding-right: 25px !important;
    cursor: pointer;
}

#tblUsuarios thead th.sorting::before {
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

#tblUsuarios thead th.sorting_asc::before {
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

#tblUsuarios thead th.sorting_desc::before {
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
#tblUsuarios thead th:not(.sorting):not(.sorting_asc):not(.sorting_desc)::before {
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

/* Estilos para inputs de contraseña */
.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    z-index: 10;
}

.form-floating {
    position: relative;
}

.form-floating .form-control {
    padding-right: 40px;
}

/* Estilos para el select de caja */
#caja {
    cursor: pointer;
}
</style>

<div class="card mt-4">
    <div class="card-header card-header-primary fw-bold">
        Usuarios
    </div>
    <div class="card-body">
        <button class="btn btn-primary mb-2" type="button" onclick="frmUsuario();">
            <i class="fas fa-plus me-1"></i> Nuevo Usuario
        </button>
        <div class="table-responsive">
            <table class="table table-light table-bordered table-hover" id="tblUsuarios">
                <thead class="thead-dark">
                    <tr>
                        <th>Id</th>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Caja</th>
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
<div class="modal fade" id="nuevo_usuario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="title">
                    <i class="fas fa-user-plus me-2"></i><span id="title-text">Nuevo Usuario</span>
                </h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frmUsuario">
                    <div class="form-floating mb-3">
                        <input type="hidden" id="id" name="id">
                        <input id="usuario" class="form-control" type="text" name="usuario" placeholder="Usuario">
                        <label for="usuario">Usuario</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Nombre del usuario">
                        <label for="nombre">Nombre</label>
                    </div>
                    <div class="row" id="claves">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input id="clave" class="form-control" type="password" name="clave" placeholder="Contraseña">
                                <button type="button" class="password-toggle" onclick="togglePassword('clave')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <label for="clave">Contraseña</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input id="confirmar" class="form-control" type="password" name="confirmar" placeholder="Confirmar contraseña">
                                <button type="button" class="password-toggle" onclick="togglePassword('confirmar')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <label for="confirmar">Confirmar Contraseña</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <select id="caja" class="form-control" name="caja">
                            <?php foreach ($data['cajas'] as $row) { ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['caja']; ?></option>
                            <?php } ?>
                        </select>
                        <label for="caja">Caja</label>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" onclick="registrarUser(event);" id="btnAccion">
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
    $('#tblUsuarios').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "columnDefs": [
            { 
                "targets": [0, 1, 2, 3, 4], // Columnas ordenables: Id, Usuario, Nombre, Caja, Estado
                "orderable": true 
            },
            { 
                "targets": [5], // Columna NO ordenable: Acciones
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
    $('#tblUsuarios').on('order.dt', function() {
        setTimeout(function() {
            $('th.sorting, th.sorting_asc, th.sorting_desc').css({
                'position': 'relative',
                'padding-right': '25px'
            });
        }, 50);
    });
});

function frmUsuario(id = '') {
    // Limpiar formulario
    document.getElementById('frmUsuario').reset();
    document.getElementById('id').value = '';
    
    // Mostrar campos de contraseña por defecto
    document.getElementById('claves').style.display = 'flex';
    
    if (id) {
        // Modo edición
        document.getElementById('title-text').textContent = 'Editar Usuario';
        document.getElementById('btn-text').textContent = 'Actualizar';
        // Ocultar campos de contraseña en edición (opcional)
        // document.getElementById('claves').style.display = 'none';
        // Aquí iría la lógica para cargar los datos del usuario
        // cargarDatosUsuario(id);
    } else {
        // Modo nuevo
        document.getElementById('title-text').textContent = 'Nuevo Usuario';
        document.getElementById('btn-text').textContent = 'Registrar';
        document.getElementById('claves').style.display = 'flex';
    }
    
    // Mostrar modal
    var modal = new bootstrap.Modal(document.getElementById('nuevo_usuario'));
    modal.show();
}

function registrarUser(event) {
    event.preventDefault();
    // Aquí va tu lógica para registrar el usuario
    console.log('Registrando usuario...');
    
    // Ejemplo básico de validación
    const usuario = document.getElementById('usuario').value;
    const nombre = document.getElementById('nombre').value;
    const clave = document.getElementById('clave').value;
    const confirmar = document.getElementById('confirmar').value;
    const caja = document.getElementById('caja').value;
    
    if (!usuario || !nombre || !caja) {
        alert('El usuario, nombre y caja son obligatorios');
        return;
    }
    
    // Validar contraseñas si están visibles
    if (document.getElementById('claves').style.display !== 'none') {
        if (!clave || !confirmar) {
            alert('Ambos campos de contraseña son obligatorios');
            return;
        }
        
        if (clave !== confirmar) {
            alert('Las contraseñas no coinciden');
            return;
        }
        
        if (clave.length < 6) {
            alert('La contraseña debe tener al menos 6 caracteres');
            return;
        }
    }
    
    // Aquí iría tu llamada AJAX para guardar el usuario
    /*
    $.post('<?php echo base_url; ?>Usuarios/registrar', 
        $('#frmUsuario').serialize(), 
        function(data) {
            if (data.estado) {
                // Recargar la tabla
                $('#tblUsuarios').DataTable().ajax.reload();
                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('nuevo_usuario')).hide();
                // Mostrar mensaje de éxito
                alert('Usuario guardado correctamente');
            } else {
                alert('Error al guardar el usuario');
            }
        }
    );
    */
}

// Función para mostrar/ocultar contraseña
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggleButton = field.nextElementSibling;
    const icon = toggleButton.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Función para formatear el usuario (sin espacios, solo minúsculas)
document.addEventListener('DOMContentLoaded', function() {
    const usuarioInput = document.getElementById('usuario');
    if (usuarioInput) {
        usuarioInput.addEventListener('input', function(e) {
            // Eliminar espacios y convertir a minúsculas
            e.target.value = e.target.value.replace(/\s/g, '').toLowerCase();
        });
    }
    
    // Validación en tiempo real de contraseñas
    const claveInput = document.getElementById('clave');
    const confirmarInput = document.getElementById('confirmar');
    
    function validatePasswords() {
        if (claveInput && confirmarInput) {
            if (claveInput.value !== confirmarInput.value && confirmarInput.value !== '') {
                confirmarInput.style.borderColor = '#dc3545';
            } else {
                confirmarInput.style.borderColor = '#ced4da';
            }
        }
    }
    
    if (claveInput) claveInput.addEventListener('input', validatePasswords);
    if (confirmarInput) confirmarInput.addEventListener('input', validatePasswords);
});
</script>

<?php include "Views/Templates/footer.php"; ?>