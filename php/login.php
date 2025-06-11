<?php

require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_POST['usuario']) || empty($_POST['senha'])) {
    header('Location: ../login.html?erro=campos');
    exit();
}

$usuario = trim($_POST['usuario']);
$senha   = trim($_POST['senha']);

try {
    $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE usuario = :usuario");
    $stmt->execute(['usuario' => $usuario]);

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $hash = $row['senha'];

        if (password_verify($senha, $hash)) {
            $_SESSION['usuario'] = $usuario;
            header('Location: ../index.html');
            exit();
        } else {
            header('Location: ../login.html?erro=senha');
            exit();
        }
    } else {
        header('Location: ../login.html?erro=usuario');
        exit();
    }
} catch (PDOException $e) {

    die("Erro de banco de dados: " . $e->getMessage());
}
