// EM OBRAS

<?php

require_once __DIR__ . '/php/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario']) || $_SESSION['usuario'] !== 'admin') {
    header('Location: index.html');
    exit;
}

$stmtPedidos = $pdo->query(
    "SELECT p.id, u.usuario AS cliente, p.data_pedido, p.total
     FROM pedidos p
     JOIN usuarios u ON p.id_usuario = u.id
     ORDER BY p.data_pedido DESC"
);
$pedidos = $stmtPedidos->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel Administrativo | Tech Solutions</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include __DIR__ . '/php/header.php'; ?>

  <main class="admin-panel" style="padding:2rem;">
    <h1>Painel Administrativo</h1>
    <h2>Pedidos</h2>
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Data</th>
          <th>Total (R$)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pedidos as $pedido): ?>
        <tr>
          <td><?= $pedido['id'] ?></td>
          <td><?= htmlspecialchars($pedido['cliente']) ?></td>
          <td><?= $pedido['data_pedido'] ?></td>
          <td><?= number_format($pedido['total'], 2, ',', '.') ?></td>
        </tr>
        <tr>
          <td colspan="4">
            <table class="admin-subtable">
              <thead>
                <tr>
                  <th>Produto</th>
                  <th>Quantidade</th>
                  <th>Preço Unitário (R$)</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $stmtItens = $pdo->prepare(
                  "SELECT pi.produto_id, p.nome AS produto, pi.quantidade, pi.preco_unitario
                   FROM pedido_itens pi
                   JOIN produtos p ON pi.produto_id = p.id
                   WHERE pi.pedido_id = ?"
                );
                $stmtItens->execute([$pedido['id']]);
                $itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);
                foreach ($itens as $item):
                ?>
                <tr>
                  <td><?= htmlspecialchars($item['produto']) ?></td>
                  <td><?= $item['quantidade'] ?></td>
                  <td><?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </main>

  <?php include __DIR__ . '/php/footer.php'; ?>
</body>
</html>
