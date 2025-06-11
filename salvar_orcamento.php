<?php

header('Content-Type: application/json');

require_once __DIR__ . '/php/config.php';

$nome    = trim($_POST['nome']    ?? '');
$email   = trim($_POST['email']   ?? '');
$mensagem= trim($_POST['mensagem']?? '');

if ($nome==='' || $email==='' || $mensagem==='') {
    http_response_code(400);
    echo json_encode(['status'=>'erro','msg'=>'Preencha todos os campos.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
      INSERT INTO orcamentos (nome, email, mensagem, data_envio)
      VALUES (:nome, :email, :mensagem, NOW())
    ");
    $stmt->execute([
      'nome'     => $nome,
      'email'    => $email,
      'mensagem' => $mensagem,
    ]);

    echo json_encode(['status'=>'sucesso']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status'=>'erro','msg'=>'Erro no servidor.']);
}
