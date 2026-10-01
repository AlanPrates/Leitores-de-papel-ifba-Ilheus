<?php
session_start();

if (!isset($_SESSION['admin_username']) && !isset($_SESSION['username'])) {
    header('Location: ../public/index.php');
    exit(); // Certifique-se de que o script pare aqui após redirecionar
}


?>











<!DOCTYPE html>



<html>







<head>



    <meta charset="UTF-8">



    <title>EXEMPLOS PRÁTICOS SOBRE PHP</title>



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



    <div class="row">



        <div class="col-sm-12">



            <?php



            // Verificar se o usuário está logado
            


            if (isset($_SESSION['username'])) {



                // Verificar se o cookie de nome existe
            


                // Verificar se a sessão ou cookie de nome existe
                $nomeUsuario = "";
                if (isset($_SESSION['nome'])) {
                    $nomeUsuario = $_SESSION['nome'];
                } elseif (isset($_COOKIE['nome'])) {
                    $nomeUsuario = $_COOKIE['nome'];
                }

                if (!empty($nomeUsuario)) {
                    // Obtém a hora atual
                    $hora = date('H');

                    // Define a saudação com base na hora
                    if ($hora >= 6 && $hora < 12) {
                        $saudacao = "Bom dia";
                    } elseif ($hora >= 12 && $hora < 18) {
                        $saudacao = "Boa tarde";
                    } else {
                        $saudacao = "Boa noite";
                    }

                    // Exibe a saudação juntamente com o nome do usuário
                    echo "<span class='text-danger'><h1>Olá, $nomeUsuario! $saudacao.</h1></span>";
                } else {
                    echo "<span class='text-danger'>Bem-vindo à página inicial.</span>";
                }

            } else {



                header('Location: ../public/index.php'); // Redirecionar para a página de login se o usuário não estiver logado
            


            }



            ?>



            <br>



        </div>



    </div>



    <div class="container">

        <div class="row">

            <div class="col-md-3">

                <a href="lista_livros.php" class="btn btn-warning btn-block">Listar Livros</a>

            </div>

            <br>

            <br>

            <div class="col-md-3">

                <a href="atualiza_dados.php" class="btn btn-warning btn-block">Atualizar Meus Dados</a>

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