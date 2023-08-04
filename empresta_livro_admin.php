<?php
session_start();
global $conn;
include 'conexao.php';

// Verifica se o usuário não está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Verifica se o formulário de empréstimo foi enviado
if (isset($_POST['livro_id'])) {
    $livro_id = $_POST['livro_id'];
    $user_id = $_SESSION['user_id'];

    // Obter a data atual para a data de empréstimo
    $data_emprestimo = date('Y-m-d H:i:s');

    // Calcular a data de devolução (adicionar 15 dias à data atual)
    $data_devolucao = date('Y-m-d H:i:s', strtotime('+15 days'));

    // Verifica a quantidade disponível do livro
    $query = "SELECT quantidade FROM livros WHERE id = '$livro_id'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $livro = $result->fetch_assoc();
        $quantidade = $livro['quantidade'];

        // Verifica se há livros disponíveis para empréstimo
        if ($quantidade > 0) {
            // Insere o livro emprestado no banco de dados com as datas de empréstimo e devolução
            $query = "INSERT INTO livros_emprestados (livro_id, user_id, data_emprestimo, data_devolucao) VALUES ('$livro_id', '$user_id', '$data_emprestimo', '$data_devolucao')";
            $result = $conn->query($query);

            if ($result) {
                // Atualiza a quantidade do livro
                $quantidade--;
                $update_query = "UPDATE livros SET quantidade = $quantidade WHERE id = '$livro_id'";
                $update_result = $conn->query($update_query);

                if ($update_result) {
                    $success_message = "Livro emprestado com sucesso!";
                } else {
                    $error_message = "Erro ao atualizar a quantidade do livro.";
                }
            } else {
                $error_message = "Erro ao emprestar o livro.";
            }
        } else {
            $error_message = "Não há livros disponíveis para empréstimo.";
        }
    } else {
        $error_message = "Livro não encontrado.";
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Empréstimo de Livro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/rodape.css">
    <link rel="stylesheet" href="assets/menu-mobile-css/style.css">

</head>

<body>
    <header>
        <nav class="nav-bar">
            <div class="logo">
                <img class="cabecalho-imagem" src="assets/img/Fotoram.io.png" title="Sempre se atualizando constantemente" alt="LOGO ALAN" />
            </div>
            <div class="nav-list">
                <ul>
                    <li class="nav-item"><a href="cadastro.php" class="nav-link">Criar Conta</a></li>
                    <li class="nav-item"><a href="cadastro.php" class="nav-link">Acessar minhas leituras</a></li>
                    <li class="nav-item"><a href="index_admin.php" class="nav-link">Acessar como Administrador </a></li>
                </ul>
            </div>
            <div class="mobile-menu-icon">
                <button onclick="menuShow()"><img class="icon" src="assets/img/menu_white_36dp.svg" alt=""></button>
            </div>
        </nav>
        <div class="mobile-menu">
            <ul>
                <li class="nav-item"><a href="cadastro.php" class="nav-link">Criar Conta</a></li>
                <li class="nav-item"><a href="cadastro.php" class="nav-link">Acessar Minhas Leituras</a></li>
                <li class="nav-item"><a href="index_admin.php" class="nav-link">Acessar como Administrador</a></li>
            </ul>
        </div>
    </header>
    <div class="container">
        <h2>Empréstimo de Livro</h2>
        <?php if (isset($success_message)) { ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php } ?>
        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php } ?>
        <br>
        <a href="lista_livros_admin.php" class="btn btn-warning">Voltar para Lista de Livros</a>
        <br>
        <br>
        <a href="devolve_livro_admin.php" class="btn btn-warning">Devolver Livro</a>
        <br>
        <br>
        <a href="logout.php" class="btn btn-danger">Sair</a>

    </div>
    <script src="assets/js/script.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <?php
    // Inclui o rodapé
    include 'rodape.php';
    ?>
</body>

</html>