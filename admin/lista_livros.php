<?php
session_start();
include_once '../includes/auth.php';
require_admin();

global $conn;
include '../config/database.php';

// Consulta SQL para obter o total de quantidade de livros disponíveis
$query_total_disponivel = "SELECT SUM(quantidade) AS total_disponivel FROM livros";
$result_total_disponivel = $conn->query($query_total_disponivel);
$row_total_disponivel = $result_total_disponivel ? $result_total_disponivel->fetch_assoc() : null;
$total_disponivel = $row_total_disponivel ? $row_total_disponivel['total_disponivel'] : 0;

// Definição das variáveis de filtro com escape
$filtroTitulo = isset($_GET['titulo']) ? trim($_GET['titulo']) : '';
$filtroAutor = isset($_GET['autor']) ? trim($_GET['autor']) : '';
$filtroAno = isset($_GET['ano']) ? trim($_GET['ano']) : '';
$filtroDisponibilidade = isset($_GET['disponibilidade']) ? $_GET['disponibilidade'] : '';
$filtroOrdem = isset($_GET['ordem']) ? $_GET['ordem'] : '';

// Construção da consulta SQL com filtros sanitizados
$query = "SELECT * FROM livros WHERE 1=1";
if (!empty($filtroTitulo)) {
    $eTitulo = $conn->real_escape_string($filtroTitulo);
    $query .= " AND titulo LIKE '%$eTitulo%'";
}
if (!empty($filtroAutor)) {
    $eAutor = $conn->real_escape_string($filtroAutor);
    $query .= " AND autor LIKE '%$eAutor%'";
}
if (!empty($filtroAno)) {
    $eAno = (int)$filtroAno;
    $query .= " AND ano_publicacao = $eAno";
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
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Lista de Livros - Administração</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <link rel="stylesheet" href="../assets/css/rodape.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

    <style>
        .book-list-wrapper {
            width: 96% !important;
            max-width: 1440px !important;
            margin: 30px auto !important;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px 35px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #eef2f6;
        }

        .book-list-wrapper h2 {
            font-weight: 700;
            color: #1e293b;
            letter-spacing: -0.5px;
            margin-bottom: 0;
        }

        .stat-badge {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 10px 18px;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
        }

        .filter-panel {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
        }

        .filter-panel label {
            font-weight: 600;
            font-size: 13px;
            color: #475569;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-responsive {
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
        }

        .table-books {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .table-books thead th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            vertical-align: middle;
            text-align: center;
            padding: 14px 16px;
            border-bottom: 2px solid #cbd5e1;
            border-top: none;
        }

        .table-books tbody td {
            vertical-align: middle;
            text-align: center;
            padding: 13px 16px;
            font-size: 14px;
            color: #334155;
            border-top: 1px solid #f1f5f9;
        }

        .table-books tbody tr:hover {
            background-color: #f8fafc;
        }

        .table-books td.col-titulo {
            text-align: left;
            font-weight: 600;
            color: #0f172a;
            min-width: 200px;
        }

        .table-books td.col-autor {
            text-align: left;
            min-width: 160px;
            color: #475569;
        }

        .table-books td.nowrap-cell {
            white-space: nowrap;
        }

        .badge-status-sim {
            background-color: #dcfce7;
            color: #166534;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
        }

        .badge-status-nao {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
        }

        .badge-genre {
            background-color: #e0e7ff;
            color: #3730a3;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
        }

        .actions-cell {
            white-space: nowrap;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
        }

        @media (max-width: 768px) {
            .book-list-wrapper {
                width: 100% !important;
                margin: 15px auto !important;
                padding: 20px 15px;
                border-radius: 0;
                border: none;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="book-list-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div>
                <h2><i class="fa fa-book text-danger mr-2"></i>Lista de Livros</h2>
                <small class="text-muted">Gerencie o acervo da biblioteca do IFBA</small>
            </div>
            <div class="stat-badge mt-2 mt-sm-0">
                <i class="fa fa-book-open mr-2 text-warning"></i>
                Total de Livros Disponíveis:&nbsp;<strong><?php echo $total_disponivel; ?></strong>
            </div>
        </div>

        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
            unset($_SESSION['success_message']);
        }

        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <div class="filter-panel">
            <form method="get" action="lista_livros.php">
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="titulo">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo"
                            placeholder="Digite o título do livro" value="<?php echo htmlspecialchars($filtroTitulo, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="autor">Autor</label>
                        <input type="text" class="form-control" id="autor" name="autor" placeholder="Digite o nome do autor"
                            value="<?php echo htmlspecialchars($filtroAutor, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="ano">Ano</label>
                        <input type="text" class="form-control" id="ano" name="ano" placeholder="Ex: 2024"
                            value="<?php echo htmlspecialchars($filtroAno, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="disponibilidade">Disponibilidade</label>
                        <select class="form-control" id="disponibilidade" name="disponibilidade">
                            <option value="">Todas</option>
                            <option value="disponivel" <?php echo ($filtroDisponibilidade == 'disponivel') ? 'selected' : ''; ?>>Disponível</option>
                            <option value="indisponivel" <?php echo ($filtroDisponibilidade == 'indisponivel') ? 'selected' : ''; ?>>Indisponível</option>
                        </select>
                    </div>
                </div>

                <div class="form-row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label for="ordem">Ordenar por</label>
                        <select class="form-control" id="ordem" name="ordem">
                            <option value="">Padrão</option>
                            <option value="az" <?php echo ($filtroOrdem == 'az') ? 'selected' : ''; ?>>Título (A-Z)</option>
                            <option value="za" <?php echo ($filtroOrdem == 'za') ? 'selected' : ''; ?>>Título (Z-A)</option>
                        </select>
                    </div>
                    <div class="col-md-8 mb-3 text-right">
                        <button class="btn btn-warning px-4" type="submit">
                            <i class="fa fa-filter mr-1"></i> Filtrar
                        </button>
                        <a href="lista_livros.php" class="btn btn-outline-secondary ml-2">
                            <i class="fa fa-times mr-1"></i> Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <a href="index.php" class="btn btn-warning btn-block">
                    <i class="fa fa-arrow-left mr-1"></i> Voltar para Painel Admin
                </a>
            </div>
            <div class="col-md-6 mb-2">
                <a href="cadastro_livro.php" class="btn btn-danger btn-block">
                    <i class="fa fa-plus-circle mr-1"></i> Cadastrar Novo Livro
                </a>
            </div>
        </div>

        <?php if (!empty($livros_paginados)) { ?>
            <div class="table-responsive">
                <table class="table-books table-striped">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Ano</th>
                            <th>ISBN</th>
                            <th>Gênero</th>
                            <th>Qtd</th>
                            <th>Disponível</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($livros_paginados as $livro) {
                            $quantidade = $livro['quantidade'];
                            $disponivel = ($quantidade > 0) ? 'Sim' : 'Não';
                            ?>
                            <tr>
                                <td class="col-titulo">
                                    <?php echo htmlspecialchars($livro['titulo']); ?>
                                </td>
                                <td class="col-autor">
                                    <?php echo htmlspecialchars($livro['autor']); ?>
                                </td>
                                <td class="nowrap-cell">
                                    <?php echo htmlspecialchars($livro['ano_publicacao']); ?>
                                </td>
                                <td class="nowrap-cell">
                                    <code><?php echo htmlspecialchars($livro['isbn']); ?></code>
                                </td>
                                <td class="nowrap-cell">
                                    <span class="badge-genre"><?php echo htmlspecialchars($livro['genero']); ?></span>
                                </td>
                                <td class="nowrap-cell font-weight-bold">
                                    <?php echo (int)$quantidade; ?>
                                </td>
                                <td class="nowrap-cell">
                                    <?php if ($disponivel === 'Sim') { ?>
                                        <span class="badge-status-sim"><i class="fa fa-check mr-1"></i>Sim</span>
                                    <?php } else { ?>
                                        <span class="badge-status-nao"><i class="fa fa-times mr-1"></i>Não</span>
                                    <?php } ?>
                                </td>
                                <td class="nowrap-cell">
                                    <div class="actions-cell">
                                        <a href="editar_livro.php?livro_id=<?php echo urlencode($livro['id']); ?>" class="btn btn-sm btn-outline-primary" title="Editar Livro">
                                            <i class="fa fa-edit mr-1"></i>Editar
                                        </a>
                                        <form method="POST" action="excluir_livro.php" style="display: inline-block; margin: 0;" onsubmit="return confirm('Tem certeza de que deseja excluir este livro?');">
                                            <input type="hidden" name="livro_id" value="<?php echo htmlspecialchars($livro['id']); ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir Livro">
                                                <i class="fa fa-trash mr-1"></i>Excluir
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-info text-center py-4 my-3" role="alert">
                <i class="fa fa-info-circle mr-1"></i> Nenhum livro encontrado com os critérios de busca.
            </div>
        <?php } ?>

        <div class="alert alert-warning text-center mt-3 mb-0 d-md-none" role="alert">
            <i class="fa fa-arrows-alt-h mr-1"></i> Role a página horizontalmente para visualizar a tabela completa.
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap">
            <div>
                <?php if (isset($_SESSION['admin_username'])) { ?>
                    <a href="lista_comentarios.php" class="btn btn-outline-secondary">
                        <i class="fa fa-comments mr-1"></i> Ver Comentários dos Livros
                    </a>
                <?php } ?>
            </div>

            <?php if ($num_paginas > 1): ?>
                <nav aria-label="Navegação de página" class="mt-2 mt-sm-0">
                    <ul class="pagination mb-0">
                        <?php for ($i = 1; $i <= $num_paginas; $i++): ?>
                            <li class="page-item <?php echo ($pagina == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="lista_livros.php?pagina=<?php echo $i; ?>&titulo=<?php echo urlencode($filtroTitulo); ?>&autor=<?php echo urlencode($filtroAutor); ?>&ano=<?php echo urlencode($filtroAno); ?>&disponibilidade=<?php echo urlencode($filtroDisponibilidade); ?>&ordem=<?php echo urlencode($filtroOrdem); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <?php include '../includes/footer.php'; ?>
</body>

</html>