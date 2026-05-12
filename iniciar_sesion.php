<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'masterpage/menu.php'; ?>
    <main class="container my-5">
        <div class="login-container">
            <div class="login-card">
                <h2>Bienvenido</h2>
                <p>Inicia sesión para continuar</p>    
                    <form id="loginForm">
                        <div class="input-group">
                            <label for="email">Correo electrónico</label>
                            <input type="email" id="email" name="email" required placeholder="ejemplo@correo.com">
                        </div>
                        <div class="input-group">
                            <label for="password">Contraseña</label>
                            <input type="password" id="password" name="password" required placeholder="••••••••">
                            <span class="toggle-password">👁️</span>
                        </div>
                        <div class="options">
                            <label>
                                <input type="checkbox" name="remember"> Recordarme
                            </label>
                            <a href="#">¿Olvidaste tu contraseña?</a>
                        </div>
                        <button type="submit" class="btn-login">Iniciar Sesión</button>
                        <div class="register-link">
                            ¿No tienes cuenta? <a href="#">Regístrate aquí</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <?php include 'masterpage/footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>