// =====================================================================
// Lapisari · splash.js (Fase 3)
//
// 1) Escrita: a assinatura é "escrita à mão" desenhando o traço aos poucos
//    (stroke-dashoffset de 1 até 0; os paths têm pathLength="1").
// 2) Espera: terminada a escrita, aparece o "role para baixo".
// 3) Saída: no primeiro scroll (roda do mouse, teclado, toque ou clique),
//    a assinatura encolhe e voa até o logotipo do header, e o fundo preto
//    recolhe até a altura do header. Técnica FLIP:
//      First  - mede onde a assinatura está agora (grande, no centro);
//      Last   - mede onde ela deve terminar (o logotipo do header);
//      Invert - calcula o deslocamento e a escala entre os dois;
//      Play   - aplica esse transform com transição e deixa o navegador animar.
//    Só transform muda durante o voo, então a animação é leve (placa de vídeo).
// =====================================================================

// Tudo dentro de uma função: as variáveis deste arquivo não se misturam
// com as de outros scripts da mesma página.
(function () {
'use strict';

const raiz = document.documentElement;
const splash = document.querySelector('[data-splash]');
const menosMovimento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Guarda a posição de rolagem ao sair da página. O splash-inicio.js usa isso
// para pular a splash quando a página é recarregada no meio.
window.addEventListener('pagehide', function () {
    try {
        sessionStorage.setItem('lapisari-posicao', String(Math.round(window.scrollY)));
    } catch (erro) {
        // sem sessionStorage (navegação privada): a splash só não é pulada no F5
    }
});

// Tempos da escrita (ms)
const DURACAO_TRACO = 2600;   // o nome inteiro
const DURACAO_PINGO = 160;    // cada pingo dos "i"
const PAUSA_ANTES = 250;      // tela preta antes da caneta começar

if (splash && !raiz.classList.contains('com-splash')) {
    // Sem splash nesta visita (âncora, aviso ou recarregar no meio): some com ela
    splash.remove();
} else if (splash) {
    iniciarSplash();
}

function iniciarSplash() {
    const assinatura = splash.querySelector('.splash__assinatura');
    const traco = assinatura.querySelector('.assinatura__traco');
    const pingos = Array.from(assinatura.querySelectorAll('.assinatura__pingo'));
    const logoHeader = document.querySelector('.cabecalho__assinatura');
    const header = document.querySelector('.cabecalho');

    let escritaPronta = false;
    let saindo = false;
    let quadroEscrita = null;

    // ---- 1) Escrita -------------------------------------------------------
    function definirTraco(elemento, quanto) {
        // Padrão "1 2": o espaço maior que a linha evita sobra visível no fim
        elemento.setAttribute('stroke-dasharray', '1 2');
        elemento.setAttribute('stroke-dashoffset', (1 - quanto).toFixed(4));
    }

    // Caneta de verdade: começa devagar, acelera no meio e desacelera no fim
    function suavizar(t) {
        return t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
    }

    function completarEscrita() {
        cancelAnimationFrame(quadroEscrita);
        definirTraco(traco, 1);
        pingos.forEach(function (pingo) {
            definirTraco(pingo, 1);
        });
        escritaPronta = true;
        splash.classList.add('splash--pronta');
    }

    function escrever() {
        definirTraco(traco, 0);
        pingos.forEach(function (pingo) {
            definirTraco(pingo, 0);
        });

        if (menosMovimento) {
            completarEscrita();
            return;
        }

        const duracaoTotal = DURACAO_TRACO + pingos.length * DURACAO_PINGO;
        let inicio = null;

        // A cada quadro, calcula quanto tempo passou e quanto do traço mostrar.
        // Primeiro o traço principal; depois cada pingo, na ordem.
        function quadro(agora) {
            if (inicio === null) {
                inicio = agora + PAUSA_ANTES;
            }
            const passado = Math.max(0, agora - inicio);

            definirTraco(traco, suavizar(Math.min(passado / DURACAO_TRACO, 1)));
            pingos.forEach(function (pingo, indice) {
                const inicioPingo = DURACAO_TRACO + indice * DURACAO_PINGO;
                const t = Math.min(Math.max((passado - inicioPingo) / DURACAO_PINGO, 0), 1);
                definirTraco(pingo, t);
            });

            if (passado < duracaoTotal) {
                quadroEscrita = requestAnimationFrame(quadro);
            } else {
                completarEscrita();
            }
        }
        quadroEscrita = requestAnimationFrame(quadro);
    }

    // ---- 3) Saída -----------------------------------------------------------
    function sair() {
        if (saindo) {
            return;
        }
        saindo = true;

        // Se a pessoa rolar antes de a escrita terminar, completamos na hora
        if (!escritaPronta) {
            completarEscrita();
        }
        removerEscutas();

        if (menosMovimento) {
            splash.classList.add('splash--saindo');
            setTimeout(finalizar, 320);
            return;
        }

        // First e Last: onde a assinatura está e onde ela deve terminar
        const primeiro = assinatura.getBoundingClientRect();
        const ultimo = logoHeader.getBoundingClientRect();

        // Invert: deslocamento entre os centros e a escala entre as larguras.
        // Os dois SVGs têm o mesmo viewBox, então a proporção é a mesma.
        const escala = ultimo.width / primeiro.width;
        const dx = (ultimo.left + ultimo.width / 2) - (primeiro.left + primeiro.width / 2);
        const dy = (ultimo.top + ultimo.height / 2) - (primeiro.top + primeiro.height / 2);

        // Play: a transição do CSS anima do estado atual até este transform.
        // A espessura do traço também transiciona até a do logotipo (4), que no
        // tamanho final dá o mesmo traço do header, sem "salto" na troca.
        assinatura.style.transition = 'transform 1000ms cubic-bezier(0.65, 0, 0.35, 1), stroke-width 1000ms cubic-bezier(0.65, 0, 0.35, 1)';
        assinatura.style.transform = 'translate(' + dx + 'px, ' + dy + 'px) scale(' + escala + ')';
        assinatura.style.strokeWidth = '4';
        splash.classList.add('splash--saindo');

        assinatura.addEventListener('transitionend', function aoTerminar(evento) {
            if (evento.propertyName !== 'transform') {
                return;
            }
            assinatura.removeEventListener('transitionend', aoTerminar);
            finalizar();
        });
    }

    // A assinatura está exatamente sobre o logotipo: trocamos uma pela outra
    // (idênticas, sem diferença visível), liberamos a página e removemos a splash
    function finalizar() {
        raiz.classList.remove('com-splash');
        splash.remove();
        header.classList.add('cabecalho--revelar');
    }

    // ---- Gatilhos da saída ---------------------------------------------------
    const TECLAS_DE_ROLAR = ['ArrowDown', 'PageDown', 'End', ' ', 'Spacebar', 'Enter'];

    function aoRolarRoda(evento) {
        if (evento.deltaY > 0) {
            sair();
        }
    }

    function aoTeclar(evento) {
        if (TECLAS_DE_ROLAR.includes(evento.key) || evento.key === 'Tab') {
            evento.preventDefault();
            sair();
        }
    }

    let toqueInicialY = null;
    function aoTocar(evento) {
        toqueInicialY = evento.touches[0].clientY;
    }
    function aoArrastar(evento) {
        if (toqueInicialY !== null && toqueInicialY - evento.touches[0].clientY > 10) {
            sair();
        }
    }

    function removerEscutas() {
        window.removeEventListener('wheel', aoRolarRoda);
        window.removeEventListener('keydown', aoTeclar);
        window.removeEventListener('touchstart', aoTocar);
        window.removeEventListener('touchmove', aoArrastar);
        splash.removeEventListener('click', sair);
    }

    window.addEventListener('wheel', aoRolarRoda, { passive: true });
    window.addEventListener('keydown', aoTeclar);
    window.addEventListener('touchstart', aoTocar, { passive: true });
    window.addEventListener('touchmove', aoArrastar, { passive: true });
    splash.addEventListener('click', sair);

    // Segurança: se o navegador restaurar uma posição de rolagem depois do
    // carregamento (reload no meio da página), pulamos direto ao estado final
    window.addEventListener('load', function () {
        if (window.scrollY > 0 && !saindo) {
            saindo = true;
            removerEscutas();
            finalizar();
        }
    });

    escrever();
}

})();
