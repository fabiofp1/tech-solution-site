<?php
session_start();
include 'conexao.php';

$id_produto = $_GET['id'];
$id_usuario = 1;

$sql = "SELECT * FROM carrinho WHERE id_usuario=$id_usuario AND id_produto=$id_produto";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    
    $sql = "UPDATE carrinho SET quantidade = quantidade + 1 WHERE id_usuario=$id_usuario AND id_produto=$id_produto";
} else {
    
    $sql = "INSERT INTO carrinho (id_usuario, id_produto, quantidade) VALUES ($id_usuario, $id_produto, 1)";
}

$conn->query($sql);
header("Location: carrinho.php");
exit();
?>
