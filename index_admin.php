<?php
session_start();
global $conn;
include 'conexao.php';
ob_start();
// Verificar se o usuário já está logado, redirecionar para a página principal
if (isset($_SESSION['admin_username'])) {
  // Redireciona para a página "admin.php" com o nome de usuário
  header("Location: admin.php?usuario=" . $_SESSION['admin_username']);
  exit;
}

// Inicializa a variável de erro
$error = "";

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Verificar se as variáveis estão definidas e não estão vazias
  if (isset($_POST['admin_username']) && isset($_POST['password']) && !empty($_POST['admin_username']) && !empty($_POST['password'])) {
    // Obter os dados do formulário
    $username = $_POST['admin_username'];
    $password = $_POST['password'];

    // Escapar os valores de entrada para evitar ataques de SQL Injection
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Consultar o banco de dados para verificar o usuário e a senha
    $query = "SELECT id, nome, password FROM admin WHERE admin_username='$username'";
    $result = $conn->query($query);

    // Verificar se o usuário foi encontrado
    if ($result && $result->num_rows == 1) {
      $row = $result->fetch_assoc();
      $storedPassword = $row['password'];

      // Verificar se a senha fornecida corresponde à senha armazenada
      if (password_verify($password, $storedPassword)) {
        // Autenticação bem-sucedida, iniciar a sessão e redirecionar para a página principal
        $_SESSION['admin_username'] = $username;
        $_SESSION['user_id'] = $row['id'];
        setcookie('nome', $row['nome']);
        header("Location: admin.php");
        exit;
      } else {
        // Autenticação falhou, definir a mensagem de erro
        $error = "Nome de usuário ou senha incorretos";
      }
    } else {
      // Autenticação falhou, definir a mensagem de erro
      $error = "Nome de usuário ou senha incorretos";
    }
  } else {
    // Algum dos campos do formulário está vazio, definir a mensagem de erro
    $error = "Por favor, preencha todos os campos";
  }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leitores de Papel - Admin</title>
  <!-- Estilos CSS -->
  <link rel="stylesheet" href="assets/menu-mobile-css/style.css">
  <link rel="stylesheet" href="css/bootstrap.css">
  <!-- Ícones -->
  <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>
</head>

<body>
  <section>
    <header>
      <?php if (isset($success_message)) { ?>
        <p><?php echo $success_message; ?></p>
        <script>
          // Redirecionar após 2 segundos
          setTimeout(function() {
            window.location.href = "aluno.php";
          }, 2000);
        </script>
      <?php } ?>
      <?php if (isset($error_message)) { ?>
        <p><?php echo $error_message; ?></p>
      <?php } ?>
      <!-- Barra de navegação -->
      <nav class="nav-bar">
        <div class="logo">
          <img class="cabecalho-imagem" src="assets/img/Fotoram.io.png" title="Sempre se atualizando constantemente" alt="LOGO ALAN" />
        </div>
        <div class="nav-list">
          <ul>
            <li class="nav-item"><a href="cadastro.php" class="nav-link">Criar conta de leitor</a></li>
            <li class="nav-item"><a href="index.php" class="nav-link">Acessar minhas leituras</a></li>
            <li class="nav-item"><a href="index_admin.php" class="nav-link">Acessar como Administrador </a></li>
          </ul>
        </div>
        <div class="mobile-menu-icon">
          <button onclick="menuShow()"><img class="icon" src="assets/img/menu_white_36dp.svg" alt=""></button>
        </div>
      </nav>
      <div class="mobile-menu">
        <ul>
          <li class="nav-item"><a href="cadastro.php" class="nav-link">Criar conta de leitor</a></li>
          <li class="nav-item"><a href="index.php" class="nav-link">Acessar minhas leituras</a></li>
          <li class="nav-item"><a href="index_admin.php" class="nav-link">Acessar como Administrador</a></li>
        </ul>
      </div>
    </header>

    <div class="container d-flex justify-content-center align-items-center vh-100">
      <div class="shadow p-4">
        <h1 class="text-center mb-4">Página Restrita</h1>
        <?php if ($error !== "") { ?>
          <div class="alert alert-danger mt-3 text-center" role="alert">
            <?php echo $error; ?>
          </div>
        <?php } ?>
        <form action="index_admin.php" method="POST">
          <div class="form-group">
            <label for="usuario">
              <i class="fas fa-user"></i> Usuário:
            </label>
            <input type="text" class="form-control" id="usuario" name="admin_username" required>
          </div>

          <div class="form-group">
            <label for="senha">
              <i class="fas fa-lock"></i> Senha:
            </label>
            <div class="input-group">
              <input type="password" class="form-control" id="senha" name="password" required>
              <div class="input-group-append">
                <span class="input-group-text">
                  <i id="icone-senha" class="fas fa-eye" onclick="mostrarSenha()"></i>
                </span>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-danger btn-block">Entrar</button>
        </form>
        <p class="text-center mt-3">Esqueceu a senha? <a href="recuperar_senha_admin.php">Clique aqui</a>.</p>
        <div class="container">
          <div class="row">
            <div class="col-md-12">
              <?php
              if (isset($_SESSION['msg'])) {
                echo '<div class="alert alert-info">' . $_SESSION['msg'] . '</div>';
                unset($_SESSION['msg']);
              }
              ?>
            </div>
          </div>
        </div>
        <div class="alert alert-warning text-center" role="alert">
            Usuário: alan
            <br> 
            Senha: 123
        </div>
      </div>
    </div>

  </section>
  <!-- Scripts JavaScript -->
  <script src="assets/js/script.js"></script>
  <script src="js/password.js"></script>
  <?php
  // Inclui o rodapé
  include 'rodape.php';
  ?>
</body>

</html>