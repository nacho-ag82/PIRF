async function cargarGaleria() {
  const res = await fetch('../../backend/galeria.php');
  let data;
  let errorDiv = document.getElementById("galeria-error");
  errorDiv.innerText = "";
  let text = await res.text();
  

  const container = document.getElementById("galeria");
  container.innerHTML = "";

  // Mostrar Top 3 fotos más votadas
  // Obtenemos los IDs de las fotos más votadas desde top3.php
  let top3 = [];
  try {
    const resTop = await fetch('../../backend/top3.php');
    top3 = await resTop.json();
  } catch (e) {
    // Si falla, no mostramos el top3
    top3 = [];
    
  }
  console.log(top3[1]);

  if (Array.isArray(top3) && top3.length > 0) {
    const topTitle = document.createElement("h2");
    topTitle.innerText = "Top 3 fotos más votadas";
    container.appendChild(topTitle);

    const topDiv = document.createElement("div");
    topDiv.style.display = "flex";
    topDiv.style.gap = "1rem";
    top3.forEach((foto, idx) => {
      const div = document.createElement("div");
      div.style.textAlign = "center";
      div.innerHTML = `
        <h4>#${idx + 1}</h4>
        <img src="../../backend/ver_foto.php?id=${foto.id}" width="300" />
        <p>Votos: ${foto.votos}</p>
      `;
      topDiv.appendChild(div);
    });
    container.appendChild(topDiv);
  }

  // Mostrar el resto de la galería
  const imagenes = Array.isArray(data.fotos) ? data.fotos : [];
  if (imagenes.length > 0) {
    const galTitle = document.createElement("h2");
    galTitle.innerText = "Galería";
    container.appendChild(galTitle);

    const galDiv = document.createElement("div");
    galDiv.style.display = "flex";
    galDiv.style.flexWrap = "wrap";
    galDiv.style.gap = "1rem";
    imagenes.forEach((imagen) => {
      const div = document.createElement("div");
      div.style.textAlign = "center";
      div.innerHTML = `
        <h4>${imagen.titulo}</h4>
        <img src="../../backend/ver_foto.php?id=${imagen.id}" width="200" />
        <p>Autor: ${imagen.autor}</p>
        <p>Votos: ${imagen.votos}</p>
      `;
      galDiv.appendChild(div);
    });
    container.appendChild(galDiv);
  }

  if ((!data.top3 || data.top3.length === 0) && (!imagenes || imagenes.length === 0)) {
    container.innerHTML = "<p>No hay imágenes en la galería.</p>";
  }
}

window.onload = cargarGaleria;
