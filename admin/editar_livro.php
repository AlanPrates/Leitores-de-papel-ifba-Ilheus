<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

// Apenas administradores autenticados podem editar livros
require_admin('../public/index.php');

$livro_id = isset($_GET['livro_id']) ? (int)$_GET['livro_id'] : 0;
if ($livro_id <= 0) {
    header("Location: lista_livros.php");
    exit;
}

// Verifica se o formulário de exclusão foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {
    $stmt_del = $conn->prepare("DELETE FROM livros WHERE id = ?");
    if ($stmt_del) {
        $stmt_del->bind_param("i", $livro_id);
        $stmt_del->execute();
        $stmt_del->close();
        $_SESSION['success_message'] = 'Livro excluído com sucesso.';
        header("Location: lista_livros.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Erro ao excluir livro.";
    }
}

// Consulta o livro com base no ID fornecido
$stmt = $conn->prepare("SELECT * FROM livros WHERE id = ? LIMIT 1");
if (!$stmt) {
    header("Location: lista_livros.php");
    exit;
}
$stmt->bind_param("i", $livro_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $livro = $result->fetch_assoc();
} else {
    $stmt->close();
    header("Location: lista_livros.php");
    exit;
}
$stmt->close();
?>





<!DOCTYPE html>



<html>







<head>



    <title>Editar Livro</title>



    <meta name="viewport" content="width=device-width, initial-scale=1">



    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">



    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">



    <link rel="stylesheet" href="../assets/css/menu-mobile.css">



    <link rel="stylesheet" href="../assets/css/rodape.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>



</head>







<body>



    <?php include '../includes/header.php'; ?>







    <div class="container">



        <h1>Editar Livro</h1>



        <form method="POST" action="../actions/atualizar_livro.php">

            <input type="hidden" name="livro_id" value="<?php echo htmlspecialchars($livro_id); ?>">

            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" class="form-control" id="titulo" name="titulo"
                    value="<?php echo htmlspecialchars($livro['titulo']); ?>">
            </div>

            <div class="form-group">
                <label for="autor">Autor:</label>
                <input type="text" class="form-control" id="autor" name="autor" value="<?php echo htmlspecialchars($livro['autor']); ?>">
            </div>

            <div class="form-group">
                <label for="ano_publicacao">Ano de Publicação:</label>
                <input type="text" class="form-control" id="ano_publicacao" name="ano_publicacao"
                    value="<?php echo htmlspecialchars($livro['ano_publicacao']); ?>">
            </div>

            <div class="form-group">
                <label for="isbn">ISBN:</label>
                <input type="text" class="form-control" id="isbn" name="isbn" value="<?php echo htmlspecialchars($livro['isbn']); ?>">
            </div>

            <div class="form-group">
                <label for="genero">Genero:</label>
                <input type="text" class="form-control" id="genero" name="genero"
                    value="<?php echo htmlspecialchars($livro['genero']); ?>">
            </div>

            <div class="form-group">
                <label for="quantidade">Quantidade:</label>
                <input type="number" class="form-control" id="quantidade" name="quantidade" min="0"
                    value="<?php echo htmlspecialchars($livro['quantidade']); ?>">
                <span id="mensagem"></span>
            </div>





            <div class="row">
                <div class="col-md-6 mb-2">
                    <button type="submit" class="btn btn-primary btn-block">Atualizar Dados</button>
                </div>
                <div class="col-md-6 mb-2">
                    <a href="lista_livros.php" class="btn btn-warning btn-block">Voltar para Lista de Livros</a>
                </div>
            </div>

        </form>

        <br>

        <div class="row">
            <div class="col-md-12">
                <a href="index.php" class="btn btn-warning btn-block">Voltar para Painel de Usuário</a>
            </div>
        </div>
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



    <script src="../assets/js/script.js"></script>



</body>







</html>