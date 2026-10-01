<?php

session_start();

global $conn;

include '../config/database.php';



// Verifica se o usuário não está logado

if (!isset($_SESSION['user_id'])) {

    header("Location: ../public/index.php");

    exit;

}



// Definir a categoria selecionada

if (isset($_GET['categoria'])) {

    $categoria = $_GET['categoria'];

} else {

    $categoria = 'all';

}



// Definir o termo de pesquisa

if (isset($_GET['pesquisa'])) {

    $pesquisa = $_GET['pesquisa'];

} else {

    $pesquisa = '';

}



// Definir a ordem de classificação

if (isset($_GET['ordem']) && ($_GET['ordem'] == 'az' || $_GET['ordem'] == 'za')) {

    $ordem = $_GET['ordem'];

} else {

    $ordem = 'az';

}

?>

<!DOCTYPE html>

<html>



<head>

    <title>Lista de Usuarios</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="stylesheet" href="../assets/css/menu-mobile.css">

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

</head>



<body>

    <?php include '../includes/header.php'; ?>

    <br>

    <div class="container">

        <h2 class="text-center">Lista de usuários</h2>

        <br>

        <form method="GET" action="lista_usuarios.php">

            <div class="row">

                <div class="col-md-3 mb-2">
                    <div class="form-group text-left">
                        <label for="categoria">Filtrar por categoria:</label>
                        <select name="categoria" id="categoria" class="form-control w-100">
                            <option value="all" <?php if ($categoria == 'all')
                                echo 'selected'; ?>>Todos</option>
                            <option value="aluno" <?php if ($categoria == 'aluno')
                                echo 'selected'; ?>>Aluno</option>
                            <option value="professor" <?php if ($categoria == 'professor')
                                echo 'selected'; ?>>Professor
                            </option>
                            <option value="funcionario" <?php if ($categoria == 'funcionario')
                                echo 'selected'; ?>>
                                Funcionário</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 mb-2">
                    <div class="form-group text-left">
                        <label for="ordem">Ordenar por:</label>
                        <select name="ordem" id="ordem" class="form-control w-100">
                            <option value="az" <?php if ($ordem == 'az')
                                echo 'selected'; ?>>A-Z</option>
                            <option value="za" <?php if ($ordem == 'za')
                                echo 'selected'; ?>>Z-A</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <div class="form-group text-left">
                        <label for="pesquisa">Pesquisar Usuário:</label>
                        <input type="text" name="pesquisa" id="pesquisa" class="form-control w-100"
                            value="<?php echo $pesquisa; ?>">
                    </div>
                </div>

                <div class="col-md-2 mb-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit"
                            class="btn btn-danger w-100 d-flex align-items-center justify-content-center"
                            style="height: 38px;">Filtrar</button>
                    </div>
                </div>

            </div>

        </form>

        <div class="table-responsive">

            <table class="table table-striped">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Username</th>

                        <th>Nome</th>

                        <th>E-Mail</th>

                        <th>Categoria</th>

                        <th>Matrícula</th>

                        <th>Data de Nascimento</th>

                        <th>Sexo</th>

                        <th>Telefone</th>

                    </tr>

                </thead>

                <tbody>

                    <?php



                    include '../config/database.php';



                    // Construir a consulta SQL
                    
                    $sql = "SELECT * FROM usuarios WHERE 1=1";



                    if ($categoria != 'all') {

                        $sql .= " AND categoria = '$categoria'";

                    }



                    if (!empty($pesquisa)) {

                        $sql .= " AND (username LIKE '%$pesquisa%' OR nome LIKE '%$pesquisa%')";

                    }



                    if ($ordem == 'az') {

                        $sql .= " ORDER BY nome ASC";

                    } else {

                        $sql .= " ORDER BY nome DESC";

                    }



                    $result = $conn->query($sql);



                    if ($result->num_rows > 0) {

                        while ($row = $result->fetch_assoc()) {

                            echo '<tr>';

                            echo '<td>' . $row["id"] . '</td>';

                            echo '<td>' . $row["username"] . '</td>';

                            echo '<td>' . $row["nome"] . '</td>';

                            echo '<td>' . $row["email"] . '</td>';

                            echo '<td>' . $row["categoria"] . '</td>';

                            echo '<td>' . $row["matricula"] . '</td>';

                            echo '<td>' . $row["datanascimento"] . '</td>';

                            echo '<td>' . $row["sexo"] . '</td>';

                            echo '<td>' . $row["telefone"] . '</td>';

                            echo '</tr>';

                        }

                    } else {

                        echo '<tr><td colspan="9">Nenhum usuário cadastrado.</td></tr>';

                    }





                    ?>

                </tbody>

            </table>

        </div>



        <div class="alert alert-warning text-center mb-0 d-md-none" role="alert">

            Por favor, role a página horizontalmente para visualizar a tabela completa.

        </div>



        <br>



        <div class="row">
            <div class="col-md-12 mb-2">
                <a href="index.php" class="btn btn-warning btn-block">Voltar para Painel de Usuário</a>
            </div>
        </div>

        <br>

    </div>



    <script src="../assets/js/script.js"></script>

    <?php

    // Inclui o rodapé
    
    include '../includes/footer.php';

    ?>

</body>



</html>