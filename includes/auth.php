<?php
/**
 * Helper de Autenticação e Controle de Acesso (RBAC)
 * Compatível com PHP 5.6 e PHP 8.2+
 */

if (session_status() == PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

/**
 * Exige que o usuário esteja autenticado (leitor ou administrador)
 */
function require_login($redirectUrl = '../public/index.php') {
    $isLogged = !empty($_SESSION['user_id']) || !empty($_SESSION['username']) || !empty($_SESSION['admin_username']);
    if (!$isLogged) {
        header("Location: " . $redirectUrl);
        exit;
    }
}

/**
 * Exige que o usuário possua perfil de administrador
 */
function require_admin($redirectUrl = '../public/index.php') {
    $isAdmin = !empty($_SESSION['admin_username']) || (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true);
    if (!$isAdmin) {
        header("Location: " . $redirectUrl . "?error=" . urlencode("Acesso restrito a administradores."));
        exit;
    }
}
