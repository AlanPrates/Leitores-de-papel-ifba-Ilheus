function atualizarDados() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            var el = document.getElementById("resultado");
            if (el) {
                el.innerHTML = xhr.responseText;
            }
        }
    };

    var endpoint = "../actions/atualizar_emprestimos.php";
    xhr.open("GET", endpoint, true);
    xhr.send();
}

// Sincroniza em segundo plano de forma não intrusiva a cada 15 segundos
setInterval(atualizarDados, 15000);