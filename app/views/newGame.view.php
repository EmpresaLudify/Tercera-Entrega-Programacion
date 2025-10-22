<?php /* variables disponibles: $mensaje */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nueva Partida - Draftosaurus</title>

  <!-- ✅ CSS correcto -->
  <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/NewGame.css">
  <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/fade.css">
</head>

<body>
  <div class="config-container">
    <form id="form-nueva-partida" action="<?= URL_BASE ?>index.php?ruta=newGame" method="POST">
      <input type="text" name="nombre" id="nombre" placeholder="Nombre de partida" required>

      <!-- Selección de jugadores -->
      <div id="jugadores-container">
        <div class="jugador-option" data-value="2"></div>
        <div class="jugador-option" data-value="3"></div>
        <div class="jugador-option" data-value="4"></div>
        <div class="jugador-option" data-value="5"></div>
        <input type="hidden" name="jugadores" id="jugadores" required>
      </div>

      <div id="zonas-tipo">
        <div id="zona-seguimiento" class="zona-tipo"></div>
        <div id="zona-online" class="zona-tipo"></div>
        <div id="zona-unirse"></div>
        <input type="hidden" name="tipo" id="tipo" required>
      </div>

      <div id="password-container" style="display:none;">
        <!-- ✅ Ruta corregida -->
        <img id="barra-contraseña" src="<?= URL_BASE ?>assets/images/BarraContraseña.png" alt="Barra contraseña">
        <label for="password"></label>
        <input type="password" name="password" id="password" placeholder="Ingresa una contraseña">
      </div>

      <div id="nombres-jugadores-container"></div>

      <button type="submit">Crear partida</button>
    </form>

    <div id="zona-volver" class="zona-volver"></div>
  </div>

  <!-- ✅ JS con rutas correctas -->
  <script>
    const jugadoresContainer = document.getElementById("jugadores-container");
    const jugadoresInput = document.getElementById("jugadores");

    const tipoInput = document.getElementById("tipo");
    const zonaSeguimiento = document.getElementById("zona-seguimiento");
    const zonaOnline = document.getElementById("zona-online");

    const passwordContainer = document.getElementById("password-container");
    const passwordInput = document.getElementById("password");
    const nombresContainer = document.getElementById("nombres-jugadores-container");

    document.getElementById("zona-unirse").addEventListener("click", () => {
      window.location.href = "<?= URL_BASE ?>index.php?ruta=join";
    });

    document.getElementById("zona-volver").addEventListener("click", () => {
      window.location.href = "<?= URL_BASE ?>index.php?ruta=play";
    });

    // Selección de jugadores
    document.querySelectorAll(".jugador-option").forEach(opt => {
      opt.addEventListener("click", () => {
        document.querySelectorAll(".jugador-option").forEach(o => o.classList.remove("selected"));
        opt.classList.add("selected");
        jugadoresInput.value = opt.dataset.value;
        actualizarCampos();
      });
    });

    // Tipo de partida
    zonaSeguimiento.addEventListener("click", () => {
      tipoInput.value = "seguimiento";
      zonaSeguimiento.classList.add("selected");
      zonaOnline.classList.remove("selected");
      actualizarCampos();
    });

    zonaOnline.addEventListener("click", () => {
      tipoInput.value = "online";
      zonaOnline.classList.add("selected");
      zonaSeguimiento.classList.remove("selected");
      actualizarCampos();
    });

    function actualizarCampos() {
      const tipo = tipoInput.value;
      const cantidad = parseInt(jugadoresInput.value);

      // Contraseña (solo online)
      if (tipo === "online") {
        passwordContainer.style.display = "block";
        passwordInput.required = true;
      } else {
        passwordContainer.style.display = "none";
        passwordInput.required = false;
      }

      // Campos de jugadores (solo seguimiento)
      nombresContainer.innerHTML = "";
      if (tipo === "seguimiento" && cantidad > 0) {
        for (let i = 1; i <= cantidad; i++) {
          const input = document.createElement("input");
          input.type = "text";
          input.name = `nombre_jugador_${i}`;
          input.placeholder = `Jugador ${i}`;
          input.required = true;
          nombresContainer.appendChild(input);
        }
      }
    }
  </script>

  <!-- ✅ Fade -->
  <script src="<?= URL_BASE ?>fade.js"></script>
</body>
</html>
