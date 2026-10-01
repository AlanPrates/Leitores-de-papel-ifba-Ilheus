<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

require_admin('../public/index.php');



// Obtém o nome de usuário a partir da sessão

$username = $_SESSION['admin_username'];



// Verifica se o formulário de atualização foi enviado

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $password = $_POST['password'];
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
    $matricula = isset($_POST['matricula']) ? trim($_POST['matricula']) : '';
    $sexo = isset($_POST['sexo']) ? trim($_POST['sexo']) : '';
    $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt_upd = $conn->prepare("UPDATE admin SET password = ?, email = ?, categoria = ?, matricula = ?, sexo = ?, telefone = ? WHERE admin_username = ?");
    if ($stmt_upd) {
        $stmt_upd->bind_param("sssssss", $hashedPassword, $email, $categoria, $matricula, $sexo, $telefone, $username);
        if ($stmt_upd->execute()) {
            $success_message = "Dados do usuário atualizados com sucesso!";
        } else {
            $error_message = "Erro ao atualizar os dados do usuário.";
        }
        $stmt_upd->close();
    } else {
        $error_message = "Erro ao preparar a atualização.";
    }
}

$emailValue = '';
$categoriaValue = '';
$matriculaValue = '';
$sexoValue = '';
$telefoneValue = '';

$stmt_sel = $conn->prepare("SELECT email, categoria, matricula, sexo, telefone FROM admin WHERE admin_username = ? LIMIT 1");
if ($stmt_sel) {
    $stmt_sel->bind_param("s", $username);
    $stmt_sel->execute();
    $result = $stmt_sel->get_result();
    if ($result && $result->num_rows > 0) {
        $aluno = $result->fetch_assoc();
        $emailValue = $aluno['email'];
        $categoriaValue = $aluno['categoria'];
        $matriculaValue = $aluno['matricula'];
        $sexoValue = $aluno['sexo'];
        $telefoneValue = $aluno['telefone'];
    }
    $stmt_sel->close();
}

?>





<!DOCTYPE html>

<html>



<head>

    <title>Atualizar Dados do Usuário</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="stylesheet" href="../assets/css/menu-mobile.css">

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

    <style>
        /* Container style removed to allow full width */
    </style>

</head>



<body>

    <?php include '../includes/header.php'; ?>

    <div class="container">

        <?php

        // Verifica se houve uma mensagem de erro
        
        if (isset($error_message)) {

            echo '<div class="alert alert-danger">' . $error_message . '</div>';

        }



        // Verifica se houve uma mensagem de sucesso
        
        if (isset($success_message)) {

            echo '<div class="alert alert-success">' . $success_message . '</div>';

        }

        ?>



        <br>

        <h2 class="text-black">Atualizar meus dados</h2>
        <h2><span style="color: red;">
                <?php echo htmlspecialchars($username); ?>
            </span></h2>

        <form method="POST" action="atualiza_dados.php">

            <div class="form-group text-left">

                <label for="password">Digite sua Senha ou uma nova, para atualizar o seu cadastro:</label>

                <input type="password" class="form-control" name="password" value="" required>

            </div>



            <div class="form-group text-left">

                <label for="email">E-mail:</label>

                <input type="email" class="form-control" name="email"
                    value="<?php echo isset($emailValue) ? $emailValue : ''; ?>" required>

            </div>



            <div class="form-group text-left">

                <label for="categoria">Categoria:</label>

                <select class="form-control" name="categoria" required>

                    <option value="Aluno" <?php echo isset($categoriaValue) && $categoriaValue == 'Aluno' ? 'selected' : ''; ?>>Aluno</option>

                    <option value="Professor" <?php echo isset($categoriaValue) && $categoriaValue == 'Professor' ? 'selected' : ''; ?>>Professor</option>

                    <option value="Funcionario" <?php echo isset($categoriaValue) && $categoriaValue == 'Funcionario' ? 'selected' : ''; ?>>Funcionário</option>

                </select>

            </div>



            <div class="form-group text-left">

                <label for="matricula">Matrícula:</label>

                <input type="text" class="form-control" name="matricula"
                    value="<?php echo isset($matriculaValue) ? $matriculaValue : ''; ?>" required>

            </div>



            <div class="form-group text-left">

                <label for="sexo">Sexo:</label>

                <select class="form-control" name="sexo" required>

                    <option value="Masculino" <?php echo isset($sexoValue) && $sexoValue == 'Masculino' ? 'selected' : ''; ?>>Masculino</option>

                    <option value="Feminino" <?php echo isset($sexoValue) && $sexoValue == 'Feminino' ? 'selected' : ''; ?>>Feminino</option>

                    <option value="Outro" <?php echo isset($sexoValue) && $sexoValue == 'Outro' ? 'selected' : ''; ?>>
                        Outro</option>

                </select>

            </div>



            <div class="form-group text-left">

                <label for="telefone">Telefone:</label>

                <input type="text" class="form-control" name="telefone"
                    value="<?php echo isset($telefoneValue) ? $telefoneValue : ''; ?>" required>

            </div>



            <div class="row align-items-end">
                <div class="col-md-6 mb-2">
                    <button type="submit" class="btn btn-danger btn-block" style="height: 38px;">Atualizar
                        Dados</button>
                </div>
                <div class="col-md-6 mb-2">
                    <a href="index.php"
                        class="btn btn-warning btn-block d-flex align-items-center justify-content-center"
                        style="height: 38px;">Voltar para Painel de Usuário</a>
                </div>
            </div>

        </form>

    </div>

    <script src="../assets/js/script.js"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <?php

    // Inclui o rodapé
    
    include '../includes/footer.php';

    ?>

</body>



</html>