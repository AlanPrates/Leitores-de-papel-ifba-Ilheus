<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

require_admin('../public/index.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Cadastrar Livro - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <br>
        <br>
        <h2>Cadastrar Livro - Admin</h2>
        <br>

        <?php if (!empty($_SESSION['success_message'])) { ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
            </div>
        <?php } ?>

        <?php if (!empty($_SESSION['error_message'])) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php } ?>

        <form method="POST" action="../actions/salvar_livro.php">

            <div class="form-group text-left">

                <label for="isbn">ISBN:</label>

                <input type="text" class="form-control" id="isbn" name="isbn" required>

            </div>

            <div class="form-group text-left">

                <label for="titulo">Título:</label>

                <input type="text" class="form-control" id="titulo" name="titulo" required>

            </div>

            <div class="form-group text-left">

                <label for="autor">Autor:</label>

                <input type="text" class="form-control" id="autor" name="autor" required>

            </div>

            <div class="form-group text-left">

                <label for="ano">Ano de Publicação:</label>

                <input type="number" class="form-control" id="ano" name="ano" required>

            </div>

            <div class="form-group text-left">

                <label for="editora">Editora:</label>

                <input type="text" class="form-control" id="editora" name="editora" required>

            </div>

            <div class="form-group text-left">

                <label for="quantidade">Quantidade:</label>

                <input type="number" class="form-control" id="quantidade" name="quantidade" required>

            </div>



            <div class="form-group text-left">

                <label for="genero">Gênero:</label>

                <input type="text" class="form-control" id="genero" name="genero" required>

            </div>



            <div class="row align-items-center">
                <div class="col-md-6 mb-2">
                    <button type="submit"
                        class="btn btn-danger w-100 d-flex align-items-center justify-content-center p-0"
                        style="height: 38px;">Cadastrar</button>
                </div>
                <div class="col-md-6 mb-2">
                    <a href="lista_livros.php"
                        class="btn btn-warning w-100 d-flex align-items-center justify-content-center p-0"
                        style="height: 38px;">Voltar para Lista de Livros</a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-2">
                    <a href="index.php" class="btn btn-warning w-100 d-flex align-items-center justify-content-center"
                        style="height: 38px;">Voltar para Painel de Usuário</a>
                </div>
            </div>

        </form>

    </div>





    <script src="../assets/js/script.js"></script>

    <?php

    // Inclui o rodapé
    
    include '../includes/footer.php';

    ?>

</body>



</html>