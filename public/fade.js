function initOverlayFade() {
  const overlay = document.createElement("div");
  overlay.id = "overlay-fade";
  document.body.appendChild(overlay);

  window.addEventListener("load", () => {
    setTimeout(() => {
      overlay.classList.add("hide");
      overlay.style.pointerEvents = "none";
    }, 50);
  });
}
document.addEventListener("DOMContentLoaded", initOverlayFade);


