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

// Tudo dentro de uma função: as variáveis deste arquivo não se misturam
// com as de outros scripts da mesma página.
(function () {
'use strict';

const menosMovimento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;


// ---- 1) Seção conceitual: traço + pilares, um de cada vez --------------
// A seção fica "presa" na tela (CSS .conceito--fixada + position: sticky)
// e a rolagem dentro dela vira uma linha do tempo de 0 a 1:
//
//   0.00 ─ 0.16   traço de grafite é desenhado
//   0.20 ─ 0.42   pilar 1 é montado
//   0.46 ─ 0.68   pilar 2 é montado
//   0.72 ─ 0.94   pilar 3 é montado
//   0.94 ─ 1.00   pausa com tudo completo, antes de a página seguir
//
// Dentro de cada pilar, as etapas também têm seus trechos (ETAPAS_PILAR).
// Os valores são calculados aqui e aplicados direto nos elementos, sem
// depender de contas em CSS (que nem todo navegador aceita com números puros).
const secaoConceito = document.querySelector('.conceito');
const traco = document.querySelector('.traco-grafite__linha');
const pilares = Array.from(document.querySelectorAll('[data-pilares] .pilar'));

const TRECHO_TRACO = [0, 0.16];
const TRECHOS_PILARES = [[0.20, 0.42], [0.46, 0.68], [0.72, 0.94]];

// Etapas dentro de um pilar (0 a 1 = o trecho daquele pilar)
const ETAPAS_PILAR = {
    linha:  [0.00, 0.35],
    icone:  [0.15, 0.60],
    numero: [0.40, 0.70],
    titulo: [0.50, 0.85],
    texto:  [0.60, 1.00],
};

// Quanto do trecho [inicio, fim] já passou, de 0 a 1
function trecho(valor, inicio, fim) {
    return Math.min(Math.max((valor - inicio) / (fim - inicio), 0), 1);
}

// Suaviza o movimento: começa rápido e pousa devagar (ease-out cúbico)
function suavizar(t) {
    return 1 - Math.pow(1 - t, 3);
}

// Desenha um traço SVG com pathLength="1": 0 = invisível, 1 = completo.
// Usa o atributo stroke-dashoffset, que todos os navegadores aceitam com número.
// O espaço do padrão é 2 (e não 1) para sobrar folga: com "1 1", o
// arredondamento do navegador deixava um pontinho visível no fim da linha.
function desenhar(elemento, quanto) {
    elemento.setAttribute('stroke-dasharray', '1 2');
    elemento.setAttribute('stroke-dashoffset', (1 - quanto).toFixed(4));
    elemento.style.visibility = quanto > 0 ? 'visible' : 'hidden';
}

// Prepara os elementos de cada pilar uma vez só
const partesPilares = pilares.map(function (pilar) {
    return {
        pilar: pilar,
        tracosIcone: Array.from(pilar.querySelectorAll('.pilar__icone [pathLength]')),
        numero: pilar.querySelector('.pilar__numero'),
        titulo: pilar.querySelector('.pilar__titulo'),
        texto: pilar.querySelector('.pilar__texto'),
    };
});

function aplicarPilar(partes, t) {
    const linha = suavizar(trecho(t, ETAPAS_PILAR.linha[0], ETAPAS_PILAR.linha[1]));
    const icone = trecho(t, ETAPAS_PILAR.icone[0], ETAPAS_PILAR.icone[1]);

    partes.pilar.style.setProperty('--linha', linha.toFixed(4));
    partes.pilar.style.setProperty('--icone', Math.min(icone * 4, 1).toFixed(4)); // aparece logo no início do desenho
    partes.tracosIcone.forEach(function (tracoIcone) {
        desenhar(tracoIcone, suavizar(icone));
    });

    ['numero', 'titulo', 'texto'].forEach(function (nome) {
        const etapa = ETAPAS_PILAR[nome];
        partes[nome].style.setProperty('--t', suavizar(trecho(t, etapa[0], etapa[1])).toFixed(4));
    });
}

if (secaoConceito && menosMovimento) {
    // Movimento reduzido: sem seção presa, tudo já completo
    if (traco) {
        desenhar(traco, 1);
    }
} else if (secaoConceito) {
    secaoConceito.classList.add('conceito--fixada');

    let agendado = false;

    function atualizarConceito() {
        agendado = false;

        // Quanto já se rolou dentro da seção, de 0 (topo da seção encostado no
        // header) a 1 (fim da seção, quando o conteúdo volta a rolar)
        // Espaço ocupado no topo: header + barra do admin (quando ela existe)
        const barraAdmin = document.querySelector('.barra-admin');
        const alturaTopo = document.querySelector('.cabecalho').offsetHeight + (barraAdmin ? barraAdmin.offsetHeight : 0);
        const caixa = secaoConceito.getBoundingClientRect();
        const percurso = secaoConceito.offsetHeight - (window.innerHeight - alturaTopo);
        const progresso = trecho(alturaTopo - caixa.top, 0, percurso);

        if (traco) {
            desenhar(traco, suavizar(trecho(progresso, TRECHO_TRACO[0], TRECHO_TRACO[1])));
        }
        partesPilares.forEach(function (partes, indice) {
            const faixa = TRECHOS_PILARES[indice] || TRECHOS_PILARES[TRECHOS_PILARES.length - 1];
            aplicarPilar(partes, trecho(progresso, faixa[0], faixa[1]));
        });
    }

    // O scroll dispara muitas vezes por quadro; calculamos no máximo uma vez
    // por quadro de desenho (requestAnimationFrame)
    function aoRolar() {
        if (!agendado) {
            agendado = true;
            requestAnimationFrame(atualizarConceito);
        }
    }

    // Só escuta o scroll enquanto a seção está na tela
    new IntersectionObserver(function (entradas) {
        if (entradas[0].isIntersecting) {
            window.addEventListener('scroll', aoRolar, { passive: true });
        } else {
            window.removeEventListener('scroll', aoRolar);
        }
        aoRolar(); // acerta o estado final ao entrar ou sair
    }).observe(secaoConceito);

    window.addEventListener('resize', aoRolar);
    atualizarConceito();
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

})();
