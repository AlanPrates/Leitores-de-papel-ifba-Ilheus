<?php

include 'conexao.php';

ob_start();

session_start();



?>



<!DOCTYPE html>

<html lang="pt-br">



<head>

    <meta charset="UTF-8">

    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-ico">

    <link rel="stylesheet" href="css/bootstrap.css">

    <title>Atualizar Senha</title>

</head>



<body>

    <div class="container">

        <h1 class="text-center">Atualizar senha</h1>



        <?php

        // Obtém o parâmetro 'chave' da URL usando o método GET
        $chave = filter_input(INPUT_GET, 'chave', FILTER_DEFAULT);

        // Verifica se a chave não está vazia
        if (!empty($chave)) {
            // Consulta para verificar se a chave de recuperação de senha existe no banco de dados
            $query_email = "SELECT id 
                    FROM usuarios 
                    WHERE recuperar_senha = ?  
                    LIMIT 1";
            $result_email = $conn->prepare($query_email);
            $result_email->bind_param('s', $chave);
            $result_email->execute();
            $result_email = $result_email->get_result();

            // Verifica se a consulta retornou resultados (ou seja, a chave é válida)
            if (($result_email) && ($result_email->num_rows != 0)) {
                $row_email = $result_email->fetch_assoc();

                // Verifica se o formulário de atualização da senha foi enviado via POST
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Obtém os dados do formulário
                    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
                    $password = $dados['password'];
                    $recuperar_senha = null;

                    // Hash da senha
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Atualiza a senha e remove a chave de recuperação do usuário no banco de dados
                    $query_up_email = "UPDATE usuarios 
                                SET password = ?,
                                recuperar_senha = ? 
                                WHERE id = ? 
                                LIMIT 1";
                    $result_up_email = $conn->prepare($query_up_email);
                    $result_up_email->bind_param('ssi', $hashed_password, $recuperar_senha, $row_email['id']);

                    // Verifica se a atualização foi bem-sucedida ou exibe uma mensagem de erro
                    if ($result_up_email->execute()) {
                        $_SESSION['msg'] = "<p style='color: green'>Senha atualizada com sucesso!</p>";
                        header("Location: index.php");
                        exit();
                    } else {
                        echo "<p style='color: #ff0000'>Erro: Tente novamente!</p>";
                    }
                }
            } else {
                // A chave não foi encontrada ou é inválida
                $_SESSION['msg_rec'] = "<p style='color: #ff0000'>Erro: Link inválido, solicite um novo link para atualizar a senha!</p>";
                header("Location: recuperar_senha.php");
                exit();
            }
        } else {
            // A chave está vazia
            $_SESSION['msg_rec'] = "<p style='color: #ff0000'>Erro: Link inválido, solicite um novo link para atualizar a senha!</p>";
            header("Location: recuperar_senha.php");
            exit();
        }
        ?>




        <form method="POST" action="">

            <div class="form-group">

                <label for="password">Senha</label>

                <input type="password" class="form-control" id="password" name="password" placeholder="Digite a nova senha">

            </div>



            <button type="submit" class="btn btn-primary" name="SendNovaSenha">Atualizar</button>

        </form>



        <br>

        <p class="text-center">Lembrou? <a href="index.php">Clique aqui</a> para logar.</p>

    </div>



    <script src="js/bootstrap.min.js"></script>

</body>



</html>