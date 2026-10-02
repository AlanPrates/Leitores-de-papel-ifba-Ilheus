<?php
session_start();

// Se o usuário já estiver logado, redireciona para a área apropriada
if (!empty($_SESSION['admin_username'])) {
    header("Location: ../admin/index.php");
    exit;
} elseif (!empty($_SESSION['username'])) {
    header("Location: ../user/aluno.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Leitores de Papel</title>

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
            transition: transform 0.2s ease;
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
            font-size: 24px;
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

        .input-group .input-group-append .input-group-text {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: none;
            cursor: pointer;
            color: #64748b;
            transition: color 0.2s;
            user-select: none;
        }

        .input-group .input-group-append .input-group-text:hover {
            color: #dc2626;
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
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.35);
        }

        .login-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 22px 0;
            color: #94a3b8;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .login-divider span {
            padding: 0 12px;
        }

        .forgot-link {
            font-size: 12px;
            color: #dc2626;
            font-weight: 600;
            transition: color 0.2s;
            text-decoration: none;
        }

        .forgot-link:hover {
            color: #991b1b;
            text-decoration: underline;
        }

        .btn-create-account {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            color: #334155;
            font-weight: 700;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-create-account:hover {
            background-color: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
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
            <!-- Cabeçalho do Card de Login -->
            <div class="text-center mb-4">
                <div class="login-brand-icon mx-auto mb-3">
                    <i class="fa fa-book-reader text-danger"></i>
                </div>
                <h2 class="login-title">Leitores de Papel</h2>
                <p class="login-subtitle">Sistema de Gestão de Acervo e Empréstimos • IFBA</p>
            </div>

            <!-- Mensagens de Feedback -->
            <?php if (isset($_GET['success_message']) && !empty($_GET['success_message'])) { ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm text-left" role="alert">
                    <i class="fa fa-check-circle mr-2"></i><?php echo htmlspecialchars($_GET['success_message'], ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php } ?>

            <?php if (isset($_GET['error']) && !empty($_GET['error'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm text-left" role="alert">
                    <i class="fa fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php } ?>

            <?php if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) { ?>
                <div class="alert alert-info alert-dismissible fade show shadow-sm text-left" role="alert">
                    <i class="fa fa-info-circle mr-2"></i><?php echo htmlspecialchars($_SESSION['msg'], ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php unset($_SESSION['msg']); } ?>

            <!-- Formulário de Autenticação -->
            <form action="../actions/login.php" method="POST">
                <div class="form-group text-left">
                    <label for="usuario">Usuário ou Matrícula <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="usuario" name="username" placeholder="Digite seu login ou matrícula" required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="form-group text-left">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="senha" class="mb-0">Senha de Acesso <span class="text-danger">*</span></label>
                        <a href="recuperar_senha.php" class="forgot-link">Esqueceu a senha?</a>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" id="senha" name="password" placeholder="Digite sua senha" required autocomplete="current-password">
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="togglePassword()" title="Visualizar senha">
                                <i id="eye-icon" class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-action-primary btn-block mt-4">
                    <i class="fa fa-sign-in-alt mr-2"></i>Entrar no Sistema
                </button>
            </form>

            <div class="login-divider">
                <span>OU</span>
            </div>

            <!-- Ação de Cadastro -->
            <a href="cadastro.php" class="btn btn-create-account btn-block">
                <i class="fa fa-user-plus mr-2"></i>Criar Conta de Leitor
            </a>

            <!-- Rodapé Institucional do Card -->
            <div class="text-center mt-4 pt-3 border-top">
                <small class="text-muted">
                    <i class="fa fa-shield-alt text-danger mr-1"></i>Ambiente Seguro • IFBA Campus Ilhéus
                </small>
            </div>
        </div>
    </div>

    <!-- Scripts JavaScript -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
    <script>
        function togglePassword() {
            var campo = document.getElementById("senha");
            var icone = document.getElementById("eye-icon");
            if (campo.type === "password") {
                campo.type = "text";
                icone.classList.remove("fa-eye");
                icone.classList.add("fa-eye-slash");
            } else {
                campo.type = "password";
                icone.classList.remove("fa-eye-slash");
                icone.classList.add("fa-eye");
            }
        }
    </script>

    <?php include '../includes/footer.php'; ?>
</body>

</html>