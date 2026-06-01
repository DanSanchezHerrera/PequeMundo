<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Externo - Empresa de Mensajería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <div class="text-center mb-4">
                    <h2 class="text-primary">🚚 Sistema de Mensajería Externo</h2>
                    <p class="text-muted small">Simulador de repartos que consume nuestra API (Sin conexión a Base de Datos)</p>
                </div>

                <form id="formActualizar">
                    <div class="mb-3">
                        <label for="codigo_pedido" class="form-label">Código del Pedido:</label>
                        <input type="text" class="form-control" id="codigo_pedido" placeholder="Ej: PED-123456" required>
                    </div>

                    <div class="mb-3">
                        <label for="estado_pedido" class="form-label">Nuevo Estado del Envío:</label>
                        <select class="form-select" id="estado_pedido" required>
                            <option value="">-- Seleccionar Estado --</option>
                            <option value="en_camino">En Camino 📦</option>
                            <option value="entregado">Entregado ✅</option>
                            <option value="cancelado">Cancelado ❌</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Actualizar en PequeMundo via API</button>
                </form>

                <div id="resultado" class="mt-4 alert d-none"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('formActualizar').addEventListener('submit', function(e) {
    e.preventDefault();

    const codigo = document.getElementById('codigo_pedido').value;
    const estado = document.getElementById('estado_pedido').value;
    const resultadoDiv = document.getElementById('resultado');

    // URL hay que cambiarlo a la de la Dani
    const urlAPI = 'http://pequemundo.xo.je/api/actualizar_estado_pedido.php';

    // Preparar los datos para enviarlos a la API
    const datos = new FormData();
    datos.append('codigo_pedido', codigo);
    datos.append('estado_pedido', estado);

    // Consumir la API usando FETCH (HTTP POST)
    fetch(urlAPI, {
        method: 'POST',
        body: datos
    })
    .then(response => response.json())
    .then(data => {
        resultadoDiv.classList.remove('d-none', 'alert-danger', 'alert-success');
        
        if(data.status === 'success' || data.success === true) {
            resultadoDiv.classList.add('alert-success');
            resultadoDiv.innerHTML = `<strong>¡Éxito!</strong> ${data.message || 'El estado del pedido fue actualizado correctamente.'}`;
        } else {
            resultadoDiv.classList.add('alert-danger');
            resultadoDiv.innerHTML = `<strong>Error de la API:</strong> ${data.message || 'No se pudo actualizar.'}`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        resultadoDiv.classList.remove('d-none', 'alert-success');
        resultadoDiv.classList.add('alert-danger');
        resultadoDiv.innerHTML = '<strong>Error de red:</strong> No se pudo conectar con el endpoint de la API.';
    });
});
</script>

</body>
</html>