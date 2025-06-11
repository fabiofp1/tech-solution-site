const btnTopo = document.getElementById("btnTopo");
if (btnTopo) {
  window.addEventListener("scroll", () => {
    btnTopo.style.display = window.scrollY > 50 ? "block" : "none";
  });
  btnTopo.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
}

let carrinho = JSON.parse(localStorage.getItem("carrinho")) || [];

function renderizarCarrinho() {
  const cartContainer = document.getElementById("cart-container");
  const totalElement  = document.getElementById("total-price");
  cartContainer.innerHTML = "";

  if (carrinho.length === 0) {
    cartContainer.innerHTML = "<p>Seu carrinho está vazio.</p>";
    totalElement.textContent = "";
    return;
  }

  let total = 0;
  carrinho.forEach(item => {
    total += item.preco * item.quantidade;
    const div = document.createElement("div");
    div.className = "cart-item";
    div.innerHTML = `
      <div class="cart-row">
        <div class="cart-col nome">${item.nome}</div>
        <div class="cart-col quantidade">
          <button class="diminuir" data-id="${item.id}"><i class="fas fa-minus"></i></button>
          <span>${item.quantidade}</span>
          <button class="aumentar" data-id="${item.id}"><i class="fas fa-plus"></i></button>
        </div>
        <div class="cart-col preco">R$ ${item.preco.toFixed(2)}</div>
        <div class="cart-col total">R$ ${(item.preco * item.quantidade).toFixed(2)}</div>
        <div class="cart-col acao">
          <button class="remover" data-id="${item.id}"><i class="fas fa-trash"></i></button>
        </div>
      </div>`;
    cartContainer.appendChild(div);
  });

  totalElement.textContent = `Total: R$ ${total.toFixed(2)}`;

  document.querySelectorAll(".remover").forEach(btn =>
    btn.addEventListener("click", () => {
      carrinho = carrinho.filter(i => i.id !== btn.dataset.id);
      localStorage.setItem("carrinho", JSON.stringify(carrinho));
      renderizarCarrinho();
    })
  );
  document.querySelectorAll(".aumentar").forEach(btn =>
    btn.addEventListener("click", () => {
      const i = carrinho.find(x => x.id === btn.dataset.id);
      if (i) {
        i.quantidade++;
        localStorage.setItem("carrinho", JSON.stringify(carrinho));
        renderizarCarrinho();
      }
    })
  );
  document.querySelectorAll(".diminuir").forEach(btn =>
    btn.addEventListener("click", () => {
      const i = carrinho.find(x => x.id === btn.dataset.id);
      if (i) {
        i.quantidade--;
        if (i.quantidade < 1) carrinho = carrinho.filter(x => x.id !== i.id);
        localStorage.setItem("carrinho", JSON.stringify(carrinho));
        renderizarCarrinho();
      }
    })
  );
}

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".btn-carrinho").forEach(btn => {
    btn.addEventListener("click", e => {
      e.preventDefault();
      const { id, nome, preco } = btn.dataset;
      const valor = parseFloat(preco);
      const card  = btn.closest(".card-inventario");
      const qtdIn = card.querySelector(".quantidade");
      const qtd   = parseInt(qtdIn.value, 10);
      const max   = parseInt(qtdIn.max, 10);

      if (isNaN(qtd) || qtd < 1 || qtd > max) {
        return Swal.fire("Quantidade inválida", `Escolha entre 1 e ${max}`, "error");
      }

      const found = carrinho.find(item => item.id === id);
      if (found) {
        found.quantidade = Math.min(found.quantidade + qtd, max);
      } else {
        carrinho.push({ id, nome, preco: valor, quantidade: qtd });
      }

      localStorage.setItem("carrinho", JSON.stringify(carrinho));
      Swal.fire({
        icon: "success",
        title: "Adicionado",
        text: `${qtd}× ${nome} no carrinho.`,
        timer: 1200,
        showConfirmButton: false
      });

      if (document.getElementById("cart-container")) {
        renderizarCarrinho();
      }
    });
  });

  if (document.getElementById("cart-container")) {
    renderizarCarrinho();

    document.getElementById("finalizar").addEventListener("click", async () => {
      if (!carrinho.length) {
        return Swal.fire("Carrinho vazio", "Adicione produtos antes de finalizar.", "info");
      }

      try {
        const res  = await fetch("php/checkout.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(carrinho)
        });
        const data = await res.json();

        if (res.ok && data.status === "sucesso") {
          Swal.fire({
            icon: "success",
            title: `Obrigado pela sua compra, ${data.cliente}!`,
            text: `Pedido #${data.pedido_id} registrado com sucesso.`,
            timer: 3000,
            showConfirmButton: false
          }).then(() => {
            localStorage.removeItem("carrinho");
            window.location.href = "index.php";
          });
        } else {
          Swal.fire("Erro", data.msg || "Não foi possível concluir a compra.", "error");
        }
      } catch (err) {
        Swal.fire("Erro de conexão", "Tente novamente mais tarde.", "error");
      }
    });
  }
});
