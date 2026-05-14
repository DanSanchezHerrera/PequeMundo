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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
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
            <h1>Conoce Nuestra Historia</h1>
            <p>PequeMundo: Transformando el comercio de muebles infantiles en Chile</p>
        </div>
    </section>

    <!-- Contenido Principal -->
    <main class="container my-5">
        
        <!-- Sección Historia -->
        <section class="mb-5">
            <h2 class="section-title">📖 Nuestra Historia</h2>
            
            <div class="historia-grid">
                <div class="historia-card">
                    <h3>🎯 Origen de PequeMundo</h3>
                    <p>
                        PequeMundo es un emprendimiento chileno dedicado al diseño, fabricación y venta de muebles 
                        infantiles de alta calidad. Durante años, la empresa ha crecido de manera consistente, atendiendo 
                        a familias de toda la región metropolitana que buscan muebles modernos, seguros y funcionales para 
                        sus hijos.
                    </p>
                </div>

                <div class="historia-card">
                    <h3>⚡ El Desafío</h3>
                    <p>
                        Inicialmente, PequeMundo gestionaba sus ventas de manera manual a través de redes sociales. Aunque 
                        efectivo, este proceso era ineficiente, propenso a errores y limitaba el potencial de crecimiento 
                        de la empresa. Los clientes carecían de una experiencia de compra moderna y segura.
                    </p>
                </div>

                <div class="historia-card">
                    <h3>✨ La Solución</h3>
                    <p>
                        En 2026, como parte del curso de Integración de Plataformas (ASY5131) de DUOC UC, se desarrolló 
                        una plataforma de comercio electrónico integral que automatiza completamente los procesos de venta, 
                        desde la visualización del catálogo hasta el seguimiento de pedidos en tiempo real.
                    </p>
                </div>
            </div>
        </section>

        <!-- Misión y Visión -->
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">Misión y Visión</h2>
            
            <div class="mission-grid">
                <div class="mission-card">
                    <h3>Nuestra Misión</h3>
                    <p>
                        Automatizar y digitalizar los procesos de venta de PequeMundo, proporcionando una plataforma de 
                        comercio electrónico moderna, segura y escalable que mejore la experiencia del cliente y la 
                        eficiencia operativa de la empresa.
                    </p>
                </div>

                <div class="mission-card">
                    <h3>Nuestra Visión</h3>
                    <p>
                        Ser la solución tecnológica integral que permita a PequeMundo expandir su presencia en el mercado, 
                        integrar múltiples canales de venta y ofrecer una experiencia de compra excepcional a clientes en 
                        toda la región.
                    </p>
                </div>
            </div>
        </section>

        <!-- Proyecto Académico -->
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">🎓 Proyecto Académico ASY5131</h2>
            
            <p class="responsive-text mb-4">
                Este proyecto fue desarrollado como parte del curso <strong>Integración de Plataformas (ASY5131)</strong> 
                de DUOC UC, bajo la dirección de <strong>Pavel Morales</strong>. Es una aplicación integral de conceptos de 
                integración de sistemas, arquitectura de software y desarrollo web.
            </p>

            <div class="row">
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">Período de Desarrollo</h5>
                            <p class="card-text">Marzo - Abril 2026</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">Organización</h5>
                            <p class="card-text">The Pixies Coop - DUOC UC</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">🎯 Enfoque</h5>
                            <p class="card-text">Integración de servicios SOAP/REST</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">Tecnologías</h5>
                            <p class="card-text">PHP, MySQL, REST API, SOAP</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">Hospedaje</h5>
                            <p class="card-text">Infinityfree (Hosting gratuito)</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">Alcance</h5>
                            <p class="card-text">Procesos de ventas y e-commerce</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Equipo de Desarrollo -->
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">Equipo de Desarrollo - The Pixies Coop</h2>
            
            <p class="responsive-text mb-4">
                Somos un equipo de estudiantes de DUOC UC especializados en desarrollo de software e integración de 
                plataformas, comprometidos con la excelencia académica y la calidad en la entrega de soluciones tecnológicas.
            </p>

            <div class="team-grid">
                <div class="team-card">
                    <div style="font-size: 3rem;">👨‍💻</div>
                    <h3>Diego Bustos</h3>
                    <div class="role">Frontend Developer</div>
                    <div class="description">
                        <strong>Arquitectura & Integración</strong><br>
                        Especializado en arquitectura de sistemas y diseño de integraciones. Responsable del diseño 
                        técnico y orquestación de servicios.
                    </div>
                </div>

                <div class="team-card">
                    <div style="font-size: 3rem;">📊</div>
                    <h3>Benjamín Rapiman</h3>
                    <div class="role">Business Analyst</div>
                    <div class="description">
                        <strong>Requerimientos & Procesos</strong><br>
                        Responsable del análisis de requerimientos funcionales y no funcionales. Especialista en 
                        mapeo de procesos de negocio.
                    </div>
                </div>

                <div class="team-card">
                    <div style="font-size: 3rem;">⚙️</div>
                    <h3>Bastian Schibar</h3>
                    <div class="role">Backend Developer</div>
                    <div class="description">
                        <strong>Infraestructura & Planificación</strong><br>
                        Especializado en arquitectura física del sistema y planificación de recursos. Responsable 
                        de infraestructura y despliegue.
                    </div>
                </div>

                <div class="team-card">
                    <div style="font-size: 3rem;">📋</div>
                    <h3>Daniela Sánchez</h3>
                    <div class="role">Project Manager</div>
                    <div class="description">
                        <strong>Gestión & Coordinación</strong><br>
                        Líder del proyecto y coordinadora general. Responsable de la comunicación con stakeholders 
                        y gestión del cronograma.
                    </div>
                </div>
            </div>
        </section>

        <!-- Arquitectura Técnica -->
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">Arquitectura Técnica</h2>
            
            <p class="responsive-text mb-4">
                La plataforma PequeMundo utiliza una arquitectura moderna de integración que combina servicios SOAP 
                y REST para garantizar flexibilidad y escalabilidad.
            </p>

            <h4 style="color: #9a7c3a; margin: 2rem 0 1rem 0; font-family: 'Nunito', sans-serif;">Stack Tecnológico</h4>
            <div class="tech-grid">
                <div class="tech-card">
                    <div class="tech-icon">🌐</div>
                    <h4>Frontend</h4>
                    <p>HTML5, CSS3, JavaScript, Bootstrap 5</p>
                </div>
                <div class="tech-card">
                    <div class="tech-icon">⚙️</div>
                    <h4>Backend</h4>
                    <p>PHP 7+, REST API, SOAP</p>
                </div>
                <div class="tech-card">
                    <div class="tech-icon">💾</div>
                    <h4>Base de Datos</h4>
                    <p>MySQL 5.7+, Diseño Relacional</p>
                </div>
                <div class="tech-card">
                    <div class="tech-icon">🚀</div>
                    <h4>Hospedaje</h4>
                    <p>Infinityfree (Free Hosting)</p>
                </div>
            </div>

            <h4 style="color: #9a7c3a; margin: 2rem 0 1rem 0; font-family: 'Nunito', sans-serif;">Servicios Integrados</h4>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">💳 Pasarela de Pagos</h5>
                            <p class="card-text">Integración segura para procesar transacciones en línea con 
                            confirmación automática.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">📦 API de Catálogo</h5>
                            <p class="card-text">Servicio RESTful que permite a terceros consultar y promocionar 
                            productos.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">🚚 API de Logística</h5>
                            <p class="card-text">Integración con empresa de transporte para seguimiento en 
                            tiempo real.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card border-left" style="border-left: 4px solid #9a7c3a;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #9a7c3a;">📊 Sistema de Logging</h5>
                            <p class="card-text">Registro centralizado de transacciones para auditoría y análisis 
                            de rendimiento.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Valores -->
        <section class="mb-5 bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">✨ Nuestros Valores</h2>
            
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">🎯</div>
                    <h4>Precisión</h4>
                    <p>Exactitud en cada detalle técnico y funcional</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🔒</div>
                    <h4>Seguridad</h4>
                    <p>Protección de datos de clientes y transacciones</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">⚡</div>
                    <h4>Rendimiento</h4>
                    <p>Velocidad y eficiencia en las operaciones</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h4>Colaboración</h4>
                    <p>Trabajo en equipo y comunicación efectiva</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">📚</div>
                    <h4>Aprendizaje</h4>
                    <p>Mejora continua y adquisición de conocimiento</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🌱</div>
                    <h4>Escalabilidad</h4>
                    <p>Diseño preparado para crecimiento futuro</p>
                </div>
            </div>
        </section>

        <!-- CTA Final -->
        <section class="mb-5 bg-light rounded p-4 shadow-sm text-center">
            <h2 class="section-title">¿Listo para comprar muebles infantiles?</h2>
            <p class="responsive-text">
                Visita nuestro catálogo y descubre muebles de alta calidad para tus hijos.
            </p>
            <a href="catalogo.php" class="btn btn-custom btn-lg mt-3">
                <i class="fas fa-shopping-bag me-2"></i> Ir al Catálogo
            </a>
        </section>

    </main>

    <!-- Footer -->
    <?php include 'masterpage/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>