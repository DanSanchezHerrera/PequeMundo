<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empresa de Paquetería - PequeMundo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container my-5">
        <section class="row justify-content-center">
            <div class="col-lg-7">
                <article class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h2 class="mb-3">Empresa de Paquetería</h2>
                        <p class="text-muted">
                            Esta página simula una empresa externa que actualiza el estado logístico de un pedido consumiendo la API de PequeMundo.
                        </p>
                        <form id="formPaqueteria">
                            <div class="mb-3">
                                <label class="form-label">Código del pedido</label>
                                <input type="text" name="codigo_pedido" class="form-control" placeholder="Ej: PM20260527233857391" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estado logístico</label>
                                <select name="estado_pedido" class="form-select" required>
                                    <option value="">Seleccione un estado</option>
                                    <option value="camino">En camino</option>
                                    <option value="entregado">Entregado</option>
                                </select>
                            </div>
                            <input type="hidden" name="api_key" value="pequemundo_transporte_2026">
                            <button type="submit" class="btn btn-primary w-100">
                                Enviar actualización
                            </button>
                        </form>
                        <div id="respuesta" class="mt-4"></div>
                    </div>
                </article>
            </div>
        </section>
    </main>
    <script>
        document.getElementById("formPaqueteria").addEventListener("submit", function(evento) {
            evento.preventDefault();
            const formulario = evento.target;
            const datos = new FormData(formulario);
            const respuesta = document.getElementById("respuesta");
            respuesta.innerHTML = '<div class="alert alert-info">Enviando actualización a PequeMundo...</div>';
            fetch("api/estado_pedido_api.php", {
                method: "POST",
                body: datos
            })
            .then(function(respuestaServidor) {
                return respuestaServidor.json();
            })
            .then(function(data) {
                if (data.estado === "ok") {
                    respuesta.innerHTML = `
                        <div class="alert alert-success">
                            <strong>${data.mensaje}</strong><br>
                            Código pedido: ${data.codigo_pedido}<br>
                            Estado anterior: ${data.estado_anterior}<br>
                            Estado nuevo: ${data.estado_nuevo}
                        </div>
                    `;
                } else {
                    respuesta.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>Error:</strong> ${data.mensaje}
                        </div>
                    `;
                }
            })
            .catch(function() {
                respuesta.innerHTML = `
                    <div class="alert alert-danger">
                        No se pudo conectar con la API de PequeMundo.
                    </div>
                `;
            });
        });
    </script>
</body>
</html>