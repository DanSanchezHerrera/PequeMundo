<?php
    // importar conexión
    require_once "../config/conexion.php";

    // procesar registro
    if(isset($_POST["btnRegistrar"])){

        $nombre = $_POST["nombre"];
        $apellido = $_POST["apellido"];
        $password = $_POST["password"];
        $telefono = $_POST["telefono"];
        $tipo_usuario = $_POST["tipo_usuario"];

        // encriptar contraseña
        $pass_enc = password_hash($password, PASSWORD_DEFAULT);

        // consulta SQL
        $sql = "INSERT INTO usuario(nombre, apellido, password, telefono, tipo_usuario)
                VALUES('$nombre', '$apellido', '$pass_enc', '$telefono', '$tipo_usuario')";

        // ejecutar consulta
        $resultado = mysqli_query($conexion, $sql);

        // validar resultado
        if($resultado){
            echo "<script>
                    alert('Usuario registrado correctamente');
                    window.location.href='../index.php';
                  </script>";
        }else{
            echo "<script>
                    alert('Error al registrar el usuario');
                    window.location.href='../index.php';
                  </script>";
        }
    }
?>