<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar">
  <div class="logo">
    <h1>Tech Solution</h1>
  </div>

  <div class="menu">
    <a href="index.html">Início</a>
    <a href="servicos.html">Serviços</a>
    <a href="pecas.php">Peças e Acessórios</a>
    <a href="orcamentos.php">Orçamentos</a>
  </div>

  <div class="icones">
    <?php if (isset($_SESSION['usuario'])): ?>
      <a href="#" class="icon-link">
        <i class="fas fa-user-circle"></i>
        <span>Olá, <strong><?= htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8') ?></strong></span>
      </a>
      <a href="php/logout.php" class="icon-link">
        <i class="fas fa-sign-out-alt"></i>
        <span>Sair</span>
      </a>
    <?php else: ?>
      <a href="login.html" class="icon-link">
        <i class="fas fa-sign-in-alt"></i>
        <span>Entrar</span>
      </a>
    <?php endif; ?>

    <a id="botao" href="carrinho.php" class="icon-link">
      <i class="fas fa-shopping-cart"></i>
      <span>Carrinho</span>
    </a>
  </div>
</nav>
