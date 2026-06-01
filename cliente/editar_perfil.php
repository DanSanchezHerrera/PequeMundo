<?php
// Iniciar sesión
session_start();
// Verificar acceso de cliente
if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "cliente") {
    header("Location: ../login.php");
    exit();
}
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
        <?php include "../masterpage/footer.php"; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
<?php
mysqli_close($conexion);
?>