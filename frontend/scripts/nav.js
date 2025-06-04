window.onload = cargar_nav();


function logout() {
  fetch("../../backend/logout.php")
    .then(response => {
      if (response.ok) {
        window.location.href = "login.html";
      } else {
        alert("Error al cerrar sesión.");
      }
    })
    .catch(() => {
      alert("Error al conectar con el servidor.");
    });
}

function cargar_nav() {
  document.getElementById("nav-container").innerHTML = `<nav>
      <img src="../icons/logo.png" alt="Logo" style="height: 50px; vertical-align: middle; margin-right: 10px;">
      <a href="../../">Inicio</a>
      <a href="participante.html" id="nav-participante">Participante</a>
      <a href="admin.html" id="nav-admin">Administración</a>
      <a href="galeria.html">Galería</a>
      <a href="estadisticas.html">Estadísticas</a>
      <a href="duelo.html">Duelo</a>
      <a href="top3.html">Top3</a>
      <span id="usuario-nav" style="float:right"></span>
</nav>`;

  fetch("../../backend/usuario_actual.php")
    .then(r => r.json())
    .then(data => {
      const nav = document.getElementById("usuario-nav");
      if (data.success && data.nombre) {
        const navParticipante = document.getElementById("nav-participante");
        if (data.rol === "admin") {
          if (navParticipante) {
            navParticipante.style.display = "none";
          }
        }
        const navAdmin = document.getElementById("nav-admin");
        if (data.rol === "participante") {
          if (navAdmin) {
            navAdmin.style.display = "none";
          }
        }

        nav.innerHTML = `<a href='../public/autogestion.html'>👤 ${data.nombre}</a> <a href="#" onclick="logout()"><img id="logout" src="../icons/logout.png" alt="Logout"/></a>`;
      } else {

        nav.innerHTML = `<a href="login.html">Login</a>`;
        document.getElementById("nav-admin").style.display = "none";
        document.getElementById("nav-participante").style.display = "none";
        
        const navParticipante = document.getElementById("nav-participante");
        navParticipante.style.display = "none";
      }
    });
}