<?php
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';

// Apenas administradores podem cadastrar novos administradores
require_admin('../public/index.php');

$msg_feedback = '';
$msg_tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_username']) && isset($_POST['password'])) {
    $username = trim($_POST['admin_username']);
    $nome = trim($_POST['nome']);
    $password = $_POST['password'];
    $confirmar_senha = isset($_POST['confirmar_senha']) ? $_POST['confirmar_senha'] : (isset($_POST['confirmar-senha']) ? $_POST['confirmar-senha'] : '');
    $email = trim($_POST['email']);
    $categoria = trim($_POST['categoria']);
    $matricula = trim($_POST['matricula']);
    $datanascimento = trim($_POST['datanascimento']);
    $sexo = trim($_POST['sexo']);
    $telefone = trim($_POST['telefone']);

    if (empty($username) || empty($password) || empty($nome) || empty($email) || empty($matricula)) {
        $msg_feedback = "Por favor, preencha todos os campos obrigatórios marcados com (*).";
        $msg_tipo = "danger";
    } elseif (!empty($confirmar_senha) && $password !== $confirmar_senha) {
        $msg_feedback = "As senhas não coincidem. Por favor, verifique e tente novamente.";
        $msg_tipo = "danger";
    } elseif (strlen($password) < 6) {
        $msg_feedback = "A senha deve conter no mínimo 6 caracteres por motivos de segurança.";
        $msg_tipo = "danger";
    } else {
        // Verifica se o usuário já existe na tabela de admin ou de usuários comuns
        $stmt_check = $conn->prepare("SELECT id FROM admin WHERE LOWER(admin_username) = LOWER(?) UNION SELECT id FROM usuarios WHERE LOWER(username) = LOWER(?)");
        $stmt_check->bind_param("ss", $username, $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $msg_feedback = "O nome de usuário '{$username}' já está em uso. Por favor, escolha outro.";
            $msg_tipo = "danger";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt_insert = $conn->prepare("INSERT INTO admin (admin_username, nome, password, email, categoria, matricula, datanascimento, sexo, telefone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("sssssssss", $username, $nome, $hashedPassword, $email, $categoria, $matricula, $datanascimento, $sexo, $telefone);

            if ($stmt_insert->execute()) {
                $msg_feedback = "Administrador '{$nome}' cadastrado com sucesso com permissões completas!";
                $msg_tipo = "success";
            } else {
                $msg_feedback = "Erro ao registrar o administrador no banco de dados.";
                $msg_tipo = "danger";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Administrador - Leitores de Papel</title>

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

        .admin-form-wrapper {
            width: 95% !important;
            max-width: 980px !important;
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

        .terms-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #dc2626;
            border-radius: 8px;
            padding: 18px 20px;
            margin: 25px 0 20px;
        }

        .terms-card p {
            font-size: 13px;
            color: #475569;
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .status-feedback {
            font-size: 12px;
            margin-top: 5px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .status-feedback.ok {
            color: #16a34a;
        }

        .status-feedback.error {
            color: #dc2626;
        }

        .status-feedback.neutral {
            color: #64748b;
        }

        .status-feedback i {
            margin-right: 4px;
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
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.35);
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
            .admin-form-wrapper {
                width: 100% !important;
                margin: 15px auto !important;
                padding: 22px 16px;
                border-radius: 8px;
            }

            .header-actions {
                width: 100%;
                margin-top: 15px;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .header-actions .btn {
                width: 100%;
                margin-right: 0 !important;
                margin-bottom: 6px;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="admin-form-wrapper">
        <!-- Topo da Página com Ícone e Ações -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap pb-3 border-bottom">
            <div class="d-flex align-items-center">
                <div class="icon-avatar mr-3">
                    <i class="fa fa-user-shield text-danger"></i>
                </div>
                <div>
                    <h2 class="mb-0 font-weight-bold" style="color: #1e293b; font-size: 22px;">Cadastrar Novo Administrador</h2>
                    <span class="text-muted small">Crie credenciais e permissões de superusuário para a equipe da biblioteca</span>
                </div>
            </div>
            <div class="header-actions mt-3 mt-md-0">
                <a href="lista.php" class="btn btn-outline-secondary btn-sm mr-2 shadow-sm font-weight-bold">
                    <i class="fa fa-users-cog mr-1"></i> Lista de Administradores
                </a>
                <a href="index.php" class="btn btn-warning btn-sm shadow-sm font-weight-bold text-dark">
                    <i class="fa fa-arrow-left mr-1"></i> Painel Admin
                </a>
            </div>
        </div>

        <?php if (!empty($msg_feedback)) { ?>
            <div class="alert alert-<?php echo htmlspecialchars($msg_tipo); ?> alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa <?php echo ($msg_tipo === 'success') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-2"></i>
                <?php echo htmlspecialchars($msg_feedback); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>

        <form action="cadastro.php" method="POST" id="form-cadastro-admin">
            <!-- Seção 1: Dados Pessoais e Identificação -->
            <div class="section-header">
                <i class="fa fa-id-card"></i> 1. Dados Pessoais e Identificação
            </div>

            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="nome">Nome Completo <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o nome completo do administrador" required>
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label for="sexo">Sexo <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-venus-mars"></i></span>
                        </div>
                        <select class="form-control" id="sexo" name="sexo" required>
                            <option value="">Selecione...</option>
                            <option value="masculino">Masculino</option>
                            <option value="feminino">Feminino</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="data-nascimento">Data de Nascimento <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                        </div>
                        <input type="date" class="form-control" id="data-nascimento" name="datanascimento" required>
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label for="categoria">Categoria Institucional <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-id-badge"></i></span>
                        </div>
                        <select class="form-control" id="categoria" name="categoria" required>
                            <option value="" disabled selected>Selecione...</option>
                            <option value="funcionario">Funcionário / Técnico</option>
                            <option value="professor">Professor / Docente</option>
                            <option value="aluno">Bolsista de Biblioteca</option>
                            <option value="administrador">Administrador Geral</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label for="matricula">Matrícula ou SIAPE <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-fingerprint"></i></span>
                        </div>
                        <input type="text" class="form-control" id="matricula" name="matricula" placeholder="Ex: 20261099" required>
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
                        <input type="email" class="form-control" id="email" name="email" placeholder="usuario@ifba.edu.br" required>
                    </div>
                </div>

                <div class="form-group col-md-5">
                    <label for="telefone">Telefone / WhatsApp <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-phone"></i></span>
                        </div>
                        <input type="tel" class="form-control" id="telefone" name="telefone" placeholder="(73) 99999-9999" required>
                    </div>
                </div>
            </div>

            <!-- Seção 2: Credenciais de Acesso e Segurança -->
            <div class="section-header mt-3">
                <i class="fa fa-key"></i> 2. Credenciais de Acesso ao Sistema
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="usuario">Nome de Usuário (Login) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-at"></i></span>
                        </div>
                        <input type="text" class="form-control" id="usuario" name="admin_username" placeholder="Ex: admin.joao" required autocomplete="off">
                    </div>
                    <div id="verificar-usuario" class="status-feedback neutral">
                        <small class="text-muted"><i class="fa fa-info-circle mr-1"></i>Mínimo 3 caracteres alfanuméricos</small>
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label for="senha">Senha de Acesso <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" id="senha" name="password" placeholder="Mínimo 6 caracteres" required>
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="alternarVisibilidadeSenha('senha', 'icone-senha')" title="Visualizar senha">
                                <i id="icone-senha" class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <div id="forca-senha" class="status-feedback neutral">
                        <small class="text-muted"><i class="fa fa-shield-alt mr-1"></i>Use letras e números</small>
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label for="confirmar-senha">Confirmar Senha <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-check-double"></i></span>
                        </div>
                        <input type="password" class="form-control" id="confirmar-senha" name="confirmar_senha" placeholder="Repita a senha" required>
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="alternarVisibilidadeSenha('confirmar-senha', 'icone-confirmar-senha')" title="Visualizar senha">
                                <i id="icone-confirmar-senha" class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <div id="feedback-confirmar-senha" class="status-feedback neutral">
                        <small class="text-muted"><i class="fa fa-circle mr-1"></i>Aguardando confirmação...</small>
                    </div>
                </div>
            </div>

            <!-- Seção 3: Termo de Responsabilidade Administrativa -->
            <div class="terms-card">
                <div class="d-flex align-items-start">
                    <i class="fa fa-user-shield text-danger mt-1 mr-3" style="font-size: 20px;"></i>
                    <div class="flex-grow-1">
                        <strong class="d-block text-dark mb-1" style="font-size: 14px;">Termo de Responsabilidade e Segurança do Sistema</strong>
                        <p>
                            O usuário cadastrado terá acesso como <strong>Superusuário / Administrador</strong> com permissões totais para inclusão, edição e exclusão de acervo de livros, visualização de históricos confidenciais de alunos e controle de empréstimos do IFBA - Campus Ilhéus.
                        </p>
                        <div class="form-check d-flex align-items-center mb-0 mt-2">
                            <input class="form-check-input mt-0 mr-2" type="checkbox" id="aceitar-termos" required style="width: 17px; height: 17px; cursor: pointer;">
                            <label class="form-check-label text-dark font-weight-bold mb-0 small" for="aceitar-termos" style="cursor: pointer;">
                                Declaro estar ciente dos privilégios administrativos concedidos e concordo com as normas de uso institucional. <span class="text-danger">*</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação com Ícones e Design Moderno -->
            <div class="row mt-4 pt-2">
                <div class="col-md-6 mb-2">
                    <button type="submit" class="btn btn-action-primary btn-block">
                        <i class="fa fa-user-plus mr-2"></i>Cadastrar Administrador
                    </button>
                </div>
                <div class="col-md-3 mb-2">
                    <button type="reset" class="btn btn-outline-secondary btn-block py-2 font-weight-bold" onclick="resetFeedbacks()">
                        <i class="fa fa-eraser mr-1"></i>Limpar
                    </button>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="index.php" class="btn btn-action-secondary btn-block">
                        <i class="fa fa-arrow-left mr-1"></i>Painel Admin
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/script.js"></script>

    <script>
        // Alternância de visibilidade de senha
        function alternarVisibilidadeSenha(campoId, iconeId) {
            var campo = document.getElementById(campoId);
            var icone = document.getElementById(iconeId);
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

        // Validação em tempo real da confirmação de senha
        function verificarSenhas() {
            var s1 = document.getElementById('senha').value;
            var s2 = document.getElementById('confirmar-senha').value;
            var feedback = document.getElementById('feedback-confirmar-senha');

            if (!s2) {
                feedback.className = "status-feedback neutral";
                feedback.innerHTML = '<small class="text-muted"><i class="fa fa-circle mr-1"></i>Aguardando confirmação...</small>';
                return;
            }

            if (s1 === s2) {
                feedback.className = "status-feedback ok";
                feedback.innerHTML = '<small><i class="fa fa-check-circle mr-1"></i>Senhas conferem perfeitamente!</small>';
            } else {
                feedback.className = "status-feedback error";
                feedback.innerHTML = '<small><i class="fa fa-times-circle mr-1"></i>As senhas não coincidem!</small>';
            }
        }

        document.getElementById('senha').addEventListener('input', verificarSenhas);
        document.getElementById('confirmar-senha').addEventListener('input', verificarSenhas);

        // Verificação em tempo real da disponibilidade do nome de usuário
        var timerUsuario = null;
        document.getElementById('usuario').addEventListener('input', function() {
            var username = this.value.trim();
            var feedbackDiv = document.getElementById('verificar-usuario');

            clearTimeout(timerUsuario);

            if (username.length < 3) {
                feedbackDiv.className = "status-feedback neutral";
                feedbackDiv.innerHTML = '<small class="text-muted"><i class="fa fa-info-circle mr-1"></i>Mínimo 3 caracteres alfanuméricos</small>';
                return;
            }

            feedbackDiv.className = "status-feedback neutral";
            feedbackDiv.innerHTML = '<small class="text-muted"><i class="fa fa-spinner fa-spin mr-1"></i>Verificando disponibilidade...</small>';

            timerUsuario = setTimeout(function() {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "verificar_usuario.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        try {
                            var resp = JSON.parse(xhr.responseText);
                            if (resp.disponivel) {
                                feedbackDiv.className = "status-feedback ok";
                                feedbackDiv.innerHTML = '<small><i class="fa fa-check-circle mr-1"></i>' + resp.mensagem + '</small>';
                            } else {
                                feedbackDiv.className = "status-feedback error";
                                feedbackDiv.innerHTML = '<small><i class="fa fa-times-circle mr-1"></i>' + resp.mensagem + '</small>';
                            }
                        } catch (e) {
                            feedbackDiv.className = "status-feedback neutral";
                            feedbackDiv.innerHTML = '';
                        }
                    }
                };
                xhr.send("username=" + encodeURIComponent(username));
            }, 350);
        });

        // Limpeza dos feedbacks ao clicar em limpar
        function resetFeedbacks() {
            document.getElementById('feedback-confirmar-senha').className = "status-feedback neutral";
            document.getElementById('feedback-confirmar-senha').innerHTML = '<small class="text-muted"><i class="fa fa-circle mr-1"></i>Aguardando confirmação...</small>';
            document.getElementById('verificar-usuario').className = "status-feedback neutral";
            document.getElementById('verificar-usuario').innerHTML = '<small class="text-muted"><i class="fa fa-info-circle mr-1"></i>Mínimo 3 caracteres alfanuméricos</small>';
        }

        // Validação final antes de submeter
        document.getElementById('form-cadastro-admin').addEventListener('submit', function(e) {
            var s1 = document.getElementById('senha').value;
            var s2 = document.getElementById('confirmar-senha').value;

            if (s1 !== s2) {
                e.preventDefault();
                alert('Atenção: A confirmação de senha não coincide com a senha informada. Por favor, verifique.');
                document.getElementById('confirmar-senha').focus();
                return false;
            }

            if (s1.length < 6) {
                e.preventDefault();
                alert('Atenção: A senha deve possuir pelo menos 6 caracteres.');
                document.getElementById('senha').focus();
                return false;
            }
        });
    </script>

    <?php include '../includes/footer.php'; ?>
</body>

</html>