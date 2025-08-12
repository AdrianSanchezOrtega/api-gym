<?php
// Configuración de CORS para la API
header('Access-Control-Allow-Origin: *'); // Cambia * por el dominio de tu frontend en producción
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejo de preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	http_response_code(204);
	exit;
}
?>
