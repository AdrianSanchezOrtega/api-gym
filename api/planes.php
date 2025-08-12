<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/cors.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

checkApiKey();

$method = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
$apiIndex = array_search('api', $uri);
$id = isset($uri[$apiIndex + 2]) ? (int)$uri[$apiIndex + 2] : null;

switch ($method) {
	case 'GET':
		if ($id) {
			$stmt = $conn->prepare('SELECT * FROM planes WHERE id = ?');
			$stmt->bind_param('i', $id);
			$stmt->execute();
			$result = $stmt->get_result();
			$data = $result->fetch_assoc();
			echo json_encode($data ?: []);
		} else {
			$result = $conn->query('SELECT * FROM planes');
			$data = $result->fetch_all(MYSQLI_ASSOC);
			echo json_encode($data);
		}
		break;
	case 'POST':
		$input = json_decode(file_get_contents('php://input'), true);
		if (!isset($input['nombre'])) {
			http_response_code(400);
			echo json_encode(['error' => 'Falta el nombre del plan.']);
			exit;
		}
		$stmt = $conn->prepare('INSERT INTO planes (nombre) VALUES (?)');
		$stmt->bind_param('s', $input['nombre']);
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
		$stmt = $conn->prepare('UPDATE planes SET nombre=? WHERE id=?');
		$stmt->bind_param('si', $input['nombre'], $id);
		$stmt->execute();
		echo json_encode(['success' => $stmt->affected_rows > 0]);
		break;
	case 'DELETE':
		if (!$id) {
			http_response_code(400);
			echo json_encode(['error' => 'ID requerido para borrar.']);
			exit;
		}
		$stmt = $conn->prepare('DELETE FROM planes WHERE id = ?');
		$stmt->bind_param('i', $id);
		$stmt->execute();
		echo json_encode(['success' => $stmt->affected_rows > 0]);
		break;
	default:
		http_response_code(405);
		echo json_encode(['error' => 'Método no permitido']);
}
?>
