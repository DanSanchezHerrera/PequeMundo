<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/navbar.css">
    <title>Nosotros - PequeMundo</title>
    <style>
        /* Estilos adicionales para página Nosotros */
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

        .historia-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .historia-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #9a7c3a;
        }

        .historia-card h3 {
            color: #9a7c3a;
            margin-bottom: 1rem;
            font-family: 'Nunito', sans-serif;
        }

        .historia-card p {
            color: #555;
            line-height: 1.8;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .team-card {
            background: linear-gradient(135deg, #fcdb7e 0%, #f5b861 100%);
            color: #2f7187;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .team-card h3 {
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            margin: 1rem 0 0.5rem 0;
        }

        .team-card .role {
            color: #9a7c3a;
            font-weight: bold;
            margin: 0.5rem 0;
        }

        .team-card .description {
            font-size: 0.9rem;
            margin-top: 1rem;
            color: #444;
        }

        .values-grid {
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

        .value-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .value-card h4 {
            color: #9a7c3a;
            font-family: 'Nunito', sans-serif;
            margin-bottom: 0.5rem;
        }

        .value-card p {
            font-size: 0.95rem;
            color: #666;
        }

        .tech-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .tech-card {
            background: white;
            border: 2px solid #fcdb7e;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .tech-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .tech-card h4 {
            color: #9a7c3a;
            font-family: 'Nunito', sans-serif;
            margin-bottom: 0.5rem;
        }

        .tech-card p {
            font-size: 0.9rem;
            color: #666;
        }

        .mission-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .mission-card {
            background: linear-gradient(135deg, rgba(252, 219, 126, 0.2), rgba(154, 124, 58, 0.1));
            border: 2px solid #fcdb7e;
            padding: 2rem;
            border-radius: 10px;
        }

        .mission-card h3 {
            color: #9a7c3a;
            font-family: 'Nunito', sans-serif;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .mission-card p {
            color: #555;
            line-height: 1.8;
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

        .responsive-text {
            color: #555;
            line-height: 1.8;
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
    <!-- Navbar -->
    <?php include 'masterpage/menu.php'; ?>

    <!-- Hero Section -->
    <section class="hero-nosotros">
        <div class="container">
            <h1>🏢 Sobre PequeMundo</h1>
            <p>Muebles infantiles de calidad para crear espacios seguros y acogedores</p>
        </div>
    </section>

    <!-- Contenido Principal -->
    <main class="container my-5">

        <!-- Sección Historia -->
        <section class="mb-5">
            <h2 class="section-title">📖 Nuestra Historia</h2>

            <div class="historia-grid">
                <div class="historia-card">
                    <h3>🎯 ¿Quiénes Somos?</h3>
                    <p>
                        PequeMundo es una empresa chilena especializada en el diseño, fabricación y venta de muebles
                        infantiles de alta calidad. Desde hace años, trabajamos con dedicación para crear productos
                        que transforman los espacios de los más pequeños en lugares seguros, acogedores y funcionales.
                    </p>
                </div>

                <div class="historia-card">
                    <h3>❤️ Nuestro Compromiso</h3>
                    <p>
                        Nos comprometemos a ofrecer muebles que combinan diseño moderno, seguridad garantizada y
                        durabilidad. Cada producto está pensado para acompañar el crecimiento de los niños,
                        proporcionando confort y estilo a las familias chilenas.
                    </p>
                </div>

                <div class="historia-card">
                    <h3>✨ La Diferencia de PequeMundo</h3>
                    <p>
                        Nos destacamos por nuestra atención personalizada, productos de calidad superior y precios
                        competitivos. Buscamos ser más que un proveedor: queremos ser el aliado de confianza de las
                        familias que desean lo mejor para sus hijos.
                    </p>
                </div>
            </div>

            <div class="mt-4 p-4 bg-light rounded" style="border-left: 4px solid #9a7c3a;">
                <p class="responsive-text mb-0">
                    <strong>Ubicación:</strong> Región Metropolitana, Santiago. Nos encontramos disponibles para retiro
                    en tienda y contamos con servicio de envío a domicilio en toda la región con cálculo de tarifas
                    según destino.
                </p>
            </div>
        </section>

        <!-- Misión y Visión -->
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">🎯 Misión y Visión</h2>

            <div class="mission-grid">
                <div class="mission-card">
                    <h3>Nuestra Misión</h3>
                    <p>
                        Diseñar y comercializar muebles infantiles que combinen calidad, seguridad y estética,
                        ofreciendo una experiencia de compra moderna y confiable que satisfaga las necesidades
                        de las familias chilenas.
                    </p>
                </div>

                <div class="mission-card">
                    <h3>Nuestra Visión</h3>
                    <p>
                        Ser la marca preferida de muebles infantiles en Chile, reconocida por nuestra calidad,
                        innovación en diseño y excelente servicio al cliente. Buscamos crecer de manera sostenible
                        manteniendo siempre nuestro compromiso con las familias.
                    </p>
                </div>
            </div>
        </section>

        <!-- Categorías de Productos -->
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">📦 Nuestros Productos</h2>

            <p class="responsive-text mb-4">
                Contamos con una amplia variedad de muebles diseñados para cada etapa del crecimiento de tu hijo:
            </p>

            <div class="row">
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">👶 Cunas y Corralitos</h5>
                            <p class="card-text">Espacios seguros y cómodos para el descanso de los bebés, con
                                diseños funcionales y modernos.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">🛏️ Camas Infantiles</h5>
                            <p class="card-text">Camas seguras y atractivas para niños en crecimiento, con
                                opciones de diferentes tamaños.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">🗄️ Cómodas y Organizadores</h5>
                            <p class="card-text">Soluciones de almacenamiento prácticas y bonitas para mantener
                                el orden en la habitación.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">🪑 Sillas y Mesas</h5>
                            <p class="card-text">Muebles ergonómicos diseñados para el tamaño y necesidades de
                                los niños.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">🎨 Accesorios Decorativos</h5>
                            <p class="card-text">Complementos que agregan estilo y calidez a los espacios de
                                los niños.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">📚 Muebles Multifuncionales</h5>
                            <p class="card-text">Soluciones inteligentes que combinan múltiples funciones en
                                un solo mueble.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Por qué elegir PequeMundo -->
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">⭐ ¿Por Qué Elegir PequeMundo?</h2>

            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">✔️</div>
                    <h4>Calidad Garantizada</h4>
                    <p>Todos nuestros productos cumplen con estándares de seguridad internacionales</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">💰</div>
                    <h4>Precios Competitivos</h4>
                    <p>Excelente relación calidad-precio sin comprometer la durabilidad</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🎨</div>
                    <h4>Diseño Moderno</h4>
                    <p>Estilos actuales que se adaptan a cualquier decoración de habitación</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h4>Atención Personalizada</h4>
                    <p>Nuestro equipo está disponible para asesorarte en tu compra</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">📦</div>
                    <h4>Entrega Rápida</h4>
                    <p>Opciones de retiro en tienda y envío a domicilio en toda la región</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">💳</div>
                    <h4>Pago Seguro</h4>
                    <p>Múltiples opciones de pago con garantía de seguridad en tus transacciones</p>
                </div>
            </div>
        </section>

        <!-- Servicio al Cliente -->
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">🛍️ Nuestros Servicios</h2>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">🏪 Retiro en Tienda</h5>
                            <p class="card-text">Sin costo de envío. Ven a elegir personalmente tu mueble en
                                nuestra sucursal. Atención de lunes a domingo.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">🚚 Envío a Domicilio</h5>
                            <p class="card-text">Entrega segura y puntual. Tarifas competitivas según región.
                                Seguimiento en tiempo real de tu pedido.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">📋 Asesoría Gratuita</h5>
                            <p class="card-text">Nuestros asesores pueden ayudarte a elegir el mueble ideal
                                según las necesidades de tu espacio.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">📞 Soporte Post-Venta</h5>
                            <p class="card-text">Garantía en todos los productos y atención a consultas o
                                problemas después de la compra.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Compromiso con la familia -->
        <section class="mb-5 bg-light rounded p-4 shadow-sm">
            <h2 class="section-title">❤️ Nuestro Compromiso con tu Familia</h2>

            <p class="responsive-text mb-3">
                En PequeMundo entendemos que los muebles de tus hijos son mucho más que simples productos. Son espacios
                donde crecen, aprenden y juegan. Por eso nos comprometemos a:
            </p>

            <ul style="list-style: none; padding: 0;">
                <li class="mb-2"><strong>✓ Seguridad Primero:</strong> Cada producto está diseñado y probado para
                    garantizar
                    la máxima seguridad de los niños.</li>
                <li class="mb-2"><strong>✓ Durabilidad:</strong> Utilizamos materiales de alta calidad que resisten el
                    uso
                    intenso de los niños.</li>
                <li class="mb-2"><strong>✓ Estética y Funcionalidad:</strong> Combinamos diseño moderno con practicidad
                    para espacios que funcionan y se ven bien.</li>
                <li class="mb-2"><strong>✓ Precios Justos:</strong> Ofrecemos excelente calidad sin precios excesivos,
                    porque sabemos que el presupuesto familiar es importante.</li>
                <li class="mb-2"><strong>✓ Disponibilidad 24/7:</strong> Gracias a nuestra plataforma en línea, puedes
                    explorar nuestro catálogo y hacer compras en cualquier momento.</li>
            </ul>
        </section>

        <!-- Valores -->
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">✨ Nuestros Valores</h2>

            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">👨‍👩‍👧‍👦</div>
                    <h4>Familia Primero</h4>
                    <p>Entendemos la importancia de crear espacios seguros para los más pequeños</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🏆</div>
                    <h4>Excelencia</h4>
                    <p>Nos esforzamos por la máxima calidad en cada producto que ofrecemos</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h4>Confianza</h4>
                    <p>Buscamos ser el aliado de confianza de las familias chilenas</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🌱</div>
                    <h4>Sostenibilidad</h4>
                    <p>Comprometidos con prácticas responsables con el medio ambiente</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">💡</div>
                    <h4>Innovación</h4>
                    <p>Constantemente mejorando nuestros diseños y procesos</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">💰</div>
                    <h4>Transparencia</h4>
                    <p>Precios justos y claros sin sorpresas en nuestras transacciones</p>
                </div>
            </div>
        </section>

        <!-- Testimonios -->
        <section class="mb-5 bg-light rounded p-4 shadow-sm">
            <h2 class="section-title">⭐ Lo que Dicen Nuestros Clientes</h2>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="mb-2">
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                            </div>
                            <p class="card-text mb-3"><em>"Excelente calidad y muy buena atención. Los muebles de
                                    PequeMundo han sido perfectos para la habitación de mis hijos."</em></p>
                            <p class="card-text"><strong>- María González, Santiago</strong></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="mb-2">
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                            </div>
                            <p class="card-text mb-3"><em>"Muy recomendado. Productos seguros, durables y con
                                    diseños modernos. El envío fue rápido y sin problemas."</em></p>
                            <p class="card-text"><strong>- Carlos Rodríguez, Providencia</strong></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="mb-2">
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                            </div>
                            <p class="card-text mb-3"><em>"Encontré exactamente lo que buscaba. Los asesorías
                                    gratuitas me ayudaron a elegir los muebles correctos para el espacio."</em></p>
                            <p class="card-text"><strong>- Andrea López, Ñuñoa</strong></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="mb-2">
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                                <i class="fas fa-star" style="color: #9a7c3a;"></i>
                            </div>
                            <p class="card-text mb-3"><em>"La mejor decisión de compra. Calidad premium a
                                    precios accesibles. Mis hijos aman sus nuevos muebles."</em></p>
                            <p class="card-text"><strong>- Patricia Díaz, La Florida</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contacto e Información -->
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">📞 Contáctanos</h2>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">
                                <i class="fas fa-map-marker-alt"></i> Ubicación
                            </h5>
                            <p class="card-text">
                                Región Metropolitana, Santiago<br>
                                <small class="text-muted">Disponible para retiro en tienda</small>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">
                                <i class="fas fa-envelope"></i> Email
                            </h5>
                            <p class="card-text">
                                contacto@pequemundo.cl<br>
                                <small class="text-muted">Respuesta en 24 horas</small>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">
                                <i class="fas fa-phone"></i> Teléfono
                            </h5>
                            <p class="card-text">
                                +56 9 XXXX XXXX<br>
                                <small class="text-muted">Lunes a Domingo 9:00 - 18:00</small>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">
                                <i class="fas fa-globe"></i> Síguenos
                            </h5>
                            <p class="card-text">
                                <a href="#" class="text-decoration-none">Facebook</a> |
                                <a href="#" class="text-decoration-none">Instagram</a><br>
                                <small class="text-muted">@PequeMundoCL</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Final -->
        <section class="mb-5 bg-light rounded p-4 shadow-sm text-center">
            <h2 class="section-title">¿Listo para crear el espacio perfecto para tus hijos?</h2>
            <p class="responsive-text">
                Explora nuestro catálogo completo y encuentra el mueble ideal para tu familia.
            </p>
            <a href="catalogo.php" class="btn btn-custom btn-lg mt-3">
                <i class="fas fa-shopping-bag me-2"></i> Ver Nuestro Catálogo
            </a>
        </section>

    </main>

    <!-- Footer -->
    <?php include 'masterpage/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>