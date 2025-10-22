function initOverlayFade() {
  const overlay = document.createElement("div");
  overlay.id = "overlay-fade";
  document.body.appendChild(overlay);

  // Fade out al cargar
  window.addEventListener("load", () => {
    setTimeout(() => overlay.classList.add("hide"), 50);
  });

  // Fade in para links y botones submit
  document.querySelectorAll("a, button[type=submit], [data-href]").forEach(el => {
    el.addEventListener("click", e => {
      // 1️⃣ Si tiene data-href (como en el login)
      const target = el.getAttribute("data-href") || el.getAttribute("href");
      if (target && target !== "#") {
        e.preventDefault();
        overlay.classList.remove("hide");
        setTimeout(() => {
          window.location.href = target;
        }, 500); // igual que tu animación CSS
      }
    });
  });
}

document.addEventListener("DOMContentLoaded", initOverlayFade);
