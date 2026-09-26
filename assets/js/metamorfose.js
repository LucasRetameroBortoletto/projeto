// =====================================================================
// Lapisari · metamorfose.js (Fase 4: o header vira o cartão de login)
//
// Abertura (clique em "Entrar"), ~650 ms no total:
//   0 ─ 150 ms    botões Entrar/Carrinho somem (fade); o véu escuro começa
//   150 ─ 570 ms  o retângulo preto do header encolhe nas laterais e cresce
//                 na vertical até virar o cartão; o logotipo sobe para o topo
//                 do cartão e a lapiseira desce para a base, onde vira o botão
//   450 ─ 700 ms  título, campos e links surgem em cascata
// Fechamento (X, clique no véu ou Esc): o caminho inverso exato.
//
// Técnica FLIP (First, Last, Invert, Play), com a Web Animations API
// (elemento.animate(), nativa do navegador, sem biblioteca):
//   - o cartão já existe no HTML, no seu lugar FINAL (centro da tela);
//   - medimos onde cada peça está no header (First) e no cartão (Last);
//   - calculamos o transform que leva a peça do cartão de volta ao header
//     (Invert) e animamos desse transform até "nenhum" (Play).
// Só transform e opacity são animados: a placa de vídeo faz o trabalho.
// Para fechar, a mesma animação é tocada no sentido contrário.
// =====================================================================

