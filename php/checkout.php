<?php


ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'msg' => 'Usuário não autenticado']);
    exit;
}

$stmtUser = $pdo->prepare(
    "SELECT id, email FROM usuarios WHERE usuario = :usuario"
);
$stmtUser->execute(['usuario' => $_SESSION['usuario']]);
$userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);
if (!$userRow) {
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'msg' => 'Usuário não encontrado']);
    exit;
}
$id_usuario    = (int) $userRow['id'];
$email_cliente = $userRow['email'];

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data) || count($data) === 0) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'msg' => 'Carrinho inválido']);
    exit;
}

try {
    $pdo->beginTransaction();

    $selProd = $pdo->prepare(
        "SELECT estoque, preco FROM produtos WHERE id = ? FOR UPDATE"
    );
    $total = 0;
    foreach ($data as $item) {
        $selProd->execute([(int)$item['id']]);
        $prod = $selProd->fetch(PDO::FETCH_ASSOC);
        if (!$prod) {
            throw new Exception("Produto não encontrado (ID {$item['id']}).");
        }
        if ($prod['estoque'] < $item['quantidade']) {
            throw new Exception("Estoque insuficiente para {$item['nome']}.");
        }
        $total += $prod['preco'] * $item['quantidade'];
    }

    $insPedido = $pdo->prepare(
        "INSERT INTO pedidos (id_usuario, data_pedido, total)
         VALUES (:id_usuario, NOW(), :total)"
    );
    $insPedido->execute([
        'id_usuario' => $id_usuario,
        'total'      => $total
    ]);
    $pedido_id = $pdo->lastInsertId();

    $updEstoque = $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?");
    $insItem    = $pdo->prepare(
        "INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario)
         VALUES (?, ?, ?, ?)"
    );

    foreach ($data as $item) {
        $pid = (int)$item['id'];
        $qtd = (int)$item['quantidade'];

        $preuStmt = $pdo->prepare("SELECT preco FROM produtos WHERE id = ?");
        $preuStmt->execute([$pid]);
        $preu = $preuStmt->fetchColumn();

        $updEstoque->execute([$qtd, $pid]);

        $insItem->execute([$pedido_id, $pid, $qtd, $preu]);
    }

    $pdo->commit();
    echo json_encode([
        'status'     => 'sucesso',
        'pedido_id'  => $pedido_id,
        'cliente'    => $_SESSION['usuario'],
        'email'      => $email_cliente
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'msg' => $e->getMessage()]);
}
