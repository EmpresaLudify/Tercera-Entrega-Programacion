document.addEventListener("DOMContentLoaded", () => {
  console.log("JS cargado");
  const mensaje = document.querySelector('.mensaje-error');
  if (mensaje) {
    console.log("Mensaje encontrado");
    setTimeout(() => {
      mensaje.style.opacity = '0';
      mensaje.style.transition = 'opacity 1s ease-out';
      setTimeout(() => mensaje.remove(), 1000);
    }, 5000);
  }
});

