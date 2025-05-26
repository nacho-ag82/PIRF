async function cargarFotos() {
  const res = await fetch("../../backend/admin_fotos.php");
  const text = await res.text();
  let fotos;
  try {
    fotos = JSON.parse(text);
  } catch (err) {
    alert("Error al cargar fotos pendientes:\n" + text);
    return;
  }
  const div = document.getElementById("fotos");
  div.innerHTML = "";

  if (!Array.isArray(fotos) || fotos.length === 0) {
    div.innerHTML = "<p>No hay fotos pendientes para admitir o rechazar.</p>";
    return;
  }

  fotos.forEach(f => {
    const cont = document.createElement("div");
    cont.innerHTML = `
      <p><strong>${f.titulo}</strong> de ${f.autor} - Estado: 
        <select onchange="cambiarEstado(${f.id}, this.value)">
          <option value="pendiente" ${f.estado === "pendiente" ? "selected" : ""}>Pendiente</option>
          <option value="admitida" ${f.estado === "admitida" ? "selected" : ""}>Admitida</option>
          <option value="rechazada" ${f.estado === "rechazada" ? "selected" : ""}>Rechazada</option>
        </select>
      </p>
      <img src="../../backend/ver_foto.php?id=${f.id}" width="300"><br>
    `;
    div.appendChild(cont);
  });
}

async function cambiarEstado(id, estado) {
  const res = await fetch("../../backend/cambiar_estado.php", {
    method: "POST",
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `foto_id=${id}&nuevo_estado=${estado}`
  });
  const data = await res.json();
  alert(data.message);
  if (data.success) cargarFotos();
}

function toDatetimeLocal(mysqlDateTime) {
  if (!mysqlDateTime) return "";
  const [date, time] = mysqlDateTime.split(' ');
  return date + 'T' + time.slice(0,5);
}

function fromDatetimeLocal(datetimeLocal) {
  if (!datetimeLocal) return "";
  return datetimeLocal.replace('T', ' ') + ':00';
}

async function cargarConfig() {
  const res = await fetch("../../backend/config.php");
  const config = await res.json();
  if (config.success === false) {
    alert(config.message || "Error al obtener configuración");
    document.getElementById("lim_subida").value = "";
    document.getElementById("lim_fvoto").value = "";
    document.getElementById("lim_votos").value = "";
    return;
  }
  document.getElementById("lim_subida").value =
    (config.lim_subida && config.lim_subida !== "null") ? toDatetimeLocal(config.lim_subida) : "";
  document.getElementById("lim_fvoto").value =
    (config.lim_fvoto && config.lim_fvoto !== "null") ? toDatetimeLocal(config.lim_fvoto) : "";
  document.getElementById("lim_votos").value =
    (config.lim_votos !== undefined && config.lim_votos !== null && config.lim_votos !== "null") ? config.lim_votos : "";
}

async function guardarConfig() {
  let lim_subida = document.getElementById("lim_subida").value;
  let lim_fvoto = document.getElementById("lim_fvoto").value;
  let lim_votos = document.getElementById("lim_votos").value;

  lim_subida = fromDatetimeLocal(lim_subida);
  lim_fvoto = fromDatetimeLocal(lim_fvoto);

  const res = await fetch("../../backend/config.php", {
    method: "POST",
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `lim_subida=${encodeURIComponent(lim_subida)}&lim_fvoto=${encodeURIComponent(lim_fvoto)}&lim_votos=${encodeURIComponent(lim_votos)}`
  });
  const data = await res.json();
  if (!data.success) {
    alert("Error: " + (data.message || "No se pudo guardar la configuración"));
  } else {
    alert("Configuración guardada");
    await cargarConfig();
  }
}

async function cargarUsuarios() {
  const container = document.getElementById("lista-usuarios");
  container.innerHTML = "<p>Cargando usuarios...</p>";

  try {
    const res = await fetch("../../backend/usuarios.php");
    const usuarios = await res.json();

    if (!Array.isArray(usuarios) || usuarios.length === 0) {
      container.innerHTML = "<p>No hay usuarios registrados.</p>";
      return;
    }

    container.innerHTML = "";
    usuarios.forEach(usuario => {
      const div = document.createElement("div");
      div.className = "usuario-item";
      div.innerHTML = `
        <span>${usuario.nombre} (${usuario.email})</span>
        <button onclick="eliminarUsuario(${usuario.id})">Eliminar</button>
      `;
      container.appendChild(div);
    });
  } catch (e) {
    container.innerHTML = "<p>Error al cargar usuarios.</p>";
  }
}

async function eliminarUsuario(id) {
  if (!confirm("¿Estás seguro de que deseas eliminar este usuario?")) return;

  try {
    const res = await fetch("../../backend/eliminar_usuario.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `id=${id}`
    });
    const data = await res.json();

    if (data.success) {
      alert("Usuario eliminado correctamente.");
      cargarUsuarios();
    } else {
      alert(data.message || "Error al eliminar el usuario.");
    }
  } catch (e) {
    alert("Error de conexión.");
  }
}

document.addEventListener("DOMContentLoaded", () => {
  cargarFotos();
  cargarConfig();
  cargarUsuarios();
});

