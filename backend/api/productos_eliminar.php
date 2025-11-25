<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

require_once '../config.php';

$database = new Database();
$db = $database->connect();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'DELETE') {
    try {
        $productoId = isset($_GET['producto_id']) ? intval($_GET['producto_id']) : 0;
        $firebaseUid = isset($_GET['firebase_uid']) ? $_GET['firebase_uid'] : null;
        
        if (!$productoId || !$firebaseUid) {
            echo json_encode(['error' => 'Datos incompletos']);
            exit();
        }
        
        $stmt = $db->prepare('SELECT id FROM usuarios WHERE firebase_uid = ?');
        $stmt->execute([$firebaseUid]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit();
        }
        
        $stmt = $db->prepare('SELECT usuario_id FROM productos WHERE id = ?');
        $stmt->execute([$productoId]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$producto) {
            echo json_encode(['error' => 'Producto no encontrado']);
            exit();
        }
        
        if ($producto['usuario_id'] != $usuario['id']) {
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
        
        $stmt = $db->prepare('DELETE FROM productos WHERE id = ?');
        $stmt->execute([$productoId]);
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Método no permitido']);
}

