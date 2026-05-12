<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="css/estilos.css">
        <link rel="stylesheet" href="css/navbar.css">
        <title>Mi Perfil - PequeMundo</title>
    </head>
    <body>
        <?php include 'masterpage/menu.php'; ?>
        <main class="container my-5">
            <div class="row">
                <!-- Panel lateral izquierdo -->
                <div class="col-md-3 mb-4">
                    <div class="bg-white rounded shadow-sm p-4">
                        <div class="text-center mb-4">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-user fa-2x text-muted"></i>
                            </div>
                            <h5 class="mb-1">Usuario PequeMundo</h5>
                            <p class="text-muted small mb-0">usuario@email.com</p>
                        </div>
                        <hr>
                        <nav class="nav flex-column">
                            <a class="nav-link active bg-light rounded mb-1" href="#">
                                <i class="fas fa-shopping-bag me-2"></i> Mis Pedidos
                            </a>
                            <a class="nav-link text-dark rounded mb-1" href="#">
                                <i class="fas fa-truck me-2"></i> Seguimiento
                            </a>
                            <a class="nav-link text-dark rounded mb-1" href="#">
                                <i class="fas fa-heart me-2"></i> Favoritos
                            </a>
                            <a class="nav-link text-dark rounded mb-1" href="#">
                                <i class="fas fa-user-edit me-2"></i> Editar Perfil
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
                    <div class="bg-white rounded shadow-sm p-4">
                        <h4 class="mb-4">Mis Pedidos</h4>
                        
                        <!-- Pedido 1 - En preparacion -->
                        <div class="border rounded p-3 mb-3">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-6">
                                    <h6 class="mb-0">Pedido #001</h6>
                                    <small class="text-muted">Fecha: 12 de Mayo, 2024</small>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <span class="badge bg-warning text-dark">En preparacion</span>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                            <th>Cantidad</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Cuna Convertible</td>
                                            <td>$150.000</td>
                                            <td>1</td>
                                            <td>$150.000</td>
                                        </tr>
                                        <tr>
                                            <td>Comoda Infantil</td>
                                            <td>$120.000</td>
                                            <td>1</td>
                                            <td>$120.000</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">Total:</td>
                                            <td class="fw-bold">$270.000</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-custom btn-sm">Ver detalle</button>
                            </div>
                        </div>

                        <!-- Pedido 2 - Entregado -->
                        <div class="border rounded p-3 mb-3">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-6">
                                    <h6 class="mb-0">Pedido #002</h6>
                                    <small class="text-muted">Fecha: 5 de Abril, 2024</small>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <span class="badge bg-success">Entregado</span>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                            <th>Cantidad</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Corral para Bebe</td>
                                            <td>$89.990</td>
                                            <td>1</td>
                                            <td>$89.990</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">Total:</td>
                                            <td class="fw-bold">$89.990</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-custom btn-sm">Ver detalle</button>
                            </div>
                        </div>

                        <!-- Pedido 3 - Enviado -->
                        <div class="border rounded p-3 mb-3">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-6">
                                    <h6 class="mb-0">Pedido #003</h6>
                                    <small class="text-muted">Fecha: 20 de Marzo, 2024</small>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <span class="badge bg-info text-dark">Enviado</span>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                            <th>Cantidad</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Set de Sabanas</td>
                                            <td>$35.000</td>
                                            <td>2</td>
                                            <td>$70.000</td>
                                        </tr>
                                        <tr>
                                            <td>Movil Musical</td>
                                            <td>$25.000</td>
                                            <td>1</td>
                                            <td>$25.000</td>
                                        </tr>
                                        <tr>
                                            <td>Almohada Antireflujo</td>
                                            <td>$20.000</td>
                                            <td>1</td>
                                            <td>$20.000</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">Total:</td>
                                            <td class="fw-bold">$115.000</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-custom btn-sm">Ver detalle</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php include 'masterpage/footer.php'; ?>
    </body>
</html>