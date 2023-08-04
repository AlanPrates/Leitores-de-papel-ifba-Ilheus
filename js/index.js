// Selecionando os elementos do formulário de login e de cadastro
var formSignin = document.querySelector('#signin');
var formSignup = document.querySelector('#signup');
var btnColor = document.querySelector('.btnColor');

// Adicionando um ouvinte de evento ao botão "Sign in"
document.querySelector('#btnSignin')
  .addEventListener('click', () => {
    // Ao clicar no botão "Sign in", mova o formulário de login para a esquerda
    formSignin.style.left = "25px";
    // Movendo o formulário de cadastro para a direita
    formSignup.style.left = "450px";
    // Movendo o botão de cor para a posição inicial
    btnColor.style.left = "0px";
});

// Adicionando um ouvinte de evento ao botão "Sign up"
document.querySelector('#btnSignup')
  .addEventListener('click', () => {
    // Ao clicar no botão "Sign up", mova o formulário de login para a esquerda
    formSignin.style.left = "-450px";
    // Movendo o formulário de cadastro para a direita
    formSignup.style.left = "25px";
    // Movendo o botão de cor para a posição desejada
    btnColor.style.left = "110px";
});
