<?php
// Garantir que a sessão esteja iniciada
if (session_status() == PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

// Obter o nome do usuário logado
$nomeUsuarioHeader = '';
if (!empty($_SESSION['nome'])) {
    $nomeUsuarioHeader = $_SESSION['nome'];
} elseif (!empty($_COOKIE['nome'])) {
    $nomeUsuarioHeader = $_COOKIE['nome'];
}

$isUsuarioLogado = !empty($_SESSION['user_id']) || !empty($_SESSION['username']) || !empty($_SESSION['admin_username']);
$isAdminUser = !empty($_SESSION['admin_username']) || (isset($_SESSION['is_admin']) && $_SESSION['is_admin']);

// Se estiver logado e ainda não tem o nome carregado na sessão, busca no banco
if ($isUsuarioLogado && empty($nomeUsuarioHeader)) {
    if (!isset($conn) || !$conn) {
        @include_once dirname(__FILE__) . '/../config/database.php';
    }
    if (isset($conn) && $conn) {
        if (!empty($_SESSION['admin_username'])) {
            $u = $conn->real_escape_string($_SESSION['admin_username']);
            $r = $conn->query("SELECT nome FROM admin WHERE admin_username='$u' LIMIT 1");
            if ($r && $row = $r->fetch_assoc()) {
                if (!empty($row['nome'])) {
                    $nomeUsuarioHeader = $row['nome'];
                    $_SESSION['nome'] = $row['nome'];
                }
            }
        } elseif (!empty($_SESSION['username'])) {
            $u = $conn->real_escape_string($_SESSION['username']);
            $r = $conn->query("SELECT nome FROM usuarios WHERE username='$u' LIMIT 1");
            if ($r && $row = $r->fetch_assoc()) {
                if (!empty($row['nome'])) {
                    $nomeUsuarioHeader = $row['nome'];
                    $_SESSION['nome'] = $row['nome'];
                }
            }
        } elseif (!empty($_SESSION['user_id'])) {
            $uid = (int)$_SESSION['user_id'];
            $r = $conn->query("SELECT nome FROM usuarios WHERE id=$uid LIMIT 1");
            if ($r && $row = $r->fetch_assoc()) {
                if (!empty($row['nome'])) {
                    $nomeUsuarioHeader = $row['nome'];
                    $_SESSION['nome'] = $row['nome'];
                }
            }
        }
    }
}

// Fallback final para username se não houver nome
if (empty($nomeUsuarioHeader)) {
    if (!empty($_SESSION['username'])) {
        $nomeUsuarioHeader = $_SESSION['username'];
    } elseif (!empty($_SESSION['admin_username'])) {
        $nomeUsuarioHeader = $_SESSION['admin_username'];
    }
}

// Detecta o diretório atual para caminhos relativos
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
$baseRelativa = ($currentDir === 'public' || $currentDir === 'admin' || $currentDir === 'user' || $currentDir === 'actions') ? '../' : './';

$logoLink = ($currentDir === 'admin') ? 'index.php' : (($currentDir === 'user') ? 'aluno.php' : ($isUsuarioLogado ? ($isAdminUser ? $baseRelativa . 'admin/index.php' : $baseRelativa . 'user/aluno.php') : $baseRelativa . 'public/index.php'));
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<header>
    <nav class="nav-bar">
        <div class="logo">
            <a href="<?php echo $logoLink; ?>">
                <img class="cabecalho-imagem" src="<?php echo $baseRelativa; ?>assets/img/Fotoram.io.png"
                    title="Sempre se atualizando constantemente" alt="LOGO ALAN" />
            </a>
        </div>

        <div class="nav-list">
            <ul>
                <?php if ($isUsuarioLogado && !empty($nomeUsuarioHeader)) { ?>
                    <li class="nav-item">
                        <span class="nav-link user-greeting" style="color: #fff; font-weight: 600;">
                            Olá, <?php echo htmlspecialchars($nomeUsuarioHeader); ?>
                        </span>
                    </li>
                <?php } ?>

                <?php if (!$isUsuarioLogado) { ?>
                    <li class="nav-item">
                        <a href="<?php echo ($currentDir === 'public') ? 'cadastro.php' : $baseRelativa . 'public/cadastro.php'; ?>" class="nav-link">Criar conta de leitor</a>
                    </li>
                <?php } ?>

                <?php if ($currentDir === 'admin') { ?>
                    <li class="nav-item"><a href="lista_livros.php" class="nav-link">Lista de Livros</a></li>
                <?php } else { ?>
                    <li class="nav-item"><a href="<?php echo ($currentDir === 'user') ? 'minhas_leituras.php' : $baseRelativa . 'user/minhas_leituras.php'; ?>" class="nav-link">Acessar minhas leituras</a></li>
                <?php } ?>

                <?php if ($isUsuarioLogado) { ?>
                    <li class="nav-item">
                        <a href="<?php echo $baseRelativa; ?>actions/logout.php" class="nav-link" style="color: #ffeb3b; font-weight: 500;">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <div class="mobile-menu-icon">
            <button onclick="menuShow()"><img class="icon" src="<?php echo $baseRelativa; ?>assets/img/menu_white_36dp.svg" alt="Menu"></button>
        </div>
    </nav>

    <div class="mobile-menu">
        <ul>
            <?php if ($isUsuarioLogado && !empty($nomeUsuarioHeader)) { ?>
                <li class="nav-item">
                    <span class="nav-link user-greeting" style="color: #fff; font-weight: 600;">
                        Olá, <?php echo htmlspecialchars($nomeUsuarioHeader); ?>
                    </span>
                </li>
            <?php } ?>

            <?php if (!$isUsuarioLogado) { ?>
                <li class="nav-item">
                    <a href="<?php echo ($currentDir === 'public') ? 'cadastro.php' : $baseRelativa . 'public/cadastro.php'; ?>" class="nav-link">Criar conta de leitor</a>
                </li>
            <?php } ?>

            <?php if ($currentDir === 'admin') { ?>
                <li class="nav-item"><a href="lista_livros.php" class="nav-link">Lista de Livros</a></li>
            <?php } else { ?>
                <li class="nav-item"><a href="<?php echo ($currentDir === 'user') ? 'minhas_leituras.php' : $baseRelativa . 'user/minhas_leituras.php'; ?>" class="nav-link">Acessar minhas leituras</a></li>
            <?php } ?>

            <?php if ($isUsuarioLogado) { ?>
                <li class="nav-item">
                    <a href="<?php echo $baseRelativa; ?>actions/logout.php" class="nav-link" style="color: #ffeb3b; font-weight: 500;">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
</header>

