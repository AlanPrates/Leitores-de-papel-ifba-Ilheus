<?php

session_start();

global $conn;

include 'conexao.php';



// Verifica se o formulário de registro foi enviado

if (isset($_POST['admin_username']) && isset($_POST['password'])) {



  // Escapa os valores de entrada para evitar ataques de SQL Injection

  $username = $conn->real_escape_string($_POST['admin_username']);

  $nome = $conn->real_escape_string($_POST['nome']);

  $password = $conn->real_escape_string($_POST['password']);

  $email = $conn->real_escape_string($_POST['email']);

  $categoria = $conn->real_escape_string($_POST['categoria']);

  $matricula = $conn->real_escape_string($_POST['matricula']);

  $datanascimento = $conn->real_escape_string($_POST['datanascimento']);

  $sexo = $conn->real_escape_string($_POST['sexo']);

  $telefone = $conn->real_escape_string($_POST['telefone']);



  // Verifica se o nome de usuário já existe no banco de dados

  $query_check = "SELECT * FROM admin WHERE admin_username = '$username'";

  $result_check = $conn->query($query_check);



  if ($result_check && $result_check->num_rows > 0) {

    echo '<script>alert("Nome de usuário já existe. Por favor, escolha outro nome de usuário.");</script>';

  } else {

    // Criptografa a senha usando a função password_hash

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);



    // Insere o novo usuário no banco de dados

    $query_insert = "INSERT INTO admin (admin_username, nome, password, email, categoria, matricula, datanascimento, sexo, telefone) VALUES ('$username', '$nome', '$hashedPassword', '$email', '$categoria', '$matricula', '$datanascimento', '$sexo', '$telefone')";



    if ($conn->query($query_insert)) {

      echo '<script>alert("Usuário registrado com sucesso! Só logar...");</script>';

      // Redirecionar para o painel administrativo após 5 segundos

      header("Refresh: 2; URL=admin.php");

      exit; // Encerrar o script após o redirecionamento

    } else {

      echo '<script>alert("Erro ao registrar o usuário: ' . $conn->error . '");</script>';

    }

  }

} else {

  echo '<script>alert("O formulário de registro não foi enviado.");</script>';

}



?>





<!DOCTYPE html>

<html lang="en">



<head>

  <meta charset="UTF-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Leitores de Papel</title>

  <!-- Estilos CSS -->

  <link rel="stylesheet" href="assets/menu-mobile-css/style.css">

  <link rel="stylesheet" href="css/bootstrap.min.css">

  <!-- Ícones -->

  <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

  <!-- Ícones -->

  <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

</head>



