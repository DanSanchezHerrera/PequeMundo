<?php
// Iniciar sesión
session_start();

// 1. Incluye tu archivo de conexión a la base de datos
include 'conexion.php'; 

// 2. Verificar si el usuario inició sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
// Verificar acceso de cliente
if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "cliente") {
    header("Location: ../login.php");
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
// Incluir conexión a la base de datos
require_once "../config/conexion.php";
// Guardar id del usuario conectado
$id_usuario = intval($_SESSION["id_usuario"]);
$mensaje_exito = "";
$mensaje_error = "";
// Actualizar datos si se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar datos
    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $mail = trim($_POST["mail"]);
    $telefono = trim($_POST["telefono"]);
    $direccion = trim($_POST["direccion"]);
    $region = trim($_POST["region"]);
    $comuna = trim($_POST["comuna"]);
    // Validar campos obligatorios
    if ($nombre == "" || $apellido == "" || $mail == "" || $telefono == "") {
        $mensaje_error = "Debe completar nombre, apellido, email y teléfono.";
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $mensaje_error = "El email ingresado no es válido.";
    } else {
        $mensaje_error = "Hubo un error al guardar los cambios: " . mysqli_error($conexion);
        // Verificar que el correo no pertenezca a otro usuario
        $sql_verificar = "SELECT id_usuario FROM usuario WHERE mail = ? AND id_usuario != ?";
        $stmt_verificar = mysqli_prepare($conexion, $sql_verificar);
        mysqli_stmt_bind_param($stmt_verificar, "si", $mail, $id_usuario);
        mysqli_stmt_execute($stmt_verificar);
        $resultado_verificar = mysqli_stmt_get_result($stmt_verificar);
        if (mysqli_num_rows($resultado_verificar) > 0) {
            $mensaje_error = "El email ingresado ya está registrado por otro usuario.";
        } else {
            // Actualizar datos del perfil
            $sql_update = "UPDATE usuario
                           SET nombre = ?,
                               apellido = ?,
                               mail = ?,
                               telefono = ?,
                               direccion = ?,
                               region = ?,
                               comuna = ?
                           WHERE id_usuario = ?";
            $stmt_update = mysqli_prepare($conexion, $sql_update);
            mysqli_stmt_bind_param($stmt_update, "sssssssi", $nombre, $apellido, $mail, $telefono, $direccion, $region, $comuna, $id_usuario);
            if (mysqli_stmt_execute($stmt_update)) {
                $mensaje_exito = "Tus datos fueron actualizados correctamente.";
            } else {
                $mensaje_error = "No se pudieron actualizar tus datos.";
            }
        }
    }
}

// 4. Obtener los datos ACTUALIZADOS para mostrarlos en el formulario
$query = "SELECT nombre, correo, telefono, direccion, foto FROM usuarios WHERE id = '$id_del_usuario'";
$resultado = mysqli_query($conexion, $query);
$usuario = mysqli_fetch_assoc($resultado);

// Si el usuario no tiene foto, ponemos una por defecto
$foto_perfil = !empty($usuario['foto']) ? $usuario['foto'] : 'img/default-avatar.png';
// Consultar datos actualizados del usuario
$sql_usuario = "SELECT nombre, apellido, mail, telefono, direccion, region, comuna FROM usuario WHERE id_usuario = ? LIMIT 1";
$stmt_usuario = mysqli_prepare($conexion, $sql_usuario);
mysqli_stmt_bind_param($stmt_usuario, "i", $id_usuario);
mysqli_stmt_execute($stmt_usuario);
$resultado_usuario = mysqli_stmt_get_result($stmt_usuario);
$usuario = mysqli_fetch_assoc($resultado_usuario);
// Dejar foto de perfil comentada para implementación futura
// $foto_perfil = !empty($usuario["foto_perfil"]) ? "../" . $usuario["foto_perfil"] : "../img/default-avatar.png";
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
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Editar perfil - PequeMundo</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/estilos.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" href="../css/footer.css">
    </head>
    <body>
        <?php include "../masterpage/menu.php"; ?>
        <main class="container my-5">
            <section class="mb-4">
                <h2>Editar perfil</h2>
                <p class="text-muted">Actualiza tus datos personales y de contacto.</p>
                <hr>
            </section>
            <section class="row">
                <div class="col-md-4 mb-4">
                    <article class="bg-white rounded shadow-sm p-4 text-center">
                        <!-- Implementar foto de perfil más adelante -->
                        <!-- <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto de perfil" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover;"> -->
                        <!-- <div class="mb-3">
                            <label class="form-label">Cambiar foto de perfil</label>
                            <input type="file" name="foto_perfil" class="form-control" accept="image/*">
                        </div> -->
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:100px;height:100px;background-color:#fce07a;color:#2f7187;font-size:40px;font-weight:bold;">
                            <?php echo strtoupper(substr($usuario["nombre"], 0, 1)); ?>
                        </div>
                        <h4><?php echo htmlspecialchars($usuario["nombre"] . " " . $usuario["apellido"]); ?></h4>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($usuario["mail"]); ?></p>
                    </article>
                </div>
                <div class="col-md-8 mb-4">
                    <article class="bg-white rounded shadow-sm p-4">
                        <?php if ($mensaje_exito != "") { ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje_exito); ?></div>
                        <?php } ?>
                        <?php if ($mensaje_error != "") { ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($mensaje_error); ?></div>
                        <?php } ?>
                        <form action="editar_perfil.php" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario["nombre"]); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" name="apellido" class="form-control" value="<?php echo htmlspecialchars($usuario["apellido"]); ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="mail" class="form-control" value="<?php echo htmlspecialchars($usuario["mail"]); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($usuario["telefono"]); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="direccion" class="form-control" value="<?php echo htmlspecialchars($usuario["direccion"]); ?>">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Región</label>
                                    <input type="text" name="region" class="form-control" value="<?php echo htmlspecialchars($usuario["region"]); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Comuna</label>
                                    <input type="text" name="comuna" class="form-control" value="<?php echo htmlspecialchars($usuario["comuna"]); ?>">
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-finalizar-compra">Guardar cambios</button>
                                <a href="cliente_panel.php" class="btn btn-seguir-comprando">Volver al perfil</a>
                            </div>
                        </form>
                    </article>
                </div>
            </section>
        </main>
    </div>

</body>
</html>
        <?php include "../masterpage/footer.php"; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
<?php
mysqli_close($conexion);
?>