<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Orçamentos | Tech Solutions</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <?php include __DIR__ . '/php/header.php'; ?>

  <section class="form-orcamento-container">
    <form class="form-orcamento">
      <h2><i class="fas fa-file-signature"></i> Solicite seu orçamento</h2>
      <p>Preencha todos os campos abaixo. Entraremos em contato com você o mais breve possível.</p>

      <label><i class="fas fa-user"></i> Nome:</label>
      <input type="text" name="nome" placeholder="Seu nome completo" required>

      <label><i class="fas fa-envelope"></i> E-mail:</label>
      <input type="email" name="email" placeholder="exemplo@email.com" required>

      <label><i class="fas fa-comment-dots"></i> Detalhes do serviço:</label>
      <textarea name="mensagem" placeholder="Descreva o que precisa..." required></textarea>

      <button type="submit" class="contact-btn">Enviar Orçamento</button>
    </form>
  </section>

  <?php include __DIR__ . '/php/footer.php'; ?>
  <button id="btnTopo" title="Voltar ao topo">↑</button>

  <script>
    document.querySelector('.form-orcamento')
      .addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = e.target;
        const data = new FormData(form);

        try {
          const res  = await fetch('salvar_orcamento.php', {
            method: 'POST',
            body: data
          });
          const json = await res.json();

          if (res.ok && json.status === 'sucesso') {
            Swal.fire({
              icon: 'success',
              title: 'Orçamento enviado!',
              text: 'Aguarde o retorno de nossos colaboradores.',
              showConfirmButton: false,
              timer: 3000
            });
            form.reset();
          } else {
            Swal.fire('Erro', json.msg || 'Não foi possível enviar.', 'error');
          }
        } catch (err) {
          Swal.fire('Erro de conexão', 'Tente novamente mais tarde.', 'error');
        }
      });
  </script>
  <script src="script.js"></script>
</body>
</html>
