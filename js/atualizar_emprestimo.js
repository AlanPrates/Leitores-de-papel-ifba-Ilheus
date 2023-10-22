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
setInterval(atualizarDados, 1000);