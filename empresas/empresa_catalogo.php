<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tienda Aliada - Catálogo PequeMundo</title>
        <!-- Cargar Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Cargar Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!-- Cargar estilos propios de la página externa -->
        <link rel="stylesheet" href="css/empresa_externa.css">
    </head>
    <body>
        <!-- Crear barra superior de empresa externa -->
        <nav class="navbar navbar-expand-lg navbar-dark navbar-externa">
            <div class="container">
                <a class="navbar-brand fw-bold" href="#">
                    <i class="fa-solid fa-store"></i> Tienda Aliada
                </a>
                <span class="navbar-text text-white">
                    Catálogo integrado con PequeMundo
                </span>
            </div>
        </nav>
        <!-- Crear contenido principal -->
        <main class="container my-5">
            <!-- Mostrar encabezado de integración -->
            <section class="hero-externo mb-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <span class="badge badge-api mb-3">Integración vía API REST</span>
                        <h1 class="titulo-externo">Productos infantiles de PequeMundo</h1>
                        <p class="texto-externo mb-0">
                            Esta página simula una empresa externa consumiendo el catálogo de productos activos de PequeMundo mediante una API en formato JSON.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <i class="fa-solid fa-code fa-4x icono-hero"></i>
                    </div>
                </div>
            </section>
            <!-- Crear buscador de productos -->
            <section class="bg-white rounded shadow-sm p-4 mb-5">
                <h4 class="mb-3">Buscar productos</h4>
                <form id="formBusqueda">
                    <div class="row g-3">
                        <div class="col-md-9">
                            <input type="text" id="busqueda" class="form-control" placeholder="Buscar por nombre o descripción">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-marca w-100">
                                <i class="fa-solid fa-magnifying-glass"></i> Buscar
                            </button>
                        </div>
                    </div>
                </form>
            </section>
            <!-- Mostrar estado de carga -->
            <section id="estadoCarga" class="text-center mensaje-carga my-4">
                <p>Cargando productos desde la API...</p>
            </section>
            <!-- Mostrar productos consumidos desde la API -->
            <section class="row" id="contenedorProductos"></section>
        </main>
        <!-- Cargar Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Guardar ruta de la API
            const rutaApi = "api/catalogo_api.php";
            // Obtener contenedor de productos
            const contenedorProductos = document.getElementById("contenedorProductos");
            // Obtener contenedor de estado de carga
            const estadoCarga = document.getElementById("estadoCarga");
            // Obtener formulario de búsqueda
            const formBusqueda = document.getElementById("formBusqueda");
            // Obtener input de búsqueda
            const inputBusqueda = document.getElementById("busqueda");
            // Formatear precio en pesos chilenos
            function formatearPrecio(precio) {
                return "$" + Number(precio).toLocaleString("es-CL");
            }
            // Limpiar contenedor de productos
            function limpiarProductos() {
                contenedorProductos.innerHTML = "";
            }
            // Mostrar mensaje simple
            function mostrarMensaje(mensaje) {
                estadoCarga.style.display = "block";
                estadoCarga.innerHTML = `<p>${mensaje}</p>`;
            }
            // Ocultar mensaje de carga
            function ocultarMensaje() {
                estadoCarga.style.display = "none";
            }
            // Crear card de producto
            function crearCardProducto(producto) {
                const imagenProducto = producto.imagen && producto.imagen !== "" ? producto.imagen : "";
                const card = `
                    <div class="col-md-6 col-lg-4 mb-4">
                        <article class="card card-producto">
                            ${imagenProducto !== "" ? `<img src="${imagenProducto}" class="card-img-top img-producto" alt="${producto.nombre}">` : `<div class="img-producto d-flex align-items-center justify-content-center"><i class="fa-solid fa-image fa-3x text-secondary"></i></div>`}
                            <div class="card-body">
                                <h5 class="card-title">${producto.nombre}</h5>
                                <p class="card-text">${producto.descripcion}</p>
                                <p class="stock-producto mb-1">Stock disponible: ${producto.stock}</p>
                                <p class="precio-producto mb-0">${formatearPrecio(producto.precio)}</p>
                            </div>
                        </article>
                    </div>
                `;
                contenedorProductos.innerHTML += card;
            }
            // Consumir API de catálogo
            function cargarProductos(busqueda = "") {
                limpiarProductos();
                mostrarMensaje("Cargando productos desde la API...");
                let url = rutaApi;
                if (busqueda !== "") {
                    url += "?busqueda=" + encodeURIComponent(busqueda);
                }
                fetch(url)
                    .then(function(respuesta) {
                        return respuesta.json();
                    })
                    .then(function(datos) {
                        limpiarProductos();
                        if (datos.estado !== "ok") {
                            mostrarMensaje("No se pudo cargar el catálogo.");
                            return;
                        }
                        if (datos.productos.length === 0) {
                            mostrarMensaje("No se encontraron productos disponibles.");
                            return;
                        }
                        ocultarMensaje();
                        datos.productos.forEach(function(producto) {
                            crearCardProducto(producto);
                        });
                    })
                    .catch(function(error) {
                        limpiarProductos();
                        mostrarMensaje("Error al consumir la API de catálogo.");
                        console.error(error);
                    });
            }
            // Buscar productos al enviar formulario
            formBusqueda.addEventListener("submit", function(evento) {
                evento.preventDefault();
                const textoBusqueda = inputBusqueda.value.trim();
                cargarProductos(textoBusqueda);
            });
            // Cargar productos al abrir la página
            cargarProductos();
        </script>
    </body>
</html>