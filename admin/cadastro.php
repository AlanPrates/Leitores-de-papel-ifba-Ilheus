<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

// Apenas administradores podem cadastrar novos administradores
require_admin('../public/index.php');

$msg_feedback = '';
$msg_tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_username']) && isset($_POST['password'])) {
    $username = trim($_POST['admin_username']);
    $nome = trim($_POST['nome']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    $categoria = trim($_POST['categoria']);
    $matricula = trim($_POST['matricula']);
    $datanascimento = trim($_POST['datanascimento']);
    $sexo = trim($_POST['sexo']);
    $telefone = trim($_POST['telefone']);

    if (empty($username) || empty($password) || empty($nome)) {
        $msg_feedback = "Por favor, preencha todos os campos obrigatórios.";
        $msg_tipo = "danger";
    } else {
        // Verifica se o usuário já existe
        $stmt_check = $conn->prepare("SELECT id FROM admin WHERE admin_username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $msg_feedback = "Nome de usuário já existe. Por favor, escolha outro.";
            $msg_tipo = "danger";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt_insert = $conn->prepare("INSERT INTO admin (admin_username, nome, password, email, categoria, matricula, datanascimento, sexo, telefone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("sssssssss", $username, $nome, $hashedPassword, $email, $categoria, $matricula, $datanascimento, $sexo, $telefone);

            if ($stmt_insert->execute()) {
                $msg_feedback = "Administrador registrado com sucesso!";
                $msg_tipo = "success";
            } else {
                $msg_feedback = "Erro ao registrar o administrador.";
                $msg_tipo = "danger";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
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

  <link rel="stylesheet" href="../assets/css/menu-mobile.css">

  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">

  <!-- Ícones -->

  <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

  <!-- Ícones -->

  <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

</head>



<body>

  <section>

    <?php include '../includes/header.php'; ?>



    <div class="row justify-content-center">

      <div class="col-lg-6">

        <div class="shadow p-4">

          <h1 class="mb-4 text-center mx-auto">Cadastrar Administrador</h1>

          <?php if (!empty($msg_feedback)) { ?>
            <div class="alert alert-<?php echo htmlspecialchars($msg_tipo); ?>" role="alert">
              <?php echo htmlspecialchars($msg_feedback); ?>
            </div>
          <?php } ?>

          <form action="cadastro.php" method="POST">

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

              <div class="row">
                <div class="col-md-6 mb-2">
                  <button type="submit" class="btn btn-danger btn-block">Cadastrar</button>
                </div>
                <div class="col-md-6 mb-2">
                  <a href="index.php" class="btn btn-warning btn-block">Voltar para o Painel de Usuário</a>
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



  <script src="../assets/js/verificar_usuario.js"></script>

  <script src="../assets/js/index.js"></script>

  <script src="../assets/js/password.js"></script>

  <script src="../assets/js/script.js"></script>

  <script src="../assets/js/bootstrap.min.js"></script>

  <?php

  // Inclui o rodapé
  
  include '../includes/footer.php';

  ?>

</body>



</html>