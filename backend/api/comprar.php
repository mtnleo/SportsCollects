<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

$database = new Database();
$db = $database->connect();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    try {
        $data = json_decode(file_get_contents("php://input"));
        
        $db->beginTransaction();
        
        $stmt = $db->prepare('SELECT id FROM usuarios WHERE firebase_uid = ?');
        $stmt->execute([$data->comprador_uid]);
        $comprador = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$comprador) {
            throw new Exception('Comprador no encontrado');
        }
        
        $stmt = $db->prepare('SELECT usuario_id, estado, titulo FROM productos WHERE id = ?');
        $stmt->execute([$data->producto_id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$producto) {
            throw new Exception('Producto no encontrado');
        }
        
        if ($producto['estado'] !== 'disponible') {
            throw new Exception('Producto no disponible');
        }
        
        $stmt = $db->prepare('UPDATE productos SET estado = "vendido" WHERE id = ?');
        $stmt->execute([$data->producto_id]);
        
        $stmt = $db->prepare('INSERT INTO compras (producto_id, comprador_id, vendedor_id) VALUES (?, ?, ?)');
        $stmt->execute([$data->producto_id, $comprador['id'], $producto['usuario_id']]);
        
        $stmt = $db->prepare('SELECT email FROM usuarios WHERE id = ?');
        $stmt->execute([$producto['usuario_id']]);
        $vendedor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $logDir = __DIR__ . '/../logs';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        $emailLog = "\n=== " . date('Y-m-d H:i:s') . " ===\n";
        $emailLog .= "Para: " . $vendedor['email'] . "\n";
        $emailLog .= "Asunto: ¡Vendiste tu artículo!\n";
        $emailLog .= "Mensaje: Has vendido: " . $producto['titulo'] . "\n";
        $emailLog .= "Comprador: " . $data->comprador_uid . "\n";
        $emailLog .= "Dirección de envío: " . $data->datos_envio . "\n\n";
        
        file_put_contents($logDir . '/emails.txt', $emailLog, FILE_APPEND);
        
        $db->commit();
        
        echo json_encode(['success' => true, 'message' => 'Compra realizada exitosamente']);
    } catch(Exception $e) {
        $db->rollBack();
        echo json_encode(['error' => $e->getMessage()]);
    }
}
