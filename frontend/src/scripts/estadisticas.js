async function cargarEstadisticas() {
  const res = await fetch('../../backend/estadisticas.php');
  let data;
  try {
    data = await res.json();
  } catch (e) {
    document.getElementById("estadisticas").innerText = "Error al cargar estadísticas.";
    return;
  }

  const container = document.getElementById("estadisticas");
  container.innerHTML = "";

  // Top 5 fotos más votadas
  if (Array.isArray(data.fotosTop) && data.fotosTop.length > 0) {
    const titulo = document.createElement("h3");
    titulo.innerText = "Top 5 fotos más votadas";
    container.appendChild(titulo);

    // Crear canvas para la gráfica de barras
    const canvasFotos = document.createElement("canvas");
    canvasFotos.id = "graficaFotosTop";
    canvasFotos.width = 400;
    canvasFotos.height = 250;
    container.appendChild(canvasFotos);

    // Preparar datos para la gráfica
    const labelsFotos = data.fotosTop.map(foto => foto.titulo);
    const votosFotos = data.fotosTop.map(foto => foto.votos);

    // Renderizar gráfica usando Chart.js
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
        responsive: false,
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
    const titulo = document.createElement("h3");
    titulo.innerText = "Top 5 usuarios con más fotos subidas";
    container.appendChild(titulo);

    // Crear canvas para la gráfica de barras
    const canvasUsuarios = document.createElement("canvas");
    canvasUsuarios.id = "graficaUsuariosTop";
    canvasUsuarios.width = 400;
    canvasUsuarios.height = 250;
    container.appendChild(canvasUsuarios);

    // Preparar datos para la gráfica
    const labelsUsuarios = data.usuariosTop.map(usuario => usuario.nombre);
    const fotosUsuarios = data.usuariosTop.map(usuario => usuario.total_fotos);

    // Renderizar gráfica usando Chart.js
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
        responsive: false,
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

window.onload = cargarEstadisticas;
