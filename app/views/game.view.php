<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Draftosaurus</title>

    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/Game.css">
    <link rel="stylesheet" href="/assets/css/fade.css">

    <!-- JS -->
    <script src="/fade.js" defer></script>
</head>

<body>
    <main class="game-board">
        <?php if ($partida['estado'] === 'pendiente'): ?>
            <div id="bloqueo-partida" class="bloqueo-overlay">
                <div class="mensaje-espera">
                     Esperando a todos los jugadores...
                </div>
            </div>
        <?php endif; ?>
        <div class="contenedor-partida">

            <!-- Tablero -->
            <section class="tablero">
                <img src="/assets/images/tablero.jpg" alt="Tablero de juego">
                <div class="fichas-tablero" id="tablero-zonas">
                    <!-- Zona 1 -->
                    <div class="zona-drop" data-zona="RIVER"
                        style="top: 60px; left: 100px; width: 200px; height: 110px;"></div>
                    <!-- Zona 2 -->
                    <div class="zona-drop" data-zona="FOREST" style="top: 70px; left: 480px;"></div>
                    <!-- Zona 3 -->
                    <div class="zona-drop" data-zona="PLAINS"
                        style="top: 250px; left: 100px; width: 140px; height: 110px;"></div>
                    <!-- Zona 4 -->
                    <div class="zona-drop" data-zona="MOUNTAINS"
                        style="top: 270px; left: 450px; width: 200px; height: 110px;"></div>
                    <!-- Zona 5 -->
                    <div class="zona-drop" data-zona="CAFETERIA"
                        style="top: 450px; left: 140px; width: 150px; height: 140px;"></div>
                    <!-- Zona 6 -->
                    <div class="zona-drop" data-zona="RESTROOMS" style="top: 450px; left: 550px;"></div>
                    <!-- Zona 7 -->
                    <div class="zona-drop" data-zona="T_REX" style="top: 550px; left: 400px;"></div>
                </div>
            </section>
            <!--  Panel lateral -->
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
                                    <div class="ficha" draggable="true" data-especie="<?= $esp ?>"
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
                <div id="zona-dado-zona" class="zona-dado" style="display:none;">
                    <button id="boton-dado-zona">🎲 Tirar dado</button>
                    <p id="resultado-dado-zona">Esperando tirada...</p>
                </div>
                <div id="zona-dado" class="zona-dado">
                    <button id="boton-dado">🎲 Tirar dado</button>
                    <p id="resultado-dado">Esperando tu tirada...</p>
                </div>
            </aside>
        </div>
    </main>

    <!-- JS de la vista -->
    <script>
        let yaColoque = false;           // si ya coloqué mi ficha en la ronda actual
        let ultimoDadoZona = null;       // para detectar cambio de ronda (cara de dado)

        // --- Botones ---
        document.getElementById("salir").addEventListener("click", () => {
            window.location.href = "/index.php?ruta=play";
        });

        document.getElementById("finalizar").addEventListener("click", () => {
            alert("Partida finalizada (función en desarrollo)");
        });

        // --- Storage por partida ---
        const idPartida = <?= (int) $partida['id'] ?>;
        const STORAGE_KEY = `drafto_estado_${idPartida}`;

        function guardarEstado() {
            const estado = {};
            document.querySelectorAll(".zona-drop").forEach(zona => {
                const fichasZona = Array.from(zona.querySelectorAll(".ficha")).map(f => f.dataset.especie);
                estado[zona.dataset.zona] = fichasZona;
            });
            localStorage.setItem(STORAGE_KEY, JSON.stringify(estado));
        }

        function cargarEstado() {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return false;
            const estado = JSON.parse(raw);
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
                        setDnDEnabled(false);
                    }
                });
            });
            return true;
        }

        function limpiarEstado() {
            localStorage.removeItem(STORAGE_KEY);
        }

        // Dejar todo “en la caja”
        function resetBoardToBox() {
            const box = document.getElementById("fichas-jugador");
            document.querySelectorAll(".zona-drop .ficha").forEach(f => {
                f.removeAttribute("style");
                f.draggable = true;
                box.appendChild(f);
            });
            // no guardamos nada todavía: tablero vacío
        }

        // === Control de movimiento ===
        let dndEnabled = false; // ← por defecto bloqueado

        function setDnDEnabled(enabled) {
            dndEnabled = !!enabled;

            // Visual (cursor + opacidad)
            document.querySelectorAll(".ficha").forEach(f => {
                f.draggable = dndEnabled;               // solo permite arrastrar si enabled
                f.classList.toggle("ficha-disabled", !dndEnabled);
            });

            document.querySelectorAll(".zona-drop").forEach(z => {
                z.classList.toggle("zona-disabled", !dndEnabled);
            });
        }

        // Gate para eventos
        function canMoveNow() {
            return dndEnabled === true;
        }

        <?php if ($partida['estado'] !== 'pendiente'): ?>
            // --- Drag & Drop habilitado solo si la partida está en curso ---
            const fichas = document.querySelectorAll(".ficha");
            const zonas = document.querySelectorAll(".zona-drop");

            fichas.forEach(ficha => {
                ficha.addEventListener("dragstart", e => {
                    if (!canMoveNow()) {
                        e.preventDefault();
                        return;
                    }
                    e.dataTransfer.setData("text/plain", ficha.dataset.especie);
                    setTimeout(() => ficha.classList.add("dragging"), 0);
                });

                ficha.addEventListener("dragend", () => ficha.classList.remove("dragging"));
            });

            zonas.forEach(zona => {
                zona.addEventListener("dragover", e => {
                    if (!canMoveNow()) return;
                    if (zona.classList.contains("zona-bloqueada")) return; // respetar restricciones del dado
                    e.preventDefault();
                    zona.classList.add("hover");
                });

                zona.addEventListener("dragleave", () => zona.classList.remove("hover"));

                zona.addEventListener("drop", e => {
                    if (!canMoveNow()) { e.preventDefault(); return; }
                    e.preventDefault();
                    zona.classList.remove("hover");

                    // Verificar que la zona no esté bloqueada (por restricción del dado)
                    if (zona.classList.contains("zona-bloqueada")) {
                        console.warn("No se puede colocar en esta zona según el dado.");
                        return;
                    }

                    const especie = e.dataTransfer.getData("text/plain");
                    const ficha = document.querySelector(`.ficha[data-especie='${especie}']`);
                    const zonaDestino = zona.dataset.zona;

                    // Verificamos que haya un dado de zona activo antes de permitir colocar
                    fetch(`<?= URL_BASE ?>index.php?ruta=estadoPartida&id=${idPartida}`)
                        .then(r => r.json())
                        .then(data => {
                            const hayDado = !!data.dado_zona;
                            if (!hayDado) {
                                console.warn("Debes esperar a que el jugador activo tire el dado de zona.");
                                return;
                            }

                            if (ficha) {
                                zona.appendChild(ficha);
                                ficha.style.position = "absolute";
                                ficha.style.top = "20px";
                                ficha.style.left = "20px";
                                ficha.draggable = false;
                                yaColoque = true;
                                setDnDEnabled(false);

                            }

                            guardarEstado();

                            const body = `idPartida=${idPartida}&especie=${encodeURIComponent(especie)}&zona=${encodeURIComponent(zonaDestino)}`;
                            return fetch("<?= URL_BASE ?>index.php?ruta=colocarYPasarTurno", {
                                method: "POST",
                                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                                body
                            });
                        })
                        .then(res => res ? res.json() : null)
                        .then(resp => {
                            if (!resp) return;
                            if (!resp.ok) {
                                alert(resp.error || "Error al registrar jugada");
                                return;
                            }

                            // Deshabilitar drag & drop para este jugador (ya colocó)
                            setDnDEnabled(false);

                            const resultadoZona = document.getElementById("resultado-dado-zona");

                            if (resp.todos_colocaron) {
                                // Todos colocaron: turno nuevo
                                if (resultadoZona)
                                    resultadoZona.textContent = " Todos colocaron. Turno de " + resp.siguiente;
                            } else {
                                //  Aún faltan jugadores
                                if (resultadoZona)
                                    resultadoZona.textContent = "Esperando que los demás jugadores coloquen...";
                            }

                            console.log(" Ficha colocada correctamente");
                        })
                        .catch(err => console.error("Error en drop:", err));
                });
            });
        <?php else: ?>
            console.log(" Partida pendiente: drag & drop deshabilitado");
        <?php endif; ?>

        // --- Cargar estado previo SOLO si la partida está en curso ---
        <?php if ($partida['estado'] === 'en_curso'): ?>
            window.addEventListener("DOMContentLoaded", () => {
                // Si hay estado guardado lo carga, si no, arranca vacío (todo en caja)
                const tenia = cargarEstado();
                if (!tenia) resetBoardToBox();
            });
        <?php else: ?>
            // Si está pendiente, garantizamos tablero limpio (todo en caja) y SIN cargar nada viejo
            window.addEventListener("DOMContentLoaded", resetBoardToBox);
        <?php endif; ?>

        // === REFRESCO DE JUGADORES ONLINE ===
        const listaJugadores = document.getElementById("lista-jugadores");

        // Esta función consulta al servidor el estado actual
        function actualizarJugadores() {
            fetch(`<?= URL_BASE ?>index.php?ruta=estadoPartida&id=${idPartida}`)
                .then(res => {
                    if (!res.ok) throw new Error("Error HTTP " + res.status);
                    return res.json();
                })
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }

                    // Actualizar lista de jugadores en el panel
                    if (Array.isArray(data.jugadores) && listaJugadores) {
                        listaJugadores.innerHTML = "";
                        data.jugadores.forEach(j => {
                            const li = document.createElement("li");
                            li.textContent = j;
                            listaJugadores.appendChild(li);
                        });
                    }

                    // Si la partida comenzó, dejar de actualizar
                    if (data.estado === "en_curso") {
                        clearInterval(refrescoJugadores);
                        const overlay = document.getElementById("bloqueo-partida");
                        if (overlay) overlay.remove();
                        location.reload();
                    }
                })
                .catch(err => console.error("Error al obtener estado de partida:", err));
        }

        <?php if ($partida['estado'] === 'pendiente'): ?>
            const refrescoJugadores = setInterval(actualizarJugadores, 3000);
            actualizarJugadores();
        <?php endif; ?>

        // Tirar dado
        const botonDado = document.getElementById("boton-dado");
        const resultadoDado = document.getElementById("resultado-dado");
        const overlay = document.getElementById("bloqueo-partida");

        let partidaIniciada = false;

        if (botonDado) {
            botonDado.addEventListener("click", () => {
                botonDado.disabled = true;
                resultadoDado.textContent = "Lanzando...";
                fetch("<?= URL_BASE ?>index.php?ruta=tirarDado", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `idPartida=${idPartida}`
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.ok) {
                            resultadoDado.textContent = `Sacaste un ${data.numero} 🎲`;
                        } else {
                            resultadoDado.textContent = data.error || "Error al tirar el dado";
                        }
                    })
                    .catch(err => {
                        console.error("Error al tirar dado:", err);
                        resultadoDado.textContent = "Error al lanzar el dado.";
                    });
            });
        }

        // Cada 3s revisamos si todos tiraron
        function verificarDados() {
            if (partidaIniciada) return;

            fetch(`<?= URL_BASE ?>index.php?ruta=estadoDados&id=${idPartida}`)
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }

                    // Empate detectado (nueva lógica)
                    if (data.empate) {
                        const empatados = data.jugadoresEmpatados?.join(", ") || "jugadores";
                        resultadoDado.textContent = ` Empate entre ${empatados}. Deben volver a tirar.`;
                        botonDado.disabled = false; // permitir nueva tirada
                        botonDado.style.display = "block"; // mostrar botón si estaba oculto
                        return; // detener el flujo aquí
                    }

                    // Todos tiraron y no hay empate
                    if (data.todosListos && !partidaIniciada) {
                        partidaIniciada = true;

                        const top = data.dados[0];
                        resultadoDado.textContent =
                            `Todos tiraron. ${top.usuario} empieza (sacó ${top.dado_inicial}).`;

                        // Eliminar overlay visual
                        if (overlay) {
                            overlay.style.transition = "opacity 0.8s ease";
                            overlay.style.opacity = 0;
                            setTimeout(() => overlay.remove(), 800);
                        }

                        // Ocultar dado (ya no se usa)
                        botonDado.style.display = "none";

                        // Activar drag & drop dinámicamente sin recargar
                        activarDragYDrop();
                    }
                    // Aún faltan jugadores por tirar
                    else if (!data.todosListos) {
                        const tirados = data.dados.filter(d => d.dado_inicial !== null).length;
                        resultadoDado.textContent = `Tiradas: ${tirados}/${data.dados.length}`;
                    }
                })
                .catch(err => console.error("Error al verificar dados:", err));
        }

        const intervaloDados = setInterval(verificarDados, 3000);
        verificarDados();

        // Función para activar el juego sin recargar
        function activarDragYDrop() {
            yaColoque = false;
            console.log(" Partida en curso: drag & drop habilitado dinámicamente");
            limpiarEstado();
            resetBoardToBox();
            const fichas = document.querySelectorAll(".ficha");
            const zonas = document.querySelectorAll(".zona-drop");

            fichas.forEach(ficha => {
                ficha.draggable = true;
                ficha.addEventListener("dragstart", e => {
                    e.dataTransfer.setData("text/plain", ficha.dataset.especie);
                    setTimeout(() => ficha.classList.add("dragging"), 0);
                });

                ficha.addEventListener("dragend", () => ficha.classList.remove("dragging"));
            });

            zonas.forEach(zona => {
                zona.addEventListener("dragover", e => {
                    if (!canMoveNow()) return;
                    e.preventDefault();
                    zona.classList.add("hover");
                });
                zona.addEventListener("dragleave", () => zona.classList.remove("hover"));

                zona.addEventListener("drop", e => {
                    if (!canMoveNow()) { e.preventDefault(); return; }
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

                        yaColoque = true;
                        setDnDEnabled(false);
                        guardarEstado();
                    }
                });
            });
        }

        // === DADO DE ZONA ===
        const zonaDadoZona = document.getElementById("zona-dado-zona");
        const botonDadoZona = document.getElementById("boton-dado-zona");
        const resultadoZona = document.getElementById("resultado-dado-zona");
        const jugadorActual = "<?= $_SESSION['usuario'] ?>";

        function formatearZona(zona) {
            switch (zona) {
                case "RIVER": return "Zona del Río 💧";
                case "FOREST": return "Bosque 🌲";
                case "PLAINS": return "Llanuras 🌾";
                case "MOUNTAINS": return "Montañas ⛰️";
                case "CAFETERIA": return "Cafetería 🍴";
                case "RESTROOMS": return "Baños 🚻";
                case "T_REX": return "Recinto del T-Rex 🦖";
                default: return zona;
            }
        }

        // Consultar estado cada 3 s (dado zona + turno actual)
        function actualizarEstadoZona() {
            fetch(`<?= URL_BASE ?>index.php?ruta=estadoPartida&id=${idPartida}`)
                .then(r => r.json())
                .then(data => {
                    if (!data) return;

                    const turnoActual = data.turno_actual;
                    window.estadoTurnoActual = turnoActual;
                    const soyYo = turnoActual === jugadorActual;
                    const hayDado = !!data.dado_zona;

                    // detectar nueva ronda por cambio de cara del dado
                    if (data.dado_zona !== ultimoDadoZona) {
                        ultimoDadoZona = data.dado_zona;
                        yaColoque = false;
                    }

                    const zonaDadoZona = document.getElementById("zona-dado-zona");
                    const botonDadoZona = document.getElementById("boton-dado-zona");
                    const resultadoZona = document.getElementById("resultado-dado-zona");

                    // --- Caso 1: No hay dado activo y es mi turno → puedo tirar
                    if (!hayDado && soyYo) {
                        zonaDadoZona.style.display = "block";
                        botonDadoZona.disabled = false;
                        resultadoZona.textContent = " Tu turno: tirá el dado de colocación";
                        limpiarRestriccionesVisuales();
                        setDnDEnabled(false);
                        return;
                    }

                    // --- Caso 2: Hay dado activo → todos deben colocar según esa cara
                    if (hayDado) {
                        zonaDadoZona.style.display = "none";
                        resultadoZona.textContent = `Zona actual: ${formatearZona(data.dado_zona)} (colocá tu ficha)`;
                        aplicarRestriccionesPorDado(data.dado_zona);
                        return;
                    }

                    // --- Caso 3: No hay dado activo y NO soy el turno actual → esperar turno
                    if (!hayDado && !soyYo) {
                        zonaDadoZona.style.display = "none";
                        resultadoZona.textContent = `Esperando a que ${turnoActual} tire el dado...`;
                        limpiarRestriccionesVisuales();
                        setDnDEnabled(false);
                        return;
                    }
                })
                .catch(err => console.error("Error estado dado:", err));
        }

        function limpiarRestriccionesVisuales() {
            document.querySelectorAll(".zona-drop").forEach(z => {
                z.classList.remove("zona-activa", "zona-bloqueada");
            });
        }

        function aplicarRestriccionesPorDado(resultadoDadoZona) {
            // habilitar solo si NO coloqué ya
            setDnDEnabled(!yaColoque);

            // limpiar visuales
            document.querySelectorAll(".zona-drop").forEach(z => {
                z.classList.remove("zona-bloqueada", "zona-activa");
            });

            const zonas = document.querySelectorAll(".zona-drop");
            const turnoActual = window.estadoTurnoActual || null;
            const soyYo = turnoActual === jugadorActual;

            // Si ya coloqué, solo mostrar visual (sin habilitar drag)
            const dragPermitido = !yaColoque;

            // Si soy quien tiró el dado → cualquier zona es válida
            if (soyYo) {
                zonas.forEach(z => z.classList.add("zona-activa"));
                // pero si ya coloqué, dejo drag bloqueado por el gate canMoveNow()
                return;
            }

            // Helper para marcar zonas válidas
            function marcarZonas(validas) {
                zonas.forEach(z => {
                    const id = z.dataset.zona;
                    if (validas.includes(id)) z.classList.add("zona-activa");
                    else z.classList.add("zona-bloqueada");
                });
            }

            switch (resultadoDadoZona) {
                case "EL_BOSQUE": marcarZonas(["RIVER", "FOREST", "PLAINS"]); break;
                case "LLANURA": marcarZonas(["MOUNTAINS", "CAFETERIA", "RESTROOMS"]); break;
                case "BAÑOS": marcarZonas(["FOREST", "MOUNTAINS", "RESTROOMS"]); break;
                case "CAFETERIA": marcarZonas(["RIVER", "PLAINS", "CAFETERIA"]); break;
                case "RECINTO_VACIO":
                    zonas.forEach(z => {
                        const vacio = z.querySelectorAll(".ficha").length === 0;
                        if (vacio) z.classList.add("zona-activa"); else z.classList.add("zona-bloqueada");
                    });
                    break;
                case "CUIDADO_T_REX":
                    zonas.forEach(z => {
                        const fichas = z.querySelectorAll(".ficha");
                        const tieneTrex = Array.from(fichas).some(f => f.dataset.especie === "TREX");
                        if (tieneTrex) z.classList.add("zona-bloqueada"); else z.classList.add("zona-activa");
                    });
                    break;
            }
        }

        setInterval(actualizarEstadoZona, 3000);
        actualizarEstadoZona();

        // Acción: tirar dado de zona
        if (botonDadoZona) {
            botonDadoZona.addEventListener("click", () => {
                botonDadoZona.disabled = true;
                resultadoZona.textContent = "Lanzando...";
                fetch("<?= URL_BASE ?>index.php?ruta=tirarDadoZona", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `idPartida=${idPartida}`
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.ok) {
                            resultadoZona.textContent = ` Te tocó: ${formatearZona(data.resultado)}`;
                            setDnDEnabled(true);
                            zonaDadoZona.style.display = "none";
                        } else {
                            resultadoZona.textContent = data.error || "Error al tirar el dado.";
                        }
                    })
                    .catch(err => {
                        console.error("Error al tirar dado de zona:", err);
                        resultadoZona.textContent = "Error al tirar el dado.";
                    });
            });
        }

        console.log(" Script de game.view cargado correctamente");
    </script>

</body>

</html>