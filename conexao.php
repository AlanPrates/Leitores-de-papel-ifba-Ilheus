<?php

// Informações de conexão com o banco de dados

$servername = "";

$username = "";

$password = "";

$dbname = "";



// Cria a conexão

$conn = new mysqli($servername, $username, $password, $dbname);



// Verifica se a conexão foi bem-sucedida

if ($conn->connect_error) {

    die("Falha na conexão com o banco de dados: " . $conn->connect_error);

}

?>
