<?php
    //importar la conexión
    require_once "../config/conexion.php";

//procesar la solicitud de registrar
if(isset($_POST["btnRegistrar"])){
        $usuario = $_POST["username"];
        $password = $_POST["password"];
        $tipo_usuario = $_POST["tipo_usuario"];
       
        //encriptar la password
        $pass_enc = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuario (username, password, tipo_usuario)
        VALUES('$usuario','$pass_enc','$tipo_usuario')";
       
        //guardar usuario en la base de datos
        $resultado = mysqli_query($conexion, $sql);
       
        //validar si se guarda o no
        if($resultado){
            echo "<script>
            alert('Usuario registrado correctamente');
                    window.location.href = '../index.php';
            </script>";
        }else{
            echo "<script>
            alert('Error al registrar el usuario');
                    window.location.href = '../index.php';
            </script>";
        }
    }
?>