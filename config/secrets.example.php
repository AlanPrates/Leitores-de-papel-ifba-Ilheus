<?php
// Arquivo de exemplo de segredos locais (Não commitar senhas reais)
// Copie este arquivo para config/secrets.php e defina suas credenciais locais
return array(
    'smtp' => array(
        'host' => getenv('SMTP_HOST') ? getenv('SMTP_HOST') : 'smtp.exemplo.com',
        'port' => getenv('SMTP_PORT') ? getenv('SMTP_PORT') : 587,
        'username' => getenv('SMTP_USER') ? getenv('SMTP_USER') : 'usuario@exemplo.com',
        'password' => getenv('SMTP_PASS') ? getenv('SMTP_PASS') : 'senha_aqui',
        'from_email' => getenv('SMTP_FROM') ? getenv('SMTP_FROM') : 'noreply@campusilheus.ifba.edu.br',
        'from_name' => 'Leitores de Papel'
    )
);
