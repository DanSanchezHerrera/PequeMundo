<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/navbar.css">
    <title>Carrito de Compras - PequeMundo</title>
    <style>
        .qty-input { width: 60px; text-align: center; }
        .cart-item i.fa-2x { width: 40px; text-align: center; }
    </style>
</head>
<body>
    <?php include 'masterpage/menu.php'; ?>
    
    <main class="container my-5">
        <h2 class="mb-4">Mi Carrito de Compras</h2>
        
        <div class="row">
            <!-- Lista de productos en el carrito -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="ps-4">Producto</th>
                                        <th scope="col">Precio</th>
                                        <th scope="col" class="text-center">Cantidad</th>
                                        <th scope="col">Subtotal</th>
                                        <th scope="col" class="text-end pe-4">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    <!-- Item 1 -->
                                    <tr class="cart-item" data-price="250000">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light p-2 rounded me-3 text-center">
                                                    <i class="fas fa-bed fa-2x text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Cuna Convertible</h6>
                                                    <small class="text-muted">Color: Blanco</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$250.000</td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center">
                                                <button class="btn btn-sm btn-outline-secondary btn-decrease" type="button"><i class="fas fa-minus"></i></button>
                                                <input type="number" class="form-control form-control-sm mx-2 qty-input" value="1" min="1" readonly>
                                                <button class="btn btn-sm btn-outline-secondary btn-increase" type="button"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </td>
                                        <td class="item-subtotal fw-bold">$250.000</td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-danger btn-remove" type="button" title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Item 2 -->
                                    <tr class="cart-item" data-price="35000">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light p-2 rounded me-3 text-center">
                                                    <i class="fas fa-music fa-2x text-info"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Móvil Musical para Cuna</h6>
                                                    <small class="text-muted">Diseño: Estrellas</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$35.000</td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center">
                                                <button class="btn btn-sm btn-outline-secondary btn-decrease" type="button"><i class="fas fa-minus"></i></button>
                                                <input type="number" class="form-control form-control-sm mx-2 qty-input" value="2" min="1" readonly>
                                                <button class="btn btn-sm btn-outline-secondary btn-increase" type="button"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </td>
                                        <td class="item-subtotal fw-bold">$70.000</td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-danger btn-remove" type="button" title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Botón de regreso al catálogo -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="catalogo.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Seguir Comprando
                    </a>
                </div>
            </div>

            <!-- Panel de Resumen de Compra -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Resumen de la Compra</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal</span>
                            <span id="summary-subtotal">$320.000</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Envío</span>
                            <span class="text-success">Gratis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5">Total</span>
                            <span class="fw-bold fs-5 text-primary" id="summary-total">$320.000</span>
                        </div>
                        <button class="btn btn-success w-100 py-2 fs-5">
                            Proceder al Pago <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'masterpage/footer.php'; ?>
    
    <!-- Lógica de Javascript para el Carrito (Interacción) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Formateador simple de moneda
            const formatCurrency = (amount) => {
                return '$' + amount.toLocaleString('es-CL');
            };

            // Función para actualizar los totales cuando cambian las cantidades o se eliminan productos
            const updateCartTotals = () => {
                let total = 0;
                document.querySelectorAll('.cart-item').forEach(item => {
                    const price = parseFloat(item.dataset.price);
                    const qty = parseInt(item.querySelector('.qty-input').value);
                    const subtotal = price * qty;
                    item.querySelector('.item-subtotal').textContent = formatCurrency(subtotal);
                    total += subtotal;
                });
                
                document.getElementById('summary-subtotal').textContent = formatCurrency(total);
                document.getElementById('summary-total').textContent = formatCurrency(total);
                
                // Mensaje si el carrito queda vacío
                if(document.querySelectorAll('.cart-item').length === 0) {
                    document.getElementById('cart-items').innerHTML = '<tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-shopping-cart fa-3x mb-3 text-light"></i><br>Tu carrito está vacío.</td></tr>';
                    document.getElementById('summary-subtotal').textContent = '$0';
                    document.getElementById('summary-total').textContent = '$0';
                }
            };

            // Event listener utilizando delegación de eventos para los botones
            document.getElementById('cart-items').addEventListener('click', function(e) {
                // Botón + (Aumentar cantidad)
                if (e.target.closest('.btn-increase')) {
                    const input = e.target.closest('td').querySelector('.qty-input');
                    input.value = parseInt(input.value) + 1;
                    updateCartTotals();
                }
                
                // Botón - (Disminuir cantidad)
                if (e.target.closest('.btn-decrease')) {
                    const input = e.target.closest('td').querySelector('.qty-input');
                    if (parseInt(input.value) > 1) {
                        input.value = parseInt(input.value) - 1;
                        updateCartTotals();
                    }
                }

                // Botón Basurero (Eliminar producto)
                if (e.target.closest('.btn-remove')) {
                    const item = e.target.closest('.cart-item');
                    // Efecto opcional de desvanecimiento antes de remover
                    item.style.transition = "opacity 0.3s";
                    item.style.opacity = 0;
                    setTimeout(() => {
                        item.remove();
                        updateCartTotals();
                    }, 300);
                }
            });
        });
    </script>
</body>
</html>
