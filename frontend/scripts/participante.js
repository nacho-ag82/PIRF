
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

  document.getElementById("formFoto").addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      const response = await fetch("../../backend/subir_foto.php", {
        method: "POST",
        body: formData
      });

      const text = await response.text();
      let result;
      try {
        result = JSON.parse(text);
      } catch (err) {
        alert("Error inesperado:\n" + text);
        return;
      }

      alert(result.message + (result.error ? "\n" + result.error : ""));
      if (result.success) location.reload();
    } catch (err) {
      alert("Error de red o servidor: " + err);
    }
  });

 

