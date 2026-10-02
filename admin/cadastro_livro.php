<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

require_admin('../public/index.php');
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Livro - Administração</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <link rel="stylesheet" href="../assets/css/rodape.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

    <style>
        body {
            background-color: #f8fafc;
        }

        .book-form-wrapper {
            width: 95% !important;
            max-width: 960px !important;
            margin: 35px auto !important;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 35px 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        .icon-avatar {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.12), rgba(239, 68, 68, 0.05));
            border: 1px solid rgba(220, 38, 38, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .section-header {
            font-size: 14px;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 25px;
            margin-bottom: 18px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f1f5f9;
            display: flex;
            align-items: center;
        }

        .section-header i {
            color: #dc2626;
            margin-right: 8px;
            font-size: 16px;
        }

        .form-group label {
            font-weight: 600;
            font-size: 13px;
            color: #334155;
            margin-bottom: 6px;
        }

        .input-group .input-group-prepend .input-group-text {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-right: none;
            color: #64748b;
            font-size: 14px;
            min-width: 42px;
            justify-content: center;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            border-left: 1px solid #dc2626;
        }

        .form-group .form-control {
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            font-size: 14px;
            height: auto;
            transition: all 0.2s ease-in-out;
            color: #1e293b;
        }

        .form-group .form-control:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
        }

        .btn-action-primary {
            background-color: #dc2626;
            border-color: #dc2626;
            color: #ffffff;
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
            transition: all 0.2s;
        }

        .btn-action-primary:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-action-secondary {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #1e293b;
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-action-secondary:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            color: #0f172a;
        }

        @media (max-width: 768px) {
            .book-form-wrapper {
                width: 100% !important;
                margin: 15px auto !important;
                padding: 22px 16px;
                border-radius: 8px;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="book-form-wrapper">
        <!-- Topo da Página -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap pb-3 border-bottom">
            <div class="d-flex align-items-center">
                <div class="icon-avatar mr-3">
                    <i class="fa fa-book-medical text-danger"></i>
                </div>
                <div>
                    <h2 class="mb-0 font-weight-bold" style="color: #1e293b; font-size: 22px;">Cadastrar Novo Livro</h2>
                    <span class="text-muted small">Adicione um novo título ao acervo bibliotecário do IFBA</span>
                </div>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <a href="lista_livros.php" class="btn btn-outline-secondary btn-sm mr-2 shadow-sm font-weight-bold">
                    <i class="fa fa-list mr-1"></i> Lista de Livros
                </a>
                <a href="index.php" class="btn btn-warning btn-sm shadow-sm font-weight-bold text-dark">
                    <i class="fa fa-arrow-left mr-1"></i> Painel Admin
                </a>
            </div>
        </div>

        <?php if (!empty($_SESSION['success_message'])) { ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa fa-check-circle mr-2"></i><?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>

        <?php if (!empty($_SESSION['error_message'])) { ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>

        <form method="POST" action="../actions/salvar_livro.php">
            <!-- Seção 1: Informações Principais da Obra -->
            <div class="section-header">
                <i class="fa fa-book"></i> 1. Identificação da Obra
            </div>

            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="titulo">Título da Obra <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-heading"></i></span>
                        </div>
                        <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Ex: Dom Casmurro" required>
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label for="isbn">ISBN <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                        </div>
                        <input type="text" class="form-control" id="isbn" name="isbn" placeholder="Ex: 9788535902778" required>
                    </div>
                </div>
            </div>

            <!-- Seção 2: Autoria e Editoração -->
            <div class="section-header mt-3">
                <i class="fa fa-feather-alt"></i> 2. Autoria e Publicação
            </div>

            <div class="form-row">
                <div class="form-group col-md-7">
                    <label for="autor">Autor(a) / Autores <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-user-pen"></i></span>
                        </div>
                        <input type="text" class="form-control" id="autor" name="autor" placeholder="Ex: Machado de Assis" required>
                    </div>
                </div>

                <div class="form-group col-md-5">
                    <label for="editora">Editora <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-building"></i></span>
                        </div>
                        <input type="text" class="form-control" id="editora" name="editora" placeholder="Ex: Companhia das Letras" required>
                    </div>
                </div>
            </div>

            <!-- Seção 3: Classificação e Acervo -->
            <div class="section-header mt-3">
                <i class="fa fa-tags"></i> 3. Classificação e Quantidade
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="ano">Ano de Publicação <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                        </div>
                        <input type="number" class="form-control" id="ano" name="ano" min="1500" max="<?php echo date('Y') + 1; ?>" placeholder="Ex: 1899" required>
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label for="genero">Gênero / Categoria <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-bookmark"></i></span>
                        </div>
                        <input type="text" class="form-control" id="genero" name="genero" placeholder="Ex: Literatura Brasileira" required>
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label for="quantidade">Quantidade em Estoque <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-layer-group"></i></span>
                        </div>
                        <input type="number" class="form-control" id="quantidade" name="quantidade" min="1" placeholder="Ex: 5" required>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="row mt-4 pt-2">
                <div class="col-md-6 mb-2">
                    <button type="submit" class="btn btn-action-primary btn-block">
                        <i class="fa fa-plus-circle mr-2"></i>Cadastrar Livro
                    </button>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="lista_livros.php" class="btn btn-action-secondary btn-block">
                        <i class="fa fa-list mr-1"></i>Lista de Livros
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="index.php" class="btn btn-outline-secondary btn-block py-2 font-weight-bold">
                        <i class="fa fa-arrow-left mr-1"></i>Painel Admin
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/script.js"></script>

    <?php include '../includes/footer.php'; ?>
</body>

</html>