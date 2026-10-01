<?php

// Informações de conexão com o banco de dados

$servername = "localhost";

$username = "root";

$password = "";

$dbname = "leitores-de-papel";



// Cria a conexão (compatível com IFBA produção, XAMPP e MAMP local)
try {
    $conn = @new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        $conn = @new mysqli($servername, $username, "root", $dbname);
    }
    if ($conn->connect_error) {
        $conn = @new mysqli("127.0.0.1", $username, "root", $dbname, 8889);
    }
} catch (Exception $e) {
    try {
        $conn = @new mysqli($servername, $username, "root", $dbname);
    } catch (Exception $e2) {
        $conn = @new mysqli("127.0.0.1", $username, "root", $dbname, 8889);
    }
}

// Verifica se a conexão foi bem-sucedida
if (!$conn || $conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . ($conn ? $conn->connect_error : "Erro de conexão"));
}

$conn->set_charset("utf8");
