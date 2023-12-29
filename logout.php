<?php

// Inicializar a sessão

session_start();



// Encerrar a sessão

session_destroy();



// Redirecionar para o login

header("Location: index.php");

exit;