(function () {
'use strict';

const overlay = document.querySelector('[data-metamorfose]');
// O "Entrar" do header. Outros links com data-abrir-metamorfose (ex.: "Entrar"
// na página de criar conta) também abrem o cartão.
const botaoEntrar = document.querySelector('.cabecalho [data-abrir-metamorfose]');
if (!overlay || !botaoEntrar) {
    return; // usuário logado ou página sem header (ex.: login.php)
}

const raiz = document.documentElement;
const header = document.querySelector('.cabecalho');
const acoesHeader = header.querySelector('.cabecalho__acoes');
const logoHeader = header.querySelector('.cabecalho__assinatura');
const lapiseiraHeader = header.querySelector('.cabecalho__lapiseira');

const veu = overlay.querySelector('.metamorfose__veu');
const fundo = overlay.querySelector('.metamorfose__fundo');
const logoCartao = overlay.querySelector('.metamorfose__assinatura');
const lapiseiraCartao = overlay.querySelector('.metamorfose__lapiseira');
const entradas = Array.from(overlay.querySelectorAll('.metamorfose__fechar, .metamorfose__entrada'));
const campoEmail = overlay.querySelector('input[name="email"]');
const campoSenha = overlay.querySelector('input[name="password"]');

const menosMovimento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Tempos (ms) e curva. A curva acelera e freia de forma simétrica, o que dá
// a sensação de uma peça pesada e precisa mudando de forma.
const FADE_BOTOES = 150;
const MORFOSE = 420;
const ENTRADA = 240;
const CASCATA = 35;
const CURVA = 'cubic-bezier(0.65, 0, 0.35, 1)';

// 'fechado' | 'abrindo' | 'aberto' | 'fechando'
let estado = overlay.hidden ? 'fechado' : 'aberto';

// Pediram para fechar (Esc, X ou véu) enquanto o cartão ainda abria:
// guardamos o pedido e fechamos assim que a abertura terminar
let fecharAoTerminarDeAbrir = false;


// ---- Invert: o transform que leva uma peça do cartão até o header ------------
// transform-origin é o canto superior esquerdo (definido no CSS), então basta
// deslocar os cantos e escalar pela razão entre os tamanhos.

// Retângulo do fundo: escala diferente na largura e na altura (é só um bloco preto)
function inverterRetangulo(de, para) {
    return 'translate(' + (de.left - para.left) + 'px, ' + (de.top - para.top) + 'px) '
         + 'scale(' + (de.width / para.width) + ', ' + (de.height / para.height) + ')';
}

// Logotipo e lapiseira: escala igual nos dois eixos, para o desenho não deformar
function inverterDesenho(de, para) {
    const escala = de.width / para.width;
    return 'translate(' + (de.left - para.left) + 'px, ' + (de.top - para.top) + 'px) '
         + 'scale(' + escala + ')';
}

function medir() {
    return {
        header: header.getBoundingClientRect(),
        fundo: fundo.getBoundingClientRect(),
        logoHeader: logoHeader.getBoundingClientRect(),
        logoCartao: logoCartao.getBoundingClientRect(),
        lapiseiraHeader: lapiseiraHeader.getBoundingClientRect(),
        lapiseiraCartao: lapiseiraCartao.getBoundingClientRect(),
    };
}

function cancelarAnimacoes(elementos) {
    elementos.forEach(function (elemento) {
        elemento.getAnimations().forEach(function (animacao) {
            animacao.cancel();
        });
    });
}

const pecasAnimadas = [veu, fundo, logoCartao, lapiseiraCartao].concat(entradas);


// ---- Abrir ---------------------------------------------------------------------
async function abrir() {
    if (estado !== 'fechado') {
        return;
    }
    estado = 'abrindo';

    overlay.hidden = false;               // cartão no lugar final (ainda invisível ao olho)
    raiz.classList.add('metamorfose-aberta');

    if (menosMovimento) {
        header.classList.add('cabecalho--metamorfose');
        await overlay.animate([{ opacity: 0 }, { opacity: 1 }], { duration: 150 }).finished;
        terminarAbertura();
        return;
    }

    // First e Last: tudo medido de uma vez, antes de qualquer animação
    const r = medir();

    // 1) Os botões do header somem. O header fica por cima do cartão nesse
    //    instante; como o fundo, o logotipo e a lapiseira do cartão começam
    //    exatamente sobre os do header, a troca é invisível.
    header.classList.add('cabecalho--acima');
    const fadeBotoes = acoesHeader.animate([{ opacity: 1 }, { opacity: 0 }],
        { duration: FADE_BOTOES, easing: 'ease-out', fill: 'forwards' });

    veu.animate([{ opacity: 0 }, { opacity: 1 }],
        { duration: FADE_BOTOES + MORFOSE, easing: 'ease', fill: 'both' });

    // 2) Invert + Play. fill: 'both' mantém cada peça no ponto de partida
    //    durante o atraso (delay), ou seja, parada sobre o header.
    const opcoes = { duration: MORFOSE, delay: FADE_BOTOES, easing: CURVA, fill: 'both' };
    const morfose = [
        fundo.animate([{ transform: inverterRetangulo(r.header, r.fundo) }, { transform: 'none' }], opcoes),
        logoCartao.animate([{ transform: inverterDesenho(r.logoHeader, r.logoCartao) }, { transform: 'none' }], opcoes),
        lapiseiraCartao.animate([{ transform: inverterDesenho(r.lapiseiraHeader, r.lapiseiraCartao) }, { transform: 'none' }], opcoes),
    ];

    // 3) Título, campos e links surgem em cascata perto do fim da transformação
    const cascata = entradas.map(function (elemento, indice) {
        return elemento.animate(
            [{ opacity: 0, transform: 'translateY(8px)' }, { opacity: 1, transform: 'none' }],
            { duration: ENTRADA, delay: FADE_BOTOES + MORFOSE * 0.7 + indice * CASCATA, easing: 'ease-out', fill: 'both' }
        );
    });

    // Quando os botões sumiram, o header pode ser escondido: a partir daqui
    // quem aparece é o fundo do cartão, que começa a encolher
    await fadeBotoes.finished;
    header.classList.add('cabecalho--metamorfose');
    header.classList.remove('cabecalho--acima');

    await Promise.all(morfose.concat(cascata).map(function (animacao) {
        return animacao.finished;
    }));

    // Estado final = o que o CSS já define: removemos as animações
    cancelarAnimacoes(pecasAnimadas);
    terminarAbertura();
}

function terminarAbertura() {
    estado = 'aberto';
    if (fecharAoTerminarDeAbrir) {
        fecharAoTerminarDeAbrir = false;
        fechar();
        return;
    }
    focarPrimeiroCampo();
}


// ---- Fechar (caminho inverso) ----------------------------------------------------
async function fechar() {
    if (estado === 'abrindo') {
        fecharAoTerminarDeAbrir = true;
        return;
    }
    if (estado !== 'aberto') {
        return;
    }
    estado = 'fechando';

    if (menosMovimento) {
        await overlay.animate([{ opacity: 1 }, { opacity: 0 }], { duration: 150 }).finished;
        concluirFechamento();
        return;
    }

    const r = medir();

    // 1) Conteúdo some rápido, na ordem inversa
    const saidaConteudo = entradas.slice().reverse().map(function (elemento, indice) {
        return elemento.animate([{ opacity: 1 }, { opacity: 0 }],
            { duration: 120, delay: indice * 15, easing: 'ease-in', fill: 'forwards' });
    });

    // 2) Cartão volta a ser o header
    const opcoes = { duration: MORFOSE, delay: 100, easing: CURVA, fill: 'forwards' };
    const morfose = [
        fundo.animate([{ transform: 'none' }, { transform: inverterRetangulo(r.header, r.fundo) }], opcoes),
        logoCartao.animate([{ transform: 'none' }, { transform: inverterDesenho(r.logoHeader, r.logoCartao) }], opcoes),
        lapiseiraCartao.animate([{ transform: 'none' }, { transform: inverterDesenho(r.lapiseiraHeader, r.lapiseiraCartao) }], opcoes),
    ];
    veu.animate([{ opacity: 1 }, { opacity: 0 }],
        { duration: MORFOSE + 100, easing: 'ease', fill: 'forwards' });

    await Promise.all(morfose.concat(saidaConteudo).map(function (animacao) {
        return animacao.finished;
    }));

    concluirFechamento();
}

// O cartão já está exatamente sobre o header: mostramos o header de novo
// (por cima), escondemos o cartão e os botões reaparecem com fade
function concluirFechamento() {
    header.classList.add('cabecalho--acima');
    header.classList.remove('cabecalho--metamorfose');
    cancelarAnimacoes([acoesHeader]);

    overlay.hidden = true;
    cancelarAnimacoes(pecasAnimadas.concat([overlay]));
    raiz.classList.remove('metamorfose-aberta');

    const fadeBotoes = acoesHeader.animate([{ opacity: 0 }, { opacity: 1 }],
        { duration: menosMovimento ? 0 : 200, easing: 'ease-out' });
    fadeBotoes.finished.then(function () {
        header.classList.remove('cabecalho--acima');
    });

    estado = 'fechado';

    // O foco volta para quem abriu o cartão. Esperamos um quadro: o header
    // acabou de voltar a ser visível e o navegador recusa foco em elemento
    // que ainda conta como invisível (acontecia com movimento reduzido ligado).
    requestAnimationFrame(function () {
        requestAnimationFrame(function () {
            quemAbriu.focus();
        });
    });
}


// ---- Acessibilidade: foco preso no cartão, Esc fecha ----------------------------
function focarPrimeiroCampo() {
    // Com e-mail preenchido (voltou de um erro), o foco vai para a senha
    (campoEmail.value ? campoSenha : campoEmail).focus();
}

function elementosFocaveis() {
    return Array.from(overlay.querySelectorAll(
        'a[href], button:not([disabled]), input:not([type="hidden"]):not([disabled])'
    ));
}

// Esc fecha. Escutamos na página inteira (e não só no cartão) porque, durante
// a abertura, o foco ainda está no link "Entrar" que foi clicado, fora do cartão.
document.addEventListener('keydown', function (evento) {
    if (evento.key === 'Escape' && estado !== 'fechado') {
        evento.preventDefault();
        fechar();
    }
});

overlay.addEventListener('keydown', function (evento) {
    // Tab no último elemento volta ao primeiro (e Shift+Tab no primeiro vai
    // ao último): o foco não escapa para a página escondida atrás do véu
    if (evento.key === 'Tab') {
        const focaveis = elementosFocaveis();
        const primeiro = focaveis[0];
        const ultimo = focaveis[focaveis.length - 1];
        if (evento.shiftKey && document.activeElement === primeiro) {
            evento.preventDefault();
            ultimo.focus();
        } else if (!evento.shiftKey && document.activeElement === ultimo) {
            evento.preventDefault();
            primeiro.focus();
        }
    }
});

overlay.querySelectorAll('[data-fechar-metamorfose]').forEach(function (elemento) {
    elemento.addEventListener('click', fechar);
});

// Quem abriu o cartão recebe o foco de volta quando ele fecha
let quemAbriu = botaoEntrar;

document.querySelectorAll('[data-abrir-metamorfose]').forEach(function (link) {
    link.addEventListener('click', function (evento) {
        evento.preventDefault(); // com JS não vamos para login.php: abrimos o cartão
        quemAbriu = link;
        abrir();
    });
});

// Voltou de um login recusado: o cartão já veio aberto do servidor, sem animação
if (estado === 'aberto') {
    focarPrimeiroCampo();
}

})();
