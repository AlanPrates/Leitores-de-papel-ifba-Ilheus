<?php
session_start();
global $conn;
include 'conexao.php';

// Verifica se o usuário não está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Consulta SQL para obter o total de quantidade de livros disponíveis
$query_total_disponivel = "SELECT SUM(quantidade) AS total_disponivel FROM livros";
$result_total_disponivel = $conn->query($query_total_disponivel);
$row_total_disponivel = $result_total_disponivel->fetch_assoc();
$total_disponivel = $row_total_disponivel['total_disponivel'];

// Definição das variáveis de filtro (mantenha essas linhas, se houver necessidade de filtrar)
$filtroTitulo = isset($_GET['titulo']) ? $_GET['titulo'] : '';
$filtroAutor = isset($_GET['autor']) ? $_GET['autor'] : '';
$filtroAno = isset($_GET['ano']) ? $_GET['ano'] : '';
$filtroDisponibilidade = isset($_GET['disponibilidade']) ? $_GET['disponibilidade'] : '';
$filtroOrdem = isset($_GET['ordem']) ? $_GET['ordem'] : '';
$query = "SELECT *, quantidade - quantidade_emprestada AS quantidade_disponivel FROM livros WHERE 1=1";

// Construção da consulta SQL com os filtros
$query = "SELECT * FROM livros WHERE 1=1";
if (!empty($filtroTitulo)) {
    $query .= " AND titulo LIKE '%$filtroTitulo%'";
}
if (!empty($filtroAutor)) {
    $query .= " AND autor LIKE '%$filtroAutor%'";
}
if (!empty($filtroAno)) {
    $query .= " AND ano_publicacao = $filtroAno";
}
if ($filtroDisponibilidade == 'disponivel') {
    $query .= " AND disponivel = 1";
}
if ($filtroDisponibilidade == 'indisponivel') {
    $query .= " AND disponivel = 0";
}

