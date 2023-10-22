<?php
session_start();
global $conn;
include 'conexao.php';
// Inicializar a variável de erro
$error = "";

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obter os dados do formulário
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Escapar os valores de entrada para evitar ataques de SQL Injection
    $username = $conn->real_escape_string($_POST['username']);

    // Consultar o banco de dados para verificar o usuário e a senha nas duas tabelas
    $query_usuarios = "SELECT id, nome, password, is_admin FROM usuarios WHERE username='$username'";
    $query_admin = "SELECT id, nome, password FROM admin WHERE admin_username='$username'";

    $result_usuarios = $conn->query($query_usuarios);
    $result_admin = $conn->query($query_admin);

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
                header("Location: admin.php");
            } else {
                $_SESSION['username'] = $username;
                header("Location: aluno.php");
            }
            $_SESSION['user_id'] = $row['id'];
            setcookie('nome', $row['nome']);
        } else {
            // Autenticação falhou, definir a mensagem de erro
            $error = "Nome de usuário ou senha incorretos";
            // Redirecionar de volta para index.php com mensagem de erro
            header("Location: index.php?error=".urlencode($error));
            exit;
        }
    } else {
        // Autenticação falhou, definir a mensagem de erro
        $error = "Nome de usuário ou senha incorretos";
        // Redirecionar de volta para index.php com mensagem de erro
        header("Location: index.php?error=".urlencode($error));
        exit;
    }
}
?>
