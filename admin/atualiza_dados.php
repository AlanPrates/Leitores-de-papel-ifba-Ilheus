<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

require_admin('../public/index.php');

$username = $_SESSION['admin_username'];
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $password = $_POST['password'];
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
    $matricula = isset($_POST['matricula']) ? trim($_POST['matricula']) : '';
    $sexo = isset($_POST['sexo']) ? trim($_POST['sexo']) : '';
    $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';

    if (empty($password)) {
        $error_message = "Por favor, digite sua senha atual ou uma nova senha para confirmar as alterações.";
    } elseif (strlen($password) < 6) {
        $error_message = "A senha deve conter no mínimo 6 caracteres.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt_upd = $conn->prepare("UPDATE admin SET password = ?, email = ?, categoria = ?, matricula = ?, sexo = ?, telefone = ? WHERE admin_username = ?");
        if ($stmt_upd) {
            $stmt_upd->bind_param("sssssss", $hashedPassword, $email, $categoria, $matricula, $sexo, $telefone, $username);
            if ($stmt_upd->execute()) {
                $success_message = "Dados administrativos atualizados com sucesso!";
            } else {
                $error_message = "Erro ao atualizar os dados no banco de dados.";
            }
            $stmt_upd->close();
        } else {
            $error_message = "Erro ao preparar a atualização dos dados.";
        }
    }
}

$emailValue = '';
$categoriaValue = '';
$matriculaValue = '';
$sexoValue = '';
$telefoneValue = '';

