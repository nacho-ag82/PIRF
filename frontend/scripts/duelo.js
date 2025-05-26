async function comprobarPlazo(clave) {
  const res = await fetch("../../backend/config.php");
  const config = await res.json();
  const ahora = new Date();
  const fechaLimite = new Date(config[clave]);
  console.log(clave);
  
  if (ahora > fechaLimite) {
    document.getElementById("aviso_plazo").innerText = "⚠️ El plazo ha terminado.";
  }

}


async function cargarDuelo() {
  const res = await fetch('../../backend/obtener_duelo.php');
  const errorDiv = document.getElementById("duelo-error");
  errorDiv.innerText = "";
  let fotos;

  try {
    // Intenta parsear directamente como JSON
    fotos = await res.json();
    console.log("Respuesta obtener_duelo.php(json):", fotos);

    // Verifica si el backend devolvió un error
    if (fotos.error) {
      throw new Error(fotos.error);
    }
  } catch (e) {
    errorDiv.innerText = "Error al cargar el duelo: " + e.message;
    console.error("Error al cargar el duelo:", e);
    return;
  }

  const container = document.getElementById("duelo");
  container.innerHTML = "";

  if (!Array.isArray(fotos.duelo) || fotos.duelo.length !== 2) {
    container.innerHTML = "<p>No hay suficientes fotos para duelo.</p>";
    return;
  }

  fotos.duelo.forEach((foto, index) => {
    const div = document.createElement("div");
    div.innerHTML = `
      <h4>${foto.titulo}</h4>
      <img src="../../backend/ver_foto.php?id=${foto.id}" width="300" style="cursor:pointer;" onclick="votar(${foto.id}, ${fotos.duelo[1 - index].id})" />
    `;
    container.appendChild(div);
  });
}

async function votar(ganadora, perdedora) {
  const res = await fetch('../../backend/registrar_voto.php', {
    method: "POST",
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `ganadora=${ganadora}&perdedora=${perdedora}`
  });
  const text = await res.text();
  let data;

  try {
    data = JSON.parse(text);
  } catch (e) {
    alert("Error al parsear JSON de registrar_voto.php:\n" + text);
    return;
  }

  // --- Registro local de votos por día ---
  const hoy = new Date().toISOString().slice(0, 10); // YYYY-MM-DD
  let votosLocal = JSON.parse(localStorage.getItem("votos_duelo") || "{}");
  if (votosLocal.fecha !== hoy) {
    votosLocal = { fecha: hoy, cantidad: 0 };
  }
  votosLocal.cantidad++;
  localStorage.setItem("votos_duelo", JSON.stringify(votosLocal));
  // ---------------------------------------

  alert(data.message + `\nVotos hoy (local): ${votosLocal.cantidad}`);
  if (data.success) cargarDuelo();
}

window.onload = cargarDuelo();
