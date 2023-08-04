<?php
session_start();
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>EXEMPLOS PRÁTICOS SOBRE PHP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/menu-mobile-css/style.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">

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
                    <li class="nav-item"><a href="aluno.php" class="nav-link">Acessar minhas leituras</a></li>
                    <li class="nav-item"><a href="index_admin.php" class="nav-link">Acessar como Administrador</a></li>
                </ul>
            </div>

            <div class="mobile-menu-icon">
                <button onclick="menuShow()"><img class="icon" src="assets/img/menu_white_36dp.svg" alt=""></button>
            </div>
        </nav>
        <div class="mobile-menu">
            <ul>
                <li class="nav-item"><a href="index.php" class="nav-link">Criar conta de leitor</a></li>
                <li class="nav-item"><a href="aluno.php" class="nav-link">Acessar minhas leituras</a></li>
                <li class="nav-item"><a href="index_admin.php" class="nav-link">Acessar como Administrador</a></li>
            </ul>
        </div>
    </header>
    <br>
    <div class="row">
        <div class="col-sm-12">
            <?php
            // Verificar se o usuário está logado
            if (isset($_SESSION['username'])) {
                // Verificar se o cookie de nome existe
                if (isset($_COOKIE['nome'])) {
                    $nomeUsuario = $_COOKIE['nome'];
                    // Obtém a hora atual
                    $hora = date('H');

                    // Define a saudação com base na hora
                    if ($hora >= 6 && $hora < 12) {
                        $saudacao = "Bom dia";
                    } elseif ($hora >= 12 && $hora < 18) {
                        $saudacao = "Boa tarde";
                    } else {
                        $saudacao = "Boa noite";
                    }

                    // Exibe a saudação juntamente com o nome do usuário
                    echo "<span class='text-danger'><h1>Olá, $nomeUsuario! $saudacao.</h1></span>";
                } else {
                    echo "<span class='text-danger'>Bem-vindo à página inicial.</span>";
                }
            } else {
                header('Location: index_admin.php'); // Redirecionar para a página de login se o usuário não estiver logado
            }
            ?>
            <br>
            <a href="lista_livros.php" class="btn btn-warning">Listar Livros</a>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <a href="atualiza_dados.php" class="btn btn-warning">Atualizar Meus Dados</a>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-12">
        </div>
        <a href="logout.php">
            <input type="button" class="btn btn-danger" value="   Sair  " />
        </a>
    </div>
    <script src="assets/js/script.js"></script>
    <?php
    // Inclui o rodapé
    include 'rodape.php';
    ?>
</body>

</html>