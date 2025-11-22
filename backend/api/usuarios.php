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
        
        $stmt = $db->prepare('SELECT * FROM usuarios WHERE firebase_uid = ?');
        $stmt->execute([$data->firebase_uid]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            echo json_encode($usuario);
        } else {
            $stmt = $db->prepare('INSERT INTO usuarios (firebase_uid, email, nombre_completo) VALUES (?, ?, ?)');
            $stmt->execute([$data->firebase_uid, $data->email, $data->nombre]);
            
            $stmt = $db->prepare('SELECT * FROM usuarios WHERE firebase_uid = ?');
            $stmt->execute([$data->firebase_uid]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode($usuario);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
