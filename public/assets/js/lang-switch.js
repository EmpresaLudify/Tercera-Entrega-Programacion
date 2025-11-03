document.addEventListener("DOMContentLoaded", () => {
  const zonaIdioma = document.getElementById("zona-idioma");

  const imagenes = {
    es: {
      home: "EntradaAlParque2.jpg",
      login: "FondoInicioSesion.jpg",
      register: "RegisterEsp.jpg",
      creditos: "CreditosEsp.jpg",
      comojugar: "ComoJugarEsp.jpg"
    },
    en: {
      home: "EntradaAlParque1.jpg",
      login: "LoginEng.jpg",
      register: "RegisterEng.jpg",
      creditos: "CreditosEng.jpg",
      comojugar: "ComoJugarEng.jpg"
    }
  };

  let idioma = localStorage.getItem("idioma") || "es";

  function aplicarIdioma() {
    const imgs = document.querySelectorAll("img");
    imgs.forEach(img => {
      const src = img.src.split("/").pop();
      Object.entries(imagenes.es).forEach(([key, esImg]) => {
        const enImg = imagenes.en[key];
        if (src === esImg || src === enImg) {
          img.src = `${URL_BASE}assets/images/${idioma === "en" ? enImg : esImg}`;
        }
      });
    });
    console.log(` Idioma actual: ${idioma.toUpperCase()}`);
  }

  aplicarIdioma();

  if (zonaIdioma) {
    zonaIdioma.addEventListener("click", () => {
      idioma = idioma === "es" ? "en" : "es";
      localStorage.setItem("idioma", idioma);
      aplicarIdioma();
    });
  }
});
