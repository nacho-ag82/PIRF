# PIRF Frontend Documentation

## Overview
This project is the frontend component of the PIRF application, which is designed for a photographic rally. It includes various pages for user interaction, such as duels, participant information, administrative functions, and galleries.

## Project Structure
The frontend is organized into the following directories:

- **public/**: Contains all the HTML files and CSS styles for the application.
  - **duelo.html**: The HTML structure for the "Duelo" page, including scripts for loading duels and handling user authentication.
  - **index.html**: The main entry point for the application, linking to other pages and resources.
  - **participante.html**: Displays information related to participants in the application.
  - **admin.html**: Intended for administrative functions, allowing admins to manage the application.
  - **galeria.html**: Displays a gallery of images or content related to the application.
  - **estadisticas.html**: Presents statistics related to the application, such as voting results or user engagement.
  - **estilos.css**: Contains the CSS styles for the frontend application.

- **src/**: Contains the JavaScript logic for the frontend application.
  - **scripts/**: Directory for JavaScript files.
    - **main.js**: The main JavaScript logic for handling user interactions and API calls.

## Setup Instructions
1. Clone the repository:
   ```
   git clone <repository-url>
   ```

2. Navigate to the frontend directory:
   ```
   cd pirf-app/frontend
   ```

3. Open the `public` directory in a web server environment (e.g., XAMPP, WAMP) to view the application in a browser.

4. Ensure that the backend is set up and running to handle API requests.

## Usage
- Open `index.html` in your web browser to start using the application.
- Navigate through the various pages to explore the functionalities offered by the application.

## Contributing
Contributions are welcome! Please submit a pull request or open an issue for any enhancements or bug fixes.

## License
This project is licensed under the MIT License. See the LICENSE file for more details.