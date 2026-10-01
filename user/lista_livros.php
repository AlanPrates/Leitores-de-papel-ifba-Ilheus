<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

require_login('../public/index.php');

// Consulta SQL para obter o total de quantidade de livros disponíveis
$query_total_disponivel = "SELECT SUM(quantidade) AS total_disponivel FROM livros";
$result_total_disponivel = $conn->query($query_total_disponivel);
$total_disponivel = 0;
if ($result_total_disponivel && $row_total = $result_total_disponivel->fetch_assoc()) {
    $total_disponivel = (int)$row_total['total_disponivel'];
}

// Definição das variáveis de filtro com sanitização
$filtroTitulo = isset($_GET['titulo']) ? trim($_GET['titulo']) : '';
$filtroAutor = isset($_GET['autor']) ? trim($_GET['autor']) : '';
$filtroAno = isset($_GET['ano']) ? trim($_GET['ano']) : '';
$filtroDisponibilidade = isset($_GET['disponibilidade']) ? trim($_GET['disponibilidade']) : '';
$filtroOrdem = isset($_GET['ordem']) ? trim($_GET['ordem']) : '';

// Construção segura da consulta SQL com os filtros
$query = "SELECT * FROM livros WHERE 1=1";

if (!empty($filtroTitulo)) {
    $titulo_safe = $conn->real_escape_string($filtroTitulo);
    $query .= " AND titulo LIKE '%$titulo_safe%'";
}

if (!empty($filtroAutor)) {
    $autor_safe = $conn->real_escape_string($filtroAutor);
    $query .= " AND autor LIKE '%$autor_safe%'";
}

if (!empty($filtroAno) && is_numeric($filtroAno)) {
    $ano_safe = (int)$filtroAno;
    $query .= " AND ano_publicacao = $ano_safe";
}

if ($filtroDisponibilidade === 'disponivel') {
    $query .= " AND disponivel = 1";
} elseif ($filtroDisponibilidade === 'indisponivel') {
    $query .= " AND disponivel = 0";
}

// Adiciona a ordenação ao final da consulta
if ($filtroOrdem === 'az') {
    $query .= " ORDER BY titulo ASC";
} elseif ($filtroOrdem === 'za') {
    $query .= " ORDER BY titulo DESC";
}

$result = $conn->query($query);

// Verifica se existem livros cadastrados
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

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/menu-mobile.css">

    <link rel="stylesheet" href="../assets/css/rodape.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

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

    <?php include '../includes/header.php'; ?>

    <div class="container">

        <h2>Lista de Livros</h2>

        <div class="alert alert-warning" role="alert">

            Total de Livros Disponíveis:
            <?php echo $total_disponivel; ?>

        </div>



        <form method="get" action="lista_livros.php">

            <div class="form-row">

                <div class="col-md-4 mb-3">
                    <label for="titulo">Título:</label>
                    <input type="text" class="form-control" id="titulo" name="titulo"
                        placeholder="Digite o título do livro" value="<?php echo htmlspecialchars($filtroTitulo); ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="autor">Autor:</label>
                    <input type="text" class="form-control" id="autor" name="autor" placeholder="Digite o nome do autor"
                        value="<?php echo htmlspecialchars($filtroAutor); ?>">
                </div>

                <div class="col-md-2 mb-3">
                    <label for="ano">Ano:</label>
                    <input type="text" class="form-control" id="ano" name="ano" placeholder="Digite o ano de publicação"
                        value="<?php echo htmlspecialchars($filtroAno); ?>">
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
        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <a href="aluno.php" class="btn btn-warning btn-block">Voltar para Painel de Usuário</a>
            </div>
            <div class="col-md-6 mb-2">
                <a href="devolve_livro.php" class="btn btn-warning btn-block">Devolver Livro</a>
            </div>
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
                            $quantidade = $livro['quantidade'];
                            $disponivel = ($quantidade > 0) ? 'Sim' : 'Não';
                            ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($livro['titulo']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($livro['autor']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($livro['ano_publicacao']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($livro['isbn']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($livro['genero']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($quantidade); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($disponivel); ?>
                                </td>
                                <td>
                                    <?php if ($disponivel == 'Sim') { ?>
                                        <form method="POST" action="empresta_livro.php" style="display: inline;">
                                            <input type="hidden" name="livro_id" value="<?php echo htmlspecialchars($livro['id']); ?>">
                                            <input type="submit" class="btn btn-danger" value="Emprestar">
                                        </form>
                                    <?php } else { ?>
                                        Livro indisponível
                                    <?php } ?>
                                    <br>
                                    <br>
                                    <a href="devolve_livro.php" class="btn btn-warning">Devolução</a>
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
        <br>
        <?php if (isset($num_paginas) && $num_paginas > 1): ?>
            <nav aria-label="Navegação de página">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $num_paginas; $i++): ?>
                        <li class="page-item <?php echo ($pagina == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="lista_livros.php?pagina=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>

    </div>

    <script src="../assets/js/script.js"></script>

    <?php

    // Inclui o rodapé
    
    include '../includes/footer.php';

    ?>

</body>



</html>