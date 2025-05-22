async function cargarEstadisticas() {
  const container = document.getElementById("estadisticas");
  container.innerHTML = "";
  let data;

  try {
    const res = await fetch('../../backend/estadisticas.php');
    if (!res.ok) throw new Error("HTTP error: " + res.status);
    data = await res.json();
  } catch (e) {
    container.innerText = "Error al cargar estadísticas.";
    console.error("Error al cargar estadísticas:", e);
    return;
  }

  // Verifica que los datos sean válidos
  if (!data || typeof data !== "object") {
    container.innerText = "Datos inválidos recibidos del backend.";
    console.error("Datos inválidos:", data);
    return;
  }

  // Top 5 fotos más votadas
  if (Array.isArray(data.fotosTop) && data.fotosTop.length > 0) {
    const tituloFotos = document.createElement("h3");
    tituloFotos.innerText = "Top 5 fotos más votadas";
    container.appendChild(tituloFotos);

    const canvasFotos = document.createElement("canvas");
    canvasFotos.id = "graficaFotosTop";
    canvasFotos.width = 400;
    canvasFotos.height = 250;
    container.appendChild(canvasFotos);

    const labelsFotos = data.fotosTop.map(foto => foto.titulo);
    const votosFotos = data.fotosTop.map(foto => foto.votos);

    new Chart(canvasFotos, {
      type: 'bar',
      data: {
        labels: labelsFotos,
        datasets: [{
          label: 'Votos',
          data: votosFotos,
          backgroundColor: 'rgba(54, 162, 235, 0.6)'
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  }

  // Top 5 usuarios con más fotos subidas
  if (Array.isArray(data.usuariosTop) && data.usuariosTop.length > 0) {
    const tituloUsuarios = document.createElement("h3");
    tituloUsuarios.innerText = "Top 5 usuarios con más fotos subidas";
    container.appendChild(tituloUsuarios);

    const canvasUsuarios = document.createElement("canvas");
    canvasUsuarios.id = "graficaUsuariosTop";
    canvasUsuarios.width = 400;
    canvasUsuarios.height = 250;
    container.appendChild(canvasUsuarios);

    const labelsUsuarios = data.usuariosTop.map(usuario => usuario.nombre);
    const fotosUsuarios = data.usuariosTop.map(usuario => usuario.total_fotos);

    new Chart(canvasUsuarios, {
      type: 'bar',
      data: {
        labels: labelsUsuarios,
        datasets: [{
          label: 'Fotos subidas',
          data: fotosUsuarios,
          backgroundColor: 'rgba(255, 99, 132, 0.6)'
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  }
}

document.addEventListener("DOMContentLoaded", cargarEstadisticas);
