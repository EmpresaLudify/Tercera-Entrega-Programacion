<?php /* variables disponibles: $mensaje */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Login - Draftosaurus</title>

  <!-- CSS -->
  <link rel="stylesheet" href="/assets/css/login.css">
  <link rel="stylesheet" href="/assets/css/fade.css">
</head>

<body>
    <div class="login-stage">
    <div class="login-image">
      <!-- Zonas clickeables -->
      <div id="zona-registro" data-href="/index.php?ruta=register"></div>
      <div id="zona-volver" data-href="/index.php?ruta=home"></div>

      <!-- Campos -->
      <form class="login-form" action="/index.php?ruta=login" method="POST">
        <input id="campo-usuario" type="text" name="usuario" placeholder="Usuario" required autocomplete="off">
        <input id="campo-pass" type="password" name="password" placeholder="Contraseña" required autocomplete="off">
        <button id="zona-boton" type="submit"></button>
      </form>

      <!-- Mensaje de error -->
      <?php if (isset($mensaje)): ?>
        <div class="mensaje-error">
          <img src="/assets/images/<?= htmlspecialchars($mensaje) ?>" alt="Error de inicio de sesión">
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- JS -->
  <script src="/fade.js"></script>
  <script>
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-href]").forEach(zona => {
      zona.addEventListener("click", () => {
        const destino = zona.getAttribute("data-href");
        if (destino) {
          document.body.classList.add("fade-out");
          setTimeout(() => window.location.href = destino, 300);
        }
      });
    });
  });
  </script>
</body>
</html>