<body>

  <section>

    <header>

      <!-- Barra de navegação -->

      <nav class="nav-bar">

        <div class="logo">
          <a href="admin.php">
            <img class="cabecalho-imagem" src="assets/img/Fotoram.io.png" title="Sempre se atualizando constantemente"
              alt="LOGO ALAN" />
          </a>
        </div>

        <div class="nav-list">

          <ul>

            <li class="nav-item"><a href="index.php" class="nav-link">Criar conta de leitor</a></li>

            <li class="nav-item"><a href="minhas_leituras.php" class="nav-link">Acessar minhas leituras</a></li>

          </ul>

        </div>

        <div class="mobile-menu-icon">

          <button onclick="menuShow()"><img class="icon" src="assets/img/menu_white_36dp.svg" alt=""></button>

        </div>

      </nav>

      <div class="mobile-menu">

        <ul>

          <li class="nav-item"><a href="index.php" class="nav-link">Criar conta de leitor</a></li>

          <li class="nav-item"><a href="minhas_leituras.php" class="nav-link">Acessar Minhas Leituras</a></li>

        </ul>

      </div>

    </header>



    <div class="row justify-content-center">

      <div class="col-lg-6">

        <div class="shadow p-4">

          <h1 class="mb-4 text-center mx-auto">Cadastrar Administrador</h1>

          <form action="cadastro_admin.php" method="POST">

            <div class="form-group">

              <label for="nome">Nome Completo<span class="text-danger">*</span>:</label>

              <input type="text" class="form-control" id="nome" name="nome" required>

            </div>



            <div class="form-group">

              <label for="sexo">Sexo<span class="text-danger">*</span>:</label>

              <select class="form-control" id="sexo" name="sexo" required>

                <option value="">Selecione...</option>

                <option value="masculino">Masculino</option>

                <option value="feminino">Feminino</option>

                <option value="outro">Outro</option>

              </select>

            </div>

            <div class="form-group">

              <label for="data-nascimento">Data de Nascimento<span class="text-danger">*</span>:</label>

              <input type="date" class="form-control" id="data-nascimento" name="datanascimento" required>

            </div>

            <div class="form-group">

              <label for="categoria">Categoria<span class="text-danger">*</span>:</label>

              <select class="form-control" id="categoria" name="categoria" required>

                <option value="" disabled selected>Selecione...</option>

                <option value="aluno">Aluno</option>

                <option value="funcionario">Bolsista</option>

                <option value="professor">Professor</option>

                <option value="funcionario">Funcionário</option>


              </select>

            </div>

            <div class="form-group">

              <label for="matricula">Matrícula ou SIAPE<span class="text-danger">*</span>:</label>

              <input type="text" class="form-control" id="matricula" name="matricula" required>

            </div>

            <div class="form-group">

              <label for="telefone">Telefone<span class="text-danger">*</span>:</label>

              <input type="tel" class="form-control" id="telefone" name="telefone" required>

            </div>

            <div class="form-group">

              <label for="email">E-Mail<span class="text-danger">*</span>:</label>

              <input type="email" class="form-control" id="email" name="email" required>

            </div>

            <div class="form-group">

              <label for="usuario">Usuário<span class="text-danger">*</span>:</label>

              <input type="text" class="form-control" id="usuario" name="admin_username" required>

              <div id="verificar-usuario"></div>

            </div>



            <div class="form-group">

              <label for="senha">Senha<span class="text-danger">*</span>:</label>

              <div class="input-group">

                <input type="password" class="form-control" id="senha" name="password" required>

                <div class="input-group-append">

                  <span class="input-group-text">

                    <i id="icone-senha" class="fas fa-eye" onclick="mostrarSenha()"></i>

                  </span>

                </div>

              </div>

              <div class="form-group">

                <label for="confirmar-senha">Repetir Senha<span class="text-danger">*</span>:</label>

                <input type="password" class="form-control" id="confirmar-senha" required>

              </div>



              <div class="divCheck">

                <div class="termos-texto p-2" style="max-height: 100px; overflow: auto;">

                  Por favor, leia e aceite os termos abaixo:

                  Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi maximus lacus non neque ullamcorper,
                  nec semper quam cursus. Donec ac felis at purus tincidunt luctus. Vivamus consequat dolor sit amet
                  rutrum ultrices. Mauris dapibus elit non risus pulvinar, sed condimentum enim hendrerit. Sed interdum
                  orci eget semper ultrices. Suspendisse aliquam, risus sit amet porttitor tempus, justo odio congue
                  purus, sed rhoncus orci enim non nulla. Ut sagittis ultrices tempor. Donec finibus metus sit amet
                  tortor efficitur dignissim. Duis vehicula, est vel gravida vulputate, augue sem pellentesque felis,
                  sed blandit lorem ligula in mi.

                </div>

                <div class="form-check form-check-inline">

                  <input class="form-check-input" type="checkbox" id="aceitar-termos" required>

                  <label class="form-check-label" for="aceitar-termos">Aceitar Termos<span
                      class="text-danger">*</span></label>

                </div>

              </div>

              <br>

              <button type="submit" class="btn btn-danger">Cadastrar</button>

              <br>

              <br>

              <div class="row">

                <div class="col-sm-12">

                  <a href="admin.php" class="btn btn-warning">Voltar para o Painel de Usuário</a>

                </div>

              </div>

              <?php if (isset($success_message)) { ?>

                <p>
                  <?php echo $success_message; ?>
                </p>

              <?php } ?>

              <?php if (isset($error_message)) { ?>

                <p>
                  <?php echo $error_message; ?>
                </p>

              <?php } ?>

          </form>

        </div>

      </div>

    </div>





  </section>



  <script src="js/verificar_usuario.js"></script>

  <script src="js/index.js"></script>

  <script src="js/password.js"></script>

  <script src="assets/js/script.js"></script>

  <script src="js/bootstrap.min.js"></script>

  <?php

  // Inclui o rodapé
  
  include 'rodape.php';

  ?>

</body>



</html>
