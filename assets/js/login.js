// =====================================================================
// Lapisari · login.js
// 1) Validação: campo vazio ganha borda de erro, aria-invalid e foco.
//    O servidor valida de novo; isto é só para responder rápido.
// 2) Grafite vivo: cresce conforme o e-mail é digitado.
// 3) Grafite quebra: se o servidor recusou o login, o grafite se parte.
// 4) Clique mecânico: com os campos preenchidos, a lapiseira "clica"
//    (botão traseiro afunda, grafite avança) e só então o formulário é enviado.
// =====================================================================

const form = document.querySelector('#formulario');
const campoEmail = document.querySelector('#email');
const campos = [campoEmail, document.querySelector('#password')];
const lapiseira = document.querySelector('#enviar');
const grafite = lapiseira.querySelector('.lapiseira__grafite');
const fragmento = lapiseira.querySelector('.lapiseira__fragmento');

// Quem pede menos movimento no sistema não vê a quebra nem espera o clique
const menosMovimento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;


// ---- 2) Grafite vivo ------------------------------------------------------
// Comprimento do grafite = escala horizontal de 1 (6 unidades, o normal)
// até 4 (24 unidades), proporcional aos caracteres digitados.
const GRAFITE_MINIMO = 1;
const GRAFITE_MAXIMO = 4;
const CARACTERES_PARA_O_MAXIMO = 28;

// Depois de uma quebra, o grafite volta curto e cresce só com o que for
// digitado a partir daí (o e-mail anterior continua no campo).
let caracteresNaQuebra = 0;
let comprimentoAtual = GRAFITE_MINIMO;

function atualizarGrafite() {
    const digitados = Math.max(0, campoEmail.value.length - caracteresNaQuebra);
    const fracao = Math.min(digitados / CARACTERES_PARA_O_MAXIMO, 1);
    comprimentoAtual = GRAFITE_MINIMO + fracao * (GRAFITE_MAXIMO - GRAFITE_MINIMO);
    lapiseira.style.setProperty('--grafite', comprimentoAtual.toFixed(3));
}

campoEmail.addEventListener('input', function () {
    // Se o usuário apagou tudo, zera a referência da quebra
    if (campoEmail.value.length < caracteresNaQuebra) {
        caracteresNaQuebra = campoEmail.value.length;
    }
    atualizarGrafite();
});


// ---- 3) Grafite quebra ----------------------------------------------------
// O PHP marca o botão com login__lapiseira--erro quando recusa o login.
// Como a página recarregou, primeiro mostramos o grafite no tamanho que ele
// tinha (o e-mail continua no campo) e então ele se parte.
function quebrarGrafite() {
    atualizarGrafite(); // grafite no comprimento do e-mail enviado

    if (menosMovimento) {
        caracteresNaQuebra = campoEmail.value.length;
        atualizarGrafite();
        return;
    }

    // Posiciona o fragmento sobre a metade de fora do grafite.
    // O grafite começa em x=276 e mede 6 unidades × escala.
    const inicio = 276;
    const fim = inicio + 6 * comprimentoAtual;
    const pontoDaQuebra = inicio + (fim - inicio) * 0.45;
    fragmento.setAttribute('d', 'M' + pontoDaQuebra.toFixed(2) + ' 12 H' + fim.toFixed(2));

    // Um instante para o usuário ver o grafite inteiro antes do estalo
    setTimeout(function () {
        fragmento.setAttribute('opacity', '1');
        fragmento.classList.add('lapiseira__fragmento--caindo');
        lapiseira.classList.add('login__lapiseira--quebrou');

        // O que sobrou é o toco: volta ao tamanho mínimo, sem transição (estalo seco)
        caracteresNaQuebra = campoEmail.value.length;
        grafite.style.transition = 'none';
        atualizarGrafite();
        void grafite.getBoundingClientRect(); // aplica agora, antes de religar a transição
        grafite.style.transition = '';
    }, 450);

    fragmento.addEventListener('animationend', function () {
        fragmento.classList.remove('lapiseira__fragmento--caindo');
        fragmento.setAttribute('opacity', '0');
        lapiseira.classList.remove('login__lapiseira--quebrou');
    }, { once: true });
}

if (lapiseira.classList.contains('login__lapiseira--erro')) {
    quebrarGrafite();
} else {
    atualizarGrafite(); // navegador pode ter preenchido o e-mail sozinho
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
