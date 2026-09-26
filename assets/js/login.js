// =====================================================================
// Lapisari · login.js
// 1) Validação: campo vazio ganha borda de erro, aria-invalid e foco.
//    O servidor valida de novo; isto é só para responder rápido.
// 2) Grafite vivo: a cada tecla na senha a lapiseira clica e o grafite cresce.
// 3) Grafite quebra: se o servidor recusou o login, o grafite se parte.
// 4) Clique mecânico: com os campos preenchidos, a lapiseira "clica"
//    (botão traseiro afunda, grafite avança) e só então o formulário é enviado.
// =====================================================================

// Tudo dentro de uma função: as variáveis deste arquivo não se misturam
// com as de outros scripts da mesma página.
(function () {
'use strict';

// O formulário de login aparece em dois lugares: na página login/login.php e
// no cartão da metamorfose (header). Por isso buscamos pelo atributo
// data-form-login e pelos nomes dos campos, e não por ids fixos.
const form = document.querySelector('[data-form-login]');
if (!form) {
    return; // página sem formulário de login (ex.: usuário já logado)
}
const campoEmail = form.elements.email;
const campoSenha = form.elements.password;
const campos = [campoEmail, campoSenha];
const lapiseira = form.querySelector('.login__lapiseira');
const grafite = lapiseira.querySelector('.lapiseira__grafite');
const fragmento = lapiseira.querySelector('.lapiseira__fragmento');

// Quem pede menos movimento no sistema não vê a quebra nem espera o clique
const menosMovimento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;


// ---- 2) Grafite vivo (campo de SENHA) --------------------------------------
// A cada tecla digitada na senha, a lapiseira "clica" (o botão de trás afunda,
// como quando se aperta uma lapiseira de verdade) e o grafite avança.
// Comprimento do grafite = escala horizontal de 1 (6 unidades, o normal)
// até 9 (54 unidades, bem para fora da ponta), proporcional aos caracteres.
const GRAFITE_MINIMO = 1;
const GRAFITE_MAXIMO = 9;
const CARACTERES_PARA_O_MAXIMO = 12;

let comprimentoAtual = GRAFITE_MINIMO;
let temporizadorClique = null;

function atualizarGrafite() {
    const fracao = Math.min(campoSenha.value.length / CARACTERES_PARA_O_MAXIMO, 1);
    comprimentoAtual = GRAFITE_MINIMO + fracao * (GRAFITE_MAXIMO - GRAFITE_MINIMO);
    lapiseira.style.setProperty('--grafite', comprimentoAtual.toFixed(3));
}

// Clique rápido do botão traseiro: põe a classe e tira logo depois.
// Se a pessoa digitar rápido, cada tecla reinicia o clique.
function clicarLapiseira() {
    if (menosMovimento) {
        return;
    }
    lapiseira.classList.remove('login__lapiseira--tecla');
    void lapiseira.offsetWidth;   // obriga o navegador a "ver" a remoção antes de repor
    lapiseira.classList.add('login__lapiseira--tecla');

    clearTimeout(temporizadorClique);
    temporizadorClique = setTimeout(function () {
        lapiseira.classList.remove('login__lapiseira--tecla');
    }, 110);
}

campoSenha.addEventListener('input', function () {
    clicarLapiseira();
    atualizarGrafite();
});


// ---- 3) Grafite quebra ----------------------------------------------------
// O PHP marca o botão com login__lapiseira--erro quando recusa o login.
// A página recarregou com a senha vazia: o grafite avança (como se ainda
// estivesse com a senha digitada) e então se parte, voltando ao toco.
const GRAFITE_NA_QUEBRA = 6;

function quebrarGrafite() {
    if (menosMovimento) {
        atualizarGrafite();
        return;
    }

    // 1) grafite avança até o tamanho da quebra
    comprimentoAtual = GRAFITE_NA_QUEBRA;
    lapiseira.style.setProperty('--grafite', comprimentoAtual);

    // Posiciona o fragmento sobre a metade de fora do grafite.
    // O grafite começa em x=276 e mede 6 unidades × escala.
    const inicio = 276;
    const fim = inicio + 6 * comprimentoAtual;
    const pontoDaQuebra = inicio + (fim - inicio) * 0.45;
    fragmento.setAttribute('d', 'M' + pontoDaQuebra.toFixed(2) + ' 12 H' + fim.toFixed(2));

    // 2) um instante depois, o estalo
    setTimeout(function () {
        fragmento.setAttribute('opacity', '1');
        fragmento.classList.add('lapiseira__fragmento--caindo');
        lapiseira.classList.add('login__lapiseira--quebrou');

        // O que sobrou é o toco: volta ao tamanho da senha (vazia), sem transição
        grafite.style.transition = 'none';
        atualizarGrafite();
        void grafite.getBoundingClientRect(); // aplica agora, antes de religar a transição
        grafite.style.transition = '';
    }, 650);

    fragmento.addEventListener('animationend', function () {
        fragmento.classList.remove('lapiseira__fragmento--caindo');
        fragmento.setAttribute('opacity', '0');
        lapiseira.classList.remove('login__lapiseira--quebrou');
    }, { once: true });
}

if (lapiseira.classList.contains('login__lapiseira--erro')) {
    quebrarGrafite();
} else {
    atualizarGrafite(); // o navegador pode ter preenchido a senha sozinho
}


// ---- 1) e 4) Validação e clique mecânico ------------------------------------
// Duração do clique: afundar (120 ms, definido no CSS) + um instante parado
const DURACAO_CLIQUE = 220;
let enviando = false;

form.addEventListener('submit', function (event) {
    let primeiroInvalido = null;

    campos.forEach(function (campo) {
        const vazio = campo.value.trim() === '';

        campo.classList.toggle('campo-invalido', vazio); // classes criadas no login.css
        campo.classList.toggle('campo-valido', !vazio);
        campo.setAttribute('aria-invalid', vazio ? 'true' : 'false');

        if (vazio && !primeiroInvalido) {
            primeiroInvalido = campo;
        }
    });

    if (primeiroInvalido) {
        event.preventDefault(); // Impede o envio do formulário
        primeiroInvalido.focus();
        return;
    }

    if (menosMovimento) {
        return; // envio normal, sem animação
    }

    // Segura o envio, mostra o clique e envia depois.
    // form.submit() envia sem disparar este evento de novo; "enviando" evita
    // que um segundo Enter durante a animação envie duas vezes.
    event.preventDefault();
    if (enviando) {
        return;
    }
    enviando = true;
    lapiseira.classList.add('login__lapiseira--clique');

    setTimeout(function () {
        form.submit();
    }, DURACAO_CLIQUE);
});

})();
