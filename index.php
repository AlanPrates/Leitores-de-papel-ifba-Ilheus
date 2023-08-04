<?php
session_start();
global $conn;
include 'conexao.php';
ob_start();
// Verifica se o formulário de registro foi enviado
if (isset($_POST['username']) && isset($_POST['password'])) {
// Constante que define o número máximo de itens por página
define('ITENS_POR_PAGINA', 20);

/**
 * 
 *
 * 
 * 
 */

  // Escapa os valores de entrada para evitar ataques de SQL Injection
  $username = $conn->real_escape_string($_POST['username']);
  $nome = $conn->real_escape_string($_POST['nome']);
  $password = $conn->real_escape_string($_POST['password']);
  $email = $conn->real_escape_string($_POST['email']);
  $categoria = $conn->real_escape_string($_POST['categoria']);
  $datanascimento = $conn->real_escape_string($_POST['datanascimento']);
  $sexo = $conn->real_escape_string($_POST['sexo']);
  $telefone = $conn->real_escape_string($_POST['telefone']);

  // Verifica se o nome de usuário já existe no banco de dados
  $query = "SELECT id FROM usuarios WHERE username='$username'";
  $result = $conn->query($query);

  if ($result && $result->num_rows > 0) {
    $error_message = "Nome de usuário já existe. Por favor, escolha outro nome de usuário.";
  } else {
    // Insere o novo usuário no banco de dados
    $query = "INSERT INTO usuarios (username, password, nome, email, categoria, datanascimento, sexo, telefone) VALUES ('$username', '$password', '$nome', '$email', '$password', '$categoria', '$datanascimento', '$sexo', '$telefone')";
    $result = $conn->query($query);

    if ($result) {
      $success_message = "Usuário registrado com sucesso!";
    } else {
      $error_message = "Erro ao registrar o usuário: " . $conn->error;
    }
  }


}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leitores de Papel</title>
  <!-- Estilos CSS -->
  <link rel="stylesheet" href="assets/menu-mobile-css/style.css">
  <link rel="stylesheet" href="css/bootstrap.css">
  <!-- Ícones -->
  <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>
</head>
<body>
<section>
  <header>
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
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="shadow p-4">
      <h1 class="text-center mb-4">Página de Login</h1>
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
      <p class="text-center mt-3">Esquerceu  a senha? <a href="recuperar_senha.php">Clique aqui</a>.</p>
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
