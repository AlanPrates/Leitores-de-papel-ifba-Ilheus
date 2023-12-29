<?php
session_start();
ob_start();
include_once 'conexao.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require './lib/vendor/autoload.php';
$mail = new PHPMailer(true);

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/menu-mobile-css/style.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <title>Recuperar Senha</title>
</head>

<body>
    <header>
        <!-- Barra de navegação -->
        <nav class="nav-bar">
            <div class="logo">
                <a href="aluno.php">
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
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="shadow p-4"> <!-- Adicionado a classe 'shadow' -->
            <div class="text-center mt-3">
                <h1>Recuperar Senha</h1>
            </div>
            <?php
            $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

            if (!empty($dados['SendRecupSenha'])) {
                //var_dump($dados);
                $query_email = "SELECT id, nome, email 
                    FROM usuarios 
                    WHERE email = ?  
                    LIMIT 1";
                $result_email = $conn->prepare($query_email);
                $result_email->bind_param('s', $dados['email']);
                $result_email->execute();
                $result_email = $result_email->get_result();

                if (($result_email) && ($result_email->num_rows != 0)) {
                    $row_email = $result_email->fetch_assoc();
                    $chave_recuperar_senha = $row_email['id'];
                    //echo "Chave $chave_recuperar_senha <br>";
            
                    $query_up_email = "UPDATE usuarios 
                        SET recuperar_senha = ? 
                        WHERE id = ? 
                        LIMIT 1";
                    $result_up_email = $conn->prepare($query_up_email);
                    $result_up_email->bind_param('si', $chave_recuperar_senha, $row_email['id']);

                    if ($result_up_email->execute()) {
                        $link = "https://alanprates.com.br/leitores-de-papel-ifba/atualizar_senha.php?chave=$chave_recuperar_senha";

                        try {
                            /*$mail->SMTPDebug = SMTP::DEBUG_SERVER;*/
                            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                            $mail->CharSet = 'UTF-8';
                            $mail->isSMTP();
                            $mail->Host = 'sandbox.smtp.mailtrap.io';
                            $mail->SMTPAuth = true;
                            $mail->Username = '89f6fb8d8f567c';
                            $mail->Password = '2174786544ed23';
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port = 2525;

                            $mail->setFrom('atendimento@celke.com', 'Atendimento');
                            $mail->addAddress($row_email['email'], $row_email['nome']);

                            $mail->isHTML(true);                                  //Set email format to HTML
                            $mail->Subject = 'Recuperar senha';
                            $mail->Body = 'Prezado(a) ' . $row_email['nome'] . ".<br><br>Você solicitou alteração de senha.<br><br>Para continuar o processo de recuperação de sua senha, clique no link abaixo ou cole o endereço no seu navegador: <br><br><a href='" . $link . "'>" . $link . "</a><br><br>Se você não solicitou essa alteração, nenhuma ação é necessária. Sua senha permanecerá a mesma até que você ative este código.<br><br>";
                            $mail->AltBody = 'Prezado(a) ' . $row_email['nome'] . "\n\nVocê solicitou alteração de senha.\n\nPara continuar o processo de recuperação de sua senha, clique no link abaixo ou cole o endereço no seu navegador: \n\n" . $link . "\n\nSe você não solicitou essa alteração, nenhuma ação é necessária. Sua senha permanecerá a mesma até que você ative este código.\n\n";

                            $mail->send();

                            $_SESSION['msg'] = "<p style='color: green'>E-mail enviado com sucesso. Verifique sua caixa de entrada para recuperar sua senha!</p>";
                            ob_end_clean(); // Limpar o buffer de saída
                            header("Location: index.php"); // Redirecionar para index.php
                            exit; // Encerrar a execução do script
                        } catch (Exception $e) {
                            echo "Erro: E-mail não pôde ser enviado. Erro do Mailer: {$mail->ErrorInfo}";
                        }
                    } else {
                        echo "<p style='color: #ff0000'>Erro: Tente novamente!</p>";
                    }
                } else {
                    echo "<p style='color: #ff0000'>Erro: Usuário não encontrado!</p>";
                }
            }

            if (isset($_SESSION['msg_rec'])) {
                echo $_SESSION['msg_rec'];
                unset($_SESSION['msg_rec']);
            }

            ?>

            <form method="POST" action="" class="text-center">
                <?php
                $email = "";
                if (isset($dados['email'])) {
                    $email = $dados['email'];
                } ?>

                <div class="form-group text-center mt-3">
                    <input type="text" name="email" id="email" class="form-control" placeholder="Digite o E-Mail"
                        value="<?php echo $email; ?>" style="width: 300px; margin: 0 auto;">
                </div>

                <input type="submit" value="Recuperar" name="SendRecupSenha" class="btn btn-primary">
            </form>

            <div class="text-center mt-3">
                Lembrou? <a href="index.php">clique aqui</a> para logar
            </div>
        </div>
    </div>

    <!-- Scripts JavaScript -->
    <script src="assets/js/script.js"></script>
    <script src="js/password.js"></script>
    <?php
    // Inclui o rodapé
    include 'rodape.php';
    ?>
</body>
</body>

</html>
