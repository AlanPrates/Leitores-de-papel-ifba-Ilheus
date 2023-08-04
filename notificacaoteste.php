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
function enviarNotificacao($email, $livro, $dataVencimento)
{
    // Instância do PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor SMTP (substitua pelas suas configurações)
        $mail->isSMTP();
        $mail->Host       = 'smtp-relay.brevo.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nzgamebr@gmail.com';
        $mail->Password   = '0LQ98cwOraSE7RX2';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Configurações do e-mail
        $mail->setFrom('nzgamebr@gmail.com', 'Nome do Remetente');
        $mail->addAddress($email); // Endereço do destinatário
        $mail->isHTML(true);
        $mail->Subject = 'Notificação de Vencimento de Entrega de Livro';
        $mail->Body    = 'Olá, <strong>' . $email . '</strong>. Este é um lembrete de que o livro <strong>' . $livro . '</strong> deve ser devolvido até ' . $dataVencimento . '.';

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

// Exemplo de uso da função
$email = 'alan_nz@live.com';
$livro = 'Título do Livro';
$dataVencimento = '31/08/2023';

if (enviarNotificacao($email, $livro, $dataVencimento)) {
    echo 'E-mail enviado com sucesso!';
} else {
    echo 'Falha ao enviar o e-mail.';
}
?>
