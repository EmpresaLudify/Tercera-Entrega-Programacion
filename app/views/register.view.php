<?php /* variables disponibles: $mensaje */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro - Draftosaurus</title>

  <!-- ✅ CSS -->
  <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/register.css">
  <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/fade.css">
</head>

<body>
  <!-- 🔙 Volver -->
  <div id="zona-volver"></div>

  <!-- 🦕 Formulario de registro -->
  <div class="register-container">
    <form id="form-register" action="<?= URL_BASE ?>index.php?ruta=register" method="POST">
      <div class="input-group">
        <input id="usuario" type="text" name="usuario" placeholder="Nombre de usuario" required>
      </div>
      <div class="input-group">
        <input id="email" type="text" name="email" placeholder="Email" required>
      </div>
      <div class="input-group">
        <input id="password" type="password" name="password" placeholder="Contraseña" required>
      </div>
      <div class="input-group">
        <input id="confirmar" type="password" name="confirmar" placeholder="Confirmar contraseña" required>
      </div>
      <div id="zona-enviar"></div>
    </form>

    <!-- ⚠️ Mensaje de error -->
    <?php if (isset($_SESSION['mensaje'])): ?>
      <div class="mensaje-error">
        <img src="<?= URL_BASE ?>assets/images/<?= htmlspecialchars($_SESSION['mensaje']) ?>" alt="Error">
      </div>
      <div class="iluminacion">
        <img src="<?= URL_BASE ?>assets/images/<?= htmlspecialchars($_SESSION['mensaje2'] ?? '') ?>" alt="Iluminación">
      </div>
      <?php unset($_SESSION['mensaje'], $_SESSION['mensaje2']); ?>
    <?php endif; ?>
  </div>

  <!-- 🧠 Scripts -->
  <script>
    document.getElementById("zona-volver").addEventListener("click", () => {
      window.location.href = "<?= URL_BASE ?>index.php?ruta=login";
    });

    document.getElementById("zona-enviar").addEventListener("click", () => {
      document.getElementById("form-register").submit();
    });

    setTimeout(() => {
      const msg = document.querySelector('.mensaje-error');
      if (msg) msg.style.display = 'none';
    }, 3000);
  </script>

  <!-- 🌈 Fade effect -->
  <script src="<?= URL_BASE ?>fade.js"></script>
</body>
</html>
