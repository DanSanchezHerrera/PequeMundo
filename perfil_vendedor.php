<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="css/estilos.css">
        <link rel="stylesheet" href="css/navbar.css">
        <title>Panel Vendedor - PequeMundo</title>
        <style>
            /* Estilo para los toggle switches */
            .form-switch .form-check-input {
                width: 2.5em;
                height: 1.25em;
                cursor: pointer;
            }
            .form-switch .form-check-input:checked {
                background-color: #198754;
                border-color: #198754;
            }
        </style>
    </head>
    <body>
        <?php include 'masterpage/menu.php'; ?>
        <main class="container my-5">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="bg-white rounded shadow-sm p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">Panel de Vendedor</h4>
                                <p class="text-muted mb-0">Bienvenido, Vendedor</p>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">Ultimo acceso: 12 de Mayo, 2024 - 10:15 AM</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjetas de resumen -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="bg-white rounded shadow-sm p-4 text-center">
                        <div class="mb-2">
                            <i class="fas fa-boxes fa-2x text-primary"></i>
                        </div>
                        <h3 class="mb-0">124</h3>
                        <p class="text-muted mb-0">Productos en Catálogo</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="bg-white rounded shadow-sm p-4 text-center">
                        <div class="mb-2">
                            <i class="fas fa-shopping-bag fa-2x text-success"></i>
                        </div>
                        <h3 class="mb-0">15</h3>
                        <p class="text-muted mb-0">Ventas de Hoy</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="bg-white rounded shadow-sm p-4 text-center">
                        <div class="mb-2">
                            <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
                        </div>
                        <h3 class="mb-0">3</h3>
                        <p class="text-muted mb-0">Productos Bajo Stock</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Panel lateral izquierdo - Modulos -->
                <div class="col-md-3 mb-4">
                    <div class="bg-white rounded shadow-sm p-4">
                        <h6 class="mb-3">Módulos de Vendedor</h6>
                        <nav class="nav flex-column">
                            <a class="nav-link active bg-light rounded mb-1" href="#">
                                <i class="fas fa-tachometer-alt me-2"></i> Panel General
                            </a>
                            <a class="nav-link text-dark rounded mb-1" href="#registroVenta">
                                <i class="fas fa-cash-register me-2"></i> Registrar Venta
                            </a>
                            <a class="nav-link text-dark rounded mb-1" href="#gestionCatalogo">
                                <i class="fas fa-list me-2"></i> Gestión de Catálogo
                            </a>
                            <a class="nav-link text-dark rounded mb-1" href="#">
                                <i class="fas fa-chart-line me-2"></i> Mis Ventas
                            </a>
                            <hr>
                            <a class="nav-link text-danger rounded" href="#">
                                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Contenido principal -->
                <div class="col-md-9">
                    
                    <!-- Registrar Venta -->
                    <div id="registroVenta" class="bg-white rounded shadow-sm p-4 mb-4 border-start border-4 border-success">
                        <h5 class="mb-4"><i class="fas fa-cash-register text-success me-2"></i>Registrar Nueva Venta</h5>
                        <p class="text-muted mb-4">Al registrar una venta, el stock de los productos se actualizará automáticamente en el sistema.</p>
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Seleccionar Producto</label>
                                    <select class="form-select">
                                        <option value="">Buscar producto...</option>
                                        <option value="1">Cuna Convertible (Stock: 12)</option>
                                        <option value="2">Cochecito de Paseo (Stock: 5)</option>
                                        <option value="3">Set de Biberones (Stock: 25)</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Cantidad</label>
                                    <input type="number" class="form-control" value="1" min="1">
                                </div>
                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-success w-100">
                                        <i class="fas fa-check me-2"></i>Registrar Venta
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Gestión de Catálogo -->
                    <div id="gestionCatalogo" class="bg-white rounded shadow-sm p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="fas fa-boxes text-primary me-2"></i>Gestión de Catálogo</h5>
                            <div>
                                <input type="text" class="form-control form-control-sm" placeholder="Buscar producto...">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Estado</th>
                                        <th>Activar/Desactivar</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="fw-bold">Cuna Convertible</div>
                                            <small class="text-muted">SKU: CUN-001</small>
                                        </td>
                                        <td>$250.000</td>
                                        <td>
                                            <span class="badge bg-success">12</span>
                                        </td>
                                        <td><span class="badge bg-success">Activo</span></td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" checked>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="fw-bold">Cochecito de Paseo</div>
                                            <small class="text-muted">SKU: COC-002</small>
                                        </td>
                                        <td>$180.000</td>
                                        <td>
                                            <span class="badge bg-success">5</span>
                                        </td>
                                        <td><span class="badge bg-success">Activo</span></td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" checked>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="fw-bold">Set de Biberones 250ml</div>
                                            <small class="text-muted">SKU: BIB-003</small>
                                        </td>
                                        <td>$15.000</td>
                                        <td>
                                            <span class="badge bg-danger">2</span>
                                        </td>
                                        <td><span class="badge bg-success">Activo</span></td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" checked>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="fw-bold">Monitor de Bebé con Cámara</div>
                                            <small class="text-muted">SKU: MON-004</small>
                                        </td>
                                        <td>$65.000</td>
                                        <td>
                                            <span class="badge bg-secondary">0</span>
                                        </td>
                                        <td><span class="badge bg-secondary">Inactivo</span></td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="fw-bold">Móvil Musical para Cuna</div>
                                            <small class="text-muted">SKU: MOV-005</small>
                                        </td>
                                        <td>$35.000</td>
                                        <td>
                                            <span class="badge bg-success">15</span>
                                        </td>
                                        <td><span class="badge bg-success">Activo</span></td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" checked>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <nav aria-label="Page navigation" class="mt-3">
                            <ul class="pagination pagination-sm justify-content-end mb-0">
                                <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">Siguiente</a></li>
                            </ul>
                        </nav>
                    </div>

                </div>
            </div>
        </main>
        <?php include 'masterpage/footer.php'; ?>
        
        <!-- Scripts de Bootstrap para tooltips y modales si se necesitan -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
