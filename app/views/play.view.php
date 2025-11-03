<?php /* variables disponibles: $usuarioNombre, $lvl */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Draftosaurus - Menú</title>

  <!--  CSS -->
  <link rel="stylesheet" href="/assets/css/play.css">
  <link rel="stylesheet" href="/assets/css/fade.css">
</head>

<body>
  <header>
    <div class="acciones">
      <p class="level">Lvl <?= htmlspecialchars($lvl ?? 1) ?></p>
      <p class="bienvenida"><?= htmlspecialchars($usuarioNombre ?? '') ?></p>
    </div>
  </header>

  <main class="menu-central">
    <div id="zona-NuevaPartida"></div>
    <div id="zona-Opciones"></div>
    <div id="zona-Salir" data-href="/index.php?ruta=home"></div>
  </main>

  <script>
    // --- Nueva Partida ---
    document.getElementById("zona-NuevaPartida").addEventListener("click", () => {
      window.location.href = "/index.php?ruta=newGame";
    });

    // --- Opciones ---
    document.getElementById("zona-Opciones").addEventListener("click", () => {
      alert("Opciones en construcción");
    });
  </script>

  <!--  JS -->
  <script src="/fade.js"></script>
</body>
</html>
