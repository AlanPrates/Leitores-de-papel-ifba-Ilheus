<?php
session_start();
global $conn;
include 'conexao.php';

// Verificar se o usuário já está logado, redirecionar para a página principal
if (isset($_SESSION['username'])) {
  // Redireciona para a página "Aluno.php" com o nome de usuário
  header("Location: aluno.php?usuario=" . $_SESSION['username']);
  exit;
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Obter os dados do formulário
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Escapar os valores de entrada para evitar ataques de SQL Injection
  $username = $conn->real_escape_string($_POST['username']);

  // Consultar o banco de dados para verificar o usuário e a senha
  $query = "SELECT id, nome, password FROM usuarios WHERE username='$username'";
  $result = $conn->query($query);

  // Verificar se o usuário foi encontrado
  if ($result && $result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $storedPassword = $row['password'];

    // Verificar se a senha fornecida corresponde à senha armazenada
    if (password_verify($password, $storedPassword)) {
      // Autenticação bem-sucedida, iniciar a sessão e redirecionar para a página principal
      $_SESSION['username'] = $username;
      $_SESSION['user_id'] = $row['id'];
      setcookie('nome', $row['nome']);
      header("Location: aluno.php");
      exit;
    }
  }

  // Autenticação falhou, exibir mensagem de erro
  $error = "Nome de usuário ou senha incorretos";
  // Redirecionar para a página "index.php" com uma mensagem de "Cadastro não encontrado"
  header("Refresh: 2; URL=index.php");
  echo "Cadastro não encontrado.";
  exit;
}
?>
