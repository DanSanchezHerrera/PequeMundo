<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>RayoPack Logistics</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                min-height: 100vh;
                background: #f4f1ec;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: Arial, sans-serif;
            }

            .contenedor-paqueteria {
                width: 100%;
                max-width: 560px;
                padding: 20px;
            }

            .tarjeta-paqueteria {
                background: #ffffff;
                border-radius: 18px;
                padding: 35px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            }

            .titulo-paqueteria {
                text-align: center;
                margin-bottom: 8px;
                font-weight: 700;
                color: #2f2f2f;
            }

            .subtitulo-paqueteria {
                text-align: center;
                color: #777;
                margin-bottom: 30px;
            }

            .icono-camion {
                font-size: 2rem;
                margin-right: 8px;
            }

            .form-label {
                font-weight: 600;
                color: #333;
            }

            .btn-rayopack {
                background-color: #a17c35;
                border: none;
                color: white;
                font-weight: 600;
                padding: 10px;
                border-radius: 8px;
            }

            .btn-rayopack:hover {
                background-color: #86652a;
                color: white;
            }

            .ayuda {
                font-size: 0.85rem;
                color: #777;
            }
        </style>
    </head>
    <body>
        <main class="contenedor-paqueteria">
            <article class="tarjeta-paqueteria">
                <h1 class="titulo-paqueteria">
                    <span class="icono-camion">🚚</span>RayoPack Logistics
                </h1>

                <p class="subtitulo-paqueteria">
                    Portal externo para actualización de pedidos
                </p>

                <form id="formPaqueteria">
                    <div class="mb-3">
                        <label class="form-label">API Key (Clave de autorización)</label>
                        <input type="text" name="api_key" class="form-control" value="pequemundo_transporte_2026" required>
                        <div class="ayuda mt-1">Clave entregada por PequeMundo para consumir la API.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Código del pedido en PequeMundo</label>
                        <input type="text" name="codigo_pedido" class="form-control" placeholder="Ej: PM20260527233857391" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Nuevo estado del pedido</label>
                        <select name="estado_pedido" class="form-select" required>
                            <option value="">-- Selecciona un estado --</option>
                            <option value="camino">En camino</option>
                            <option value="entregado">Entregado</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-rayopack w-100">
                        Actualizar estado en PequeMundo
                    </button>
                </form>

                <div id="respuesta" class="mt-4"></div>
            </article>
        </main>

        <script>
            document.getElementById("formPaqueteria").addEventListener("submit", function(evento) {
                evento.preventDefault();

                const formulario = evento.target;
                const datos = new FormData(formulario);
                const respuesta = document.getElementById("respuesta");

                respuesta.innerHTML = `
                    <div class="alert alert-info mb-0">
                        Enviando actualización a PequeMundo...
                    </div>
                `;

                fetch("../api/estado_pedido_api.php", {
                    method: "POST",
                    body: datos
                })
                .then(function(respuestaServidor) {
                    return respuestaServidor.json();
                })
                .then(function(data) {
                    if (data.estado === "ok") {
                        respuesta.innerHTML = `
                            <div class="alert alert-success mb-0">
                                <strong>${data.mensaje}</strong><br>
                                Código pedido: ${data.codigo_pedido}<br>
                                Estado anterior: ${data.estado_anterior}<br>
                                Estado nuevo: ${data.estado_nuevo}
                            </div>
                        `;
                    } else {
                        respuesta.innerHTML = `
                            <div class="alert alert-danger mb-0">
                                <strong>Error:</strong> ${data.mensaje}
                            </div>
                        `;
                    }
                })
                .catch(function() {
                    respuesta.innerHTML = `
                        <div class="alert alert-danger mb-0">
                            No se pudo conectar con la API de PequeMundo.
                        </div>
                    `;
                });
            });
        </script>
    </body>
</html>