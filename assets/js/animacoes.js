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
// Por que a versão anterior falhava nos primeiros cards: eles já estavam na
// tela ao abrir a página, então o observador os marcava como visíveis antes
// do navegador pintar o estado escondido. Sem um "antes" pintado, não há
// transição: o card simplesmente aparece. Agora:
//   1) aplicamos o estado escondido;
//   2) forçamos o navegador a calcular esse estado (leitura de offsetHeight);
//   3) só revelamos dois quadros depois (requestAnimationFrame duplo),
//      com uma pequena pausa extra para quem já estava na tela ao abrir.
const grade = document.querySelector('[data-entrada-cards]');
const topoVitrine = document.querySelector('.vitrine__topo');

// Tempo total da entrada de um card (foto + cascata do texto), para
// saber quando remover as transições longas
const DURACAO_ENTRADA = 1700;

function revelarDepoisDaPintura(callback) {
    requestAnimationFrame(function () {
        requestAnimationFrame(callback);
    });
}

if (grade && !menosMovimento) {
    const cards = Array.from(grade.querySelectorAll('.card-produto'));

    // Posição do card na linha (mesmo topo = mesma linha): vira o --atraso
    let topoDaLinha = null;
    let posicao = 0;
    cards.forEach(function (card) {
        const topo = card.offsetTop;
        posicao = topo === topoDaLinha ? posicao + 1 : 0;
        topoDaLinha = topo;
        card.style.setProperty('--atraso', posicao);
        card.classList.add('card-produto--oculto', 'card-produto--animando');
    });

    if (topoVitrine) {
        topoVitrine.classList.add('vitrine__topo--oculto', 'vitrine__topo--animando');
    }

    // Passo 2: obriga o navegador a aplicar os estilos escondidos agora
    void grade.offsetHeight;

    // Quem já está na tela ao abrir a página espera um instante,
    // para a animação acontecer quando o usuário já está olhando
    const inicioDaPagina = performance.now();
    const PAUSA_INICIAL = 350;

    function revelar(elemento, classeOculto, classeAnimando) {
        const espera = Math.max(0, PAUSA_INICIAL - (performance.now() - inicioDaPagina));
        setTimeout(function () {
            revelarDepoisDaPintura(function () {
                elemento.classList.remove(classeOculto);
                // Terminada a entrada, devolve as transições normais (hover rápido)
                const atraso = Number(elemento.style.getPropertyValue('--atraso')) || 0;
                setTimeout(function () {
                    elemento.classList.remove(classeAnimando);
                }, DURACAO_ENTRADA + atraso * 120);
            });
        }, espera);
    }

    const observador = new IntersectionObserver(function (entradas) {
        entradas.forEach(function (entrada) {
            if (!entrada.isIntersecting) {
                return;
            }
            const elemento = entrada.target;
            observador.unobserve(elemento); // anima uma vez só

            if (elemento === topoVitrine) {
                revelar(elemento, 'vitrine__topo--oculto', 'vitrine__topo--animando');
            } else {
                revelar(elemento, 'card-produto--oculto', 'card-produto--animando');
            }
        });
    }, {
        // Dispara quando 20% do card aparece, contando 8% acima da base da
        // janela: o card entra um pouco "dentro" da tela antes de animar
        threshold: 0.2,
        rootMargin: '0px 0px -8% 0px',
    });

    if (topoVitrine) {
        observador.observe(topoVitrine);
    }
    cards.forEach(function (card) {
        observador.observe(card);
    });
}
