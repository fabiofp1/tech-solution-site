<?php

$dbHost = 'localhost';
$dbUser = 'root';
$dbPassword = '';
$dbName = 'projeto';

$conexao = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

if ($conexao->connect_error) {
    header('Location: ../cadastro.html?erro=conexao');
    exit;
}

$nome = $_POST['nome'];
$email = $_POST['email'];
$usuario = $_POST['usuario'];
$senha = $_POST['senha'];
$confirmaSenha = $_POST['confirma_senha'];

if ($senha !== $confirmaSenha) {
    header('Location: ../cadastro.html?erro=senhas');
    exit;
}

$senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nome, email, usuario, senha) 
        VALUES ('$nome', '$email', '$usuario', '$senhaCriptografada')";

if ($conexao->query($sql) === TRUE) {
    header('Location: ../cadastro.html?sucesso');
} else {
    header('Location: ../cadastro.html?erro=bd');
}

$conexao->close();
?>
