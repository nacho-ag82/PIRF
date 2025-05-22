function setupAutocomplete(inputId, datalistId, campo) {
  const input = document.getElementById(inputId);
  const datalist = document.getElementById(datalistId);
  input.addEventListener('input', async function() {
    const val = input.value.trim();
    if (val.length < 2) return;
    try {
      const res = await fetch(`../../backend/perfil.php?autocomplete=${campo}&term=${encodeURIComponent(val)}`);
      if (!res.ok) return;
      const opciones = await res.json();
      datalist.innerHTML = '';
      opciones.forEach(op => {
        const option = document.createElement('option');
        option.value = op;
        datalist.appendChild(option);
      });
    } catch {}
  });
}

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("autogestion-form");
  const msg = document.getElementById("autogestion-msg");
  const error = document.getElementById("autogestion-error");
  const logoutBtn = document.getElementById("logout-btn");

  // Cargar datos actuales del perfil
  async function cargarPerfil() {
    msg.innerText = "";
    error.innerText = "";
    try {
      const res = await fetch("../../backend/perfil.php");
      if (!res.ok) throw new Error("No autenticado");
      const data = await res.json();
      document.getElementById("nombre").value = data.nombre || "";
      document.getElementById("email").value = data.email || "";
      document.getElementById("direccion").value = data.direccion || "";
      document.getElementById("telefono").value = data.numero_telefono || "";
      document.getElementById("fecha_nacimiento").value = data.fecha_nacimiento || "";
    } catch (e) {
      error.innerText = "No se pudo cargar el perfil. ¿Sesión iniciada?";
      form.querySelectorAll("input,button").forEach(el => el.disabled = true);
    }
  }

  // Guardar cambios
  form.onsubmit = async (e) => {
    e.preventDefault();
    msg.innerText = "";
    error.innerText = "";
    const nombre = form.nombre.value.trim();
    const email = form.email.value.trim();
    const direccion = form.direccion.value.trim();
    const telefono = form.telefono.value.trim();
    const fecha_nacimiento = form.fecha_nacimiento.value;
    const password = form.password.value;
    try {
      const res = await fetch("../../backend/perfil.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ nombre, email, direccion, telefono, fecha_nacimiento, password })
      });
      const data = await res.json();
      if (data.success) {
        msg.innerText = "Perfil actualizado correctamente.";
        form.password.value = "";
      } else {
        error.innerText = data.error || "Error al actualizar perfil.";
      }
    } catch (e) {
      error.innerText = "Error de conexión.";
    }
  };

  // Cerrar sesión
  if (logoutBtn) {
    logoutBtn.onclick = async () => {
      await fetch("../../backend/logout.php");
      window.location.href = "index.html";
    };
  }

  setupAutocomplete('direccion', 'direccion-list', 'direccion');
  setupAutocomplete('telefono', 'telefono-list', 'telefono');
  cargarPerfil();
});
