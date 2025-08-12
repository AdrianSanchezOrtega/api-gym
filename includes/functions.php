<?php
// Función para validar la API key enviada en la cabecera 'Authorization'
function checkApiKey() {
	$apiKey = getenv('API_KEY');
	$headers = getallheaders();
	if (!isset($headers['Authorization']) || $headers['Authorization'] !== $apiKey) {
		http_response_code(401);
		echo json_encode(['error' => 'API key inválida o no proporcionada.']);
		exit;
	}
}
?>
