<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

$database = new Database();
$db = $database->connect();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $firebaseUid = isset($_GET['firebase_uid']) ? $_GET['firebase_uid'] : null;
        
        if (!$firebaseUid) {
            echo json_encode(['error' => 'firebase_uid requerido']);
            exit();
        }
        
        $stmt = $db->prepare('SELECT id FROM usuarios WHERE firebase_uid = ?');
        $stmt->execute([$firebaseUid]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit();
        }
        
        $stmt = $db->prepare('SELECT c.id, c.fecha_compra, c.estado_envio, c.metodo_pago, c.direccion AS direccion_envio,
                         p.titulo, p.precio, p.imagen_url, p.categoria, p.estado AS estado_producto,
                         u.nombre_completo AS vendedor_nombre, u.email AS vendedor_email
                              FROM compras c
                              INNER JOIN productos p ON c.producto_id = p.id
                              LEFT JOIN usuarios u ON c.vendedor_id = u.id
                              WHERE c.comprador_id = ?
                              ORDER BY c.fecha_compra DESC');
        $stmt->execute([$usuario['id']]);
        $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
         $stmt = $db->prepare('SELECT p.id, p.titulo, p.precio, p.imagen_url, p.categoria, p.estado, p.fecha_publicacion, p.latitud, p.longitud,
                          c.id AS compra_id, c.estado_envio, c.fecha_compra AS fecha_venta, c.direccion AS direccion_envio,
                          cu.email AS comprador_email, cu.nombre_completo AS comprador_nombre, cu.direccion AS comprador_direccion
                      FROM productos p
                      LEFT JOIN compras c ON p.id = c.producto_id
                      LEFT JOIN usuarios cu ON c.comprador_id = cu.id
                      WHERE p.usuario_id = ?
                      ORDER BY p.fecha_publicacion DESC');
        $stmt->execute([$usuario['id']]);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'compras' => $compras,
            'productos' => $productos
        ]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Método no permitido']);
}

