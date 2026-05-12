<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="css/estilos.css">
        <link rel="stylesheet" href="css/navbar.css">
        <title>Panel Admin - PequeMundo</title>
    </head>
    <body>
        <?php include 'masterpage/menu.php'; ?>
        <main class="container my-5">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="bg-white rounded shadow-sm p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">Panel de Administracion</h4>
                                <p class="text-muted mb-0">Bienvenido, Administrador</p>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">Ultimo acceso: 12 de Mayo, 2024 - 09:30 AM</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjetas de resumen -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="bg-white rounded shadow-sm p-4 text-center">
                        <div class="mb-2">
                            <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                        </div>
                        <h3 class="mb-0">24</h3>
                        <p class="text-muted mb-0">Pedidos Realizados</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="bg-white rounded shadow-sm p-4 text-center">
                        <div class="mb-2">
                            <i class="fas fa-box fa-2x text-warning"></i>
                        </div>
                        <h3 class="mb-0">8</h3>
                        <p class="text-muted mb-0">En Preparacion</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="bg-white rounded shadow-sm p-4 text-center">
                        <div class="mb-2">
                            <i class="fas fa-truck fa-2x text-info"></i>
                        </div>
                        <h3 class="mb-0">12</h3>
                        <p class="text-muted mb-0">En Despacho</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="bg-white rounded shadow-sm p-4 text-center">
                        <div class="mb-2">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                        <h3 class="mb-0">45</h3>
                        <p class="text-muted mb-0">Entregados</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Panel lateral izquierdo - Modulos -->
                <div class="col-md-3 mb-4">
                    <div class="bg-white rounded shadow-sm p-4">
                        <h6 class="mb-3">Modulos de Administracion</h6>
                        <nav class="nav flex-column">
                            <a class="nav-link active bg-light rounded mb-1" href="#">
                                <i class="fas fa-tachometer-alt me-2"></i> Panel General
                            </a>
                            <a class="nav-link text-dark rounded mb-1" href="#">
                                <i class="fas fa-shopping-bag me-2"></i> Gestion de Pedidos
                            </a>
                            <a class="nav-link text-dark rounded mb-1" href="#">
                                <i class="fas fa-boxes me-2"></i> Gestion de Productos
                            </a>
                            <a class="nav-link text-dark rounded mb-1" href="#">
                                <i class="fas fa-warehouse me-2"></i> Control de Stock
                            </a>
                            <a class="nav-link text-dark rounded mb-1" href="#">
                                <i class="fas fa-users me-2"></i> Gestion de Usuarios
                            </a>
                            <a class="nav-link text-dark rounded mb-1" href="#">
                                <i class="fas fa-chart-bar me-2"></i> Reportes
                            </a>
                            <a class="nav-link text-dark rounded mb-1" href="#">
                                <i class="fas fa-cog me-2"></i> Configuracion
                            </a>
                            <hr>
                            <a class="nav-link text-danger rounded" href="#">
                                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesion
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Contenido principal -->
                <div class="col-md-9">
                    <!-- Resumen de pedidos recientes -->
                    <div class="bg-white rounded shadow-sm p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Pedidos Recientes</h5>
                            <a href="#" class="btn btn-custom btn-sm">Ver todos</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Accion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#024</td>
                                        <td>Maria Garcia</td>
                                        <td>12/05/2024</td>
                                        <td>$270.000</td>
                                        <td><span class="badge bg-warning text-dark">En preparacion</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Gestionar</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#023</td>
                                        <td>Carlos Lopez</td>
                                        <td>11/05/2024</td>
                                        <td>$89.990</td>
                                        <td><span class="badge bg-info text-dark">Enviado</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Gestionar</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#022</td>
                                        <td>Ana Martinez</td>
                                        <td>10/05/2024</td>
                                        <td>$115.000</td>
                                        <td><span class="badge bg-success">Entregado</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Ver</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#021</td>
                                        <td>Pedro Rodriguez</td>
                                        <td>09/05/2024</td>
                                        <td>$340.000</td>
                                        <td><span class="badge bg-success">Entregado</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Ver</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#020</td>
                                        <td>Laura Fernandez</td>
                                        <td>08/05/2024</td>
                                        <td>$150.000</td>
                                        <td><span class="badge bg-warning text-dark">En preparacion</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Gestionar</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Estado del inventario -->
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="bg-white rounded shadow-sm p-4">
                                <h5 class="mb-3">Productos con Bajo Stock</h5>
                                <div class="list-group">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                            Cuna Convertible
                                        </div>
                                        <span class="badge bg-danger">3 unidades</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                            Set de Sabanas
                                        </div>
                                        <span class="badge bg-danger">2 unidades</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                            Movil Musical
                                        </div>
                                        <span class="badge bg-danger">4 unidades</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="bg-white rounded shadow-sm p-4">
                                <h5 class="mb-3">Ultimos Usuarios Registrados</h5>
                                <div class="list-group">
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <i class="fas fa-user me-2 text-muted"></i>
                                                Maria Garcia
                                            </div>
                                            <small class="text-muted">Hace 1 dia</small>
                                        </div>
                                        <small class="text-muted ms-4">maria@email.com</small>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <i class="fas fa-user me-2 text-muted"></i>
                                                Carlos Lopez
                                            </div>
                                            <small class="text-muted">Hace 2 dias</small>
                                        </div>
                                        <small class="text-muted ms-4">carlos@email.com</small>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <i class="fas fa-user me-2 text-muted"></i>
                                                Ana Martinez
                                            </div>
                                            <small class="text-muted">Hace 3 dias</small>
                                        </div>
                                        <small class="text-muted ms-4">ana@email.com</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reportes rapidos -->
                    <div class="bg-white rounded shadow-sm p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Reportes del Sistema</h5>
                            <a href="#" class="btn btn-custom btn-sm">Generar Reporte</a>
                        </div>
                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3">
                                    <i class="fas fa-file-invoice-dollar fa-2x text-success mb-2"></i>
                                    <h6>Ventas del Mes</h6>
                                    <h4 class="text-primary">$2.450.000</h4>
                                    <small class="text-success">+12% vs mes anterior</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3">
                                    <i class="fas fa-star fa-2x text-warning mb-2"></i>
                                    <h6>Producto Mas Vendido</h6>
                                    <h4 class="text-primary">Cuna Convertible</h4>
                                    <small class="text-muted">15 unidades</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3">
                                    <i class="fas fa-users fa-2x text-info mb-2"></i>
                                    <h6>Total Clientes</h6>
                                    <h4 class="text-primary">156</h4>
                                    <small class="text-success">+8 este mes</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php include 'masterpage/footer.php'; ?>
    </body>
</html>