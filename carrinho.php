<?php

include __DIR__ . '/php/header.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Carrinho de Compras | Tech Solutions</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <main class="carrinho-conteudo">
    <h1>Seu Carrinho</h1>
    <div id="cart-container"></div>
    <div class="total">
      <p id="total-price">Total: R$ 0,00</p>
      <button
        id="esvaziar"
        class="contact-btn"
        style="background-color:#e74c3c; color:#fff;"
      >
        Esvaziar Carrinho
      </button>
      <button id="finalizar" class="contact-btn">Finalizar Compra</button>
    </div>
  </main>

  <?php include __DIR__ . '/php/footer.php'; ?>

  <button id="btnTopo" title="Voltar ao topo">↑</button>

  <script>

    const btnTopo = document.getElementById("btnTopo");
    if (btnTopo) {
      window.addEventListener("scroll", () => {
        btnTopo.style.display = window.scrollY > 50 ? "block" : "none";
      });
      btnTopo.addEventListener("click", () =>
        window.scrollTo({ top: 0, behavior: "smooth" })
      );
    }


    let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];

    function desenhaCarrinho() {
      const container = document.getElementById('cart-container');
      const totalEl   = document.getElementById('total-price');
      container.innerHTML = '';
      if (!carrinho.length) {
        container.innerHTML = '<p>Seu carrinho está vazio.</p>';
        totalEl.textContent = 'Total: R$ 0,00';
        return;
      }
      let total = 0;
      carrinho.forEach(item => {
        total += item.preco * item.quantidade;
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.innerHTML = `
          <div class="cart-row">
            <div class="cart-col nome">${item.nome}</div>
            <div class="cart-col quantidade">
              <button class="diminuir" data-id="${item.id}"><i class="fas fa-minus"></i></button>
              <span>${item.quantidade}</span>
              <button class="aumentar" data-id="${item.id}"><i class="fas fa-plus"></i></button>
            </div>
            <div class="cart-col preco">R$ ${item.preco.toFixed(2)}</div>
            <div class="cart-col total">R$ ${(item.preco*item.quantidade).toFixed(2)}</div>
            <div class="cart-col acao">
              <button class="remover" data-id="${item.id}"><i class="fas fa-trash"></i></button>
            </div>
          </div>`;
        container.appendChild(div);
      });
      totalEl.textContent = 'Total: R$ ' + total.toFixed(2);

      document.querySelectorAll('.remover').forEach(btn =>
        btn.addEventListener('click', () => {
          carrinho = carrinho.filter(i => i.id !== btn.dataset.id);
          salvaECara();
        })
      );

      document.querySelectorAll('.aumentar').forEach(btn =>
        btn.addEventListener('click', () => {
          const it = carrinho.find(i => i.id === btn.dataset.id);
          if (it) { it.quantidade++; salvaECara(); }
        })
      );

      document.querySelectorAll('.diminuir').forEach(btn =>
        btn.addEventListener('click', () => {
          const it = carrinho.find(i => i.id === btn.dataset.id);
          if (it) {
            it.quantidade--;
            if (it.quantidade < 1) carrinho = carrinho.filter(x => x.id !== it.id);
            salvaECara();
          }
        })
      );
    }

    function salvaECara() {
      localStorage.setItem('carrinho', JSON.stringify(carrinho));
      desenhaCarrinho();
    }

    document.addEventListener('DOMContentLoaded', () => {
      desenhaCarrinho();

      document.getElementById('finalizar').addEventListener('click', async e => {
        e.preventDefault();
        if (!carrinho.length) {
          return Swal.fire('Carrinho vazio', 'Adicione produtos antes de finalizar.', 'info');
        }
        try {
          const res  = await fetch('php/checkout.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(carrinho)
          });
          const data = await res.json();
          if (res.ok && data.status === 'sucesso') {
            Swal.fire({
              icon: 'success',
              title: `Olá, ${data.cliente}!`,
              html: `
                Seu pedido <strong>#${data.pedido_id}</strong> foi confirmado.<br>
                Enviaremos um e-mail de confirmação e as atualizações para:<br>
                <strong>${data.email}</strong>
              `,
              showConfirmButton: false,
              timer: 6000
            }).then(() => {
              localStorage.removeItem('carrinho');
              window.location.href = 'index.html';
            });
          } else {
            Swal.fire('Erro', data.msg || 'Não foi possível concluir a compra.', 'error');
          }
        } catch (err) {
          console.error(err);
          Swal.fire('Erro de conexão', 'Tente novamente mais tarde.', 'error');
        }
      });

      document.getElementById('esvaziar').addEventListener('click', () => {
        carrinho = [];
        localStorage.removeItem('carrinho');
        desenhaCarrinho();
        Swal.fire('Carrinho esvaziado', 'Todos os itens foram removidos.', 'success');
      });
    });
  </script>
</body>
</html>
