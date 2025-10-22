<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Draftosaurus</title>
  <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/index.css" />
  <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/fade.css" />
</head>

<body>
  <header class="<?= $sesion ? 'con-sesion' : '' ?>">
    <?php if ($sesion): ?>
      <p class="level"><?= htmlspecialchars($lvl) ?></p>

      <form method="POST" action="<?= URL_BASE ?>index.php?ruta=home">
        <button type="submit" name="perfil" class="bienvenida">
          <?= htmlspecialchars($usuarioNombre) ?>
        </button>
      </form>

      <form method="POST" action="<?= URL_BASE ?>index.php?ruta=home">
        <button type="submit" name="cerrar" class="cerrar-sesion">Cerrar sesión</button>
      </form>
    <?php else: ?>
      <div id="zona-taquilla"></div>
    <?php endif; ?>
  </header>

  <?php if (isset($mensaje)): ?>
    <div class="mensaje-error">
      <img src="<?= URL_BASE ?>assets/images/<?= htmlspecialchars($mensaje) ?>" alt="Debes iniciar sesión" />
    </div>
    <div class="iluminacion">
      <img src="<?= URL_BASE ?>assets/images/<?= htmlspecialchars($mensaje2) ?>" alt="Iluminación de inicio" />
    </div>
  <?php endif; ?>

  <main class="menu-central">
    <div id="zona-jugar"></div>
    <div id="zona-como-jugar"></div>
    <div id="zona-creditos"></div>
  </main>

<script>
document.addEventListener("DOMContentLoaded", () => {
  // --- Botón JUGAR ---
  const jugar = document.getElementById("zona-jugar");
  if (jugar) {
    jugar.addEventListener("click", () => {
      const form = document.createElement("form");
      form.method = "POST";
      form.action = "<?= URL_BASE ?>index.php?ruta=home";
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = "jugar";
      input.value = "1";
      form.appendChild(input);
      document.body.appendChild(form);
      form.submit();
    });
  }

  // --- Botón CÓMO JUGAR ---
  const como = document.getElementById("zona-como-jugar");
  if (como) {
    como.addEventListener("click", () => {
      window.location.href = "<?= URL_BASE ?>assets/html/comojugar.html";
    });
  }

  // --- Botón CRÉDITOS ---
  const creditos = document.getElementById("zona-creditos");
  if (creditos) {
    creditos.addEventListener("click", () => {
      window.location.href = "<?= URL_BASE ?>assets/html/creditos.html";
    });
  }

  // --- Botón TAQUILLA (solo si no hay sesión) ---
  const taquilla = document.getElementById("zona-taquilla");
  if (taquilla) {
    taquilla.addEventListener("click", () => {
      window.location.href = "<?= URL_BASE ?>index.php?ruta=login";
    });
  }
});
</script>
</body>
</html>
