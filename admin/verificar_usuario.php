<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

// Apenas administradores logados podem consultar
require_admin();

header('Content-Type: application/json; charset=utf-8');

if (isset($_POST['username'])) {
    $username = trim($_POST['username']);
    
    if (strlen($username) < 3) {
        echo json_encode(['disponivel' => false, 'mensagem' => 'O nome de usuário deve ter no mínimo 3 caracteres.']);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE LOWER(username) = LOWER(?) UNION SELECT id FROM admin WHERE LOWER(admin_username) = LOWER(?)");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $stmt->store_result();
    
    $existe = ($stmt->num_rows > 0);
    $stmt->close();
    
    if ($existe) {
        echo json_encode(['disponivel' => false, 'mensagem' => 'Este nome de usuário já está em uso.']);
    } else {
        echo json_encode(['disponivel' => true, 'mensagem' => 'Nome de usuário disponível!']);
    }
} else {
    echo json_encode(['disponivel' => false, 'mensagem' => 'Nenhum usuário informado.']);
}
