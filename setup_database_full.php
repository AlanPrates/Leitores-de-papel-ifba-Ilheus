<?php
include 'config/database.php';

function createTable($conn, $sql, $tableName)
{
    if ($conn->query($sql) === TRUE) {
        echo "Tabela '$tableName' verificada/criada com sucesso.\n";
    } else {
        echo "Erro ao criar tabela '$tableName': " . $conn->error . "\n";
    }
}

// 1. Tabela usuarios
$sql_usuarios = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    nome VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    categoria VARCHAR(50),
    matricula VARCHAR(50),
    datanascimento DATE,
    sexo VARCHAR(20),
    telefone VARCHAR(20),
    is_admin TINYINT(1) DEFAULT 0,
    recuperar_senha VARCHAR(255) DEFAULT NULL
)";
createTable($conn, $sql_usuarios, "usuarios");

// 2. Tabela admin
$sql_admin = "CREATE TABLE IF NOT EXISTS admin (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    admin_username VARCHAR(255) NOT NULL UNIQUE,
    nome VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    categoria VARCHAR(50),
    matricula VARCHAR(50),
    datanascimento DATE,
    sexo VARCHAR(20),
    telefone VARCHAR(20)
)";
createTable($conn, $sql_admin, "admin");

// 3. Tabela livros
$sql_livros = "CREATE TABLE IF NOT EXISTS livros (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    autor VARCHAR(255) NOT NULL,
    ano_publicacao VARCHAR(4),
    editora VARCHAR(255),
    disponivel TINYINT(1) DEFAULT 1,
    quantidade INT(11) DEFAULT 0,
    isbn VARCHAR(20),
    genero VARCHAR(50)
)";
createTable($conn, $sql_livros, "livros");

// 4. Tabela livros_emprestados
$sql_livros_emprestados = "CREATE TABLE IF NOT EXISTS livros_emprestados (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    livro_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    data_emprestimo DATETIME,
    data_devolucao DATETIME,
    titulo VARCHAR(255)
)";
createTable($conn, $sql_livros_emprestados, "livros_emprestados");

// 5. Tabela historico_emprestimos
$sql_historico = "CREATE TABLE IF NOT EXISTS historico_emprestimos (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    livro_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    data_emprestimo DATETIME,
    data_devolucao DATETIME
)";
createTable($conn, $sql_historico, "historico_emprestimos");

// 6. Tabela comentarios
$sql_comentarios = "CREATE TABLE IF NOT EXISTS comentarios (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    livro_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    comentario TEXT,
    avaliacao VARCHAR(5)
)";
createTable($conn, $sql_comentarios, "comentarios");

// Inserir Admin Padrão
$adminUser = 'admin';
$adminPass = password_hash('admin', PASSWORD_DEFAULT);
$checkAdmin = $conn->query("SELECT * FROM admin WHERE admin_username = '$adminUser'");
if ($checkAdmin->num_rows == 0) {
    $sql_insert_admin = "INSERT INTO admin (admin_username, nome, password, email, categoria, matricula) 
                        VALUES ('$adminUser', 'Administrador', '$adminPass', 'admin@admin.com', 'admin', '0000')";
    if ($conn->query($sql_insert_admin) === TRUE) {
        echo "Administrador padrão criado (User: admin, Pass: admin).\n";
    } else {
        echo "Erro ao criar administrador: " . $conn->error . "\n";
    }
} else {
    echo "Administrador já existe.\n";
}

$conn->close();
?>