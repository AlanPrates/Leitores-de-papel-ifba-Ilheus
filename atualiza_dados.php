<?php
// Inicia a sessão
session_start();
global $conn;
// Inclui o arquivo de conexão com o banco de dados
global $conn;
include 'conexao.php';

// Verifica se o usuário está autenticado
if (!isset($_SESSION['username'])) {
    // Redireciona para a página de login ou exibe uma mensagem de erro
    header('Location: login.php');
    exit();
}

// Obtém o nome de usuário a partir da sessão
$username = $_SESSION['username'];

// Inicializa as variáveis de mensagem de sucesso e erro
$success_message = "";
$error_message = "";

// Verifica se o formulário de atualização foi enviado
if (isset($_POST['password'])) {

    // Escapa os valores de entrada para evitar ataques de SQL Injection
    $password = $conn->real_escape_string($_POST['password']);
    $email = $conn->real_escape_string($_POST['email']);
    $categoria = $conn->real_escape_string($_POST['categoria']);
    $matricula = $conn->real_escape_string($_POST['matricula']);
    $sexo = $conn->real_escape_string($_POST['sexo']);
    $telefone = $conn->real_escape_string($_POST['telefone']);

    // Criptografa a senha usando a função password_hash
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Atualiza os dados do usuário existente
    $query = "UPDATE usuarios SET password='$hashedPassword', email='$email', categoria='$categoria', matricula='$matricula', sexo='$sexo', telefone='$telefone' WHERE username='$username'";
    $result = $conn->query($query);

    // Verifica se a atualização foi bem-sucedida ou exibe uma mensagem de erro
    if ($result) {
        $success_message = "Dados do usuário atualizados com sucesso!";
    } else {
        $error_message = "Erro ao atualizar os dados do usuário: " . $conn->error;
    }
}

// Seleciona os dados do usuário atual do banco de dados
$query = "SELECT * FROM usuarios WHERE username='$username'";
$result = $conn->query($query);

// Verifica se há resultados e atribui os valores aos campos do formulário
if ($result && $result->num_rows > 0) {
    $aluno = $result->fetch_assoc();
    $passwordValue = $aluno['password'];
    $emailValue = $aluno['email'];
    $categoriaValue = $aluno['categoria'];
    $matriculaValue = $aluno['matricula'];
    $sexoValue = $aluno['sexo'];
    $telefoneValue = $aluno['telefone'];
}

// Fecha a conexão com o banco de dados
$conn->close();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Atualizar Dados do Usuário</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/menu-mobile-css/style.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }
    </style>
</head>

<body>
    <header>
        <nav class="nav-bar">
            <div class="logo">
                <img class="cabecalho-imagem" src="assets/img/Fotoram.io.png" title="Sempre se atualizando constantemente" alt="LOGO ALAN" />
            </div>
            <div class="nav-list">
                <ul>
                    <li class="nav-item"><a href="index.php" class="nav-link">Criar conta de leitor</a></li>
                    <li class="nav-item"><a href="aluno.php" class="nav-link">Acessar minhas leituras</a></li>
                </ul>
            </div>

            <div class="mobile-menu-icon">
                <button onclick="menuShow()"><img class="icon" src="assets/img/menu_white_36dp.svg" alt=""></button>
            </div>
        </nav>
        <div class="mobile-menu">
            <ul>
                <li class="nav-item"><a href="index.php" class="nav-link">Criar conta de leitor</a></li>
                <li class="nav-item"><a href="aluno.php" class="nav-link">Acessar minhas leituras</a></li>
            </ul>
        </div>
    </header>
    <div class="container">
        <?php
        // Verifica se houve uma mensagem de erro
        if (isset($error_message)) {
            echo '<div class="alert alert-danger">' . $error_message . '</div>';
        }

        // Verifica se houve uma mensagem de sucesso
        if (isset($success_message)) {
            echo '<div class="alert alert-success">' . $success_message . '</div>';
        }
        ?>

        <br>
        <h2 class="text-black">Atualizar meus dados</h2>
        <h2><span style="color: red;"><?php echo $username; ?></span></h2>


        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="password">Digite sua Senha ou uma nova, para atualizar o seu cadastro:</label>
                <input type="password" class="form-control" name="password" value="" required>
            </div>


            <div class="form-group">
                <label for="email">Nova E-mail:</label>
                <input type="email" class="form-control" name="email" value="<?php echo isset($emailValue) ? $emailValue : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="categoria">Nova Categoria:</label>
                <select class="form-control" name="categoria" required>
                    <option value="Aluno" <?php echo isset($categoriaValue) && $categoriaValue == 'Aluno' ? 'selected' : ''; ?>>Aluno</option>
                    <option value="Professor" <?php echo isset($categoriaValue) && $categoriaValue == 'Professor' ? 'selected' : ''; ?>>Professor</option>
                    <option value="Funcionario" <?php echo isset($categoriaValue) && $categoriaValue == 'Funcionario' ? 'selected' : ''; ?>>Funcionário</option>
                </select>
            </div>

            <div class="form-group">
                <label for="matricula">Nova Matrícula:</label>
                <input type="text" class="form-control" name="matricula" value="<?php echo isset($matriculaValue) ? $matriculaValue : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="sexo">Novo Sexo:</label>
                <select class="form-control" name="sexo" required>
                    <option value="Masculino" <?php echo isset($sexoValue) && $sexoValue == 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                    <option value="Feminino" <?php echo isset($sexoValue) && $sexoValue == 'Feminino' ? 'selected' : ''; ?>>Feminino</option>
                    <option value="Outro" <?php echo isset($sexoValue) && $sexoValue == 'Outro' ? 'selected' : ''; ?>>Outro</option>
                </select>
            </div>

            <div class="form-group">
                <label for="telefone">Novo Telefone:</label>
                <input type="text" class="form-control" name="telefone" value="<?php echo isset($telefoneValue) ? $telefoneValue : ''; ?>" required>
            </div>

            <div class="btn-group-vertical">
                <button type="submit" class="btn btn-danger">Atualizar Dados</button>
                <br>
                <a href="aluno.php" class="btn btn-warning">Voltar para Painel de Usuário</a>
            </div>
        </form>
    </div>
    <script src="assets/js/script.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php
    // Inclui o rodapé
    include 'rodape.php';
    ?>
</body>

</html>