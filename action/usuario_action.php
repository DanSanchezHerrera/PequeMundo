<?php
    // importar la conexión
    require_once "../config/conexion.php";
	
    // procesar la solicitud de registrar
    if(isset($_POST["btnRegistrar"])){

        $nombre = $_POST["nombre"];
        $apellido = $_POST["apellido"];
        $password = $_POST["password"];
        $telefono = $_POST["telefono"];
        $tipo_usuario = $_POST["tipo_usuario"];

        // encriptar la contraseña
        $pass_enc = password_hash($password, PASSWORD_DEFAULT);

        // consulta SQL
        $sql = "INSERT INTO cliente (nombre, apellido, password, telefono, tipo_usuario)
                VALUES ('$nombre', '$apellido', '$pass_enc', '$telefono', '$tipo_usuario')";

        // guardar en la base de datos
        $resultado = mysqli_query($conexion, $sql);

        // validar resultado
        if($resultado){
            echo "<script>
                    alert('Cliente registrado correctamente');
                    window.location.href = '../index.php';
                  </script>";
        }else{
            echo "<script>
                    alert('Error al registrar el cliente');
                    window.location.href = '../index.php';
                  </script>";
        }
    }
?>