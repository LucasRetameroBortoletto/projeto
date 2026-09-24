// =====================================================================
// Lapisari · animacoes.js (Fase 2: animações de scroll da página inicial)
//
// 1) Traço de grafite: desenhado conforme o scroll.
// 2) Cards da vitrine: entram suavemente ao aparecer na tela.
//
// Para não pesar:
// - IntersectionObserver avisa quando um elemento entra/sai da tela, sem
//   precisar checar posições a cada pixel rolado.
// - O cálculo do traço só roda enquanto ele está visível, e no máximo uma
//   vez por quadro (requestAnimationFrame).
// =====================================================================

const menosMovimento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;


// ---- 1) Traço de grafite ---------------------------------------------
const traco = document.querySelector('.traco-grafite');

if (traco && menosMovimento) {
    // Sem animação: a linha já aparece completa
    traco.style.setProperty('--progresso', 1);
} else if (traco) {
    let quadroAgendado = false;

    // Progresso de 0 a 1 conforme o scroll:
    // - início: quando o traço está a 90% da altura da janela (entrando por baixo).
    //   Se ele já aparece na tela ao abrir a página, o início é a posição de
    //   abertura, para o desenho começar do zero no primeiro scroll.
    // - fim: depois de rolar mais 45% da altura da janela.
    function atualizarTraco() {
        quadroAgendado = false;

        const alturaJanela = window.innerHeight;
        const topo = traco.getBoundingClientRect().top;          // posição na tela agora
        const topoNaPagina = topo + window.scrollY;              // posição com scroll = 0
        const inicio = Math.min(alturaJanela * 0.9, topoNaPagina);
        const fim = inicio - alturaJanela * 0.45;

        let progresso = (inicio - topo) / (inicio - fim);
        progresso = Math.min(Math.max(progresso, 0), 1); // limita entre 0 e 1

        traco.style.setProperty('--progresso', progresso.toFixed(4));
    }

    // O evento scroll dispara muitas vezes por quadro. Em vez de recalcular
    // em todas, agendamos um único cálculo para o próximo quadro de desenho.
    function aoRolar() {
        if (!quadroAgendado) {
            quadroAgendado = true;
            requestAnimationFrame(atualizarTraco);
        }
    }

    // Só escuta o scroll enquanto o traço está (quase) na tela
    const observadorTraco = new IntersectionObserver(function (entradas) {
        if (entradas[0].isIntersecting) {
            window.addEventListener('scroll', aoRolar, { passive: true });
            aoRolar();
        } else {
            window.removeEventListener('scroll', aoRolar);
            aoRolar(); // acerta o estado final (0 ou 1) ao sair da tela
        }
    }, { rootMargin: '100px 0px' });

    observadorTraco.observe(traco);
    window.addEventListener('resize', aoRolar);
    atualizarTraco();
}


// ---- 2) Entrada dos cards --------------------------------------------
const grade = document.querySelector('[data-entrada-cards]');

if (grade) {
    const cards = grade.querySelectorAll('.card-produto');

    // Atraso escalonado: cards da mesma linha entram um após o outro.
    // A posição na linha vem do topo do card: mesmo topo = mesma linha.
    function definirAtrasos() {
        let topoDaLinha = null;
        let posicao = 0;
        cards.forEach(function (card) {
            const topo = card.offsetTop;
            posicao = topo === topoDaLinha ? posicao + 1 : 0;
            topoDaLinha = topo;
            card.style.setProperty('--atraso', posicao);
        });
    }
    definirAtrasos();

    // Só esconde os cards depois de saber que o JS está rodando
    grade.classList.add('animar-entrada');

    const observadorCards = new IntersectionObserver(function (entradas, observador) {
        entradas.forEach(function (entrada) {
            if (entrada.isIntersecting) {
                entrada.target.classList.add('visivel');
                observador.unobserve(entrada.target); // anima uma vez só
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

    cards.forEach(function (card) {
        observadorCards.observe(card);
    });
}
