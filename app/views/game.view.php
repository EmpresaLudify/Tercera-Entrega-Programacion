<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Draftosaurus</title>

    <!-- ✅ CSS -->
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/Game.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/fade.css">

    <!-- ✅ JS -->
    <script src="<?= URL_BASE ?>fade.js" defer></script>
</head>

<body>
    <main class="game-board">
        <div class="contenedor-partida">

            <!-- 🦕 Tablero -->
            <section class="tablero">
                <img src="<?= URL_BASE ?>assets/images/tablero.jpg" alt="Tablero de juego">

                <div class="fichas-tablero" id="tablero-zonas">
                    <!-- Zonas de drop -->
                    <div class="zona-drop" data-zona="WOODLANDS" style="top: 80px; left: 120px;"></div>
                    <div class="zona-drop" data-zona="GRASSLANDS" style="top: 250px; left: 300px;"></div>
                    <div class="zona-drop" data-zona="LEFT" style="top: 180px; left: 50px;"></div>
                    <div class="zona-drop" data-zona="RIGHT" style="top: 180px; left: 480px;"></div>
                </div>
            </section>

            <!-- 🧭 Panel lateral -->
            <aside class="panel-derecho">
                <div class="acciones">
                    <button id="finalizar">Finalizar</button>
                    <button id="salir">Salir</button>
                </div>

                <div class="info-partida">
                    <h2 id="nombre-partida">
                        <?= htmlspecialchars($partida['nombre'] ?? 'Partida sin nombre') ?> |
                        Jugadores <?= isset($jugadores) ? count($jugadores) : 0 ?>
                    </h2>

                    <ul id="lista-jugadores">
                        <?php if (!empty($jugadores)): ?>
                            <?php foreach ($jugadores as $jug): ?>
                                <li><?= htmlspecialchars($jug) ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>Esperando jugadores...</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="abajo-panel">
                    <div class="cartel-advertencia">
                        <strong>Cartel de advertencia</strong>
                    </div>

                    <div class="fichas-panel" id="fichas-panel">
                        <h3>Fichas</h3>
                        <div class="fichas-jugador" id="fichas-jugador">
                            <?php if (!empty($mano)): ?>
                                <?php foreach ($mano as $esp): ?>
                                    <div class="ficha"
                                         draggable="true"
                                         data-especie="<?= $esp ?>"
                                         style="background-color: <?= htmlspecialchars($colores[$esp] ?? '#777') ?>">
                                         <?= htmlspecialchars($esp) ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No hay fichas disponibles.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    <!-- ✅ JS de la vista -->
    <script>
        // --- Botones ---
        document.getElementById("salir").addEventListener("click", () => {
            window.location.href = "<?= URL_BASE ?>index.php?ruta=play";
        });

        document.getElementById("finalizar").addEventListener("click", () => {
            alert("Partida finalizada (función en desarrollo)");
        });

        // --- FUNCIONES DE GUARDADO ---
        function guardarEstado() {
            const estado = {};
            document.querySelectorAll(".zona-drop").forEach(zona => {
                const fichasZona = Array.from(zona.querySelectorAll(".ficha"))
                    .map(f => f.dataset.especie);
                estado[zona.dataset.zona] = fichasZona;
            });

            // Guardar en localStorage
            localStorage.setItem("estadoPartida", JSON.stringify(estado));
        }

        function cargarEstado() {
            const estadoGuardado = localStorage.getItem("estadoPartida");
            if (!estadoGuardado) return;

            const estado = JSON.parse(estadoGuardado);
            Object.entries(estado).forEach(([zonaId, especies]) => {
                const zona = document.querySelector(`.zona-drop[data-zona='${zonaId}']`);
                especies.forEach(especie => {
                    const ficha = document.querySelector(`.ficha[data-especie='${especie}']`);
                    if (ficha && zona) {
                        zona.appendChild(ficha);
                        ficha.style.position = "absolute";
                        ficha.style.top = "20px";
                        ficha.style.left = "20px";
                        ficha.draggable = false;
                    }
                });
            });
        }

        // --- Drag & Drop ---
        const fichas = document.querySelectorAll(".ficha");
        const zonas = document.querySelectorAll(".zona-drop");

        fichas.forEach(ficha => {
            ficha.addEventListener("dragstart", e => {
                e.dataTransfer.setData("text/plain", ficha.dataset.especie);
                setTimeout(() => ficha.classList.add("dragging"), 0);
            });

            ficha.addEventListener("dragend", () => ficha.classList.remove("dragging"));
        });

        zonas.forEach(zona => {
            zona.addEventListener("dragover", e => {
                e.preventDefault();
                zona.classList.add("hover");
            });

            zona.addEventListener("dragleave", () => zona.classList.remove("hover"));

            zona.addEventListener("drop", e => {
                e.preventDefault();
                zona.classList.remove("hover");

                const especie = e.dataTransfer.getData("text/plain");
                const ficha = document.querySelector(`.ficha[data-especie='${especie}']`);

                if (ficha) {
                    zona.appendChild(ficha);
                    ficha.style.position = "absolute";
                    ficha.style.top = "20px";
                    ficha.style.left = "20px";
                    ficha.draggable = false;

                    guardarEstado(); // 🔒 Guarda cada movimiento
                }
            });
        });

        // --- Cargar estado previo ---
        window.addEventListener("DOMContentLoaded", cargarEstado);
    </script>
</body>
</html>
