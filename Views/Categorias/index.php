<?php include "Views/Templates/header.php"; ?>

<!-- CSS específico para los iconos de ordenamiento -->
<style>
/* Reset completo para los iconos de DataTables */
#tblCategorias thead th.sorting:after,
#tblCategorias thead th.sorting_asc:after,
#tblCategorias thead th.sorting_desc:after {
    display: none !important;
    opacity: 0 !important;
    content: '' !important;
}

/* Iconos de ordenamiento personalizados */
#tblCategorias thead th {
    position: relative;
    padding-right: 25px !important;
    cursor: pointer;
}

#tblCategorias thead th.sorting::before {
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

#tblCategorias thead th.sorting_asc::before {
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

#tblCategorias thead th.sorting_desc::before {
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
#tblCategorias thead th:not(.sorting):not(.sorting_asc):not(.sorting_desc)::before {
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
</style>

<div class="card mt-4">
    <div class="card-header card-header-primary fw-bold">
        Categorias
    </div>
    <div class="card-body">
        <button class="btn btn-primary mb-2" type="button" onclick="frmCategoria();"><i class="fas fa-plus"></i></button>
        <div class="table-responsive">
            <table class="table table-light table-bordered table-hover" id="tblCategorias">
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
<div class="modal fade" id="nuevo_categoria" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="title">Nueva Categoria</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frmCategoria">
                    <div class="form-floating mb-3">
                        <input type="hidden" id="id" name="id">
                        <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Nombre de la Categoria">
                        <label for="nombre">Nombre</label>
                    </div>
                    <button class="btn btn-primary" type="button" onclick="registrarCat(event);" id="btnAccion">Registrar</button>
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script para inicializar DataTable -->
<script>
function frmCategoria(id = '') {
    // Limpiar formulario
    document.getElementById('frmCategoria').reset();
    document.getElementById('id').value = '';
    
    if (id) {
        // Modo edición
        document.getElementById('title').textContent = 'Editar Categoria';
        document.getElementById('btnAccion').textContent = 'Actualizar';
        // Aquí iría la lógica para cargar los datos de la categoría
        // cargarDatosCategoria(id);
    } else {
        // Modo nuevo
        document.getElementById('title').textContent = 'Nueva Categoria';
        document.getElementById('btnAccion').textContent = 'Registrar';
    }
    
    // Mostrar modal
    var modal = new bootstrap.Modal(document.getElementById('nuevo_categoria'));
    modal.show();
}

function registrarCat(event) {
    event.preventDefault();
    // Aquí va tu lógica para registrar la categoría
    console.log('Registrando categoría...');
    
    // Ejemplo básico de validación
    const nombre = document.getElementById('nombre').value;
    if (!nombre) {
        alert('El nombre de la categoría es obligatorio');
        return;
    }
    
    // Aquí iría tu llamada AJAX para guardar la categoría
    /*
    $.post('<?php echo base_url; ?>Categorias/registrar', 
        $('#frmCategoria').serialize(), 
        function(data) {
            if (data.estado) {
                // Recargar la tabla
                $('#tblCategorias').DataTable().ajax.reload();
                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('nuevo_categoria')).hide();
                // Mostrar mensaje de éxito
                alert('Categoría guardada correctamente');
            } else {
                alert('Error al guardar la categoría');
            }
        }
    );
    */
}
</script>

<?php include "Views/Templates/footer.php"; ?>