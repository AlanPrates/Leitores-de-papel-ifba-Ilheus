<?php

session_start();



include_once 'conexao.php';



use PHPMailer\PHPMailer\PHPMailer;

use PHPMailer\PHPMailer\SMTP;

use PHPMailer\PHPMailer\Exception;



// Inclua o arquivo do PHPMailer

require './lib/vendor/phpmailer/phpmailer/src/Exception.php';

require './lib/vendor/phpmailer/phpmailer/src/PHPMailer.php';

require './lib/vendor/phpmailer/phpmailer/src/SMTP.php';



// Função para enviar e-mails de notificação

function enviarNotificacao($email, $livro, $dataDevolucao)
{

    // Instância do PHPMailer

    $mail = new PHPMailer(true);



    try {

        // Configurações do servidor SMTP (substitua pelas suas configurações)

        $mail->isSMTP();

        $mail->Host = 'smtp-relay.brevo.com';

        $mail->SMTPAuth = true;

        $mail->Username = 'nzgamebr@gmail.com';

        $mail->Password = '0LQ98cwOraSE7RX2';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Port = 587;



        // Configurações do e-mail

        $mail->setFrom('nzgamebr@gmail.com', 'Nome do Remetente');

        $mail->addAddress($email);

        $mail->CharSet = 'UTF-8';

        $mail->isHTML(true);

        $mail->Subject = 'Notificação de Vencimento de Entrega de Livro';

        $mail->Body = 'Olá, <strong>' . $email . '</strong>. Este é um lembrete de que o livro <strong>' . $livro . '</strong> deve ser devolvido até ' . $dataDevolucao . '.';



        // Envia o e-mail

        if ($mail->send()) {

            return true;

        } else {

            echo 'Erro ao enviar o e-mail: ' . $mail->ErrorInfo;

            return false;

        }

    } catch (Exception $e) {

        echo 'Erro ao enviar o e-mail: ' . $e->getMessage();

        return false;

    }

}



// Consulta ao banco de dados para obter os dados de notificação

$conn = new mysqli($servername, $username, $password, $dbname);



if ($conn->connect_error) {

    die("Conexão falhou: " . $conn->connect_error);

}



$sql = "SELECT u.email, l.livro_id, l.data_devolucao, lv.titulo 

        FROM livros_emprestados l 

        INNER JOIN usuarios u ON l.user_id = u.id 

        INNER JOIN livros lv ON l.livro_id = lv.id

        WHERE l.data_devolucao < NOW()";



$result = $conn->query($sql);



if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $email = $row["email"];

        $livro = $row["titulo"];

        $dataDevolucao = $row["data_devolucao"];



        if (enviarNotificacao($email, $livro, $dataDevolucao)) {

            echo "E-mail enviado para: " . $email . "<br>";

        } else {

            echo "Falha ao enviar e-mail para: " . $email . "<br>";

        }

    }

} else {

    echo "0 resultados";

}



$conn->close();

?>

