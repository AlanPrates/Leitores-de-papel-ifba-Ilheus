<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
global $conn;
include '../config/database.php';

// Inicializar a variável de erro
$error = "";

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obter os dados do formulário
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consultar o banco de dados para verificar o usuário e a senha nas duas tabelas
    $query_usuarios = "SELECT id, nome, password, is_admin FROM usuarios WHERE username=?";
    $query_admin = "SELECT id, nome, password FROM admin WHERE admin_username=?";

    // Usar declarações preparadas para evitar SQL Injection
    if ($stmt = $conn->prepare($query_usuarios)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result_usuarios = $stmt->get_result();
        $stmt->close();
    }

    if ($stmt = $conn->prepare($query_admin)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result_admin = $stmt->get_result();
        $stmt->close();
    }

    // Verificar se o usuário foi encontrado nas tabelas de usuários ou admin
    if (($result_usuarios && $result_usuarios->num_rows == 1) || ($result_admin && $result_admin->num_rows == 1)) {
        $row = null;

        // Definir a variável $is_admin para identificar o tipo de usuário (0 - usuário comum, 1 - admin)
        $is_admin = 0;

        // Verificar se o usuário é um administrador
        if ($result_admin && $result_admin->num_rows == 1) {
            $row = $result_admin->fetch_assoc();
            $is_admin = 1;
        } elseif ($result_usuarios && $result_usuarios->num_rows == 1) {
            $row = $result_usuarios->fetch_assoc();
            $is_admin = $row['is_admin'];
        }

        $storedPassword = $row['password'];

        // Verificar se a senha fornecida corresponde à senha armazenada
        if (password_verify($password, $storedPassword)) {
            // Autenticação bem-sucedida, iniciar a sessão e redirecionar para a página apropriada
            if ($is_admin) {
                $_SESSION['admin_username'] = $username;
                header("Location: ../admin/index.php");
            } else {
                $_SESSION['username'] = $username;
                header("Location: ../user/aluno.php");
            }
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['nome'] = $row['nome'];
            setcookie('nome', $row['nome']);
        } else {
            // Autenticação falhou, definir a mensagem de erro
            $error = "Nome de usuário ou senha incorretos";
            // Redirecionar de volta para index.php com mensagem de erro
            header("Location: ../public/index.php?error=" . urlencode($error));
            exit;
        }
    } else {
        // Autenticação falhou, definir a mensagem de erro
        $error = "Nome de usuário ou senha incorretos";
        // Redirecionar de volta para index.php com mensagem de erro
        header("Location: ../public/index.php?error=" . urlencode($error));
        exit;
    }
}