<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

$database = new Database();
$db = $database->connect();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        if (isset($_GET['id'])) {
            $stmt = $db->prepare('SELECT p.*, u.nombre_completo, u.email FROM productos p 
                                   LEFT JOIN usuarios u ON p.usuario_id = u.id 
                                   WHERE p.id = ?');
            $stmt->execute([$_GET['id']]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($producto) {
                echo json_encode($producto);
            } else {
                echo json_encode(['error' => 'Producto no encontrado']);
            }
        } else {
            $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;
            
            if ($categoria) {
                $stmt = $db->prepare('SELECT * FROM productos WHERE estado = "disponible" AND categoria = ? ORDER BY fecha_publicacion DESC');
                $stmt->execute([$categoria]);
            } else {
                $stmt = $db->query('SELECT * FROM productos WHERE estado = "disponible" ORDER BY fecha_publicacion DESC');
            }
            
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($productos);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} elseif ($method === 'POST') {
    try {
        $data = json_decode(file_get_contents("php://input"));
        
        $stmt = $db->prepare('SELECT id FROM usuarios WHERE firebase_uid = ?');
        $stmt->execute([$data->firebase_uid]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit();
        }
        
        $stmt = $db->prepare('INSERT INTO productos (usuario_id, titulo, descripcion, precio, imagen_url, categoria, latitud, longitud) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $usuario['id'],
            $data->titulo,
            $data->descripcion,
            $data->precio,
            $data->imagen_url,
            $data->categoria,
            $data->latitud,
            $data->longitud
        ]);
        
        echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
