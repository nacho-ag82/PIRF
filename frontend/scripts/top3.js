async function cargarGaleria() {
  const container = document.getElementById("galeria");
  const errorDiv = document.getElementById("galeria-error");
  container.innerHTML = "";
  errorDiv.innerText = "";

  let top3 = [];
  try {
    const resTop = await fetch('../../backend/top3.php');
    if (!resTop.ok) throw new Error("HTTP error: " + resTop.status);
    const data = await resTop.json();

    // Verifica si el backend devolvió un error
    if (data.error) {
      throw new Error(data.error);
    }

    top3 = data;
    if (!Array.isArray(top3)) {
      throw new Error("Respuesta no válida del backend.");
    }
  } catch (e) {
    errorDiv.innerText = "No se pudo cargar el Top 3.";
    console.error("Error al cargar el Top 3:", e);
    return;
  }

  if (top3.length === 0) {
    container.innerHTML = "<p>No hay fotos en el Top 3.</p>";
    return;
  }

  const topTitle = document.createElement("h2");
  topTitle.innerText = "";
  container.appendChild(topTitle);

  const topDiv = document.createElement("div");
  topDiv.style.display = "flex";
  topDiv.style.gap = "1rem";
  top3.forEach((foto, idx) => {
    console.log("Top 3 foto:", foto);
    const div = document.createElement("div");
    div.style.textAlign = "center";
    div.innerHTML = `
      <h4>#${idx + 1}</h4>
      <img src="../../backend/ver_foto.php?id=${foto.id}" width="300" />
      <p>Votos: ${foto.votos}</p>
    `;
    topDiv.appendChild(div);
    document.getElementById("podium").style.display = "flex";
  });
  container.appendChild(topDiv);
}

document.addEventListener("DOMContentLoaded", cargarGaleria);
