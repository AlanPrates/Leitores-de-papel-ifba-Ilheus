<!DOCTYPE html>
<html>
<head>
    <title>Notificações por E-mail</title>
</head>
<body>

    <!-- Conteúdo da sua página aqui -->

    <script>
        function consultarPHP() {
            // Faz uma requisição AJAX para o seu script PHP
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'notificacaoteste.php', true);
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 400) {
                    console.log('Consulta ao PHP bem-sucedida');
                } else {
                    console.error('Erro ao consultar PHP');
                }
            };
            xhr.onerror = function () {
                console.error('Erro ao consultar PHP');
            };
            xhr.send();
        }

        // Consulta inicial
        consultarPHP();

        // Agendamento para consultar a cada 24 horas (86400000 milissegundos)
        setInterval(consultarPHP, 86400000);
    </script>

</body>
</html>
