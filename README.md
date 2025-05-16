# PIRF - Proyecto de Rally Fotográfico

Este proyecto es una aplicación web para un rally fotográfico, donde los usuarios pueden participar en duelos de fotos, votar por sus favoritas y ver estadísticas relacionadas con la participación.

## Estructura del Proyecto

El proyecto está dividido en dos componentes principales: **frontend** y **backend**.

### Frontend

La carpeta `frontend` contiene todos los archivos necesarios para la interfaz de usuario:

- **public/**: Archivos HTML y CSS que componen la interfaz de usuario.
  - `duelo.html`: Página para participar en duelos de fotos.
  - `index.html`: Página principal de la aplicación.
  - `participante.html`: Página que muestra información sobre los participantes.
  - `admin.html`: Página para funciones administrativas.
  - `galeria.html`: Página que muestra una galería de imágenes.
  - `estadisticas.html`: Página que presenta estadísticas de participación.
  - `estilos.css`: Archivo de estilos CSS para la aplicación.

- **src/**: Contiene scripts de JavaScript.
  - `scripts/main.js`: Lógica principal de la aplicación, manejando interacciones del usuario y llamadas a la API.

### Backend

La carpeta `backend` contiene los archivos necesarios para la lógica del servidor:

- `config.php`: Configuración del backend, incluyendo detalles de conexión a la base de datos.
- `logout.php`: Maneja la funcionalidad de cierre de sesión.
- `obtener_duelo.php`: Recupera datos de duelos desde la base de datos.
- `registrar_voto.php`: Procesa las votaciones de los usuarios.
- `usuario_actual.php`: Verifica la sesión del usuario actual y devuelve información del usuario.
- `ver_foto.php`: Sirve imágenes desde el backend.
- **db/**: Contiene archivos relacionados con la base de datos.
  - `connection.php`: Establece la conexión a la base de datos.

## Instalación

1. Clona el repositorio en tu máquina local.
2. Asegúrate de tener un servidor web (como XAMPP) configurado para ejecutar el backend.
3. Configura la base de datos según las instrucciones en `backend/config.php`.
4. Abre el archivo `frontend/public/index.html` en tu navegador para acceder a la aplicación.

## Contribuciones

Las contribuciones son bienvenidas. Si deseas contribuir, por favor abre un issue o envía un pull request.

## Licencia

Este proyecto está bajo la Licencia MIT.