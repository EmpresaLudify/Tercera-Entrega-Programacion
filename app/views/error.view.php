<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Error - Partida no encontrada</title>

  <!-- ✅ CSS -->
  <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/error.css">
</head>
<body>
  <div class="error-container">
    <img src="<?= URL_BASE ?>assets/images/ErrorPartida.jpg" alt="Error: Partida no encontrada">
    <form action="<?= URL_BASE ?>index.php" method="get">
      <input type="hidden" name="ruta" value="play">
      <button type="submit" class="volver-btn" aria-label="Volver"></button>
    </form>
  </div>

  <!-- ✅ JS (opcional si querés mantener el fade global) -->
  <script src="<?= URL_BASE ?>fade.js"></script>
</body>
</html>
