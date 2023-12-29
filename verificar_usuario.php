<?php
include 'conexao.php';

if (isset($_POST['username'])) {
    $username = $conn->real_escape_string($_POST['username']);

    $query_check_users = "SELECT * FROM usuarios WHERE LOWER(username) = LOWER('$username') UNION SELECT * FROM admin WHERE LOWER(admin_username) = LOWER('$username')";
    $result_check_users = $conn->query($query_check_users);

    if ($result_check_users && $result_check_users->num_rows > 0) {
        echo "false"; // Se o usuário já existe, retorna "false"
    } else {
        echo "true"; // Se o usuário não existe, retorna "true"
    }
} else {
    echo "false"; // Se o campo 'username' não foi enviado, retorna "false"
}

$conn->close();
?>

