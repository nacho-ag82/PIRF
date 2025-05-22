 document.getElementById("formLogin").addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const email = formData.get("email").trim();
      const password = formData.get("password").trim();
      if (!email || !password) {
        document.getElementById("mensaje").innerText = "Completa todos los campos.";
        return;
      }
      try {
        const response = await fetch("../../backend/login.php", {
          method: "POST",
          body: formData
        });
        const result = await response.json();
        if (result.success) {
          if (result.rol === "admin") {
            location.href = "admin.html";
          } else {
            location.href = "participante.html";
          }
        } else {
          document.getElementById("mensaje").innerText = "Credenciales incorrectas.";
        }
      } catch {
        document.getElementById("mensaje").innerText = "Error de conexión.";
      }
    });

    fetch("../../backend/usuario_actual.php")
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