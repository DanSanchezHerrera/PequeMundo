<?php
// Iniciar sesión antes de imprimir HTML
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros - PequeMundo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <style>
        .hero-nosotros {
            background: linear-gradient(135deg, #fcdb7e 0%, #f5b861 100%);
            padding: 4rem 2rem;
            text-align: center;
            color: #2f7187;
        }
        .hero-nosotros h1 {
            font-family: 'Nunito', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .hero-nosotros p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .historia-grid,
        .mission-grid,
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        .historia-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 5px solid #9a7c3a;
        }
        .historia-card h3,
        .mission-card h3,
        .value-card h4,
        .tech-card h4 {
            color: #9a7c3a;
            font-family: 'Nunito', sans-serif;
            margin-bottom: 1rem;
        }
        .historia-card p,
        .mission-card p,
        .responsive-text {
            color: #555;
            line-height: 1.8;
        }
        .mission-card {
            background: linear-gradient(135deg, rgba(252,219,126,0.2), rgba(154,124,58,0.1));
            border: 2px solid #fcdb7e;
            padding: 2rem;
            border-radius: 10px;
        }
        .values-grid,
        .tech-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        .value-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            border-top: 3px solid #9a7c3a;
        }
        .value-icon,
        .tech-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .value-card p,
        .tech-card p {
            font-size: 0.95rem;
            color: #666;
        }
        .section-title {
            font-family: 'Nunito', sans-serif;
            color: #9a7c3a;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 1rem;
            font-size: 2rem;
            font-weight: 700;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: #9a7c3a;
            border-radius: 2px;
        }
        @media (max-width: 768px) {
            .hero-nosotros h1 {
                font-size: 1.8rem;
            }
            .section-title {
                font-size: 1.5rem;
            }
            .historia-grid,
            .team-grid,
            .mission-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include "masterpage/menu.php"; ?>
    <section class="hero-nosotros">
        <div class="container">
            <h1>🏢 Sobre PequeMundo</h1>
            <p>Muebles infantiles de calidad para crear espacios seguros y acogedores</p>
        </div>
    </section>
    <main class="container my-5">
        <section class="mb-5">
            <h2 class="section-title">📖 Nuestra Historia</h2>
            <div class="historia-grid">
                <div class="historia-card">
                    <h3>🎯 ¿Quiénes Somos?</h3>
                    <p>PequeMundo es una empresa chilena especializada en el diseño, fabricación y venta de muebles infantiles de alta calidad. Trabajamos con dedicación para crear productos que transforman los espacios de los más pequeños en lugares seguros, acogedores y funcionales.</p>
                </div>
                <div class="historia-card">
                    <h3>❤️ Nuestro Compromiso</h3>
                    <p>Nos comprometemos a ofrecer muebles que combinan diseño moderno, seguridad garantizada y durabilidad. Cada producto está pensado para acompañar el crecimiento de los niños, proporcionando confort y estilo a las familias chilenas.</p>
                </div>
                <div class="historia-card">
                    <h3>✨ La Diferencia de PequeMundo</h3>
                    <p>Nos destacamos por nuestra atención personalizada, productos de calidad superior y precios competitivos. Buscamos ser más que un proveedor: queremos ser el aliado de confianza de las familias que desean lo mejor para sus hijos.</p>
                </div>
            </div>
            <div class="mt-4 p-4 bg-light rounded" style="border-left: 4px solid #9a7c3a;">
                <p class="responsive-text mb-0"><strong>Ubicación:</strong> Región Metropolitana, Santiago. Nos encontramos disponibles para retiro en tienda y contamos con servicio de envío a domicilio en toda la región con cálculo de tarifas según destino.</p>
            </div>
        </section>
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">🎯 Misión y Visión</h2>
            <div class="mission-grid">
                <div class="mission-card">
                    <h3>Nuestra Misión</h3>
                    <p>Diseñar y comercializar muebles infantiles que combinen calidad, seguridad y estética, ofreciendo una experiencia de compra moderna y confiable que satisfaga las necesidades de las familias chilenas.</p>
                </div>
                <div class="mission-card">
                    <h3>Nuestra Visión</h3>
                    <p>Ser la marca preferida de muebles infantiles en Chile, reconocida por nuestra calidad, innovación en diseño y excelente servicio al cliente. Buscamos crecer de manera sostenible manteniendo siempre nuestro compromiso con las familias.</p>
                </div>
            </div>
        </section>
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">📦 Nuestros Productos</h2>
            <p class="responsive-text mb-4">Contamos con una variedad de muebles diseñados para acompañar distintas etapas del crecimiento infantil.</p>
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">👶 Cunas y Corralitos</h5>
                            <p class="card-text">Espacios seguros y cómodos para el descanso de los bebés.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">🛏️ Camas Infantiles</h5>
                            <p class="card-text">Camas seguras y atractivas para niños en crecimiento.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">🗄️ Cómodas y Organizadores</h5>
                            <p class="card-text">Soluciones prácticas para mantener el orden en la habitación.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">🪑 Sillas y Mesas</h5>
                            <p class="card-text">Muebles adecuados para juegos, dibujo, lectura y actividades infantiles.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">🎨 Accesorios Decorativos</h5>
                            <p class="card-text">Complementos que agregan estilo y calidez a los espacios infantiles.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">📚 Muebles Multifuncionales</h5>
                            <p class="card-text">Soluciones inteligentes que combinan funcionalidad y diseño.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">⭐ ¿Por Qué Elegir PequeMundo?</h2>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">✔️</div>
                    <h4>Calidad Garantizada</h4>
                    <p>Productos pensados para seguridad, resistencia y comodidad.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">💰</div>
                    <h4>Precios Competitivos</h4>
                    <p>Buena relación entre precio, calidad y durabilidad.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🎨</div>
                    <h4>Diseño Moderno</h4>
                    <p>Estilos actuales que se adaptan a distintos espacios infantiles.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h4>Atención Personalizada</h4>
                    <p>Acompañamiento para orientar la compra según cada necesidad.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">📦</div>
                    <h4>Entrega Flexible</h4>
                    <p>Opciones de retiro en tienda y despacho a domicilio.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">💳</div>
                    <h4>Pago Seguro</h4>
                    <p>Integración de pago para una compra más cómoda y confiable.</p>
                </div>
            </div>
        </section>
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">🛍️ Nuestros Servicios</h2>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card h-100" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">🏪 Retiro en Tienda</h5>
                            <p class="card-text">Permite retirar productos directamente en tienda.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">🚚 Envío a Domicilio</h5>
                            <p class="card-text">Entrega a domicilio con seguimiento del estado del pedido.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="mb-5 bg-light rounded p-4 shadow-sm">
            <h2 class="section-title">❤️ Nuestro Compromiso con tu Familia</h2>
            <p class="responsive-text mb-3">En PequeMundo entendemos que los muebles de tus hijos son más que simples productos: son espacios donde crecen, juegan y descansan.</p>
            <ul style="list-style: none; padding: 0;">
                <li class="mb-2"><strong>✓ Seguridad primero:</strong> Productos pensados para el uso infantil.</li>
                <li class="mb-2"><strong>✓ Durabilidad:</strong> Materiales resistentes para el uso diario.</li>
                <li class="mb-2"><strong>✓ Estética y funcionalidad:</strong> Diseño bonito y práctico.</li>
                <li class="mb-2"><strong>✓ Precios justos:</strong> Calidad sin precios excesivos.</li>
                <li class="mb-2"><strong>✓ Plataforma en línea:</strong> Compra disponible desde el catálogo web.</li>
            </ul>
        </section>
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">✨ Nuestros Valores</h2>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">👨‍👩‍👧‍👦</div>
                    <h4>Familia primero</h4>
                    <p>Crear espacios seguros y acogedores para los más pequeños.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🏆</div>
                    <h4>Excelencia</h4>
                    <p>Buscar calidad en cada producto ofrecido.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h4>Confianza</h4>
                    <p>Ser una opción confiable para las familias.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🌱</div>
                    <h4>Sostenibilidad</h4>
                    <p>Promover prácticas responsables y conscientes.</p>
                </div>
            </div>
        </section>
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">📞 Contáctanos</h2>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card h-100" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;"><i class="fas fa-map-marker-alt"></i> Ubicación</h5>
                            <p class="card-text">Región Metropolitana, Santiago<br><small class="text-muted">Disponible para retiro en tienda</small></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;"><i class="fas fa-envelope"></i> Email</h5>
                            <p class="card-text">contacto@pequemundo.cl<br><small class="text-muted">Respuesta en 24 horas</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="mb-5 bg-light rounded p-4 shadow-sm text-center">
            <h2 class="section-title">¿Listo para crear el espacio perfecto para tus hijos?</h2>
            <p class="responsive-text">Explora nuestro catálogo completo y encuentra el mueble ideal para tu familia.</p>
            <a href="catalogo.php" class="btn btn-custom btn-lg mt-3">
                <i class="fas fa-shopping-bag me-2"></i> Ver Nuestro Catálogo
            </a>
        </section>
    </main>
    <?php include "masterpage/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>