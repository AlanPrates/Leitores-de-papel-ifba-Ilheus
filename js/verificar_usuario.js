document.getElementById('usuario').addEventListener('input', function() {
    var username = this.value;
    var verificarUsuarioDiv = document.getElementById('verificar-usuario');

    if (username.length >= 3) {
        // Faz uma requisição AJAX para verificar_usuario.php
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText == "false") {
                    verificarUsuarioDiv.innerHTML = "Nome de usuário já existe!";
                } else {
                    verificarUsuarioDiv.innerHTML = "";
                }
            }
        };
        xhttp.open("POST", "verificar_usuario.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("username=" + username);
    } else {
        verificarUsuarioDiv.innerHTML = "";
    }
});