// Adiciona a ordenação ao final da consulta
if ($filtroOrdem == 'az') {
    $query .= " ORDER BY titulo ASC";
} elseif ($filtroOrdem == 'za') {
    $query .= " ORDER BY titulo DESC";
}

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $livros = $result->fetch_all(MYSQLI_ASSOC);

    // Implementação da paginação
    $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
    $livros_por_pagina = 15;
    $num_livros = count($livros);
    $num_paginas = ceil($num_livros / $livros_por_pagina);
    $offset = ($pagina - 1) * $livros_por_pagina;

    // Filtra os livros de acordo com a página atual
    $livros_paginados = array_slice($livros, $offset, $livros_por_pagina);
} else {
    $livros_paginados = [];
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Lista de Livros</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="assets/menu-mobile-css/style.css">
    <link rel="stylesheet" href="assets/css/rodape.css">
    <style>
        .table-responsive {
            overflow-x: auto;
        }

        @media (max-width: 576px) {
            .btn-group-vertical {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-top: 1rem;
            }
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
        <h2>Lista de Livros</h2>
        <div class="alert alert-warning" role="alert">
            Total de Livros Disponíveis: <?php echo $total_disponivel; ?>
        </div>
        <!-- Adicione após a consulta SQL para obter o total de quantidade de livros disponíveis -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 'true') : ?>
        <div class="alert alert-success" role="alert">
            Livro atualizado com sucesso!
        </div>
    <?php endif; ?>
        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success" role="alert">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']);
        }

        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <form method="get" action="lista_livros_admin.php">
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="titulo">Título:</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Digite o título do livro" value="<?php echo $filtroTitulo; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="autor">Autor:</label>
                    <input type="text" class="form-control" id="autor" name="autor" placeholder="Digite o nome do autor" value="<?php echo $filtroAutor; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="ano">Ano:</label>
                    <input type="text" class="form-control" id="ano" name="ano" placeholder="Digite o ano de publicação" value="<?php echo $filtroAno; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="disponibilidade">Disponibilidade:</label>
                    <select class="form-control" id="disponibilidade" name="disponibilidade">
                        <option value="">Todos</option>
                        <option value="disponivel" <?php echo ($filtroDisponibilidade == 'disponivel') ? 'selected' : ''; ?>>Disponível</option>
                        <option value="indisponivel" <?php echo ($filtroDisponibilidade == 'indisponivel') ? 'selected' : ''; ?>>Indisponível</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="ordem">Ordenar por:</label>
                <select class="form-control" id="ordem" name="ordem">
                    <option value="">Nenhum</option>
                    <option value="az" <?php echo ($filtroOrdem == 'az') ? 'selected' : ''; ?>>A-Z</option>
                    <option value="za" <?php echo ($filtroOrdem == 'za') ? 'selected' : ''; ?>>Z-A</option>
                </select>
            </div>
            <button class="btn btn-warning" type="submit">Filtrar</button>
        </form>
        <br>
        <div class="btn-group-vertical">
            <div class="btn-group-vertical">
            </div>
            <a href="admin.php" class="btn btn-warning">Voltar para Painel de Usuário</a>
            <br>
            <a href="cadastro_livro_admin.php" class="btn btn-warning">Cadastro de Livros</a>
            <br>
            <a href="logout.php" class="btn btn-danger">Sair</a>
        </div>
        <!-- Adiciona a tabela com os livros paginados -->
        <?php if (!empty($livros_paginados)) { ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Ano de Publicação</th>
                            <th>ISBN</th>
                            <th>Genero</th>
                            <th>Quantidade</th>
                            <th>Disponível</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($livros_paginados as $livro) {
                            $quantidade = $livro['quantidade']; // Obtém a quantidade do livro
                            $disponivel = ($quantidade > 0) ? 'Sim' : 'Não'; // Verifica se o livro está disponível


                            // Atualiza a quantidade do livro quando emprestado ou devolvido
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livro_id']) && $_POST['livro_id'] == $livro['id']) {
                                if ($quantidade > 0) {
                                    // Livro emprestado, diminui a quantidade
                                    $quantidade--;
                                    // Adicione aqui o código para atualizar a quantidade no banco de dados
                                }
                            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livro_devolvido']) && $_POST['livro_devolvido'] == $livro['id']) {
                                // Livro devolvido, aumenta a quantidade
                                $quantidade++;
                                // Adicione aqui o código para atualizar a quantidade no banco de dados
                            }
                        ?>
                            <tr>
                                <td><?php echo $livro['titulo']; ?></td>
                                <td><?php echo $livro['autor']; ?></td>
                                <td><?php echo $livro['ano_publicacao']; ?></td>
                                <td><?php echo $livro['isbn']; ?></td>
                                <td><?php echo $livro['genero']; ?></td>
                                <td><?php echo $quantidade; ?></td>
                                <td><?php echo $disponivel; ?></td>
                                <td>
                                    <?php if ($disponivel == 'Sim') { ?>
                                    <?php } else { ?>
                                        Livro indisponível
                                    <?php } ?>

                                    <!-- Botão Editar -->
                                    <form method="GET" action="editar_livro_admin.php" style="display: inline;">
                                        <input type="hidden" name="livro_id" value="<?php echo $livro['id']; ?>">
                                        <input type="submit" class="btn btn-primary" value="Editar Livro ">
                                    </form>
                                    <br>
                                    <br>
                                    <!-- Botão Excluir -->
                                    <form method="POST" action="excluir_livro.php" style="display: inline;">
                                        <input type="hidden" name="livro_id" value="<?php echo $livro['id']; ?>">
                                        <input type="submit" class="btn btn-danger" value="Excluir Livro">
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p>Nenhum livro encontrado com os critérios de busca.</p>
        <?php } ?>
        <!-- Adicione a mensagem de aviso aqui -->
        <div class="alert alert-warning text-center mb-0 d-md-none" role="alert">
            Por favor, role a página horizontalmente para visualizar a tabela completa.
        </div>
        <!-- Após o loop que exibe os comentários do livro -->
        <div class="mt-4">
            <?php if (isset($_SESSION['admin_username'])) { ?>
                <a href="lista_comentarios.php" class="btn btn-primary">Ver os comentários dos livros</a>
            <?php } ?>
        </div>
        <br>
        <?php if ($num_paginas > 1) : ?>
            <nav aria-label="Navegação de página">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $num_paginas; $i++) : ?>
                        <li class="page-item <?php echo ($pagina == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="lista_livros_admin.php?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>


    </div>
    <script src="assets/js/script.js"></script>
    <?php
    // Inclui o rodapé
    include 'rodape.php';
    ?>
</body>

</html>