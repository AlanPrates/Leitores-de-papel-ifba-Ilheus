<?php

session_start();

global $conn;

include '../config/database.php';



// Verificar se o usuário está logado

if (isset($_SESSION['username'])) {



    // Obter o ID do usuário logado

    $user_id = $_SESSION['user_id'];



    // Consulta o histórico de empréstimos do aluno

    $query = "SELECT historico_emprestimos.*, livros.titulo as livro_titulo, livros.autor as livro_autor, livros.editora as livro_editora, livros.ano_publicacao as livro_ano_publicacao FROM historico_emprestimos JOIN livros ON historico_emprestimos.livro_id = livros.id WHERE user_id='$user_id'";

    $result = $conn->query($query);



    if ($result === false) {

        $error_message = "Erro na consulta: " . $conn->error;

    }

} else {

    header('Location: ../public/index.php'); // Redirecionar para a página de login se o usuário não estiver logado

}

?>



<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Minhas Leituras - Leitores de Papel</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <link rel="stylesheet" href="../assets/css/rodape.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            margin-top: 20px;
            margin-bottom: 40px;
        }
    </style>
</head>

<body>

    <header>
        <nav class="nav-bar">
            <div class="logo">
                <a href="aluno.php">
                    <img class="cabecalho-imagem" src="../assets/img/Fotoram.io.png"
                        title="Sempre se atualizando constantemente" alt="LOGO ALAN" />
                </a>
            </div>

            <div class="nav-list">
                <ul>
                    <li class="nav-item"><a href="../public/cadastro.php" class="nav-link">Criar conta de leitor</a></li>
                    <li class="nav-item"><a href="minhas_leituras.php" class="nav-link">Acessar minhas leituras</a></li>
                </ul>
            </div>

            <div class="mobile-menu-icon">
                <button onclick="menuShow()"><img class="icon" src="../assets/img/menu_white_36dp.svg" alt=""></button>
            </div>
        </nav>

        <div class="mobile-menu">
            <ul>
                <li class="nav-item"><a href="../public/cadastro.php" class="nav-link">Criar conta de leitor</a></li>
                <li class="nav-item"><a href="minhas_leituras.php" class="nav-link">Acessar minhas leituras</a></li>
            </ul>
        </div>
    </header>

    <div class="container main-content">

        <h2 class="mb-4">Minhas Leituras</h2>

        <?php
        if (isset($error_message)) {
            echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
        } elseif ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>

                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php echo $row['livro_titulo']; ?>
                        </h5>
                        <h6 class="card-subtitle mb-2 text-muted">
                            <?php echo $row['livro_autor']; ?>
                        </h6>
                        <p class="card-text">
                            <strong>Editora:</strong> <?php echo $row['livro_editora']; ?><br>
                            <strong>Ano de Publicação:</strong> <?php echo $row['livro_ano_publicacao']; ?><br>
                            <strong>Data Empréstimo:</strong> <?php echo $row['data_emprestimo']; ?><br>
                            <strong>Data Devolução:</strong> <?php echo $row['data_devolucao']; ?><br>
                        </p>
                    </div>
                </div>

                <?php
            }
        } else {
            echo '<div class="alert alert-info" role="alert">Nenhum registro de empréstimo encontrado.</div>';
        }
        ?>

        <div class="row mt-4 mb-4">
            <div class="col-md-3 mb-2">
                <a href="aluno.php" class="btn btn-warning btn-block">Voltar para Página Principal</a>
            </div>
            <div class="col-md-3 mb-2">
                <a href="minhas_leituras.php" class="btn btn-danger btn-block">Atualizar Minhas Leituras</a>
            </div>
        </div>

    </div>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/atualizar_emprestimo.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>

    <?php
    // Inclui o rodapé
    include '../includes/footer.php';
    ?>

</body>

</html>
