<?php
// Inicializar a sessão
session_start();

// Verificar se o usuário está logado, redirecionar para o login se não estiver
if (!isset($_SESSION['username'])) {
    header("Location: login_admin.php");
    exit;
}

// Exibir o nome de usuário
$username = $_SESSION['username'];

// Definir a categoria selecionada
if (isset($_GET['categoria'])) {
    $categoria = $_GET['categoria'];
} else {
    $categoria = 'all';
}

// Definir o termo de pesquisa
if (isset($_GET['pesquisa'])) {
    $pesquisa = $_GET['pesquisa'];
} else {
    $pesquisa = '';
}

// Definir a ordem de classificação
if (isset($_GET['ordem']) && ($_GET['ordem'] == 'az' || $_GET['ordem'] == 'za')) {
    $ordem = $_GET['ordem'];
} else {
    $ordem = 'az';
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Lista de Usuarios</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/rodape.css">
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
                    <li class="nav-item"><a href="index.php" class="nav-link">Acessar minhas leituras</a></li>
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
                <li class="nav-item"><a href="index.php" class="nav-link">Acessar minhas leituras</a></li>
                <li class="nav-item"><a href="index_admin.php" class="nav-link">Acessar como Administrador</a></li>
            </ul>
        </div>
    </header>
    <br>
    <div class="container">
        <h2 class="text-center">Lista de usuários</h2>
        <br>
        <form method="GET" action="lista_usuarios.php" class="form-inline">
            <div class="form-group">
                <label for="categoria" class="mr-2">Filtrar por categoria:</label>
                <select name="categoria" id="categoria" class="form-control">
                    <option value="all" <?php if ($categoria == 'all') echo 'selected'; ?>>Todos</option>
                    <option value="aluno" <?php if ($categoria == 'aluno') echo 'selected'; ?>>Aluno</option>
                    <option value="professor" <?php if ($categoria == 'professor') echo 'selected'; ?>>Professor</option>
                    <option value="funcionario" <?php if ($categoria == 'funcionario') echo 'selected'; ?>>Funcionário</option>
                </select>
            </div>
            <div class="form-group ml-2">
                <label for="ordem" class="mr-2">Ordenar por:</label>
                <select name="ordem" id="ordem" class="form-control">
                    <option value="az" <?php if ($ordem == 'az') echo 'selected'; ?>>A-Z</option>
                    <option value="za" <?php if ($ordem == 'za') echo 'selected'; ?>>Z-A</option>
                </select>
            </div>
            <div class="form-group ml-2">
                <label for="pesquisa" class="mr-2">Pesquisar Usuário:</label>
                <input type="text" name="pesquisa" id="pesquisa" class="form-control" value="<?php echo $pesquisa; ?>">
                <br>
                <input type="submit" value="Filtrar" class="btn btn-danger">
            </div>
        </form>



        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nome</th>
                        <th>E-Mail</th>
                        <th>Categoria</th>
                        <th>Matrícula</th>
                        <th>Data de Nascimento</th>
                        <th>Sexo</th>
                        <th>Telefone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    include 'conexao.php';

                    // Construir a consulta SQL
                    $sql = "SELECT * FROM usuarios WHERE 1=1";

                    if ($categoria != 'all') {
                        $sql .= " AND categoria = '$categoria'";
                    }

                    if (!empty($pesquisa)) {
                        $sql .= " AND (username LIKE '%$pesquisa%' OR nome LIKE '%$pesquisa%')";
                    }

                    if ($ordem == 'az') {
                        $sql .= " ORDER BY nome ASC";
                    } else {
                        $sql .= " ORDER BY nome DESC";
                    }

                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row["id"] . '</td>';
                            echo '<td>' . $row["username"] . '</td>';
                            echo '<td>' . $row["nome"] . '</td>';
                            echo '<td>' . $row["email"] . '</td>';
                            echo '<td>' . $row["categoria"] . '</td>';
                            echo '<td>' . $row["matricula"] . '</td>';
                            echo '<td>' . $row["datanascimento"] . '</td>';
                            echo '<td>' . $row["sexo"] . '</td>';
                            echo '<td>' . $row["telefone"] . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="9">Nenhum usuário cadastrado.</td></tr>';
                    }


                    ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-warning text-center mb-0 d-md-none" role="alert">
            Por favor, role a página horizontalmente para visualizar a tabela completa.
        </div>

        <br>

        <div class="btn-group-vertical">
            <a href="admin.php" class="btn btn-warning">Voltar para Painel de Usuário</a>

        </div>
        <br>
        <br>
        <div class="btn-group-vertical">
            <a href="logout.php" class="btn btn-danger">Sair </a>
        </div>
        <br>
    </div>

    <script src="assets/js/script.js"></script>
    <?php
    // Inclui o rodapé
    include 'rodape.php';
    ?>
</body>

</html>