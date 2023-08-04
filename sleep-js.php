<?php
session_start();
include 'conexao.php';
// Verifica se o usuário não está logado
if (!isset($_SESSION['user_id'])) {
    exit;
}

// Busca os registros na tabela livros_emprestados que não estão na tabela historico_emprestimos
$sql = "SELECT * FROM livros_emprestados WHERE livro_id NOT IN (SELECT livro_id FROM historico_emprestimos)";
$result = $conn->query($sql);

// Verifica se ocorreu um erro na consulta SQL
if (!$result) {
    die("Erro na consulta SQL: " . $conn->error);
}

// Verifica se existem registros para serem transferidos
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $livro_id = $row["livro_id"];

        // Verifica se o livro já possui um registro no histórico
        $check_sql = "SELECT * FROM historico_emprestimos WHERE livro_id = '$livro_id'";
        $check_result = $conn->query($check_sql);

        // Insere os dados na tabela historico_emprestimos apenas se não houver registro para o livro
        if ($check_result->num_rows === 0) {
            $user_id = $row["user_id"];
            $data_emprestimo = $row["data_emprestimo"];
            $data_devolucao = $row["data_devolucao"];

            // Insere os dados na tabela historico_emprestimos
            $insert_sql = "INSERT INTO historico_emprestimos (livro_id, user_id, data_emprestimo, data_devolucao) VALUES ('$livro_id', '$user_id', '$data_emprestimo', '$data_devolucao')";
            $conn->query($insert_sql);
        }
    }
}

// Fecha a conexão com o banco de dados
$conn->close();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Atualização Automática</title>
</head>

<body>


    <script>
        function atualizarDados() {
            // Cria um objeto XMLHttpRequest para fazer a requisição AJAX
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // Atualiza o conteúdo do elemento "resultado" com os dados obtidos do servidor
                        document.getElementById("resultado").innerHTML = xhr.responseText;
                    }
                }
            };

            // Faz uma requisição GET para o arquivo "atualizar_emprestimos.php"
            xhr.open("GET", "atualizar_emprestimos.php", true);
            xhr.send();
        }

        // Atualiza os dados a cada 5 segundos
        setInterval(atualizarDados, 5000);
    </script>
</body>

</html>
<!-- Em resumo, esse código PHP transfere registros da tabela "livros_emprestados" 
para a tabela "historico_emprestimos" desde que o livro não tenha um registro 
no histórico. Isso pode ser útil para manter um registro histórico de empréstimos
de livros, permitindo que a tabela "livros_emprestados" seja usada apenas para 
rastrear empréstimos ativos. -->

<!-- utilizando o CRON (agendador de tarefas) no servidor para executar automaticamente 
o código PHP a cada 5 segundos, é importante ressaltar que a maioria dos sistemas de 
hospedagem compartilhada não permite a configuração de tarefas CRON com intervalos 
tão curtos. Isso ocorre porque tarefas CRON frequentes podem consumir muitos recursos
 do servidor, afetando o desempenho de outros sites hospedados no mesmo servidor. -->