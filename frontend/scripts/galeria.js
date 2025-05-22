async function cargarGaleria() {
  const container = document.getElementById("galeria");
  const errorDiv = document.getElementById("galeria-error");
  container.innerHTML = "";
  errorDiv.innerText = "";

  let fotos = [];
  try {
    const res = await fetch("../../backend/galeria.php");
    if (!res.ok) throw new Error("HTTP error: " + res.status);
    const text = await res.text();
    console.log("Respuesta galeria.php(text):", text.length, text);

    // Verifica si el texto contiene algo inesperado
    if (!text.trim() || text.trim() === "0") {
      errorDiv.innerText = "Respuesta vacía o inesperada del backend (JS)";
      return;
    }

    fotos = JSON.parse(text);
    if (!Array.isArray(fotos)) throw new Error("Respuesta no es un array");
  } catch (e) {
    errorDiv.innerText = "No se pudo cargar la galería.";
    console.error("Error al cargar la galería:", e);
    return;
  }

  if (fotos.length === 0) {
    container.innerHTML = "<p>No hay fotos admitidas.</p>";
    return;
  }

  fotos.forEach(foto => {
    const div = document.createElement("div");
    div.style.textAlign = "center";
    div.innerHTML = `
      <img src="../../backend/ver_foto.php?id=${foto.id}" width="250" /><br>
      <b>${foto.titulo}</b><br>
      <span>Autor: ${foto.autor || ""}</span>
    `;
    container.appendChild(div);
  });
}

document.addEventListener("DOMContentLoaded", cargarGaleria);
