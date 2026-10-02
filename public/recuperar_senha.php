<?php
session_start();
ob_start();

require_once '../config/database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (file_exists('../lib/vendor/autoload.php')) {
    require_once '../lib/vendor/autoload.php';
}

$feedback_msg = '';
$feedback_tipo = '';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (!empty($dados['SendRecupSenha'])) {
    $email_rec = trim($dados['email']);
    
    if (empty($email_rec)) {
        $feedback_msg = "Por favor, digite seu e-mail cadastrado.";
        $feedback_tipo = "danger";
    } else {
        $query_email = "SELECT id, nome, email FROM usuarios WHERE email = ? LIMIT 1";
        $stmt_email = $conn->prepare($query_email);
        $stmt_email->bind_param('s', $email_rec);
        $stmt_email->execute();
        $result_email = $stmt_email->get_result();

        if ($result_email && $result_email->num_rows > 0) {
            $row_email = $result_email->fetch_assoc();
            $chave_recuperar_senha = $row_email['id'];

            $query_up = "UPDATE usuarios SET recuperar_senha = ? WHERE id = ? LIMIT 1";
            $stmt_up = $conn->prepare($query_up);
            $stmt_up->bind_param('si', $chave_recuperar_senha, $row_email['id']);

            if ($stmt_up->execute()) {
                $link = "https://alanprates.com.br/leitores-de-papel-ifba/user/atualizar_senha.php?chave=" . urlencode($chave_recuperar_senha);

                try {
                    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                        $mail = new PHPMailer(true);
                        include_once '../includes/mail_config.php';
                        $smtp = function_exists('get_smtp_config') ? get_smtp_config() : [];

                        $mail->CharSet = 'UTF-8';
                        if (!empty($smtp['host'])) {
                            $mail->isSMTP();
                            $mail->Host = $smtp['host'];
                            $mail->SMTPAuth = !empty($smtp['username']);
                            $mail->Username = $smtp['username'];
                            $mail->Password = $smtp['password'];
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port = $smtp['port'];
                            $mail->setFrom($smtp['from_email'], $smtp['from_name']);
                        }

                        $mail->addAddress($row_email['email'], $row_email['nome']);
                        $mail->isHTML(true);
                        $mail->Subject = 'Recuperação de Senha - Leitores de Papel IFBA';
                        $mail->Body = 'Prezado(a) ' . htmlspecialchars($row_email['nome']) . ",<br><br>Você solicitou a alteração de sua senha no sistema Leitores de Papel.<br><br>Para continuar, clique no link a seguir:<br><a href='" . $link . "'>" . $link . "</a><br><br>Se você não solicitou essa alteração, ignore este e-mail.<br><br>Atenciosamente,<br>Equipe Leitores de Papel - IFBA";
                        $mail->AltBody = "Prezado(a) " . $row_email['nome'] . ",\n\nVocê solicitou a alteração de sua senha.\n\nPara continuar, acesse o link:\n" . $link . "\n\nSe você não solicitou, ignore esta mensagem.";

                        $mail->send();
                        $_SESSION['msg'] = "Instruções para recuperação de senha enviadas com sucesso para seu e-mail!";
                        header("Location: index.php");
                        exit;
                    } else {
                        $feedback_msg = "Link de recuperação gerado: <a href='{$link}'>Clique aqui para redefinir</a>";
                        $feedback_tipo = "success";
                    }
                } catch (Exception $e) {
                    $feedback_msg = "Erro ao enviar e-mail: " . $mail->ErrorInfo;
                    $feedback_tipo = "danger";
                }
            } else {
                $feedback_msg = "Erro ao registrar solicitação. Tente novamente.";
                $feedback_tipo = "danger";
            }
            $stmt_up->close();
        } else {
            $feedback_msg = "Nenhum usuário localizado com o e-mail informado.";
            $feedback_tipo = "danger";
        }
        $stmt_email->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - Leitores de Papel</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/menu-mobile.css">
    <link rel="stylesheet" href="../assets/css/rodape.css">
    <script src="https://kit.fontawesome.com/cf6fa412bd.js" crossorigin="anonymous"></script>

    <style>
        body {
            background-color: #f8fafc;
        }

        .login-wrapper {
            min-height: calc(100vh - 180px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 15px;
        }

        .login-card {
            width: 100%;
            max-width: 460px;
            background-color: #ffffff;
            border-radius: 14px;
            padding: 40px 36px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
            border: 1px solid #e2e8f0;
        }

        .login-brand-icon {
            width: 58px;
            height: 58px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.12), rgba(239, 68, 68, 0.05));
            border: 1px solid rgba(220, 38, 38, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }

        .login-title {
            font-weight: 700;
            color: #1e293b;
            font-size: 22px;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            color: #64748b;
            font-size: 13px;
        }

        .form-group label {
            font-weight: 600;
            font-size: 13px;
            color: #334155;
            margin-bottom: 6px;
        }

        .input-group .input-group-prepend .input-group-text {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-right: none;
            color: #64748b;
            font-size: 14px;
            min-width: 44px;
            justify-content: center;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            border-left: 1px solid #dc2626;
        }

        .form-control {
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            padding: 11px 14px;
            font-size: 14px;
            height: auto;
            color: #1e293b;
            transition: all 0.2s ease-in-out;
        }

        .form-control:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
        }

        .btn-action-primary {
            background-color: #dc2626;
            border-color: #dc2626;
            color: #ffffff;
            font-weight: 700;
            padding: 11px 24px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
            transition: all 0.2s;
            font-size: 14px;
        }

        .btn-action-primary:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
            color: #ffffff;
            transform: translateY(-1px);
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 28px 20px;
                border-radius: 10px;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="text-center mb-4">
                <div class="login-brand-icon mx-auto mb-3">
                    <i class="fa fa-key text-danger"></i>
                </div>
                <h2 class="login-title">Recuperar Senha</h2>
                <p class="login-subtitle">Informe seu e-mail institucional para redefinir o acesso</p>
            </div>

            <?php if (!empty($feedback_msg)) { ?>
                <div class="alert alert-<?php echo htmlspecialchars($feedback_tipo); ?> alert-dismissible fade show shadow-sm text-left" role="alert">
                    <i class="fa <?php echo ($feedback_tipo === 'success') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-2"></i>
                    <?php echo $feedback_msg; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php } ?>

            <form method="POST" action="recuperar_senha.php">
                <div class="form-group text-left">
                    <label for="email">E-mail Cadastrado <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                        </div>
                        <input type="email" name="email" id="email" class="form-control" placeholder="exemplo@ifba.edu.br"
                            value="<?php echo isset($dados['email']) ? htmlspecialchars($dados['email']) : ''; ?>" required autofocus>
                    </div>
                </div>

                <button type="submit" name="SendRecupSenha" value="1" class="btn btn-action-primary btn-block mt-4">
                    <i class="fa fa-paper-plane mr-2"></i>Enviar Link de Recuperação
                </button>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <p class="text-muted small mb-0">
                    Lembrou sua senha? <a href="index.php" class="text-danger font-weight-bold">Voltar ao Login</a>
                </p>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>

    <?php include '../includes/footer.php'; ?>
</body>

</html>