<?php
session_start();

// 1. Incluye tu archivo de conexión a la base de datos
include 'conexion.php'; 

// 2. Verificar si el usuario inició sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_del_usuario = $_SESSION['usuario_id'];
$mensaje_exito = '';
$mensaje_error = '';

// 3. Procesar el formulario si se hizo clic en Guardar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Escapar los datos para mayor seguridad (evitar inyección SQL)
    $nombre_nuevo = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $correo_nuevo = mysqli_real_escape_string($conexion, trim($_POST['correo']));
    $telefono_nuevo = mysqli_real_escape_string($conexion, trim($_POST['telefono']));
    $direccion_nueva = mysqli_real_escape_string($conexion, trim($_POST['direccion']));

    // Consulta para actualizar los datos en la tabla (Asegúrate que los nombres de las columnas coincidan)
    $query_update = "UPDATE usuarios 
                     SET nombre = '$nombre_nuevo', 
                         correo = '$correo_nuevo', 
                         telefono = '$telefono_nuevo', 
                         direccion = '$direccion_nueva' 
                     WHERE id = '$id_del_usuario'";

    if (mysqli_query($conexion, $query_update)) {
        $mensaje_exito = "¡Tus datos han sido actualizados con éxito!";
    } else {
        $mensaje_error = "Hubo un error al guardar los cambios: " . mysqli_error($conexion);
    }
}

// 4. Obtener los datos ACTUALIZADOS para mostrarlos en el formulario
$query = "SELECT nombre, correo, telefono, direccion, foto FROM usuarios WHERE id = '$id_del_usuario'";
$resultado = mysqli_query($conexion, $query);
$usuario = mysqli_fetch_assoc($resultado);

// Si el usuario no tiene foto, ponemos una por defecto
$foto_perfil = !empty($usuario['foto']) ? $usuario['foto'] : 'img/default-avatar.png';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PequeMundo - Editar Perfil</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #fcfbf9; color: #333; }

        .dashboard-wrapper { display: flex; max-width: 1200px; margin: 40px auto; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 8px; overflow: hidden; min-height: 600px; }
        .sidebar { width: 280px; background-color: #fce07a; padding: 40px 30px; display: flex; flex-direction: column; align-items: center; }
        .profile-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 15px; border: 3px solid white; background-color: white; }
        .sidebar h2 { font-size: 20px; color: #2c7a7b; margin-bottom: 30px; text-align: center; }
        .sidebar-menu { width: 100%; list-style: none; }
        .sidebar-menu li { margin-bottom: 15px; }
        .sidebar-menu a { text-decoration: none; color: #2c7a7b; font-weight: 600; font-size: 15px; display: block; transition: opacity 0.3s; }
        .sidebar-menu a:hover { opacity: 0.7; }
        .sidebar-menu a.activo { border-bottom: 2px solid #2c7a7b; padding-bottom: 2px; display: inline-block; }
        .divider { width: 100%; height: 1px; background-color: #d1b860; margin: 20px 0; }

        .main-content { flex: 1; padding: 40px; background-color: #fcfbf9; }
        
        .edit-profile-card { background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); max-width: 600px; }
        .edit-profile-card h3 { font-size: 20px; color: #2d3748; margin-bottom: 20px; border-bottom: 1px solid #edf2f7; padding-bottom: 10px; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; color: #4a5568; font-weight: 600; margin-bottom: 8px; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 14px; color: #2d3748; transition: border-color 0.3s; }
        .form-group input:focus { outline: none; border-color: #2c7a7b; box-shadow: 0 0 0 3px rgba(44, 122, 123, 0.1); }
        
        .btn-guardar { background-color: #2c7a7b; color: white; border: none; padding: 12px 24px; font-size: 15px; font-weight: bold; border-radius: 6px; cursor: pointer; transition: background-color 0.3s; width: 100%; margin-top: 10px; }
        .btn-guardar:hover { background-color: #235f5f; }
        
        .alert-success { background-color: #c6f6d5; color: #22543d; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; border: 1px solid #9ae6b4; }
        .alert-error { background-color: #fed7d7; color: #822727; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; border: 1px solid #feb2b2; }
    </style>
</head>
<body>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Perfil" class="profile-img">
            <h2>¡Hola <?php echo htmlspecialchars($usuario['nombre'] ?? 'Usuario'); ?>!</h2>
            
            <ul class="sidebar-menu">
                <li><a href="cliente_panel.php">Pedidos</a></li>
                <li><a href="#">Seguimiento de pedidos</a></li>
                <li><a href="#">Favoritos</a></li>
                <li><a href="editar_perfil.php" class="activo">Editar perfil</a></li>
            </ul>
            <div class="divider"></div>
            <ul class="sidebar-menu">
                <li><a href="login.php?logout=true">Cerrar sesión</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="edit-profile-card">
                <h3>Información Personal</h3>
                
                <?php if($mensaje_exito): ?>
                    <div class="alert-success"><?php echo $mensaje_exito; ?></div>
                <?php endif; ?>
                
                <?php if($mensaje_error): ?>
                    <div class="alert-error"><?php echo $mensaje_error; ?></div>
                <?php endif; ?>

                <form action="editar_perfil.php" method="POST">
                    
                    <div class="form-group">
                        <label for="nombre">Nombre Completo</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="correo">Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección de Despacho</label>
                        <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($usuario['direccion'] ?? ''); ?>">
                    </div>

                    <button type="submit" class="btn-guardar">Guardar Cambios</button>
                    
                </form>
            </div>
        </main>
    </div>

</body>
</html>