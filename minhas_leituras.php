<?php
session_start();
global $conn;
include 'conexao.php';

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
    header('Location: index.php'); // Redirecionar para a página de login se o usuário não estiver logado
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Histórico de Empréstimos</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body class="container mt-5">

    <h1 class="mb-4">Minhas Leituras</h1>

    <?php
    if (isset($error_message)) {
        echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
    } elseif ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
    ?>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $row['livro_titulo']; ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo $row['livro_autor']; ?></h6>
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
        echo "Nenhum registro de empréstimo encontrado.";
    }
    ?>
    <div class="row">
            <div class="col-md-3">
                <a href="aluno.php" class="btn btn-warning">Voltar para Página Principal</a>
            </div>
            <div class="col-md-3">
                <a href="minhas_leituras.php" class="btn btn-danger">Atualizar Minhas Leituras</a>
            </div>
    </div>
<script src="js/atualizar_emprestimo.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>

</html>
