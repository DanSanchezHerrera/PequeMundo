<?php
session_start();

// 1. Incluye tu archivo de conexión a la base de datos
// Cambia esta ruta por la correcta en tu proyecto
include 'conexion.php'; 

// 2. Verificar si el usuario inició sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php"); // Ajusta la ruta a tu login
    exit();
}

$id_del_usuario = $_SESSION['usuario_id'];

// 3. Obtener los datos del usuario actual
$query_usuario = "SELECT nombre, foto FROM usuarios WHERE id = '$id_del_usuario'";
$resultado_usuario = mysqli_query($conexion, $query_usuario);
$usuario = mysqli_fetch_assoc($resultado_usuario);

// Si el usuario no tiene foto en la BD, le ponemos una por defecto
$foto_perfil = !empty($usuario['foto']) ? $usuario['foto'] : 'img/default-avatar.png';

// 4. (Opcional) Aquí iría la consulta real para obtener los pedidos.
// Por ahora, para que no se te rompa el diseño si no tienes la tabla pedidos creada,
// dejo el arreglo simulado. Cuando tengas la tabla, lo cambias por un mysqli_query() con un WHILE.
$pedidos = [
    [
        'id' => '657', 'fecha' => '05 Marzo 2026, 08:28 PM', 'estado' => 'En preparación',
        'total_items' => 1, 'total_precio' => '129.990',
        'items' => [
            ['nombre' => 'Cuna Nube Sueño', 'descripcion' => 'Cuna de diseño moderno', 'precio' => '129.990', 'cantidad' => 1, 'imagen' => 'img/cuna.jpg']
        ]
    ]
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PequeMundo - Mi Perfil</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #fcfbf9; color: #333; }

        .dashboard-wrapper { display: flex; max-width: 1200px; margin: 40px auto; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 8px; overflow: hidden; min-height: 600px; }
        .sidebar { width: 280px; background-color: #fce07a; padding: 40px 30px; display: flex; flex-direction: column; align-items: center; }
        .profile-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 15px; border: 3px solid white; background-color: white;}
        .sidebar h2 { font-size: 20px; color: #2c7a7b; margin-bottom: 30px; text-align: center; }
        .sidebar-menu { width: 100%; list-style: none; }
        .sidebar-menu li { margin-bottom: 15px; }
        .sidebar-menu a { text-decoration: none; color: #2c7a7b; font-weight: 600; font-size: 15px; display: block; }
        .sidebar-menu a:hover { opacity: 0.7; }
        .sidebar-menu a.activo { border-bottom: 2px solid #2c7a7b; padding-bottom: 2px; display: inline-block; }
        .divider { width: 100%; height: 1px; background-color: #d1b860; margin: 20px 0; }

        .main-content { flex: 1; padding: 40px; background-color: #fcfbf9; }
        .orders-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .order-card { background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .order-header { display: flex; justify-content: space-between; border-bottom: 1px solid #edf2f7; padding-bottom: 10px; margin-bottom: 15px; }
        .order-header h3 { font-size: 16px; color: #2d3748; }
        .order-header p { font-size: 12px; color: #718096; }
        .order-item { display: flex; align-items: center; margin-bottom: 15px; }
        .order-item img { width: 50px; height: 50px; margin-right: 15px; border-radius: 4px; object-fit: cover; background:#eee;}
        .order-item-details h4 { font-size: 14px; color: #2b6cb0; margin-bottom: 2px; }
        .order-item-details p { font-size: 11px; color: #a0aec0; margin-bottom: 4px; }
        .order-item-price { font-size: 13px; font-weight: bold; color: #4a5568; }
        .order-item-price span { color: #718096; font-weight: normal; margin-left: 10px; }
        .order-footer { border-top: 1px solid #edf2f7; padding-top: 15px; display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: #718096; }
        .status { color: #319795; font-weight: bold; }
    </style>
</head>
<body>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Perfil" class="profile-img">
            <h2>¡Hola <?php echo htmlspecialchars($usuario['nombre'] ?? 'Usuario'); ?>!</h2>
            
            <ul class="sidebar-menu">
                <li><a href="cliente_panel.php" class="activo">Pedidos</a></li>
                <li><a href="#">Seguimiento de pedidos</a></li>
                <li><a href="#">Favoritos</a></li>
                <li><a href="editar_perfil.php">Editar perfil</a></li>
            </ul>
            <div class="divider"></div>
            <ul class="sidebar-menu">
                <li><a href="login.php?logout=true">Cerrar sesión</a></li> 
            </ul>
        </aside>

        <main class="main-content">
            <div class="orders-grid">
                
                <?php foreach($pedidos as $pedido): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order #<?php echo htmlspecialchars($pedido['id']); ?></h3>
                            <p><?php echo htmlspecialchars($pedido['fecha']); ?></p>
                        </div>
                        <div class="icon">🌍</div>
                    </div>
                    
                    <?php foreach($pedido['items'] as $item): ?>
                    <div class="order-item">
                        <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>">
                        <div class="order-item-details">
                            <h4><?php echo htmlspecialchars($item['nombre']); ?></h4>
                            <p><?php echo htmlspecialchars($item['descripcion']); ?></p>
                            <div class="order-item-price">
                                $<?php echo htmlspecialchars($item['precio']); ?> 
                                <span>Cantidad: <?php echo htmlspecialchars($item['cantidad']); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="order-footer">
                        <span>X<?php echo htmlspecialchars($pedido['total_items']); ?> Items</span>
                        <span>Total más envío: $<?php echo htmlspecialchars($pedido['total_precio']); ?></span>
                        <span class="status">Estado: <?php echo htmlspecialchars($pedido['estado']); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
                
            </div>
        </main>
    </div>

</body>
</html>