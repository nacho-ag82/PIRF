fetch("../../backend/usuario_actual.php")
  .then(r => r.json())
  .then(data => {
    const nav = document.getElementById("usuario-nav");
    if (data.success && data.nombre) {
      if (data.rol === "admin") {
        document.getElementById("nav-participante").style.display = "none";
      }
      if (data.rol === "participante") {
        document.getElementById("nav-admin").style.display = "none";
      }
      nav.innerHTML = `👤 ${data.nombre} <a href="#" onclick="logout()"><img id="logout" src="../../icons/logout.png" alt="Logout"/></a>`;
    } else {
      nav.innerHTML = `<a href="login.html">Login</a>`;
    }
  });
function logout() {
  fetch("../../backend/logout.php").then(() => location.reload());
}