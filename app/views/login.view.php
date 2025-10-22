<?php /* variables disponibles: $mensaje */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Login - Draftosaurus</title>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/login.css">
  <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/fade.css">
</head>

<body>
  <div class="login-container">
    <!-- Botones con data-href para usar con fade.js -->
    <div id="zona-registro" data-href="<?= URL_BASE ?>index.php?ruta=register"></div>
    <div id="zona-volver" data-href="<?= URL_BASE ?>index.php?ruta=home"></div>

    <!-- Formulario de login -->
    <form class="login-form" action="<?= URL_BASE ?>index.php?ruta=login" method="POST">
      <input id="campo-usuario" type="text" name="usuario" placeholder="Usuario" required>
      <input id="campo-pass" type="password" name="password" placeholder="Contraseña" required>
      <button id="zona-boton" type="submit"></button>
    </form>

    <!-- Mensaje de error (imagen dinámica) -->
    <?php if (isset($mensaje)): ?>
      <div class="mensaje-error">
        <img src="<?= URL_BASE ?>assets/images/<?= htmlspecialchars($mensaje) ?>" alt="Error de inicio de sesión">
      </div>
    <?php endif; ?>
  </div>

  <!-- Script para ocultar mensaje de error -->
  <script src="<?= URL_BASE ?>assets/js/index.js"></script>

  <!-- Efecto de transición -->
  <script src="<?= URL_BASE ?>fade.js"></script>
  <script>
document.addEventListener("DOMContentLoaded", () => {
  const reg = document.getElementById("zona-registro");
  const vol = document.getElementById("zona-volver");

  if (reg) {
    reg.style.cursor = "pointer";
    reg.addEventListener("click", () => {
      console.log("CLICK REGISTRO");
      window.location.href = "<?= URL_BASE ?>index.php?ruta=register";
    });
  }

  if (vol) {
    vol.style.cursor = "pointer";
    vol.addEventListener("click", () => {
      console.log("CLICK VOLVER");
      window.location.href = "<?= URL_BASE ?>index.php?ruta=home";
    });
  }
});
</script>

</body>
</html>
