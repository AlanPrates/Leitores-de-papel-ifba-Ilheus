<?php
session_start();

global $conn;

include 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT livros.id, livros.titulo, livros.autor, livros.ano_publicacao, livros.isbn, livros.genero, livros_emprestados.data_emprestimo, livros_emprestados.data_devolucao
          FROM livros_emprestados
          INNER JOIN livros ON livros_emprestados.livro_id = livros.id
          WHERE livros_emprestados.user_id = '$user_id'";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $livros = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $livros = [];
}
?>


<!DOCTYPE html>

<html>



<head>

    <title>Devolver Livro</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">

    <link rel="stylesheet" href="css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/rodape.css">

    <link rel="stylesheet" href="assets/menu-mobile-css/style.css">

    <style>
        .container {

            margin-top: 20px;

        }
    </style>





</head>



<body>

    <header>

        <nav class="nav-bar">

            <div class="logo">

                <a href="aluno.php">
                    <img class="cabecalho-imagem" src="assets/img/Fotoram.io.png"
                        title="Sempre se atualizando constantemente" alt="LOGO ALAN" />
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

                <li class="nav-item"><a href="minhas_leituras.php" class="nav-link">Acessar minhas leituras</a></li>

            </ul>

        </div>

    </header>

    <div class="container">

        <h2>Devolver Livro</h2>

        <?php if (!empty($livros)) { ?>

            <div class="table-responsive">

                <table class="table">

                    <thead>

                        <tr>

                            <th>Título</th>

                            <th>Autor</th>

                            <th>Ano de Publicação</th>

                            <th>ISBN</th>

                            <th>Genero</th>

                            <th>Data de Empréstimo</th>

                            <th>Data da Devolução</th>

                            <th>Ação</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($livros as $livro) { ?>

                            <tr>

                                <td>
                                    <?php echo $livro['titulo']; ?>
                                </td>

                                <td>
                                    <?php echo $livro['autor']; ?>
                                </td>

                                <td>
                                    <?php echo $livro['ano_publicacao']; ?>
                                </td>

                                <td>
                                    <?php echo $livro['isbn']; ?>
                                </td>

                                <td>
                                    <?php echo $livro['genero']; ?>
                                </td>

                                <td>
                                    <?php echo date('d/m/Y', strtotime($livro['data_emprestimo'])); ?>
                                </td>



                                <td>
                                    <?php echo date('d/m/Y', strtotime($livro['data_emprestimo'] . '+1 days')); ?>
                                </td>



                                <td>

                                    <form method="POST" action="devolve_livro_action.php">

                                        <input type="hidden" name="livro_id" value="<?php echo $livro['id']; ?>">

                                        <button type="submit" class="btn btn-danger">Devolver</button>

                                    </form>

                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        <?php } else { ?>

            <p>Nenhum livro emprestado.</p>

        <?php } ?>

        <br>

        <div class="alert alert-warning text-center mb-0 d-md-none" role="alert">

            Por favor, role a página horizontalmente para visualizar a tabela completa.

        </div>

        <br>

        <style>
            /* Estilo para a caixa de comentário */

            .comment-box:nth-child(odd) {

                background-color: #f1f1f1;

            }



            .comment-box:nth-child(even) {

                background-color: #eaeaea;

            }
        </style>



        <?php foreach ($livros as $livro) { ?>
            <div class="container mt-4">
                <h3>Comentários sobre o livro "
                    <?php echo $livro['titulo']; ?>":
                </h3>
                <!-- Formulário para adicionar comentário -->
                <form action="salvar_comentario_action.php" method="post">
                    <input type="hidden" name="livro_id" value="<?php echo $livro['id']; ?>">
                    <div class="form-group">
                        <textarea name="comentario" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="avaliacao">Avaliação:</label>
                        <select name="avaliacao" class="form-control" required>
                            <option value="1">1 estrela</option>
                            <option value="2">2 estrelas</option>
                            <option value="3">3 estrelas</option>
                            <option value="4">4 estrelas</option>
                            <option value="5">5 estrelas</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Adicionar Comentário</button>
                </form>


                <h3>Comentários sobre o livro "
                    <?php echo $livro['titulo']; ?>":
                </h3>
                <?php
                $livro_id = $livro['id'];

                $query_comentarios = "SELECT c.comentario, c.avaliacao, u.username FROM comentarios c, usuarios u WHERE c.livro_id = '$livro_id' AND c.user_id = u.id";

                $result_comentarios = $conn->query($query_comentarios);

                if ($result_comentarios && $result_comentarios->num_rows > 0) {
                    while ($row_comentario = $result_comentarios->fetch_assoc()) {
                        echo '<div class="card comment-box">';
                        echo '<div class="card-body">';
                        echo '<strong>' . $row_comentario['username'] . ' comentou:</strong> ' . $row_comentario['comentario'];
                        echo '<br>Avaliação: ' . $row_comentario['avaliacao'] . ' estrela(s)';

                        // Adiciona as estrelas correspondentes à avaliação
                        $avaliacao = $row_comentario['avaliacao'];
                        echo '<br>Estrelas: ';
                        for ($i = 1; $i <= 5; $i++) {
                            echo ($i <= $avaliacao) ? '★' : '☆';
                        }

                        echo '</div></div>';
                    }

                    // Cálculo de avaliação
                    $query_avaliacao = "SELECT AVG(avaliacao) AS media_avaliacao, SUM(avaliacao) AS total_estrelas FROM comentarios WHERE livro_id = '$livro_id'";
                    $result_avaliacao = $conn->query($query_avaliacao);

                    if ($result_avaliacao && $row_avaliacao = $result_avaliacao->fetch_assoc()) {
                        $media_avaliacao = $row_avaliacao['media_avaliacao'];
                        $total_estrelas = $row_avaliacao['total_estrelas'];

                        echo "<p>Média de Avaliação: " . number_format($media_avaliacao, 1) . " estrela(s)</p>";
                        echo "<p>Total de Estrelas: " . $total_estrelas . "</p>";
                    } else {
                        echo '<p>Sem avaliações</p>';
                    }
                } else {
                    echo '<p>Nenhum comentário para este livro.</p>';
                }
                ?>
            </div>

        <?php } ?>

        <br>

        <br>

        <div class="btn-group-vertical">

            <a href="minhas_leituras.php" class="btn btn-warning">Voltar para Painel de Usuário</a>

            <br>

            <a href="lista_livros.php" class="btn btn-warning">Voltar para Lista de Livros</a>

            <br>

            <a href="logout.php" class="btn btn-danger">Sair</a>

        </div>

    </div>

    <script src="assets/js/script.js"></script>

    <script src="js/bootstrap.min.js"></script>

    <?php

    // Inclui o rodapé
    
    include 'rodape.php';

    ?>

</body>



</html>
