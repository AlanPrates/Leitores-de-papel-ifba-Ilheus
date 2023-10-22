<?php
session_start();
global $conn;
include 'conexao.php';

// Verifica se o usuário não está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login_admin.php");
    exit;
}


$user_id = $_SESSION['user_id'];

// Obtém os livros emprestados pelo usuário atual
$query = "SELECT livros.id, livros.titulo, livros.autor, livros.ano_publicacao, livros_emprestados.data_emprestimo, livros_emprestados.data_devolucao
          FROM livros_emprestados
          INNER JOIN livros ON livros_emprestados.livro_id = livros.id
          WHERE livros_emprestados.user_id = '$user_id'";

$result = $conn->query($query);

// Verifica se o usuário possui livros emprestados
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
                            <th>Data de Empréstimo</th>
                            <th>Data da Devolução</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($livros as $livro) { ?>
                            <tr>
                                <td><?php echo $livro['titulo']; ?></td>
                                <td><?php echo $livro['autor']; ?></td>
                                <td><?php echo $livro['ano_publicacao']; ?></td>
                                <td><?php echo $livro['data_emprestimo']; ?></td>
                                <td><?php echo $livro['data_devolucao']; ?></td>
                                <td>
                                    <form method="POST" action="devolve_livro_action_admin.php">
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
            <p>Você não possui livros emprestados no momento.</p>
        <?php } ?>
        <br>

        <a href="lista_livros_admin.php" class="btn btn-warning">Voltar para Lista de Livros</a>
        <br>
        <br>
        <a href="empresta_livro_admin.php" class="btn btn-warning">Emprestar Livro</a>
        <br>
        <br>
        <a href="logout.php" class="btn btn-danger">Sair</a>
    </div>
    <script src="assets/js/script.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <?php
    // Inclui o rodapé
    include 'rodape.php';
    ?>
</body>

</html>