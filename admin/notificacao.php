<?php
session_start();
include_once '../includes/auth.php';
require_admin();

include_once '../config/database.php';
include_once '../includes/mail_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once '../lib/vendor/autoload.php';

// Função para enviar e-mails de notificação
function enviarNotificacao($email, $livro, $dataDevolucao)
{
    $smtp = get_smtp_config();
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $smtp['host'];
        $mail->SMTPAuth = !empty($smtp['username']);
        $mail->Username = $smtp['username'];
        $mail->Password = $smtp['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $smtp['port'];

        $mail->setFrom($smtp['from_email'], $smtp['from_name']);


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



// $conn já foi inicializado por config/database.php
global $conn;




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

