// =====================================================================
// Lapisari · splash-inicio.js
// Roda no <head>, ANTES de a página ser desenhada (por isso sem "defer").
// Só decide se a splash vai aparecer, marcando <html class="com-splash">.
// Se essa decisão ficasse para depois, a vitrine piscaria na tela por um
// instante antes de a splash cobri-la. O resto fica no splash.js.
// =====================================================================
(function () {
    var raiz = document.documentElement;

    // O PHP marca "sem-splash" quando o cartão de login precisa abrir já aberto (senha errada)
    if (raiz.classList.contains('sem-splash')) {
        return;
    }

    // Veio de outra página da própria loja (ex.: clicou no logotipo estando no
    // carrinho): a splash é a "porta de entrada" do site, então só aparece
    // para quem chega de fora ou digita o endereço, não a cada volta à inicial
    if (document.referrer && new URL(document.referrer).origin === window.location.origin) {
        return;
    }

    // Link para um ponto da página (ex.: index.php#vitrine depois de
    // adicionar ao carrinho): vai direto ao ponto, sem splash
    if (window.location.hash) {
        return;
    }

    // Recarregou (F5) ou voltou pelo navegador no meio da página: o navegador
    // vai restaurar a posição, então pulamos a splash e o header já aparece
    // no estado final. A posição é salva pelo splash.js ao sair da página.
    var navegacao = (performance.getEntriesByType && performance.getEntriesByType('navigation')[0]) || {};
    var voltando = navegacao.type === 'reload' || navegacao.type === 'back_forward';
    var posicaoSalva = 0;
    try {
        posicaoSalva = Number(sessionStorage.getItem('lapisari-posicao')) || 0;
    } catch (erro) {
        // navegação privada pode bloquear o sessionStorage; seguimos sem ele
    }
    if (voltando && posicaoSalva > 0) {
        return;
    }

    raiz.classList.add('com-splash');
})();
