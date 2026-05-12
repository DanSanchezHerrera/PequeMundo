<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="css/estilos.css">
        <link rel="stylesheet" href="css/navbar.css">
        <title>Registro - PequeMundo</title>
    </head>
    <body>
        <?php include 'masterpage/menu.php'; ?>
        <main class="container my-5">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white text-center">
                            <h4>Registro de Cliente</h4>
                        </div>
                        <div class="card-body">
                            <form action="action/usuario_action.php" method="POST">

                                <div class="mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="nombre" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" name="apellido" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Contraseña</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tipo de Usuario</label>
                                    <select name="tipo_usuario" class="form-select" required>
                                        <option value="cliente">Cliente</option>
                                        <option value="admin">Administrador</option>
                                    </select>
                                </div>

                                <button type="submit" name="btnRegistrar" class="btn btn-success w-100">
                                    Registrar Usuario
                                </button>

                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </main>
        <?php include 'masterpage/footer.php'; ?>
    </body>
</html>