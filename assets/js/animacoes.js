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


// ---- 1b) Pilares montados pelo scroll ----------------------------------
// Mesmo raciocínio do traço: cada pilar recebe --p de 0 a 1 conforme rola.
// O CSS transforma esse número nas etapas (linha, ícone, texto).
// As colunas são escalonadas: a segunda começa um pouco depois da primeira,
// a terceira um pouco depois da segunda.
const grupoPilares = document.querySelector('[data-pilares]');

if (grupoPilares && !menosMovimento) {
    const pilares = Array.from(grupoPilares.querySelectorAll('.pilar'));
    let pilaresAgendado = false;

    function atualizarPilares() {
        pilaresAgendado = false;

        const alturaJanela = window.innerHeight;
        const topo = grupoPilares.getBoundingClientRect().top;
        const topoNaPagina = topo + window.scrollY;
        // Começa quando os pilares entram por baixo (ou, se já aparecem ao
        // abrir a página, a partir da posição de abertura)
        const inicio = Math.min(alturaJanela * 0.92, topoNaPagina);
        // Termina quando os pilares chegam a 25% da altura da janela: o último
        // pilar fica completo enquanto ainda está bem visível, em qualquer tela
        const total = Math.max(inicio - alturaJanela * 0.25, 200);
        const escalonamento = total * 0.12;               // atraso entre colunas
        const distancia = total - 2 * escalonamento;      // quanto rolar para montar um pilar

        pilares.forEach(function (pilar, indice) {
            let p = (inicio - topo - indice * escalonamento) / distancia;
            p = Math.min(Math.max(p, 0), 1);
            pilar.style.setProperty('--p', p.toFixed(4));
        });
    }

    function aoRolarPilares() {
        if (!pilaresAgendado) {
            pilaresAgendado = true;
            requestAnimationFrame(atualizarPilares);
        }
    }

    new IntersectionObserver(function (entradas) {
        if (entradas[0].isIntersecting) {
            window.addEventListener('scroll', aoRolarPilares, { passive: true });
        } else {
            window.removeEventListener('scroll', aoRolarPilares);
        }
        aoRolarPilares();
    }, { rootMargin: '100px 0px' }).observe(grupoPilares);

    window.addEventListener('resize', aoRolarPilares);
    atualizarPilares();
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
