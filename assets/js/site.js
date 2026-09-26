// =====================================================================
// Lapisari · site.js
// Comportamentos pequenos usados em várias páginas. Carregado com "defer",
// então roda depois que o HTML inteiro já foi lido.
// =====================================================================

// Tudo dentro de uma função: as variáveis deste arquivo não se misturam
// com as de outros scripts da mesma página.
(function () {
'use strict';

// ---- Confirmação antes de enviar -------------------------------------
// Qualquer <form data-confirmar="mensagem"> pede confirmação antes de enviar.
// Um único listener no document atende todos os formulários da página
// ("delegação de eventos"): o evento submit sobe até o document.
document.addEventListener('submit', function (event) {
    const mensagem = event.target.dataset.confirmar;
    if (mensagem && !window.confirm(mensagem)) {
        event.preventDefault();
    }
});


// ---- Filtros da vitrine ----------------------------------------------
// Com JS ativo, o filtro é aplicado assim que um select muda e o botão
// "Filtrar" fica escondido. Sem JS, o botão continua funcionando.
document.querySelectorAll('form[data-envio-automatico]').forEach(function (form) {
    const botao = form.querySelector('[data-botao-filtrar]');
    if (botao) {
        botao.hidden = true;
    }
    form.addEventListener('change', function () {
        form.submit();
    });
});


// ---- Prévia da foto ---------------------------------------------------
// <input type="file" data-previa="id-da-img">: mostra a foto escolhida
// antes do envio. URL.createObjectURL cria um endereço temporário para o
// arquivo local, sem precisar mandá-lo ao servidor.
document.querySelectorAll('input[type="file"][data-previa]').forEach(function (input) {
    const previa = document.getElementById(input.dataset.previa);
    if (!previa) {
        return;
    }
    const imagemOriginal = previa.src;

    input.addEventListener('change', function () {
        const arquivo = input.files[0];
        previa.src = arquivo ? URL.createObjectURL(arquivo) : imagemOriginal;
    });

    // "Limpar"/"Desfazer" apaga o arquivo escolhido; a prévia volta junto
    if (input.form) {
        input.form.addEventListener('reset', function () {
            previa.src = imagemOriginal;
        });
    }
});

})();
