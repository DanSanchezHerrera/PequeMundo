<?php
// Iniciar sesión
session_start();
// Incluir conexión a la base de datos
require_once "../config/conexion.php";
// Verificar que exista sesión iniciada
if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../login.php");
    exit();
}
// Verificar que solo el administrador pueda acceder
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "admin") {
    header("Location: ../index.php");
    exit();
}
// Inicializar variables para el formulario
$modo_edicion = false;
$id_usuario_editar = "";
$nombre_editar = "";
$apellido_editar = "";
$mail_editar = "";
$telefono_editar = "";
$direccion_editar = "";
$region_editar = "";
$comuna_editar = "";
$tipo_usuario_editar = "";
// Verificar si se está editando un usuario
if (isset($_GET["editar"])) {
    $id_usuario_editar = intval($_GET["editar"]);
    $sql_editar = "SELECT id_usuario, nombre, apellido, mail, telefono, direccion, region, comuna, tipo_usuario FROM usuario WHERE id_usuario = ?";
    $stmt_editar = mysqli_prepare($conexion, $sql_editar);
    if ($stmt_editar) {
        mysqli_stmt_bind_param($stmt_editar, "i", $id_usuario_editar);
        mysqli_stmt_execute($stmt_editar);
        $resultado_editar = mysqli_stmt_get_result($stmt_editar);
        if (mysqli_num_rows($resultado_editar) == 1) {
            $usuario_editar = mysqli_fetch_assoc($resultado_editar);
            $modo_edicion = true;
            $nombre_editar = $usuario_editar["nombre"];
            $apellido_editar = $usuario_editar["apellido"];
            $mail_editar = $usuario_editar["mail"];
            $telefono_editar = $usuario_editar["telefono"];
            $direccion_editar = $usuario_editar["direccion"];
            $region_editar = $usuario_editar["region"];
            $comuna_editar = $usuario_editar["comuna"];
            $tipo_usuario_editar = $usuario_editar["tipo_usuario"];
        }
    }
}
// Listar todos los usuarios
$sql_usuarios = "SELECT id_usuario, nombre, apellido, mail, telefono, direccion, region, comuna, tipo_usuario FROM usuario ORDER BY id_usuario DESC";
$resultado_usuarios = mysqli_query($conexion, $sql_usuarios);
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestionar usuarios - PequeMundo</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!-- Estilos propios -->
        <link rel="stylesheet" href="../css/estilos.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" href="../css/footer.css">
    </head>
    <body>
        <!-- Menú -->
        <?php include "../masterpage/menu.php"; ?>
        <!-- Contenido principal -->
        <main class="container my-5">
            <!-- Encabezado -->
            <section class="mb-4">
                <h2>Gestionar usuarios</h2>
                <p>Administrar cuentas de clientes, vendedores, finanzas y administradores.</p>
                <hr>
            </section>
            <!-- Formulario de usuario -->
            <section class="bg-white rounded shadow-sm p-4 mb-5">
                <?php if ($modo_edicion) { ?>
                    <h4 class="mb-4">Editar usuario</h4>
                <?php } else { ?>
                    <h4 class="mb-4">Registrar usuario</h4>
                <?php } ?>
                <form action="../action/usuario_admin_action.php" method="POST">
                    <?php if ($modo_edicion) { ?>
                        <input type="hidden" name="id_usuario" value="<?php echo intval($id_usuario_editar); ?>">
                    <?php } ?>
                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($nombre_editar); ?>" required>
                        </div>
                        <!-- Apellido -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apellido</label>
                            <input type="text" name="apellido" class="form-control" value="<?php echo htmlspecialchars($apellido_editar); ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Mail -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="mail" class="form-control" value="<?php echo htmlspecialchars($mail_editar); ?>" required>
                        </div>
                        <!-- Teléfono -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" placeholder="+56912345678" pattern="^\+56[0-9]{9}$" value="<?php echo htmlspecialchars($telefono_editar); ?>" required>
                            <small class="form-text text-muted">Formato requerido: +56912345678</small>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Contraseña -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Contraseña
                                <?php if ($modo_edicion) { ?>
                                    <span class="text-muted">(opcional)</span>
                                <?php } ?>
                            </label>
                            <?php if ($modo_edicion) { ?>
                                <input type="password" name="password" class="form-control" minlength="8">
                                <small class="form-text text-muted">Completar solo si se desea cambiar la contraseña.</small>
                            <?php } else { ?>
                                <input type="password" name="password" class="form-control" minlength="8" required>
                                <small class="form-text text-muted">Mínimo 8 caracteres.</small>
                            <?php } ?>
                        </div>
                        <!-- Tipo de usuario -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de usuario</label>
                            <select name="tipo_usuario" class="form-control" required>
                                <option value="">Seleccione tipo</option>
                                <option value="cliente" <?php if ($tipo_usuario_editar == "cliente") echo "selected"; ?>>Cliente</option>
                                <option value="vendedor" <?php if ($tipo_usuario_editar == "vendedor") echo "selected"; ?>>Vendedor</option>
                                <option value="finanzas" <?php if ($tipo_usuario_editar == "finanzas") echo "selected"; ?>>Finanzas</option>
                                <option value="admin" <?php if ($tipo_usuario_editar == "admin") echo "selected"; ?>>Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <!-- Dirección -->
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="<?php echo htmlspecialchars($direccion_editar); ?>">
                    </div>
                    <div class="row">
                        <!-- Región -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Región</label>
                            <select name="region" class="form-control">
                                <option value="">Seleccione una región</option>
                                <option value="Arica y Parinacota" <?php if ($region_editar == "Arica y Parinacota") echo "selected"; ?>>Arica y Parinacota</option>
                                <option value="Tarapacá" <?php if ($region_editar == "Tarapacá") echo "selected"; ?>>Tarapacá</option>
                                <option value="Antofagasta" <?php if ($region_editar == "Antofagasta") echo "selected"; ?>>Antofagasta</option>
                                <option value="Atacama" <?php if ($region_editar == "Atacama") echo "selected"; ?>>Atacama</option>
                                <option value="Coquimbo" <?php if ($region_editar == "Coquimbo") echo "selected"; ?>>Coquimbo</option>
                                <option value="Valparaíso" <?php if ($region_editar == "Valparaíso") echo "selected"; ?>>Valparaíso</option>
                                <option value="Región Metropolitana" <?php if ($region_editar == "Región Metropolitana") echo "selected"; ?>>Región Metropolitana</option>
                                <option value="O'Higgins" <?php if ($region_editar == "O'Higgins") echo "selected"; ?>>O'Higgins</option>
                                <option value="Maule" <?php if ($region_editar == "Maule") echo "selected"; ?>>Maule</option>
                                <option value="Ñuble" <?php if ($region_editar == "Ñuble") echo "selected"; ?>>Ñuble</option>
                                <option value="Biobío" <?php if ($region_editar == "Biobío") echo "selected"; ?>>Biobío</option>
                                <option value="La Araucanía" <?php if ($region_editar == "La Araucanía") echo "selected"; ?>>La Araucanía</option>
                                <option value="Los Ríos" <?php if ($region_editar == "Los Ríos") echo "selected"; ?>>Los Ríos</option>
                                <option value="Los Lagos" <?php if ($region_editar == "Los Lagos") echo "selected"; ?>>Los Lagos</option>
                                <option value="Aysén" <?php if ($region_editar == "Aysén") echo "selected"; ?>>Aysén</option>
                                <option value="Magallanes" <?php if ($region_editar == "Magallanes") echo "selected"; ?>>Magallanes</option>
                            </select>
                        </div>
                        <!-- Comuna -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Comuna</label>
                            <input type="text" name="comuna" class="form-control" value="<?php echo htmlspecialchars($comuna_editar); ?>">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <?php if ($modo_edicion) { ?>
                            <button type="submit" name="btnActualizarUsuario" class="btn btn-finalizar-compra">
                                Actualizar usuario
                            </button>
                            <a href="gestionar_usuarios.php" class="btn btn-volver-carrito">
                                Cancelar edición
                            </a>
                        <?php } else { ?>
                            <button type="submit" name="btnRegistrarUsuario" class="btn btn-finalizar-compra">
                                Registrar usuario
                            </button>
                        <?php } ?>
                    </div>
                </form>
            </section>
            <!-- Listado de usuarios -->
            <section class="bg-white rounded shadow-sm p-4">
                <h4 class="mb-4">Usuarios registrados</h4>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-warning">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Tipo</th>
                                <th>Región</th>
                                <th>Comuna</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($resultado_usuarios && mysqli_num_rows($resultado_usuarios) > 0) { ?>
                                <?php while ($usuario = mysqli_fetch_assoc($resultado_usuarios)) { ?>
                                    <tr>
                                        <td><?php echo intval($usuario["id_usuario"]); ?></td>
                                        <td><?php echo htmlspecialchars($usuario["nombre"] . " " . $usuario["apellido"]); ?></td>
                                        <td><?php echo htmlspecialchars($usuario["mail"]); ?></td>
                                        <td><?php echo htmlspecialchars($usuario["telefono"]); ?></td>
                                        <td>
                                            <?php if ($usuario["tipo_usuario"] == "admin") { ?>
                                                <span class="badge bg-dark">Administrador</span>
                                            <?php } elseif ($usuario["tipo_usuario"] == "vendedor") { ?>
                                                <span class="badge bg-primary">Vendedor</span>
                                            <?php } elseif ($usuario["tipo_usuario"] == "finanzas") { ?>
                                                <span class="badge bg-success">Finanzas</span>
                                            <?php } else { ?>
                                                <span class="badge bg-secondary">Cliente</span>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($usuario["region"]); ?></td>
                                        <td><?php echo htmlspecialchars($usuario["comuna"]); ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <!-- Editar usuario -->
                                                <a href="gestionar_usuarios.php?editar=<?php echo intval($usuario["id_usuario"]); ?>" class="btn btn-sm btn-finalizar-compra">
                                                    Editar
                                                </a>
                                                <!-- Eliminar usuario -->
                                                <?php if (intval($usuario["id_usuario"]) != intval($_SESSION["id_usuario"])) { ?>
                                                    <form action="../action/usuario_admin_action.php" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                                        <input type="hidden" name="id_usuario" value="<?php echo intval($usuario["id_usuario"]); ?>">
                                                        <button type="submit" name="btnEliminarUsuario" class="btn btn-sm btn-danger">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                <?php } else { ?>
                                                    <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                        Usuario actual
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="8" class="text-center">No hay usuarios registrados.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
        <!-- Footer -->
        <?php include "../masterpage/footer.php"; ?>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
<?php
// Cerrar conexión
mysqli_close($conexion);
?>