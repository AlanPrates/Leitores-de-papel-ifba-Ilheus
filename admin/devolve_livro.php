<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

require_admin('../public/index.php');

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$livros = [];

if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT livros.id, livros.titulo, livros.autor, livros.ano_publicacao, livros_emprestados.data_emprestimo, livros_emprestados.data_devolucao
              FROM livros_emprestados
              INNER JOIN livros ON livros_emprestados.livro_id = livros.id
              WHERE livros_emprestados.user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $livros = $result->fetch_all(MYSQLI_ASSOC);
        }
        $stmt->close();
    }
}
?>



<!DOCTYPE html>

<html>



<head>

    <title>Devolver Livro</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">

    <link rel="stylesheet" href="../assets/css/rodape.css">

    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

    <style>
        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            flex: 1 0 auto;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        footer, .footer {
            flex-shrink: 0;
            margin-top: auto;
        }
    </style>

</head>



<body>

    <?php include '../includes/header.php'; ?>

    <div class="container">

        <h2>Devolver Livro</h2>

        <?php
        $msg_sucesso = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : (isset($_GET['success_message']) ? $_GET['success_message'] : (isset($_GET['message']) ? $_GET['message'] : null));
        $msg_erro = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : (isset($_GET['error_message']) ? $_GET['error_message'] : null);
        if (isset($_SESSION['success_message'])) { unset($_SESSION['success_message']); }
        if (isset($_SESSION['error_message'])) { unset($_SESSION['error_message']); }
        ?>

        <?php if (!empty($msg_sucesso)) { ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($msg_sucesso); ?>
            </div>
        <?php } ?>

        <?php if (!empty($msg_erro)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($msg_erro); ?>
            </div>
        <?php } ?>

        <?php if (!empty($livros)) { ?>

            <div class="table-responsive">

                <table class="table">

                    <thead>

                        <tr>

                            <th>Título</th>

                            <th>Autor</th>

                            <th>Ano de Publicação</th>

                            <th>Data de Empréstimo</th>

                            <th>Data da Devolução</th>

                            <th>Ação</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($livros as $livro) { ?>

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
                                    <?php echo htmlspecialchars($livro['data_emprestimo']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($livro['data_devolucao']); ?>
                                </td>

                                <td>

                                    <form method="POST" action="../actions/devolve_livro_action_admin.php">

                                        <input type="hidden" name="livro_id" value="<?php echo $livro['id']; ?>">

                                        <button type="submit" class="btn btn-danger">Devolver</button>

                                    </form>

                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        <?php } else { ?>

            <p>Você não possui livros emprestados no momento.</p>

        <?php } ?>

        <br>



        <a href="lista_livros.php" class="btn btn-warning">Voltar para Lista de Livros</a>

        <br>

        <br>

        <a href="empresta_livro_admin.php" class="btn btn-warning">Emprestar Livro</a>
    </div>

    <script src="../assets/js/script.js"></script>

    <script src="../assets/js/bootstrap.min.js"></script>

    <?php

    // Inclui o rodapé
    
    include '../includes/footer.php';

    ?>

</body>



</html>
