<?php

session_start();

global $conn;

include 'conexao.php';



// Verifica se o usuário não está logado

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");

    exit;

}



// Verifica se o ID do livro foi fornecido

if (!isset($_GET['livro_id'])) {

    header("Location: lista_livros.php");

    exit;

}



// Obtém o ID do livro a ser editado

$livro_id = $_GET['livro_id'];



// Verifica se o formulário de exclusão foi enviado

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {

    // Desativa a verificação de chaves estrangeiras temporariamente

    $conn->query("SET FOREIGN_KEY_CHECKS=0");



    // Exclui o livro do banco de dados

    $query = "DELETE FROM livros WHERE id = $livro_id";

    $result = $conn->query($query);



    // Ativa a verificação de chaves estrangeiras novamente

    $conn->query("SET FOREIGN_KEY_CHECKS=1");



    if ($result) {

        // Redireciona de volta para a página de lista de livros

        header("Location: lista_livros_admin.php");

        exit;

    } else {

        echo "Erro ao excluir o livro: " . $conn->error;

    }

}



// Consulta o livro com base no ID fornecido

$query = "SELECT * FROM livros WHERE id = $livro_id";

$result = $conn->query($query);



// Verifica se o livro foi encontrado

if ($result && $result->num_rows > 0) {

    $livro = $result->fetch_assoc();

} else {

    // Livro não encontrado, redireciona de volta para a lista de livros

    header("Location: lista_livros_admin.php");

    exit;

}

?>





<!DOCTYPE html>



<html>







<head>



    <title>Editar Livro</title>



    <meta name="viewport" content="width=device-width, initial-scale=1">



    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">



    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">



    <link rel="stylesheet" href="assets/menu-mobile-css/style.css">



    <link rel="stylesheet" href="assets/css/rodape.css">



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







    <div class="container">



        <h1>Editar Livro</h1>



        <form method="POST" action="atualizar_livro.php">



            <input type="hidden" name="livro_id" value="<?php echo $livro_id; ?>">







            <div class="form-group">



                <label for="titulo">Título:</label>



                <input type="text" class="form-control" id="titulo" name="titulo"
                    value="<?php echo $livro['titulo']; ?>">



            </div>







            <div class="form-group">



                <label for="autor">Autor:</label>



                <input type="text" class="form-control" id="autor" name="autor" value="<?php echo $livro['autor']; ?>">



            </div>







            <div class="form-group">



                <label for="ano_publicacao">Ano de Publicação:</label>



                <input type="text" class="form-control" id="ano_publicacao" name="ano_publicacao"
                    value="<?php echo $livro['ano_publicacao']; ?>">



            </div>

            <div class="form-group">



                <label for="isbn">ISBN:</label>



                <input type="text" class="form-control" id="isbn" name="isbn" value="<?php echo $livro['isbn']; ?>">



            </div>







            <div class="form-group">



                <label for="genero">Genero:</label>



                <input type="text" class="form-control" id="genero" name="genero"
                    value="<?php echo $livro['genero']; ?>">



            </div>







            <div class="form-group">

                <label for="quantidade">Quantidade:</label>

                <input type="text" class="form-control" id="quantidade" name="quantidade"
                    value="<?php echo $livro['quantidade']; ?>">

                <span id="mensagem"></span>

            </div>





            <button type="submit" class="btn btn-primary">Atualizar Dados</button>



        </form>

        <br>

        <a href="lista_livros_admin.php" class="btn btn-warning">Voltar para Lista de Livros</a>

        <br>

        <br>

        <a href="admin.php" class="btn btn-warning">Voltar para Painel de Usuário</a>



    </div>



    <script>

        document.getElementById('quantidade').addEventListener('input', function () {

            var quantidade = this.value;

            var mensagemElement = document.getElementById('mensagem');

            if (quantidade == 0) {

                mensagemElement.innerText = "Se o valor for zero, o livro fica indisponível";

            } else {

                mensagemElement.innerText = "";

            }

        });

    </script>



    <script src="assets/menu-mobile-js/script.js"></script>



</body>







</html>
