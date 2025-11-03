<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Draftosaurus</title>

  <!--  CSS -->
  <link rel="stylesheet" href="/assets/css/home.css" />
  <link rel="stylesheet" href="/assets/css/fade.css" />
</head>

<body>
  <div class="home-stage">
    <div class="home-image">
      <img id="imagen-parque" src="/assets/images/EntradaAlParque2.jpg" alt="Entrada al parque">

      <!--  Zona de cambio de idioma -->
      <div id="zona-idioma"></div>

      <!--  Zonas clickeables -->
      <div id="zona-jugar"></div>
      <div id="zona-como-jugar"></div>
      <div id="zona-creditos"></div>
      <div id="zona-taquilla"></div>
    </div>

    <!-- Header (usuario logueado) -->
    <header class="<?= $sesion ? 'con-sesion' : '' ?>">
      <?php if ($sesion): ?>
        <p class="level"><?= htmlspecialchars($lvl) ?></p>

        <form method="POST" action="/index.php?ruta=home">
          <button type="submit" name="perfil" class="bienvenida">
            <?= htmlspecialchars($usuarioNombre) ?>
          </button>
        </form>

        <form method="POST" action="/index.php?ruta=home">
          <button type="submit" name="cerrar" class="cerrar-sesion">Cerrar sesión</button>
        </form>
      <?php endif; ?>
    </header>

    <!--  Mensajes de error o inicio -->
    <?php if (isset($mensaje)): ?>
      <div class="mensaje-error">
        <img src="/assets/images/<?= htmlspecialchars($mensaje) ?>" alt="Mensaje de error">
      </div>
      <div class="iluminacion">
        <img src="/assets/images/<?= htmlspecialchars($mensaje2) ?>" alt="Iluminación">
      </div>
    <?php endif; ?>
  </div>

  <!--  JS -->
  <!--  Definir primero la URL base (así no da error en lang-switch.js) -->

  <!--  Fade de transición -->
  <script src="/fade.js"></script>

  <!--  Zonas clickeables -->
  <script>
document.addEventListener("DOMContentLoaded", () => {
  const zonas = {
    jugar: "index.php?ruta=home",
    taquilla: "index.php?ruta=login",
    como: "assets/html/comojugar.html",
    creditos: "assets/html/creditos.html",
  };

  // Diagnóstico visual opcional (borde transparente)
  Object.keys(zonas).forEach(id => {
    const el = document.getElementById(`zona-${id}`);
    if (el) {
      el.style.pointerEvents = "auto";
      el.style.zIndex = 10;
      el.addEventListener("click", () => {
        console.log(` Click en zona-${id}`);
        if (id === "jugar") {
          const form = document.createElement("form");
          form.method = "POST";
          form.action = zonas[id];
          const input = document.createElement("input");
          input.type = "hidden";
          input.name = "jugar";
          input.value = "1";
          form.appendChild(input);
          document.body.appendChild(form);
          form.submit();
        } else {
          window.location.href = zonas[id];
        }
      });
    }
  });
});
</script>
<script>
  const URL_BASE = "/";
</script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  function ajustarRotacion() {
    const esVertical = window.innerHeight > window.innerWidth;
    const stage = document.querySelector(".home-stage");

    if (esVertical) {
      document.body.style.width = window.innerHeight + "px";
      document.body.style.height = window.innerWidth + "px";
      stage.style.transform = "rotate(-90deg)";
      stage.style.width = window.innerHeight + "px";
      stage.style.height = window.innerWidth + "px";
    } else {
      document.body.style.width = "";
      document.body.style.height = "";
      stage.style.transform = "";
      stage.style.width = "";
      stage.style.height = "";
    }
  }

  ajustarRotacion();
  window.addEventListener("resize", ajustarRotacion);
});
</script>
  <!--  Switch de idioma -->
<script src="/assets/js/lang-switch.js" defer></script>
</body>
</html>
