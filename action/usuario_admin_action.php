<?php
// Iniciar sesión para validar usuario conectado
session_start();
// Incluir conexión a la base de datos
require_once "../config/conexion.php";
// Verificar que exista sesión iniciada
if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../login.php");
    exit();
}
// Verificar que solo el administrador pueda realizar estas acciones
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "admin") {
    header("Location: ../index.php");
    exit();
}
// Validar tipo de usuario permitido
function tipoUsuarioValido($tipo_usuario) {
    $tipos_permitidos = array("cliente", "vendedor", "finanzas", "admin");
    return in_array($tipo_usuario, $tipos_permitidos);
}
// Validar teléfono chileno
function telefonoValido($telefono) {
    return preg_match('/^\+56[0-9]{9}$/', $telefono);
}
// Redirigir a gestionar usuarios
function volverGestionUsuarios() {
    header("Location: ../gestionar_usuarios.php");
    exit();
}
/* REGISTRAR USUARIO */
if (isset($_POST["btnRegistrarUsuario"])) {
    // Capturar datos del formulario
    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $mail = trim($_POST["mail"]);
    $telefono = trim($_POST["telefono"]);
    $password = $_POST["password"];
    $direccion = isset($_POST["direccion"]) ? trim($_POST["direccion"]) : "";
    $region = isset($_POST["region"]) ? trim($_POST["region"]) : "";
    $comuna = isset($_POST["comuna"]) ? trim($_POST["comuna"]) : "";
    $tipo_usuario = trim($_POST["tipo_usuario"]);
    // Validar campos obligatorios
    if (empty($nombre) || empty($apellido) || empty($mail) || empty($telefono) || empty($password) || empty($tipo_usuario)) {
        echo "<script>
                alert('Debe completar todos los campos obligatorios');
                window.location.href = '../gestionar_usuarios.php';
            </script>";
        exit();
    }
    // Validar formato de email
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('El email ingresado no es válido');
                window.location.href = '../gestionar_usuarios.php';
            </script>";
        exit();
    }
    // Validar teléfono chileno
    if (!telefonoValido($telefono)) {
        echo "<script>
                alert('El teléfono debe tener formato chileno. Ejemplo: +56912345678');
                window.location.href = '../gestionar_usuarios.php';
            </script>";
        exit();
    }
    // Validar largo de contraseña
    if (strlen($password) < 8) {
        echo "<script>
                alert('La contraseña debe tener al menos 8 caracteres');
                window.location.href = '../gestionar_usuarios.php';
            </script>";
        exit();
    }
    // Validar tipo de usuario
    if (!tipoUsuarioValido($tipo_usuario)) {
        echo "<script>
                alert('Tipo de usuario no válido');
                window.location.href = '../gestionar_usuarios.php';
            </script>";
        exit();
    }
    // Verificar que el correo no exista
    $sql_verificar = "SELECT id_usuario FROM usuario WHERE mail = ?";
    $stmt_verificar = mysqli_prepare($conexion, $sql_verificar);
    if (!$stmt_verificar) {
        echo "<script>
                alert('Error al verificar correo');
                window.location.href = '../gestionar_usuarios.php';
            </script>";
        exit();
    }
    mysqli_stmt_bind_param($stmt_verificar, "s", $mail);
    mysqli_stmt_execute($stmt_verificar);
    $resultado_verificar = mysqli_stmt_get_result($stmt_verificar);
    if (mysqli_num_rows($resultado_verificar) > 0) {
        echo "<script>
                alert('El email ya está registrado');
                window.location.href = '../gestionar_usuarios.php';
            </script>";
        exit();
    }
    // Encriptar contraseña
    $password_encriptada = password_hash($password, PASSWORD_DEFAULT);
    // Insertar usuario
    $sql = "INSERT INTO usuario (
                nombre,
                apellido,
                mail,
                password,
                telefono,
                direccion,
                region,
                comuna,
                tipo_usuario
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $sql);
    if (!$stmt) {
        echo "<script>
                alert('Error al preparar el registro de usuario');
                window.location.href = '../gestionar_usuarios.php';
            </script>";
        exit();
    }
    mysqli_stmt_bind_param(
        $stmt,
        "sssssssss",
        $nombre,
        $apellido,
        $mail,
        $password_encriptada,
        $telefono,
        $direccion,
        $region,
        $comuna,
        $tipo_usuario
    );
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('Usuario registrado correctamente');
                window.location.href = '../gestionar_usuarios.php';
            </script>";
        exit();
    } else {
        echo "<script>
                alert('Error al registrar usuario');
                window.location.href = '../gestionar_usuarios.php';
                </script>";
        exit();
    }
}
/* ACTUALIZAR USUARIO */
if (isset($_POST["btnActualizarUsuario"])) {
    // Capturar datos del formulario
    $id_usuario = intval($_POST["id_usuario"]);
    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $mail = trim($_POST["mail"]);
    $telefono = trim($_POST["telefono"]);
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    $direccion = isset($_POST["direccion"]) ? trim($_POST["direccion"]) : "";
    $region = isset($_POST["region"]) ? trim($_POST["region"]) : "";
    $comuna = isset($_POST["comuna"]) ? trim($_POST["comuna"]) : "";
    $tipo_usuario = trim($_POST["tipo_usuario"]);
    // Validar datos principales
    if ($id_usuario <= 0 || empty($nombre) || empty($apellido) || empty($mail) || empty($telefono) || empty($tipo_usuario)) {
        echo "<script>
                alert('Debe completar correctamente los campos obligatorios');
                window.location.href = '../gestionar_usuarios.php';
            </script>";
        exit();
    }
    // Validar formato de email
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('El email ingresado no es válido');
                window.location.href = '../gestionar_usuarios.php?editar=$id_usuario';
            </script>";
        exit();
    }
    // Validar teléfono chileno
    if (!telefonoValido($telefono)) {
        echo "<script>
                alert('El teléfono debe tener formato chileno. Ejemplo: +56912345678');
                window.location.href = '../gestionar_usuarios.php?editar=$id_usuario';
            </script>";
        exit();
    }
    // Validar tipo de usuario
    if (!tipoUsuarioValido($tipo_usuario)) {
        echo "<script>
                alert('Tipo de usuario no válido');
                window.location.href = '../gestionar_usuarios.php?editar=$id_usuario';
            </script>";
        exit();
    }
    // Validar contraseña solo si se completó
    if (!empty($password) && strlen($password) < 8) {
        echo "<script>
                alert('La contraseña debe tener al menos 8 caracteres');
                window.location.href = '../gestionar_usuarios.php?editar=$id_usuario';
            </script>";
        exit();
    }
    // Verificar que el correo no pertenezca a otro usuario
    $sql_verificar = "SELECT id_usuario FROM usuario WHERE mail = ? AND id_usuario != ?";
    $stmt_verificar = mysqli_prepare($conexion, $sql_verificar);
    if (!$stmt_verificar) {
        echo "<script>
                alert('Error al verificar correo');
                window.location.href = '../gestionar_usuarios.php?editar=$id_usuario';
            </script>";
        exit();
    }
    mysqli_stmt_bind_param($stmt_verificar, "si", $mail, $id_usuario);
    mysqli_stmt_execute($stmt_verificar);
    $resultado_verificar = mysqli_stmt_get_result($stmt_verificar);
    if (mysqli_num_rows($resultado_verificar) > 0) {
        echo "<script>
                alert('El email ya está registrado por otro usuario');
                window.location.href = '../gestionar_usuarios.php?editar=$id_usuario';
            </script>";
        exit();
    }
    // Actualizar con contraseña nueva si fue completada
    if (!empty($password)) {
        // Encriptar nueva contraseña
        $password_encriptada = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuario
                SET nombre = ?,
                    apellido = ?,
                    mail = ?,
                    password = ?,
                    telefono = ?,
                    direccion = ?,
                    region = ?,
                    comuna = ?,
                    tipo_usuario = ?
                WHERE id_usuario = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) {
            echo "<script>
                    alert('Error al preparar actualización');
                    window.location.href = '../gestionar_usuarios.php?editar=$id_usuario';
                </script>";
            exit();
        }
        mysqli_stmt_bind_param(
            $stmt,
            "sssssssssi",
            $nombre,
            $apellido,
            $mail,
            $password_encriptada,
            $telefono,
            $direccion,
            $region,
            $comuna,
            $tipo_usuario,
            $id_usuario
        );
    } else {
        // Actualizar sin cambiar contraseña
        $sql = "UPDATE usuario
                SET nombre = ?,
                    apellido = ?,
                    mail = ?,
                    telefono = ?,
                    direccion = ?,
                    region = ?,
                    comuna = ?,
                    tipo_usuario = ?
                WHERE id_usuario = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) {
            echo "<script>
                    alert('Error al preparar actualización');
                    window.location.href = '../gestionar_usuarios.php?editar=$id_usuario';
                </script>";
            exit();
        }
        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssi",
            $nombre,
            $apellido,
            $mail,
            $telefono,
            $direccion,
            $region,
            $comuna,
            $tipo_usuario,
            $id_usuario
        );
    }
    // Ejecutar actualización
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('Usuario actualizado correctamente');
                window.location.href = '../gestionar_usuarios.php';
            </script>";
        exit();
    } else {
        echo "<script>
                alert('Error al actualizar usuario');
                window.location.href = '../gestionar_usuarios.php?editar=$id_usuario';
            </script>";
        exit();
    }
}
/* ELIMINAR USUARIO */
if (isset($_POST["btnEliminarUsuario"])) {
    // Capturar usuario a eliminar
    $id_usuario_eliminar = intval($_POST["id_usuario"]);
    // Validar usuario recibido
    if ($id_usuario_eliminar <= 0) {
        volverGestionUsuarios();
    }
    // Evitar que el administrador elimine su propia cuenta
    if ($id_usuario_eliminar == intval($_SESSION["id_usuario"])) {
        echo "<script>
                alert('No puedes eliminar tu propia cuenta');
                window.location.href = '../gestionar_usuarios.php';
            </script>";
        exit();
    }
    // Verificar si el usuario tiene pedidos asociados
    $sql_pedidos = "SELECT id_pedido FROM pedido WHERE id_usuario = ? LIMIT 1";
    $stmt_pedidos = mysqli_prepare($conexion, $sql_pedidos);
    if ($stmt_pedidos) {
        mysqli_stmt_bind_param($stmt_pedidos, "i", $id_usuario_eliminar);
        mysqli_stmt_execute($stmt_pedidos);
        $resultado_pedidos = mysqli_stmt_get_result($stmt_pedidos);
        if (mysqli_num_rows($resultado_pedidos) > 0) {
            echo "<script>
                    alert('No se puede eliminar este usuario porque tiene pedidos asociados');
                    window.location.href = '../gestionar_usuarios.php';
                </script>";
            exit();
        }
    }
    // Eliminar usuario
    $sql_eliminar = "DELETE FROM usuario WHERE id_usuario = ?";
    $stmt_eliminar = mysqli_prepare($conexion, $sql_eliminar);
    if (!$stmt_eliminar) {
        echo "<script>
                alert('Error al preparar eliminación');
                window.location.href = '../gestionar_usuarios.php';
            </script>";
        exit();
    }
    mysqli_stmt_bind_param($stmt_eliminar, "i", $id_usuario_eliminar);
    if (mysqli_stmt_execute($stmt_eliminar)) {
        echo "<script>
                alert('Usuario eliminado correctamente');
                window.location.href = '../gestionar_usuarios.php';
            </script>";
        exit();
    } else {
        echo "<script>
                alert('Error al eliminar usuario');
                window.location.href = '../gestionar_usuarios.php';
            </script>";
        exit();
    }
}
// Redirigir si no se recibe una acción válida
header("Location: ../gestionar_usuarios.php");
exit();
?>