<?php include "Views/Templates/header.php"; ?>
<div class="card mt-4">
    <div class="card-header card-header-primary fw-bold">
        Productos
    </div>
    <div class="card-body">
        <button class="btn btn-primary mb-2" type="button" onclick="frmProducto();"><i class="fas fa-plus"></i></button>
        <div class="table-responsive">
            <table class="table table-light table-bordered table-hover" id="tblProductos">
                <thead class="thead-dark">
                    <tr>
                        <th>Id</th>
                        <th>Foto</th>
                        <th>Codigo</th>
                        <th>Descripcion</th>
                        <th>Precio de Venta</th>
                        <th>Precio de Venta BsD</th>
                        <th>Stock</th>
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
<div class="modal fade" id="nuevo_producto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="title">Nuevo Producto</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frmProducto" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-floating mb-3">
                                <input type="hidden" id="id" name="id">
                                <input id="codigo" class="form-control" type="text" name="codigo" placeholder="Codigo de Barras">
                                <label for="codigo">Codigo de Barras</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input id="descripcion" class="form-control" type="text" name="descripcion" placeholder="Descripcion del Producto">
                                <label for="descripcion">Descripcion</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating mb-3">
                                <select id="medida" class="form-control" name="medida">
                                    <?php foreach ($data['medidas'] as $row) { ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
                                    <?php } ?>
                                </select>
                                <label for="medida">Medida</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input id="precio_compra" class="form-control" type="text" name="precio_compra" placeholder="Precio de Compra">
                                <label for="precio_compra">Precio de Compra</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input id="precio_venta" class="form-control" type="text" name="precio_venta" placeholder="Precio de Venta">
                                <label for="precio_venta">Precio de Venta</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input id="precio_compra_bolos" class="form-control" type="text" name="precio_compra_bolos" placeholder="Precio Compra BsD">
                                <label for="precio_compra_bolos">Precio Compra BsD</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input id="precio_venta_bolos" class="form-control" type="text" name="precio_venta_bolos" placeholder="Precio Venta BsD">
                                <label for="precio_venta_bolos">Precio Venta BsD</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input id="cantidad" class="form-control" type="number" name="cantidad" placeholder="Stock Inicial">
                                <label for="cantidad">Stock Inicial</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <select id="categoria" class="form-control" name="categoria">
                                    <?php foreach ($data['categorias'] as $row) { ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
                                    <?php } ?>
                                </select>
                                <label for="categoria">Categoria</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <div class="form-group">
                                <label class="form-label fw-bold">Foto del Producto</label>
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <!-- Asegúrate que el botón de seleccionar imagen siempre sea visible -->
                                        <label for="imagen" id="icon-image" class="btn btn-primary btn-sm mb-2">
                                            <i class="fas fa-image me-1"></i> 
                                            <span id="btn-text">Seleccionar Imagen</span>
                                        </label>
                                        <span id="icon-cerrar" class="d-none">
                                            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarPreview()">
                                                <i class="fas fa-times"></i> Eliminar
                                            </button>
                                        </span>
                                        <input type="file" class="d-none" name="imagen" id="imagen" onChange="preview(event)" accept="image/*">
                                        <input type="hidden" id="foto_actual" name="foto_actual">
                                        <input type="hidden" id="foto_delete" name="foto_delete">
                                        <div class="mt-2">
                                            <img class="img-thumbnail" id="img-preview" style="max-height: 150px; max-width: 200px; display: none;">
                                        </div>
                                        <!-- Mensaje de ayuda -->
                                        <div class="form-text mt-2">
                                            <small>Haz clic en "Seleccionar Imagen" para cambiar la foto actual</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" onclick="registrarPro(event);" id="btnAccion">Registrar</button>
                        <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include "Views/Templates/footer.php"; ?>