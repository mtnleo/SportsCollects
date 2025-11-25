<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

require_once '../config.php';

$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);

    $firebaseUid = $data['firebase_uid'] ?? null;
    $compraId = $data['compra_id'] ?? null;
    $estadoEnvio = $data['estado_envio'] ?? 'enviado';

    if (!$firebaseUid || !$compraId) {
        echo json_encode(['error' => 'Datos incompletos']);
        exit();
    }

    $estadosPermitidos = ['pendiente', 'enviado', 'entregado'];
    if (!in_array($estadoEnvio, $estadosPermitidos, true)) {
        echo json_encode(['error' => 'Estado de envío inválido']);
        exit();
    }

    $stmt = $db->prepare('SELECT id FROM usuarios WHERE firebase_uid = ?');
    $stmt->execute([$firebaseUid]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit();
    }

    $stmt = $db->prepare('SELECT id, vendedor_id, estado_envio FROM compras WHERE id = ?');
    $stmt->execute([$compraId]);
    $compra = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$compra) {
        echo json_encode(['error' => 'Compra no encontrada']);
        exit();
    }

    if ((int)$compra['vendedor_id'] !== (int)$usuario['id']) {
        echo json_encode(['error' => 'No tienes permisos para actualizar este envío']);
        exit();
    }

    if ($compra['estado_envio'] === $estadoEnvio) {
        echo json_encode(['success' => true, 'message' => 'Estado de envío actualizado']);
        exit();
    }

    $stmt = $db->prepare('UPDATE compras SET estado_envio = ? WHERE id = ?');
    $stmt->execute([$estadoEnvio, $compraId]);

    echo json_encode(['success' => true, 'message' => 'Estado de envío actualizado']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
