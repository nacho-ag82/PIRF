async function cargarParticipante() {
  const res = await fetch('../../backend/obtener_participante.php'); // Suponiendo que existe este endpoint
  const data = await res.json();
  const infoDiv = document.getElementById("participante-info");
  if (data.success) {
    infoDiv.innerHTML = `<h2>${data.nombre}</h2><p>${data.descripcion}</p>`;
  } else {
    infoDiv.innerHTML = "<p>Error al cargar la información del participante.</p>";
  }
}

window.onload = cargarParticipante;
