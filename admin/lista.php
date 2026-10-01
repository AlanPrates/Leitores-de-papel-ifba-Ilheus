<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

require_admin('../public/index.php');

$username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : '';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Lista de Administradores</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <br>
    <div class="container-fluid">
        <h2 class="text-center">Lista de Administradores</h2>
        <br>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nome</th>
                        <th>E-Mail</th>
                        <th>Data de Nascimento</th>
                        <th>Sexo</th>
                        <th>Telefone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Consulta para obter os administradores cadastrados
                    $sql = "SELECT admin_username, nome, email, datanascimento, sexo, telefone FROM admin";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row["admin_username"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["nome"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["email"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["datanascimento"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["sexo"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["telefone"]) . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">Nenhum administrador cadastrado.</td></tr>';
                    }

                    ?>
                </tbody>
            </table>
        </div>
        <br>
        <div class="alert alert-warning text-center mb-0 d-md-none" role="alert">
            Por favor, role a página horizontalmente para visualizar a tabela completa.
        </div>
        <br>
        <div class="row">
            <div class="col-md-12 mb-2">
                <a href="index.php" class="btn btn-warning btn-block">Voltar para Painel de Usuário</a>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <?php
    // Inclui o rodapé
    include '../includes/footer.php';
    ?>
</body>

</html>