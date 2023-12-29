<?php
session_start();
global $conn;
include 'conexao.php';

// Verifica se o usuário não está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login_admin.php");
    exit;
}

// Consulta para obter todos os registros do histórico de empréstimos
$sql_historico = "SELECT * FROM historico_emprestimos";
$result_historico = $conn->query($sql_historico);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Informações dos Usuários</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="assets/menu-mobile-css/style.css">
    <link rel="stylesheet" href="assets/css/rodape.css">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            white-space: nowrap;
            /* Impede a quebra de linha */
            overflow-x: auto;
            /* Habilita a rolagem horizontal */
        }

        th,
        td {
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
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
                <li class="nav-item"><a href="index.php" class="nav-link">Acessar Minhas Leituras</a></li>
            </ul>
        </div>
    </header>
    <br>
    <h2>Históricos</h2>
    <br>
    <?php
    if ($result_historico !== false && $result_historico->num_rows > 0) {
        echo '<div class="table-responsive">
            <table>
                <tr>
                    <th>Nome do Usuário</th>
                    <th>Título do Livro</th>
                    <th>Autor do Livro</th>
                    <th>Data de Empréstimo</th>
                    <th>Data de Devolução</th>
                    <th>ID do Usuário</th>
                </tr>';

        while ($row_historico = $result_historico->fetch_assoc()) {
            $user_id = $row_historico["user_id"];
            $livro_id = $row_historico["livro_id"];
            $data_emprestimo = $row_historico["data_emprestimo"];
            $data_devolucao = $row_historico["data_devolucao"];

            // Consulta para obter o nome do usuário
            $sql_user = "SELECT nome FROM usuarios WHERE id = '$user_id'";
            $result_user = $conn->query($sql_user);

            if ($result_user !== false && $result_user->num_rows > 0) {
                $nome_usuario = $result_user->fetch_assoc()["nome"];

                // Consulta para obter o título e autor do livro
                $sql_livro = "SELECT titulo, autor FROM livros WHERE id = '$livro_id'";
                $result_livro = $conn->query($sql_livro);

                if ($result_livro !== false && $result_livro->num_rows > 0) {
                    $livro = $result_livro->fetch_assoc();
                    $titulo_livro = $livro["titulo"];
                    $autor_livro = $livro["autor"];

                    echo '<tr>
                        <td>' . $nome_usuario . '</td>
                        <td>' . $titulo_livro . '</td>
                        <td>' . $autor_livro . '</td>
                        <td>' . $data_emprestimo . '</td>
                        <td>' . $data_devolucao . '</td>
                        <td>' . $user_id . '</td>
                    </tr>';
                }
            }
        }

        echo '</table>
        </div>';
    } else {
        echo 'Nenhum registro encontrado no histórico de empréstimos.';
    }
    ?>
    <br>
    <div class="alert alert-warning text-center mb-0 d-md-none" role="alert">
        Por favor, role a página horizontalmente para visualizar a tabela completa.
    </div>
    <div class="btn-group-vertical">
        <a href="admin.php" class="btn btn-warning">Voltar para Painel de Usuário</a>
        <br>
        <a href="logout.php" class="btn btn-danger">Sair</a>
    </div>
    <br>
    <script src="js/atualizar_emprestimo.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="assets/menu-mobile-js/script.js"></script>
    <?php
    // Inclui o rodapé
    include 'rodape.php';
    ?>
</body>

</html>
