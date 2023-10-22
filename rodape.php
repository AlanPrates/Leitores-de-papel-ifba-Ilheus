<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .content {
            min-height: calc(50vh - 50px);
            /* Altura total da janela menos a altura do rodapé */
            padding-bottom: 100px;
            /* Altura do rodapé */
        }

        .footer {
            position: flex;
            bottom: 0;
            width: 100%;
            height: 100px;
            /* Altura do rodapé #dc3545 */
            background-color: #eab676;
            color: #fff;
            padding: 20px;
            text-align: center;
            /* Centraliza o texto */
        }
    </style>
    <title>Document</title>
</head>

<body>
    <div class="content">
        <!-- Conteúdo da página -->
    </div>
    <footer class="footer text-center"> <!-- Adiciona a classe "text-center" -->
        <p class="mb-0">Leitores de Papel &copy; 2023 - Todos os direitos reservados.</p>
    </footer>
</body>

</html>