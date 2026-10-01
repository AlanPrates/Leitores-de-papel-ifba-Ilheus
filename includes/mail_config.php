<?php
/**
 * Helper de Configuração de E-mail / SMTP
 * Evita credenciais hardcoded no repositório.
 */

function get_smtp_config() {
    $configFile = dirname(__FILE__) . '/../config/secrets.php';
    $config = array();
    if (file_exists($configFile)) {
        $loaded = include $configFile;
        if (is_array($loaded) && isset($loaded['smtp'])) {
            $config = $loaded['smtp'];
        }
    }

    $host = isset($config['host']) ? $config['host'] : (getenv('SMTP_HOST') ? getenv('SMTP_HOST') : 'sandbox.smtp.mailtrap.io');
    $port = isset($config['port']) ? $config['port'] : (getenv('SMTP_PORT') ? getenv('SMTP_PORT') : 2525);
    $user = isset($config['username']) ? $config['username'] : (getenv('SMTP_USER') ? getenv('SMTP_USER') : '');
    $pass = isset($config['password']) ? $config['password'] : (getenv('SMTP_PASS') ? getenv('SMTP_PASS') : '');
    $from = isset($config['from_email']) ? $config['from_email'] : (getenv('SMTP_FROM') ? getenv('SMTP_FROM') : 'noreply@campusilheus.ifba.edu.br');
    $name = isset($config['from_name']) ? $config['from_name'] : 'Leitores de Papel';

    return array(
        'host' => $host,
        'port' => $port,
        'username' => $user,
        'password' => $pass,
        'from_email' => $from,
        'from_name' => $name
    );
}
