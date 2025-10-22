<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Unirse a partida</title>
  <link rel="stylesheet" href="assets/css/join.css">
  <link rel="stylesheet" href="assets/css/fade.css">
</head>

<body>
  <div class="config-container">
    <form action="index.php?ruta=join" method="POST">
      <input type="text" name="nombre" id="nombre" placeholder="Escribe el nombre" required>

      <div id="password-container">
        <input type="password" name="password" id="password" placeholder="Ingresa una contraseña">
      </div>

      <button type="submit"></button>
    </form>

    <div id="zona-crear" class="zona-crear"></div>
    <div id="zona-volver" class="zona-voler"></div>

    <?php if (isset($mensaje) && $mensaje): ?>
      <div class="mensaje-error">
        <p><?= htmlspecialchars($mensaje) ?></p>
      </div>
    <?php endif; ?>
  </div>

  <script>
    // Ir a crear partida
    document.getElementById("zona-crear").addEventListener("click", () => {
      window.location.href = "index.php?ruta=newGame";
    });

    // Volver al menú
    document.getElementById("zona-volver").addEventListener("click", () => {
      window.location.href = "index.php?ruta=play";
    });
  </script>

  <script src="fade.js"></script>
</body>
</html>
