<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
global $conn;
include '../config/database.php';

$error_message = '';
$success_message = '';

// Verifica se o formulário de registro foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = trim($_POST['username']);
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
        $error_message = "Por favor, preencha todos os campos obrigatórios marcados com (*).";
    } elseif (!empty($confirmar_senha) && $password !== $confirmar_senha) {
        $error_message = "As senhas não coincidem. Por favor, digite novamente.";
    } elseif (strlen($password) < 6) {
        $error_message = "A senha deve conter no mínimo 6 caracteres.";
    } else {
        if ($conn) {
            // Verifica se o nome de usuário já existe na tabela 'usuarios' ou 'admin'
            $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE LOWER(username) = LOWER(?) UNION SELECT id FROM admin WHERE LOWER(admin_username) = LOWER(?)");
            $stmt_check->bind_param("ss", $username, $username);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $error_message = "Nome de usuário '{$username}' já está em uso. Por favor, escolha outro.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insere o novo usuário leitor
                $stmt_ins = $conn->prepare("INSERT INTO usuarios (username, nome, password, email, categoria, matricula, datanascimento, sexo, telefone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt_ins) {
                    $stmt_ins->bind_param("sssssssss", $username, $nome, $hashedPassword, $email, $categoria, $matricula, $datanascimento, $sexo, $telefone);
                    if ($stmt_ins->execute()) {
                        $success_message = "Cadastro realizado com sucesso! Redirecionando para o login...";
                        echo '<script>
                                setTimeout(function() {
                                  window.location.href = "index.php?success_message=" + encodeURIComponent("Cadastro realizado com sucesso! Só logar...");
                                }, 1500);
                              </script>';
                    } else {
                        $error_message = "Erro ao registrar o usuário: " . $stmt_ins->error;
                    }
                    $stmt_ins->close();
                } else {
                    $error_message = "Erro ao preparar o cadastro.";
                }
            }
            $stmt_check->close();
        } else {
            $error_message = "Erro de conexão com o banco de dados.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Leitor - Leitores de Papel</title>
    
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

        .public-form-wrapper {
            width: 95% !important;
            max-width: 960px !important;
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

        .btn-action-primary {
            background-color: #dc2626;
            border-color: #dc2626;
            color: #ffffff;
            font-weight: 700;
            padding: 11px 24px;
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

        @media (max-width: 768px) {
            .public-form-wrapper {
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

    <div class="public-form-wrapper">
        <!-- Header da Página -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap pb-3 border-bottom">
            <div class="d-flex align-items-center">
                <div class="icon-avatar mr-3">
                    <i class="fa fa-user-plus text-danger"></i>
                </div>
                <div>
                    <h2 class="mb-0 font-weight-bold" style="color: #1e293b; font-size: 22px;">Cadastro de Leitor</h2>
                    <span class="text-muted small">Crie sua conta para solicitar empréstimos de livros e acompanhar leituras</span>
                </div>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="index.php" class="btn btn-outline-secondary btn-sm shadow-sm font-weight-bold">
                    <i class="fa fa-sign-in-alt mr-1"></i> Já tenho conta
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

        <form method="POST" action="cadastro.php" id="form-cadastro-leitor">
            <!-- Seção 1: Dados Pessoais -->
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
                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite seu nome completo" required>
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
                    <label for="categoria">Categoria <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-id-badge"></i></span>
                        </div>
                        <select class="form-control" id="categoria" name="categoria" required>
                            <option value="" disabled selected>Selecione...</option>
                            <option value="aluno">Aluno</option>
                            <option value="aluno">Bolsista</option>
                            <option value="professor">Professor</option>
                            <option value="funcionario">Funcionário</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label for="matricula">Matrícula ou SIAPE <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-fingerprint"></i></span>
                        </div>
                        <input type="text" class="form-control" id="matricula" name="matricula" placeholder="Ex: 20261001" required>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-7">
                    <label for="email">E-mail Institucional ou Pessoal <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                        </div>
                        <input type="email" class="form-control" id="email" name="email" placeholder="seuemail@exemplo.com" required>
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

            <!-- Seção 2: Credenciais de Acesso -->
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
                        <input type="text" class="form-control" id="usuario" name="username" placeholder="Ex: joao.silva" required autocomplete="off">
                    </div>
                    <div id="verificar-usuario" class="status-feedback neutral">
                        <small class="text-muted"><i class="fa fa-info-circle mr-1"></i>Mínimo 3 caracteres</small>
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label for="senha">Senha <span class="text-danger">*</span></label>
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

            <!-- Seção 3: Termos de Uso -->
            <div class="terms-card">
                <div class="d-flex align-items-start">
                    <i class="fa fa-book-reader text-danger mt-1 mr-3" style="font-size: 20px;"></i>
                    <div class="flex-grow-1">
                        <strong class="d-block text-dark mb-1" style="font-size: 14px;">Termo de Compromisso e Uso da Biblioteca</strong>
                        <p class="text-muted small mb-2">
                            Ao cadastrar-se no Leitores de Papel do IFBA - Campus Ilhéus, você se compromete a zelar pelos livros emprestados, respeitar os prazos de devolução e cumprir o regulamento institucional da biblioteca.
                        </p>
                        <div class="form-check d-flex align-items-center mb-0">
                            <input class="form-check-input mt-0 mr-2" type="checkbox" id="aceitar-termos" required style="width: 17px; height: 17px; cursor: pointer;">
                            <label class="form-check-label text-dark font-weight-bold mb-0 small" for="aceitar-termos" style="cursor: pointer;">
                                Li e concordo com os termos de uso e regulamento da biblioteca. <span class="text-danger">*</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="row mt-4 pt-2">
                <div class="col-md-8 mb-2">
                    <button type="submit" class="btn btn-action-primary btn-block">
                        <i class="fa fa-check-circle mr-2"></i>Concluir Cadastro
                    </button>
                </div>
                <div class="col-md-4 mb-2">
                    <a href="index.php" class="btn btn-outline-secondary btn-block py-2 font-weight-bold">
                        <i class="fa fa-arrow-left mr-1"></i>Ir para o Login
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/script.js"></script>

    <script>
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
                feedback.innerHTML = '<small><i class="fa fa-check-circle mr-1"></i>Senhas conferem!</small>';
            } else {
                feedback.className = "status-feedback error";
                feedback.innerHTML = '<small><i class="fa fa-times-circle mr-1"></i>As senhas não coincidem!</small>';
            }
        }

        document.getElementById('senha').addEventListener('input', verificarSenhas);
        document.getElementById('confirmar-senha').addEventListener('input', verificarSenhas);

        // Verificação de usuário via AJAX
        var timerUsuario = null;
        document.getElementById('usuario').addEventListener('input', function() {
            var username = this.value.trim();
            var feedbackDiv = document.getElementById('verificar-usuario');

            clearTimeout(timerUsuario);

            if (username.length < 3) {
                feedbackDiv.className = "status-feedback neutral";
                feedbackDiv.innerHTML = '<small class="text-muted"><i class="fa fa-info-circle mr-1"></i>Mínimo 3 caracteres</small>';
                return;
            }

            feedbackDiv.className = "status-feedback neutral";
            feedbackDiv.innerHTML = '<small class="text-muted"><i class="fa fa-spinner fa-spin mr-1"></i>Verificando...</small>';

            timerUsuario = setTimeout(function() {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "verificar_usuario.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        if (xhr.responseText === "false") {
                            feedbackDiv.className = "status-feedback error";
                            feedbackDiv.innerHTML = '<small><i class="fa fa-times-circle mr-1"></i>Nome de usuário já existe!</small>';
                        } else {
                            feedbackDiv.className = "status-feedback ok";
                            feedbackDiv.innerHTML = '<small><i class="fa fa-check-circle mr-1"></i>Usuário disponível!</small>';
                        }
                    }
                };
                xhr.send("username=" + encodeURIComponent(username));
            }, 350);
        });

        document.getElementById('form-cadastro-leitor').addEventListener('submit', function(e) {
            var s1 = document.getElementById('senha').value;
            var s2 = document.getElementById('confirmar-senha').value;

            if (s1 !== s2) {
                e.preventDefault();
                alert('Atenção: A confirmação de senha não coincide com a senha informada.');
                document.getElementById('confirmar-senha').focus();
            }
        });
    </script>

    <?php include '../includes/footer.php'; ?>
</body>

</html>