
    function mostrarSenha()
    {
        var tipo = document.getElementById("senha");
        if(tipo.type === "password")
    {
        tipo.type = "text";
    }
        else
    {
        tipo.type = "password";
    }
    }
