<?php

$pdo = new PDO(
  'mysql:host=localhost;dbname=projeto;charset=utf8mb4',
  'root',
  '',
  [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$stmt     = $pdo->query("SELECT * FROM produtos ORDER BY id");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Peças e Acessórios | Tech Solutions</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <?php include __DIR__ . '/php/header.php'; ?>

  <section class="pecas-container">
    <div class="introducao-pecas">
      <h2><i class="fa-solid fa-screwdriver-wrench"></i> Peças e Acessórios</h2>
      <p>
        Bem-vindo à nossa seção de peças e acessórios. Aqui você encontra os principais itens para manutenção,
        upgrade e reposição de equipamentos. Trabalhamos apenas com peças de qualidade, prontas para melhorar o
        desempenho dos seus dispositivos.
      </p>
    </div>

    <div class="inventario-grade">
      <?php foreach ($produtos as $p): ?>
        <div class="card-inventario">
          <?php if (!empty($p['tag'])): ?>
            <div class="tag-produto"><?= htmlspecialchars($p['tag']) ?></div>
          <?php endif; ?>

          <img src="<?= htmlspecialchars($p['imagem']) ?>"
               alt="<?= htmlspecialchars($p['nome']) ?>">

          <div class="info-produto">
            <h3><?= htmlspecialchars($p['nome']) ?></h3>
            <p><?= htmlspecialchars($p['descricao']) ?></p>
            <div class="precos">
              <span class="preco-pix">
                R$ <?= number_format($p['preco'], 2, ',', '.') ?> no Pix
              </span>
              <?php if (!empty($p['preco_parcelado'])): ?>
                <span class="preco-parcelado">
                  ou <?= htmlspecialchars($p['preco_parcelado']) ?>
                </span>
              <?php endif; ?>
            </div>

            <div class="quantidade-box">
              <button type="button" class="quantidade-btn diminuir">–</button>
              <input
                type="number"
                id="qtd-<?= $p['id'] ?>"
                class="quantidade"
                min="1"
                max="<?= $p['estoque'] ?>"
                value="1"
              >
              <button type="button" class="quantidade-btn aumentar">+</button>
            </div>
          </div>

          <button class="btn-carrinho"
                  data-id="<?= $p['id'] ?>"
                  data-nome="<?= htmlspecialchars($p['nome']) ?>"
                  data-preco="<?= $p['preco'] ?>">
            <i class="fa-solid fa-cart-plus"></i>
            Adicionar ao carrinho
          </button>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <?php include __DIR__ . '/php/footer.php'; ?>

  <button id="btnTopo" title="Voltar ao topo">↑</button>

  <script src="script.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.quantidade-box .aumentar').forEach(btn => {
        btn.addEventListener('click', () => {
          const input = btn.parentElement.querySelector('.quantidade');
          const max = parseInt(input.max, 10);
          let val = parseInt(input.value, 10) || 1;
          if (val < max) input.value = val + 1;
        });
      });
      document.querySelectorAll('.quantidade-box .diminuir').forEach(btn => {
        btn.addEventListener('click', () => {
          const input = btn.parentElement.querySelector('.quantidade');
          let val = parseInt(input.value, 10) || 1;
          if (val > 1) input.value = val - 1;
        });
      });
    });
  </script>
</body>
</html>
