<?php
session_start();
global $conn;
include 'conexao.php';
ob_start();

// Verificar se o usuário ou administrador já está logado
if (isset($_SESSION['is_admin'])) {
  // Se o usuário for um administrador, redirecionar para a página admin.php
  if ($_SESSION['is_admin']) {
    header("Location: admin.php");
  } else {
    // Caso contrário, redirecionar para a página aluno.php
    header("Location: aluno.php");
  }
  exit;
}

// Inicializar a variável de erro
$error = "";

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Verificar se as variáveis estão definidas e não estão vazias
  if (isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    // Obter os dados do formulário
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Escapar os valores de entrada para evitar ataques de SQL Injection
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Consultar o banco de dados para verificar o usuário ou administrador e a senha
    $query_users = "SELECT id, username, password FROM usuarios WHERE username='$username'";
    $query_admin = "SELECT id, admin_username as username, password FROM admin WHERE admin_username='$username'";

    $result_users = $conn->query($query_users);
    $result_admin = $conn->query($query_admin);

    // Verificar se o usuário ou administrador foi encontrado
    if (($result_users && $result_users->num_rows == 1) || ($result_admin && $result_admin->num_rows == 1)) {
      $row = null;
      if ($result_users->num_rows == 1) {
        $row = $result_users->fetch_assoc();
      } else {
        $row = $result_admin->fetch_assoc();
      }
      $storedPassword = $row['password'];

      // Verificar se a senha fornecida corresponde à senha armazenada
      if (password_verify($password, $storedPassword)) {
        // Autenticação bem-sucedida, iniciar a sessão e redirecionar para a página apropriada
        $_SESSION['username'] = $row['username'];
        $_SESSION['user_id'] = $row['id'];

        // Definir o campo 'is_admin' na sessão para identificar se é um administrador
        if (isset($result_admin)) {
          $_SESSION['is_admin'] = true;
        } else {
          $_SESSION['is_admin'] = false;
        }

        // Redirecionar para a página apropriada com base no tipo de usuário
        if ($_SESSION['is_admin']) {
          header("Location: admin.php");
        } else {
          header("Location: aluno.php");
        }
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
  <title>Leitores de Papel - Login</title>
  <!-- Estilos CSS -->
  <link rel="stylesheet" href="assets/menu-mobile-css/style.css">
  <link rel="stylesheet" href="css/bootstrap.css">
  <!-- Ícones -->
  <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>
</head>

<body>
  <section>
    <header>
      <header>
        <!-- Barra de navegação -->
        <nav class="nav-bar">
          <div class="logo">
            <img class="cabecalho-imagem" src="assets/img/Fotoram.io.png" title="Sempre se atualizando constantemente" alt="LOGO ALAN" />
          </div>
          <div class="nav-list">
            <ul>
              <li class="nav-item"><a href="index.php" class="nav-link">Criar conta de leitor</a></li>
              <li class="nav-item"><a href="index.php" class="nav-link">Acessar minhas leituras</a></li>
            </ul>
          </div>
          <div class="mobile-menu-icon">
            <button onclick="menuShow()"><img class="icon" src="assets/img/menu_white_36dp.svg" alt=""></button>
          </div>
        </nav>
        <div class="mobile-menu">
          <ul>
            <li class="nav-item"><a href="index.php" class="nav-link">Criar conta de leitor</a></li>
            <li class="nav-item"><a href="index.php" class="nav-link">Acessar minhas leituras</a></li>
          </ul>
        </div>
      </header>
    </header>

    <div class="container d-flex justify-content-center align-items-center vh-100">
      <div class="shadow p-4">
        <h1 class="text-center mb-4">Página de Login</h1>
        <?php if ($error !== "") { ?>
          <div class="alert alert-danger mt-3 text-center" role="alert">
            <?php echo $error; ?>
          </div>
        <?php } ?>
        <form action="login.php" method="POST">
          <div class="form-group">
            <label for="usuario">
              <i class="fas fa-user"></i> Usuário:
            </label>
            <input type="text" class="form-control" id="usuario" name="username" required>
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
        <p class="text-center mt-3">Não possui cadastro? <a href="cadastro.php">Clique aqui</a>.</p>
        <p class="text-center mt-3">Esquerceu a senha? <a href="recuperar_senha.php">Clique aqui</a>.</p>
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
          Usuário: alanj
          <br>
          Senha: 123
        </div>
        <div class="alert alert-warning text-center" role="alert">
          Usuário Admin: alan
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