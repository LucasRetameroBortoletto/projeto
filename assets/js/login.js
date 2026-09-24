// =====================================================================
// Lapisari · login.js
// Validação do formulário de login antes do envio (antes ficava inline em login.php).
// Campo vazio: borda de erro, aria-invalid e foco no campo. O servidor valida de novo.
// =====================================================================

const form = document.querySelector('#formulario');
const campos = [document.querySelector('#email'), document.querySelector('#password')];

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
    }
});
