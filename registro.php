<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro - PequeMundo</title>
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
            <section class="registro-contenedor">
                <div class="row g-0 align-items-stretch">
                    <!-- Columna izquierda: formulario -->
                    <div class="col-lg-7">
                        <div class="registro-formulario">
                            <h2>Registrarse</h2>
                            <p class="registro-texto">
                                Crea tu cuenta para comprar productos y revisar tus pedidos.
                            </p>
                            <p class="registro-aviso">
                                Los campos marcados con <span class="campo-obligatorio">*</span> son obligatorios.
                            </p>
                            <form action="action/usuario_action.php" method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Nombre <span class="campo-obligatorio">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            name="nombre" 
                                            class="form-control" 
                                            required
                                        >
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Apellido <span class="campo-obligatorio">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            name="apellido" 
                                            class="form-control" 
                                            required
                                        >
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        Email <span class="campo-obligatorio">*</span>
                                    </label>
                                    <input 
                                        type="email" 
                                        name="mail" 
                                        class="form-control" 
                                        required
                                    >
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Contraseña <span class="campo-obligatorio">*</span>
                                        </label>
                                        <input 
                                            type="password" 
                                            name="password" 
                                            class="form-control" 
                                            minlength="8"
                                            required
                                        >
                                        <small class="form-text text-muted">
                                            Mínimo 8 caracteres.
                                        </small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Confirmar contraseña <span class="campo-obligatorio">*</span>
                                        </label>
                                        <input 
                                            type="password" 
                                            name="confirmar_password" 
                                            class="form-control" 
                                            minlength="8"
                                            required
                                        >
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        Teléfono <span class="campo-opcional">(opcional)</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="telefono" 
                                        class="form-control" 
                                        placeholder="+56912345678"
                                        pattern="^\+56[0-9]{9}$"
                                        required
                                    >
                                    <small class="form-text text-muted">
                                        Formato requerido: +56XXXXXXXXX (nueve dígitos)
                                    </small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        Dirección <span class="campo-opcional">(opcional)</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="direccion" 
                                        class="form-control"
                                    >
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">
                                            Región <span class="campo-opcional">(opcional)</span>
                                        </label>
                                        <select name="region" class="form-control">
                                            <option value="">Seleccione una región</option>
                                            <option value="Arica y Parinacota">Arica y Parinacota</option>
                                            <option value="Tarapacá">Tarapacá</option>
                                            <option value="Antofagasta">Antofagasta</option>
                                            <option value="Atacama">Atacama</option>
                                            <option value="Coquimbo">Coquimbo</option>
                                            <option value="Valparaíso">Valparaíso</option>
                                            <option value="Región Metropolitana">Región Metropolitana</option>
                                            <option value="O'Higgins">O'Higgins</option>
                                            <option value="Maule">Maule</option>
                                            <option value="Ñuble">Ñuble</option>
                                            <option value="Biobío">Biobío</option>
                                            <option value="La Araucanía">La Araucanía</option>
                                            <option value="Los Ríos">Los Ríos</option>
                                            <option value="Los Lagos">Los Lagos</option>
                                            <option value="Aysén">Aysén</option>
                                            <option value="Magallanes">Magallanes</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">
                                            Comuna <span class="campo-opcional">(opcional)</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            name="comuna" 
                                            class="form-control"
                                        >
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" name="btnRegistrar" class="btn btn-custom px-5">
                                        Registrar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Columna derecha: imagen -->
                    <div class="col-lg-5 d-none d-lg-flex">
                        <div class="registro-imagen">
                            <img src="img/pequeMundo_icono2.png" alt="Logo PequeMundo">
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <?php include 'masterpage/footer.php'; ?>
    </body>
</html>