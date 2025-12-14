<?php include "Views/Templates/header.php"; ?>
<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <h4 class="mb-0">Nueva Compra</h4>
    </div>
    <div class="card">
        <div class="card-body">
            <form id="frmCompra">
                <!-- Primera fila: Código y Descripción -->
                <div class="row g-2 mb-3">
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="form-group">
                            <label for="codigo" class="form-label small">
                                <i class="fas fa-barcode"></i> Código
                            </label>
                            <input type="hidden" name="id" id="id">
                            <input type="text" name="codigo" id="codigo" class="form-control form-control-sm" 
                                   placeholder="Código" onkeyup="buscarCodigoCompras(event)">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-8 col-lg-5">
                        <div class="form-group">
                            <label for="descripcion" class="form-label small">
                                <i class="fas fa-monument"></i> Descripción
                            </label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control form-control-sm" 
                                   placeholder="Descripción del producto" disabled>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-md-4 col-lg-2">
                        <div class="form-group">
                            <label for="cantidad" class="form-label small">
                                <i class="fas fa-list-ol"></i> Cantidad
                            </label>
                            <input type="number" name="cantidad" id="cantidad" class="form-control form-control-sm" 
                                   placeholder="Cant" onchange="calcularPrecioCompra(event)" 
                                   onkeyup="calcularPrecioCompra(event)" disabled>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-md-4 col-lg-2">
                        <div class="form-group">
                            <label for="precio" class="form-label small">
                                <i class="fas fa-dollar-sign"></i> Precio $
                            </label>
                            <input type="text" name="precio" id="precio" class="form-control form-control-sm" 
                                   placeholder="Precio $" disabled>
                        </div>
                    </div>
                </div>
                
                <!-- Segunda fila: Precio BsD y Subtotales -->
                <div class="row g-2">
                    <div class="col-6 col-sm-4 col-md-4 col-lg-2">
                        <div class="form-group">
                            <label for="precio_bolos" class="form-label small">
                                <i class="fas fa-money-bill"></i> Precio BsD
                            </label>
                            <input type="text" name="precio_bolos" id="precio_bolos" class="form-control form-control-sm" 
                                   placeholder="Precio BsD" disabled>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-md-4 col-lg-2">
                        <div class="form-group">
                            <label for="sub_total" class="form-label small">
                                <i class="fas fa-dollar-sign"></i> Sub Total $
                            </label>
                            <input type="text" name="sub_total" id="sub_total" class="form-control form-control-sm" 
                                   placeholder="0.00" disabled>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-md-4 col-lg-2">
                        <div class="form-group">
                            <label for="sub_total_bolos" class="form-label small">
                                <i class="fas fa-wallet"></i> Sub Total BsD
                            </label>
                            <input type="text" name="sub_total_bolos" id="sub_total_bolos" 
                                   class="form-control form-control-sm" placeholder="0.00" disabled>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla responsive -->
    <div class="table-responsive mt-3">
        <table class="table table-light table-bordered table-hover mb-0">
            <thead class="thead-dark">
                <tr>
                    <th class="d-none d-sm-table-cell">Id</th>
                    <th>Descripción</th>
                    <th class="text-nowrap">Cant</th>
                    <th class="d-none d-md-table-cell">Precio $</th>
                    <th class="d-none d-md-table-cell">Precio BsD</th>
                    <th class="d-none d-lg-table-cell">Sub Total $</th>
                    <th class="d-none d-lg-table-cell">Sub Total BsD</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="tblDetalleCompra">
            </tbody>
        </table>
    </div>
    
    <div class="card-footer bg-light">
        <div class="row align-items-end g-3 justify-content-end">
            <!-- Totales y botón -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="row g-2 justify-content-end">
                    <div class="col-6 col-sm-6 col-md-6">
                        <div class="form-group text-md-end">
                            <label for="total" class="form-label small fw-bold">
                                <i class="fas fa-dollar-sign d-none d-sm-inline"></i> Total $
                            </label>
                            <input type="text" name="total" id="total" class="form-control form-control-sm text-end" 
                                   placeholder="0.00" disabled>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-md-6">
                        <div class="form-group text-md-end">
                            <label for="total_bolos" class="form-label small fw-bold">Total BsD</label>
                            <input type="text" name="total_bolos" id="total_bolos" 
                                   class="form-control form-control-sm text-end" placeholder="0.00" disabled>
                        </div>
                    </div>
                    <div class="col-12 col-md-12">
                        <button type="button" class="btn btn-primary btn-sm w-100 mt-2" onclick="generarCompra()">
                            <i class="fas fa-shopping-cart me-1"></i> Generar Compra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles en móvil (opcional) -->
<div class="modal fade" id="detalleCompraMovilModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detalleCompraMovilContent">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos adicionales para mejorar la responsividad */
@media (max-width: 576px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    .form-label {
        margin-bottom: 0.25rem;
    }
    .card-body {
        padding: 0.75rem;
    }
    .card-footer {
        padding: 0.75rem;
    }
}

/* Mejorar la legibilidad en pantallas pequeñas */
.form-control-sm {
    min-height: calc(1.5em + 0.5rem + 2px);
}

/* Asegurar que los inputs no se desborden */
input[type="text"], input[type="number"], select {
    max-width: 100%;
}

/* Estilos para la tabla en móviles */
@media (max-width: 768px) {
    .table th, .table td {
        padding: 0.5rem;
    }
}

/* Mejorar visualización de la tabla en móviles */
@media (max-width: 576px) {
    .table-responsive {
        border: 1px solid #dee2e6;
    }
}
</style>

<script>
// Función opcional para ver detalles en móvil
function verDetalleCompraMovil(id) {
    // Implementar lógica para mostrar detalles de compra en modal
    $('#detalleCompraMovilModal').modal('show');
}

// Función para ajustar dinámicamente la interfaz
function ajustarInterfazCompra() {
    const anchoPantalla = window.innerWidth;
    const tabla = document.getElementById('tblDetalleCompra');
    
    if (anchoPantalla < 768) {
        // Ocultar columnas menos críticas en móviles
        document.querySelectorAll('.d-md-table-cell').forEach(col => {
            col.style.display = 'none';
        });
    }
}
</script>

<?php include "Views/Templates/footer.php"; ?>