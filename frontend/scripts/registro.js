
  document.getElementById("formRegistro").addEventListener("submit", async (e) => {
    e.preventDefault();

    // Obtener los datos del formulario
    const formData = new FormData(e.target);
    const nombre = formData.get("nombre").trim();
    const email = formData.get("email").trim();
    const password = formData.get("password").trim();

    // Validación de datos
    if (nombre === "") {
      alert("El nombre es obligatorio.");
      return;
    }
    if (!email.includes("@") || !email.includes(".")) {
      alert("El formato del email es inválido.");
      return;
    }
    if (password.length < 6) {
      alert("La contraseña debe tener al menos 6 caracteres.");
      return;
    }
    if (!/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
      alert("La contraseña debe incluir al menos una letra mayúscula y un número.");
      return;
    }

    try {
      // Enviar los datos al servidor
      const response = await fetch("../../backend/registro.php", {
        method: "POST",
        body: formData,
      });

      // Manejo de la respuesta del servidor
      const result = await response.json();
      if (result.success) {
        alert(result.message);
        location.href = "login.html";
      } else {
        alert(result.message || "Error al registrar el usuario.");
      }
    } catch (error) {
      alert("Ocurrió un error al conectar con el servidor. Inténtalo de nuevo más tarde.");
    }
  });

  fetch("../backend/usuario_actual.php")
    .then(r => r.json())
    .then(data => {
      const nav = document.getElementById("usuario-nav");
      if (data.success && data.nombre) {
        nav.innerHTML = `👤 ${data.nombre} <a href="#" onclick="logout()">[Salir]</a>`;
      } else {
        nav.innerHTML = `<a href="login.html">Login</a>`;
      }
    });

  function logout() {
    fetch("../backend/logout.php").then(() => location.reload());
  }