$stmt_sel = $conn->prepare("SELECT email, categoria, matricula, sexo, telefone FROM admin WHERE admin_username = ? LIMIT 1");
if ($stmt_sel) {
    $stmt_sel->bind_param("s", $username);
    $stmt_sel->execute();
    $result = $stmt_sel->get_result();
    if ($result && $result->num_rows > 0) {
        $aluno = $result->fetch_assoc();
        $emailValue = $aluno['email'];
        $categoriaValue = $aluno['categoria'];
        $matriculaValue = $aluno['matricula'];
        $sexoValue = $aluno['sexo'];
        $telefoneValue = $aluno['telefone'];
    }
    $stmt_sel->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Atualizar Meus Dados - Administração</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

        .profile-form-wrapper {
            width: 95% !important;
            max-width: 920px !important;
            margin: 35px auto !important;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 35px 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        .icon-avatar {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.12), rgba(239, 68, 68, 0.05));
            border: 1px solid rgba(220, 38, 38, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .section-header {
            font-size: 14px;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 25px;
            margin-bottom: 18px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f1f5f9;
            display: flex;
            align-items: center;
        }

        .section-header i {
            color: #dc2626;
            margin-right: 8px;
            font-size: 16px;
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
            min-width: 42px;
            justify-content: center;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            border-left: 1px solid #dc2626;
        }

        .form-group .form-control {
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            font-size: 14px;
            height: auto;
            transition: all 0.2s ease-in-out;
            color: #1e293b;
        }

        .form-group .form-control:focus {
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

        .badge-user {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 13px;
        }

        .notice-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #475569;
        }

        .btn-action-primary {
            background-color: #dc2626;
            border-color: #dc2626;
            color: #ffffff;
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
            transition: all 0.2s;
        }

        .btn-action-primary:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-action-secondary {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #1e293b;
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-action-secondary:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            color: #0f172a;
        }

        @media (max-width: 768px) {
            .profile-form-wrapper {
                width: 100% !important;
                margin: 15px auto !important;
                padding: 22px 16px;
                border-radius: 8px;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="profile-form-wrapper">
        <!-- Topo com Ícone e Identificação -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap pb-3 border-bottom">
            <div class="d-flex align-items-center">
                <div class="icon-avatar mr-3">
                    <i class="fa fa-user-cog text-danger"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center flex-wrap">
                        <h2 class="mb-0 mr-2 font-weight-bold" style="color: #1e293b; font-size: 22px;">Atualizar Meus Dados</h2>
                        <span class="badge-user"><i class="fa fa-shield-alt mr-1"></i><?php echo htmlspecialchars($username); ?></span>
                    </div>
                    <span class="text-muted small">Gerencie suas informações cadastrais e credenciais de acesso de administrador</span>
                </div>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="index.php" class="btn btn-warning btn-sm shadow-sm font-weight-bold text-dark">
                    <i class="fa fa-arrow-left mr-1"></i> Painel Admin
                </a>
            </div>
        </div>

        <?php if (!empty($error_message)) { ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>

        <?php if (!empty($success_message)) { ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa fa-check-circle mr-2"></i><?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>

        <form method="POST" action="atualiza_dados.php">
            <!-- Seção 1: Dados Pessoais e Institucionais -->
            <div class="section-header">
                <i class="fa fa-id-card"></i> 1. Dados Pessoais e Institucionais
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="matricula">Matrícula ou SIAPE <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-fingerprint"></i></span>
                        </div>
                        <input type="text" class="form-control" id="matricula" name="matricula"
                            value="<?php echo htmlspecialchars($matriculaValue); ?>" required>
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label for="categoria">Categoria Institucional <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-id-badge"></i></span>
                        </div>
                        <select class="form-control" id="categoria" name="categoria" required>
                            <option value="Funcionario" <?php echo (strcasecmp($categoriaValue, 'Funcionario') === 0 || strcasecmp($categoriaValue, 'funcionario') === 0) ? 'selected' : ''; ?>>Funcionário</option>
                            <option value="Professor" <?php echo (strcasecmp($categoriaValue, 'Professor') === 0 || strcasecmp($categoriaValue, 'professor') === 0) ? 'selected' : ''; ?>>Professor</option>
                            <option value="Aluno" <?php echo (strcasecmp($categoriaValue, 'Aluno') === 0 || strcasecmp($categoriaValue, 'aluno') === 0) ? 'selected' : ''; ?>>Bolsista / Aluno</option>
                            <option value="administrador" <?php echo (strcasecmp($categoriaValue, 'administrador') === 0 || strcasecmp($categoriaValue, 'admin') === 0) ? 'selected' : ''; ?>>Administrador Geral</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label for="sexo">Sexo <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-venus-mars"></i></span>
                        </div>
                        <select class="form-control" id="sexo" name="sexo" required>
                            <option value="Masculino" <?php echo (strcasecmp($sexoValue, 'Masculino') === 0 || strcasecmp($sexoValue, 'masculino') === 0) ? 'selected' : ''; ?>>Masculino</option>
                            <option value="Feminino" <?php echo (strcasecmp($sexoValue, 'Feminino') === 0 || strcasecmp($sexoValue, 'feminino') === 0) ? 'selected' : ''; ?>>Feminino</option>
                            <option value="Outro" <?php echo (strcasecmp($sexoValue, 'Outro') === 0 || strcasecmp($sexoValue, 'outro') === 0) ? 'selected' : ''; ?>>Outro</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-7">
                    <label for="email">E-mail Institucional <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                        </div>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?php echo htmlspecialchars($emailValue); ?>" required>
                    </div>
                </div>

                <div class="form-group col-md-5">
                    <label for="telefone">Telefone / WhatsApp <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-phone"></i></span>
                        </div>
                        <input type="tel" class="form-control" id="telefone" name="telefone"
                            value="<?php echo htmlspecialchars($telefoneValue); ?>" required>
                    </div>
                </div>
            </div>

            <!-- Seção 2: Confirmação de Segurança -->
            <div class="section-header mt-3">
                <i class="fa fa-shield-alt"></i> 2. Segurança e Confirmação de Senha
            </div>

            <div class="notice-card">
                <i class="fa fa-info-circle text-warning mr-1"></i>
                Para salvar as alterações no seu perfil administrativo, confirme sua senha atual ou digite uma nova senha para atualizá-la.
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="password">Senha de Confirmação ou Nova Senha <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Digite sua senha (mínimo 6 dígitos)" required>
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="alternarSenha()" title="Visualizar senha">
                                <i id="icone-senha" class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="row mt-4 pt-2">
                <div class="col-md-6 mb-2">
                    <button type="submit" class="btn btn-action-primary btn-block">
                        <i class="fa fa-save mr-2"></i>Salvar Alterações
                    </button>
                </div>
                <div class="col-md-6 mb-2">
                    <a href="index.php" class="btn btn-action-secondary btn-block">
                        <i class="fa fa-arrow-left mr-1"></i>Voltar ao Painel Admin
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/script.js"></script>
    <script>
        function alternarSenha() {
            var campo = document.getElementById("password");
            var icone = document.getElementById("icone-senha");
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