// =====================================================================
// Lapisari · login.js
// 1) Validação: campo vazio ganha borda de erro, aria-invalid e foco.
//    O servidor valida de novo; isto é só para responder rápido.
// 2) Clique mecânico: com os campos preenchidos, a lapiseira "clica"
//    (botão traseiro afunda, grafite avança) e só então o formulário é enviado.
// =====================================================================

const form = document.querySelector('#formulario');
const campos = [document.querySelector('#email'), document.querySelector('#password')];
const lapiseira = document.querySelector('#enviar');

// Quem pede menos movimento no sistema envia na hora, sem esperar a animação
const menosMovimento = window.matchMedia('(prefers-reduced-motion: reduce)');

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

    if (menosMovimento.matches) {
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
