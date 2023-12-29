<!DOCTYPE html>

<html>



<head>

    <title>Atualizar Relatório</title>

</head>



<body>

    <?php

    // Inicia a sessão
    
    session_start();

    global $conn;

    // Inclui o arquivo de conexão com o banco de dados
    
    global $conn;

    include 'conexao.php';



    // Consulta para obter todas as informações de empréstimos de livros que ainda não foram registrados no histórico
    
    $sql_relatorio = "SELECT u.nome, l.titulo, l.autor, le.data_emprestimo, le.data_devolucao

    FROM livros_emprestados le

    INNER JOIN usuarios u ON u.id = le.user_id

    INNER JOIN livros l ON l.id = le.livro_id

    WHERE u.matricula = '$matricula' AND le.historico = 0";

    $result_relatorio = $conn->query($sql_relatorio);



    // Verificar se há dados no relatório
    
    if ($result_relatorio->num_rows > 0) {

        echo '<table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Nome do Usuário</th>

                    <th>Título do Livro</th>

                    <th>Autor do Livro</th>

                    <th>Data de Empréstimo</th>

                    <th>Data de Devolução</th>

                </tr>

            </thead>

            <tbody>';

        while ($row_relatorio = $result_relatorio->fetch_assoc()) {

            echo '<tr>

                <td>' . $row_relatorio["id"] . '</td>

                <td>' . $row_relatorio["nome"] . '</td>

                <td>' . $row_relatorio["titulo"] . '</td>

                <td>' . $row_relatorio["autor"] . '</td>

                <td>' . $row_relatorio["data_emprestimo"] . '</td>

                <td>' . $row_relatorio["data_devolucao"] . '</td>

            </tr>';

        }

        echo '</tbody>

        </table>';

    } else {

        echo '<p>Nenhum registro encontrado no histórico de empréstimos.</p>';

    }

    ?>



    <?php

    // Inclui o rodapé
    
    include 'rodape.php';

    ?>



</body>



</html>
