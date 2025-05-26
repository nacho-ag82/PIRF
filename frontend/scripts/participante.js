async function comprobarPlazo(clave) {
  const res = await fetch("../../backend/config.php");
  const config = await res.json();
  const ahora = new Date();
  // Si lim_subida es null o vacío, permite siempre
  if (!config[clave]) {
    document.getElementById("formFoto").style.display = "block";
    document.getElementById("aviso_plazo").innerText = "";
    return;
  }
  const fechaLimite = new Date(config[clave]);
  if (ahora > fechaLimite) {
    document.getElementById("aviso_plazo").innerText = "⚠️ El plazo ha terminado.";
    document.getElementById("formFoto").style.display = "none";
  } else {
    document.getElementById("formFoto").style.display = "block";
    document.getElementById("aviso_plazo").innerText = "";
  }
}
comprobarPlazo("lim_subida");

document.addEventListener("DOMContentLoaded", () => {
  cargarFotosSubidas();

  const form = document.getElementById("formFoto");
  form.onsubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    try {
      const res = await fetch("../../backend/subir_foto.php", {
        method: "POST",
        body: formData,
      });
      const data = await res.json();
      if (data.success) {
        alert("Foto subida correctamente.");
        form.reset();
        cargarFotosSubidas();
      } else {
        alert(data.message || "Error al subir la foto.");
      }
    } catch (e) {
      alert("Error de conexión.");
    }
  };
});

async function cargarFotosSubidas() {
  const container = document.getElementById("mis-fotos");
  container.innerHTML = "<p>Cargando fotos...</p>";

  try {
    const res = await fetch("../../backend/mis_fotos.php");
    const fotos = await res.json();

    if (!Array.isArray(fotos) || fotos.length === 0) {
      container.innerHTML = "<p>No has subido fotos aún.</p>";
      return;
    }

    container.innerHTML = "";
    fotos.forEach((foto) => {
      const div = document.createElement("div");
      div.className = "foto-item";
      div.innerHTML = `
        <input type="text" value="${foto.titulo}" onchange="editarTitulo(${foto.id}, this.value)" />
        <img src="../../backend/ver_foto.php?id=${foto.id}" width="400" />
        
      `;
      container.appendChild(div);
    });
  } catch (e) {
    container.innerHTML = "<p>Error al cargar tus fotos.</p>";
  }
}

async function editarTitulo(id, nuevoTitulo) {
  try {
    const res = await fetch("../../backend/editar_titulo.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `id=${id}&titulo=${encodeURIComponent(nuevoTitulo)}`,
    });
    const data = await res.json();
    if (!data.success) {
      alert(data.message || "Error al actualizar el título.");
    }
  } catch (e) {
    alert("Error de conexión.");
  }
}



