async function cargarDuelo() {
  const res = await fetch('../../backend/obtener_duelo.php');
  let fotos;
  let errorDiv = document.getElementById("duelo-error");
  errorDiv.innerText = "";
  let text = await res.text();
  try {
    fotos = JSON.parse(text);
  } catch (e) {
    errorDiv.innerText = "Error al parsear JSON: " + text;
    return;
  }

  const container = document.getElementById("duelo");
  container.innerHTML = "";

  if (!Array.isArray(fotos) || fotos.length !== 2) {
    container.innerHTML = "<p>No hay suficientes fotos para duelo.</p>";
    return;
  }

  fotos.forEach((foto, index) => {
    const div = document.createElement("div");
    div.innerHTML = `
      <h4>${foto.titulo}</h4>
      <img src="../backend/ver_foto.php?id=${foto.id}" width="300" style="cursor:pointer;" onclick="votar(${foto.id}, ${fotos[1 - index].id})" />
    `;
    container.appendChild(div);
  });
}

async function votar(ganadora, perdedora) {
  const res = await fetch('../backend/registrar_voto.php', {
    method: "POST",
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `ganadora=${ganadora}&perdedora=${perdedora}`
  });
  let text = await res.text();
  let data;
  try {
    data = JSON.parse(text);
  } catch (e) {
    alert("Error al parsear JSON de registrar_voto.php:\n" + text);
    return;
  }

  const hoy = new Date().toISOString().slice(0, 10);
  let votosLocal = JSON.parse(localStorage.getItem("votos_duelo") || "{}");
  if (votosLocal.fecha !== hoy) {
    votosLocal = { fecha: hoy, cantidad: 0 };
  }
  votosLocal.cantidad++;
  localStorage.setItem("votos_duelo", JSON.stringify(votosLocal));

  alert(data.message + `\nVotos hoy (local): ${votosLocal.cantidad}`);
  if (data.success) cargarDuelo();
}

window.onload = () => {
  cargarDuelo();
};