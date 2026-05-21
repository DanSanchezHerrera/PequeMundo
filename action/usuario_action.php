<?php
session_start();

require_once "../config/conexion.php";

/* REGISTRO DE USUARIO */
if (isset($_POST["btnRegistrar"])) {

    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $mail = trim($_POST["mail"]);
    $password = $_POST["password"];
    $confirmar_password = $_POST["confirmar_password"];
    $telefono = trim($_POST["telefono"]);

    $direccion = isset($_POST["direccion"]) ? trim($_POST["direccion"]) : "";
    $region = isset($_POST["region"]) ? trim($_POST["region"]) : "";
    $comuna = isset($_POST["comuna"]) ? trim($_POST["comuna"]) : "";

    /* Validar campos obligatorios */
    if (
        empty($nombre) ||
        empty($apellido) ||
        empty($mail) ||
        empty($password) ||
        empty($confirmar_password) ||
        empty($telefono)
    ) {
        echo "<script>
                alert('Debe completar todos los campos obligatorios');
                window.location.href = '../registro.php';
            </script>";
        exit();
    }

    /* Validar email */
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('El email ingresado no es válido');
                window.location.href = '../registro.php';
            </script>";
        exit();
    }

    /* Validar largo de contraseña */
    if (strlen($password) < 8) {
        echo "<script>
                alert('La contraseña debe tener al menos 8 caracteres');
                window.location.href = '../registro.php';
            </script>";
        exit();
    }

    /* Validar confirmación de contraseña */
    if ($password !== $confirmar_password) {
        echo "<script>
                alert('Las contraseñas no coinciden');
                window.location.href = '../registro.php';
            </script>";
        exit();
    }

    /* Validar teléfono chileno- +56 y 9 dígitos :) */
    if (!preg_match('/^\+56[0-9]{9}$/', $telefono)) {
        echo "<script>
                alert('El teléfono debe tener formato chileno. Ejemplo: +56912345678');
                window.location.href = '../registro.php';
            </script>";
        exit();
    }

    /* Verificar si el mail ya existe */
    $sql_verificar = "SELECT id_usuario FROM usuario WHERE mail = ?";
    $stmt_verificar = mysqli_prepare($conexion, $sql_verificar);

    if (!$stmt_verificar) {
        echo "<script>
                alert('Error al preparar la consulta de verificación');
                window.location.href = '../registro.php';
            </script>";
        exit();
    }

    mysqli_stmt_bind_param($stmt_verificar, "s", $mail);
    mysqli_stmt_execute($stmt_verificar);

    $resultado_verificar = mysqli_stmt_get_result($stmt_verificar);

    if (mysqli_num_rows($resultado_verificar) > 0) {
        echo "<script>
                alert('El email ya está registrado');
                window.location.href = '../registro.php';
            </script>";
        exit();
    }

    /* Encriptar contraseña */
    $password_encriptada = password_hash($password, PASSWORD_DEFAULT);

    /* Insertar usuario */
    $sql = "INSERT INTO usuario (
                nombre,
                apellido,
                mail,
                `password`,
                telefono,
                direccion,
                region,
                comuna
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conexion, $sql);

    if (!$stmt) {
        echo "<script>
                alert('Error al preparar el registro');
                window.location.href = '../registro.php';
            </script>";
        exit();
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ssssssss",
        $nombre,
        $apellido,
        $mail,
        $password_encriptada,
        $telefono,
        $direccion,
        $region,
        $comuna
    );

    $resultado = mysqli_stmt_execute($stmt);

    if ($resultado) {
        echo "<script>
                alert('Usuario registrado correctamente');
                window.location.href = '../login.php';
            </script>";
        exit();
    } else {
        echo "<script>
                alert('Error al registrar el usuario');
                window.location.href = '../registro.php';
            </script>";
        exit();
    }
}


/* LOGIN DE USUARIO */
if (isset($_POST["btnLogin"])) {

    $mail = trim($_POST["mail"]);
    $password = $_POST["password"];

    if (empty($mail) || empty($password)) {
        echo "<script>
                alert('Debe ingresar email y contraseña');
                window.location.href = '../login.php';
            </script>";
        exit();
    }

    $sql = "SELECT * FROM usuario WHERE mail = ?";
    $stmt = mysqli_prepare($conexion, $sql);

    if (!$stmt) {
        echo "<script>
                alert('Error al preparar el inicio de sesión');
                window.location.href = '../login.php';
            </script>";
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $mail);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) == 1) {

        $usuario = mysqli_fetch_assoc($resultado);

        if (password_verify($password, $usuario["password"])) {

            $_SESSION["id_usuario"] = $usuario["id_usuario"];
            $_SESSION["usuario"] = $usuario["nombre"];
            $_SESSION["tipo_usuario"] = $usuario["tipo_usuario"];

            header("Location: ../index.php");
            exit();

        } else {
            echo "<script>
                    alert('Contraseña incorrecta');
                    window.location.href = '../login.php';
                </script>";
            exit();
        }

    } else {
        echo "<script>
                alert('El usuario no existe');
                window.location.href = '../login.php';
            </script>";
        exit();
    }
}
?>