<!DOCTYPE html>
<html>

<head>
    <title>Informações dos Usuários</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="assets/menu-mobile-css/style.css">
    <link rel="stylesheet" href="assets/css/rodape.css">
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
    <br>
    <br>
    <div class="container">
        <h2>Relatório de Empréstimos de Livros</h2>
        <form method="GET">
            <div class="form-group">
                <label for="matricula" class="form-label">Número de Matrícula</label>
                <input type="text" class="form-control" id="matricula" name="matricula">
            </div>
            <div class="form-group">
                <label for="data_inicio" class="form-label">Data de Início</label>
                <input type="date" name="data_inicio" class="form-control">
            </div>
            <div class="form-group">
                <label for="data_fim" class="form-label">Data de Fim</label>
                <input type="date" name="data_fim" class="form-control">
            </div>
            <button type="submit" class="btn btn-warning" name="pesquisar">Pesquisar</button>
            <button type="submit" class="btn btn-warning" name="listar_todos">Listar Todos</button>
            <br>
            <br>
            <a href="admin.php" class="btn btn-warning">Voltar para Painel de Usuário</a>
        </form>
        <br>
        <!-- Adicione a mensagem de aviso aqui -->
        <div class="alert alert-warning text-center mb-0 d-md-none" role="alert">
            Por favor, role a página horizontalmente para visualizar a tabela completa.
        </div>
        <br>
        <?php
        session_start();
        global $conn;
        include 'conexao.php';

        // Verificar se a ação de "Listar Todos" foi acionada
        if (isset($_GET['listar_todos'])) {
            // Consulta para obter todas as informações de empréstimos de livros sem filtro de matrícula
            $sql_relatorio = "SELECT u.nome, l.titulo, l.autor, le.data_emprestimo, le.data_devolucao
            FROM livros_emprestados le
            INNER JOIN usuarios u ON u.id = le.user_id
            INNER JOIN livros l ON l.id = le.livro_id";

            // Adicionar o filtro por data, se as datas de início e fim forem fornecidas
            if (!empty($_GET["data_inicio"]) && !empty($_GET["data_fim"])) {
                $data_inicio = $_GET["data_inicio"];
                $data_fim = $_GET["data_fim"];
                $sql_relatorio .= " WHERE le.data_emprestimo >= '$data_inicio' AND le.data_emprestimo <= '$data_fim'";
            }

            $result_relatorio = $conn->query($sql_relatorio);

            // Verificar se há informações de empréstimos
            if ($result_relatorio !== false && $result_relatorio->num_rows > 0) {
                echo '<div class="table-responsive">';
                echo '<table class="table">';
                echo '<thead><tr><th>Nome do Usuário</th><th>Título do Livro</th><th>Autor do Livro</th><th>Data de Empréstimo</th><th>Data de Devolução</th></tr></thead>';
                echo '<tbody>';

                while ($row = $result_relatorio->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row["nome"] . '</td>';
                    echo '<td>' . $row["titulo"] . '</td>';
                    echo '<td>' . $row["autor"] . '</td>';
                    echo '<td>' . $row["data_emprestimo"] . '</td>';
                    echo '<td>' . $row["data_devolucao"] . '</td>';
                    echo '</tr>';
                }

                echo '</tbody></table>';
                echo '</div>';
            } else {
                echo 'Nenhum empréstimo encontrado.';
            }
        } else if (isset($_GET["matricula"]) && isset($_GET["pesquisar"])) {
            $matricula = $_GET["matricula"];

            // Verificar se o número de matrícula está vazio
            if (!empty($matricula)) {
                // Consulta para obter todas as informações de empréstimos de livros para a matrícula especificada
                $sql_relatorio = "SELECT u.nome, l.titulo, l.autor, le.data_emprestimo, le.data_devolucao
                FROM livros_emprestados le
                INNER JOIN usuarios u ON u.id = le.user_id
                INNER JOIN livros l ON l.id = le.livro_id
                WHERE u.matricula = '$matricula'";

                // Adicionar o filtro por data, se as datas de início e fim forem fornecidas
                if (!empty($_GET["data_inicio"]) && !empty($_GET["data_fim"])) {
                    $data_inicio = $_GET["data_inicio"];
                    $data_fim = $_GET["data_fim"];
                    $sql_relatorio .= " AND le.data_emprestimo >= '$data_inicio' AND le.data_emprestimo <= '$data_fim'";
                }

                $result_relatorio = $conn->query($sql_relatorio);

                // Verificar se há informações de empréstimos
                if ($result_relatorio !== false && $result_relatorio->num_rows > 0) {
                    echo '<div class="table-responsive">';
                    echo '<table class="table">';
                    echo '<thead><tr><th>Nome do Usuário</th><th>Título do Livro</th><th>Autor do Livro</th><th>Data de Empréstimo</th><th>Data de Devolução</th></tr></thead>';
                    echo '<tbody>';

                    while ($row = $result_relatorio->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row["nome"] . '</td>';
                        echo '<td>' . $row["titulo"] . '</td>';
                        echo '<td>' . $row["autor"] . '</td>';
                        echo '<td>' . $row["data_emprestimo"] . '</td>';
                        echo '<td>' . $row["data_devolucao"] . '</td>';
                        echo '</tr>';
                    }

                    echo '</tbody></table>';
                    echo '</div>';
                } else {
                    echo 'Nenhum empréstimo encontrado para a matrícula especificada.';
                }
            } else {
                echo 'Por favor, forneça uma matrícula válida.';
            }
        }
        ?>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="assets/menu-mobile-js/script.js"></script>
    <?php
    // Inclui o rodapé
    include 'rodape.php';
    ?>
</body>

</html>
