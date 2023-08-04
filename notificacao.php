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

// Conexão com o banco de dados (ajuste de acordo com a sua configuração)
$servername = 'localhost';
$username = 'root';
$password = 'root';
$dbname = 'biblioteca';

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die('Conexão falhou: ' . $conn->connect_error);
}

// Consulta para obter empréstimos com data de entrega vencida
$sql = "SELECT u.email, l.titulo, le.data_devolucao
        FROM livros_emprestados le
        INNER JOIN usuarios u ON u.id = le.user_id
        INNER JOIN livros l ON l.id = le.livro_id
        WHERE le.data_devolucao < NOW()";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $email = $row['email'];
        $livro = $row['titulo'];
        $dataVencimento = $row['data_devolucao'];

        // Envia a notificação por e-mail
        enviarNotificacao($email, $livro, $dataVencimento);
    }
}

$conn->close();
