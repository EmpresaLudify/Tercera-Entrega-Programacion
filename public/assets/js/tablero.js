document.addEventListener("DOMContentLoaded", () => {
  const fichas = document.querySelectorAll(".ficha");
  const zonas = document.querySelectorAll(".zona-drop");
  let fichaArrastrada = null;
  let turnoActual = 1;

  const jugadorActual = window.USUARIO_ACTUAL || "<?= $_SESSION['usuario'] ?? 'Invitado' ?>";

  console.log("🎮 Sistema de tablero con guardado local iniciado");

  // Si no existe la estructura local, crearla
  if (!localStorage.getItem("movimientos_partida")) {
    localStorage.setItem("movimientos_partida", JSON.stringify([]));
  }

  // 🦕 Preparar fichas para arrastrar
  fichas.forEach(ficha => {
    ficha.setAttribute("draggable", "true");

    ficha.addEventListener("dragstart", e => {
      fichaArrastrada = ficha;
      e.dataTransfer.setData("text/plain", ficha.dataset.especie);
      setTimeout(() => ficha.classList.add("dragging"), 0);
    });

    ficha.addEventListener("dragend", () => {
      ficha.classList.remove("dragging");
      fichaArrastrada = null;
    });
  });

  // 🎯 Preparar zonas de drop
  zonas.forEach(zona => {
    zona.addEventListener("dragover", e => {
      e.preventDefault();
      zona.classList.add("hover");
    });

    zona.addEventListener("dragleave", () => zona.classList.remove("hover"));

    zona.addEventListener("drop", async e => {
      e.preventDefault();
      zona.classList.remove("hover");

      if (!fichaArrastrada) return;

      // Evitar soltar más de una ficha por zona (opcional)
      if (zona.querySelector(".ficha")) {
        alert("Esa zona ya tiene una ficha colocada.");
        return;
      }

      // Mover visualmente la ficha
      zona.appendChild(fichaArrastrada);
      fichaArrastrada.classList.add("en-tablero");
      fichaArrastrada.draggable = false;

      // Registrar la jugada localmente 💾
      const movimiento = {
  id_partida: window.PARTIDA_ID, // 🧩 ahora lo guarda correctamente
  jugador: jugadorActual,
  color: fichaArrastrada.style.backgroundColor || "#777",
  zona: zona.dataset.zona,
  turno: turnoActual++,
  puntos: calcularPuntos(zona.dataset.zona, fichaArrastrada.dataset.especie),
  fecha: new Date().toISOString()
};


      guardarMovimiento(
        movimiento.jugador,
        movimiento.color,
        movimiento.zona,
        movimiento.turno,
        movimiento.puntos
      );

      // (opcional) también guardamos directo en localStorage manualmente
      const movimientos = JSON.parse(localStorage.getItem("movimientos_partida"));
      movimientos.push(movimiento);
      localStorage.setItem("movimientos_partida", JSON.stringify(movimientos));

      console.log("💾 Movimiento registrado:", movimiento);

      // Enviar la jugada al backend inmediatamente (si querés mantener eso)
      try {
        await fetch("index.php?ruta=game/colocarDino", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            nombre: fichaArrastrada.dataset.especie,
            jugador: jugadorActual,
            zona: zona.dataset.zona
          })
        });
        console.log("✅ Jugada enviada al backend");
      } catch (err) {
        console.error("❌ Error al registrar jugada:", err);
      }
    });
  });

  // 🧮 Calcular puntos según zona (ejemplo simple)
  function calcularPuntos(zona, especie) {
    switch (zona) {
      case "WOODLANDS": return 6;
      case "GRASSLANDS": return 4;
      case "LEFT": return 3;
      case "RIGHT": return 5;
      default: return 0;
    }
  }
});
