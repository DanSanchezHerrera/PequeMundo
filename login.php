<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Iniciar sesión - PequeMundo</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!-- Estilos personalizados -->
        <link rel="stylesheet" href="css/estilos.css">
        <link rel="stylesheet" href="css/navbar.css">
        <link rel="stylesheet" href="css/footer.css">
        <link rel="stylesheet" href="css/registro.css">
    </head>
    <body>
        <?php include 'masterpage/menu.php'; ?>
        <main class="container my-5">
            <section class="registro-contenedor login-contenedor">
                <div class="row g-0 align-items-stretch">
                    <!-- Columna izquierda: formulario -->
                    <div class="col-lg-7">
                        <div class="registro-formulario login-formulario">
                            <h2>Iniciar sesión</h2>
                            <p class="registro-texto">
                                Ingresa con tu cuenta para revisar tus pedidos y continuar comprando.
                            </p>
                            <form action="action/usuario_action.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">
                                        Email <span class="campo-obligatorio">*</span>
                                    </label>
                                    <input 
                                        type="email" 
                                        name="mail" 
                                        class="form-control" 
                                        placeholder="ejemplo@correo.com"
                                        required
                                    >
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        Contraseña <span class="campo-obligatorio">*</span>
                                    </label>
                                    <input 
                                        type="password" 
                                        name="password" 
                                        class="form-control" 
                                        placeholder="Ingresa tu contraseña"
                                        required
                                    >
                                </div>
                                <div class="text-end mb-4">
                                    <a href="#" class="login-link">Olvidé mi contraseña</a>
                                </div>
                                <div class="text-center mb-4">
                                    <button type="submit" name="btnLogin" class="btn btn-custom px-5">
                                        Iniciar sesión
                                    </button>
                                </div>
                                <p class="login-registro text-center">
                                    ¿No tienes cuenta?
                                    <a href="registro.php">Regístrate aquí</a>
                                </p>
                            </form>
                        </div>
                    </div>
                    <!-- Columna derecha: imagen -->
                    <div class="col-lg-5 d-none d-lg-flex">
                        <div class="registro-imagen">
                            <img src="img/pequeMundo_icono.png" alt="Logo PequeMundo">
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <?php include 'masterpage/footer.php'; ?>
    </body>
</html>