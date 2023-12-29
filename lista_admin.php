<?php
session_start();
global $conn;
include 'conexao.php';

// Verificar se o usuário está logado, redirecionar para o login se não estiver
if (!isset($_SESSION['admin_username'])) {
    header("Location: login_admin.php");
    exit;
}

// Exibir o nome de usuário
$username = $_SESSION['admin_username'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Lista de Administradores</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/menu-mobile-css/style.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">

</head>

<body>
    <header>
        <nav class="nav-bar">
            <div class="logo">
                <a href="admin.php">
                    <img class="cabecalho-imagem" src="assets/img/Fotoram.io.png"
                        title="Sempre se atualizando constantemente" alt="LOGO ALAN" />
                </a>
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
    <br>
    <div class="container-fluid">
        <h2 class="text-center">Lista de Administradores</h2>
        <br>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nome</th>
                        <th>E-Mail</th>
                        <th>Data de Nascimento</th>
                        <th>Sexo</th>
                        <th>Telefone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    // Consulta para obter os administradores cadastrados
                    $sql = "SELECT * FROM admin";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row["admin_username"] . '</td>';
                            echo '<td>' . $row["nome"] . '</td>';
                            echo '<td>' . $row["email"] . '</td>';
                            echo '<td>' . $row["datanascimento"] . '</td>';
                            echo '<td>' . $row["sexo"] . '</td>';
                            echo '<td>' . $row["telefone"] . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">Nenhum administrador cadastrado.</td></tr>';
                    }

                    ?>
                </tbody>
            </table>
        </div>
        <br>
        <div class="alert alert-warning text-center mb-0 d-md-none" role="alert">
            Por favor, role a página horizontalmente para visualizar a tabela completa.
        </div>
        <br>
        <div class="btn-group-vertical">
            <a href="admin.php" class="btn btn-warning">Voltar para Painel de Usuário</a>
            <br>
            <a href="logout.php" class="btn btn-danger">Sair</a>

        </div>
    </div>

    <script src="assets/js/script.js"></script>
    <?php
    // Inclui o rodapé
    include 'rodape.php';
    ?>
</body>

</html>
