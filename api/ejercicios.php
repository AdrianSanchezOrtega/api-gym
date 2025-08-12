<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/cors.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

checkApiKey();

// Obtener el método y el id si existe
$method = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
$apiIndex = array_search('api', $uri);
$id = isset($uri[$apiIndex + 2]) ? (int)$uri[$apiIndex + 2] : null;

switch ($method) {
	case 'GET':
		if ($id) {
			// GET /api/ejercicios/{id}
			$stmt = $conn->prepare('SELECT * FROM ejercicios WHERE id = ?');
			$stmt->bind_param('i', $id);
			$stmt->execute();
			$result = $stmt->get_result();
			$data = $result->fetch_assoc();
			echo json_encode($data ?: []);
		} else {
			// GET /api/ejercicios
			$result = $conn->query('SELECT * FROM ejercicios');
			$data = $result->fetch_all(MYSQLI_ASSOC);
			echo json_encode($data);
		}
		break;
	case 'POST':
		$input = json_decode(file_get_contents('php://input'), true);
		if (!isset($input['nombre'], $input['nivel'], $input['musculo'], $input['imagen'], $input['video'])) {
			http_response_code(400);
			echo json_encode(['error' => 'Faltan datos requeridos.']);
			exit;
		}
		$stmt = $conn->prepare('INSERT INTO ejercicios (nombre, nivel, musculo, imagen, video) VALUES (?, ?, ?, ?, ?)');
		$stmt->bind_param('sssss', $input['nombre'], $input['nivel'], $input['musculo'], $input['imagen'], $input['video']);
		$stmt->execute();
		echo json_encode(['id' => $conn->insert_id]);
		break;
	case 'PUT':
		if (!$id) {
			http_response_code(400);
			echo json_encode(['error' => 'ID requerido para actualizar.']);
			exit;
		}
		$input = json_decode(file_get_contents('php://input'), true);
		$stmt = $conn->prepare('UPDATE ejercicios SET nombre=?, nivel=?, musculo=?, imagen=?, video=? WHERE id=?');
		$stmt->bind_param('sssssi', $input['nombre'], $input['nivel'], $input['musculo'], $input['imagen'], $input['video'], $id);
		$stmt->execute();
		echo json_encode(['success' => $stmt->affected_rows > 0]);
		break;
	case 'DELETE':
		if (!$id) {
			http_response_code(400);
			echo json_encode(['error' => 'ID requerido para borrar.']);
			exit;
		}
		$stmt = $conn->prepare('DELETE FROM ejercicios WHERE id = ?');
		$stmt->bind_param('i', $id);
		$stmt->execute();
		echo json_encode(['success' => $stmt->affected_rows > 0]);
		break;
	default:
		http_response_code(405);
		echo json_encode(['error' => 'Método no permitido']);
}
?>
