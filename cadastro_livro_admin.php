<?php
// Inicializar a sessão
session_start();

// Verificar se o usuário está logado, redirecionar para o login se não estiver
if (!isset($_SESSION['admin_username'])) {
    header("Location: login_admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Cadastrar Livro - Admin</title>
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
        <br>
        <br>
        <h2>Cadastrar Livro - Admin</h2>
        <br>
        <form method="POST" action="salvar_livro.php">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
            </div>
            <div class="form-group">
                <label for="autor">Autor:</label>
                <input type="text" class="form-control" id="autor" name="autor" required>
            </div>
            <div class="form-group">
                <label for="ano">Ano de Publicação:</label>
                <input type="number" class="form-control" id="ano" name="ano" required>
            </div>
            <div class="form-group">
                <label for="editora">Editora:</label>
                <input type="text" class="form-control" id="editora" name="editora" required>
            </div>
            <div class="form-group">
                <label for="quantidade">Quantidade:</label>
                <input type="number" class="form-control" id="quantidade" name="quantidade" required>
            </div>

            <button type="submit" class="btn btn-danger">Cadastrar</button>
            <br>
            <br>
            <a href="lista_livros_admin.php" class="btn btn-warning">Voltar para Lista de Livros</a>
        </form>
        <br>
        <a href="admin.php" class="btn btn-warning">Voltar para Painel de Usuário</a>
    </div>

    <script src="assets/js/script.js"></script>
    <?php
    // Inclui o rodapé
    include 'rodape.php';
    ?>
</body>

</html>