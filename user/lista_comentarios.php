<?php
session_start();
include '../config/database.php';

// Verifica se o usuário está logado como admin
if (!isset($_SESSION['admin_username'])) {
    // Se não estiver logado como admin, redireciona para a página de login
    header("Location: ../public/index.php");
    exit;
}

// Consulta todos os comentários
$query_comentarios = "SELECT c.id, l.titulo, u.username, c.comentario
                      FROM comentarios_livros c
                      LEFT JOIN livros l ON c.livro_id = l.id
                      LEFT JOIN usuarios u ON c.user_id = u.id";

$result_comentarios = $conn->query($query_comentarios);

if ($result_comentarios && $result_comentarios->num_rows > 0) {
    $comentarios = $result_comentarios->fetch_all(MYSQLI_ASSOC);
} else {
    $comentarios = [];
}
?>



<!DOCTYPE html>

<html lang="pt-BR">



<head>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">

    <link rel="stylesheet" href="../assets/css/rodape.css">

    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

    <title>Lista de Comentários</title>

</head>



<body>

    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">

        <h2 class="h2-responsive">Lista de Comentários</h2>

        <div class="table-responsive"> <!-- Adicione a classe 'table-responsive' para tornar a tabela responsiva -->

            <table class="table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Livro</th>

                        <th>Usuário</th>

                        <th>Comentário</th>

                    </tr>

                </thead>

                <tbody>

                    <?php

                    // Consulta todos os comentários
                    $query_comentarios = "SELECT c.id, l.titulo, u.username, c.comentario
                      FROM comentarios c
                      LEFT JOIN livros l ON c.livro_id = l.id
                      LEFT JOIN usuarios u ON c.user_id = u.id";

                    $result_comentarios = $conn->query($query_comentarios);

                    if ($result_comentarios && $result_comentarios->num_rows > 0) {
                        while ($row_comentario = $result_comentarios->fetch_assoc()) {

                            ?>

                            <tr>

                                <td>
                                    <?php echo $row_comentario['id']; ?>
                                </td>

                                <td>
                                    <?php echo $row_comentario['titulo']; ?>
                                </td>

                                <td>
                                    <?php echo $row_comentario['username']; ?>
                                </td>

                                <td>
                                    <?php echo $row_comentario['comentario']; ?>
                                </td>

                            </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>

                            <td colspan="4">Nenhum comentário encontrado.</td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

        <!-- Adicione a mensagem de aviso aqui -->

        <div class="alert alert-warning text-center mb-0 d-md-none" role="alert">

            Por favor, role a página horizontalmente para visualizar a tabela completa.

        </div>

        <div class="row mb-3 mt-4">
            <div class="col-md-12 mb-2">
                <a href="aluno.php" class="btn btn-warning btn-block">Voltar para Painel de Usuário</a>
            </div>
        </div>

    </div>

    <script src="../assets/js/script.js"></script>

    <script src="../assets/js/bootstrap.min.js"></script>

    <?php

    // Inclui o rodapé
    
    include '../includes/footer.php';

    ?>