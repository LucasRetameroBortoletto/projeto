<?php
// Campos do formulário de lapiseira, compartilhados por app/create.php e app/update.php.
// Espera que a página tenha definido:
//   $valores       -> ['modelo', 'marca', 'bitola', 'preco', 'ativo'] para preencher os campos
//   $erros         -> ['campo' => 'mensagem'] vindos de validar_lapiseira()
//   $imagem_atual  -> (opcional) caminho da foto já cadastrada, na edição

$imagem_atual = $imagem_atual ?? null;

// Atributos de acessibilidade de um campo com erro: marca como inválido e
// liga o campo à mensagem, para o leitor de tela ler a mensagem junto.
$atributos_erro = function ($campo) use ($erros) {
    return isset($erros[$campo]) ? 'aria-invalid="true" aria-describedby="erro-' . $campo . '"' : '';
};
$mensagem_erro = function ($campo) use ($erros) {
    return isset($erros[$campo]) ? '<p class="campo__erro" id="erro-' . $campo . '">' . e($erros[$campo]) . '</p>' : '';
};
?>
<div class="formulario__linha">
<div class="campo">
    <label for="modelo">Modelo</label>
    <input type="text" name="modelo" id="modelo" class="campo__controle" maxlength="120" required
           value="<?= e($valores['modelo']) ?>" <?= $atributos_erro('modelo') ?>>
    <?= $mensagem_erro('modelo') ?>
</div>

<div class="campo">
    <label for="marca">Marca</label>
    <input type="text" name="marca" id="marca" class="campo__controle" maxlength="60" required
           value="<?= e($valores['marca']) ?>" <?= $atributos_erro('marca') ?>>
    <?= $mensagem_erro('marca') ?>
</div>
</div>

<div class="formulario__linha">
    <div class="campo">
        <label for="bitola">Bitola</label>
        <select name="bitola" id="bitola" class="campo__controle" required <?= $atributos_erro('bitola') ?>>
            <option value="">Selecione</option>
            <?php foreach (BITOLAS as $opcao): ?>
                <option value="<?= $opcao ?>" <?= $opcao === $valores['bitola'] ? 'selected' : '' ?>><?= formatar_bitola($opcao) ?></option>
            <?php endforeach; ?>
        </select>
        <?= $mensagem_erro('bitola') ?>
    </div>

    <div class="campo">
        <label for="preco">Preço (R$)</label>
        <input type="number" name="preco" id="preco" class="campo__controle" min="0" step="0.01" required
               inputmode="decimal" value="<?= e($valores['preco']) ?>" <?= $atributos_erro('preco') ?>>
        <?= $mensagem_erro('preco') ?>
    </div>
</div>

<div class="formulario__linha">
<div class="campo-foto">
    <!-- A prévia é atualizada pelo site.js assim que um arquivo é escolhido -->
    <img src="<?= $imagem_atual ? url($imagem_atual) : asset('assets/img/produto-sem-foto.svg') ?>" alt=""
         class="campo-foto__previa" id="previa-imagem">
    <div class="campo">
        <label for="imagem">Foto</label>
        <input type="file" name="imagem" id="imagem" class="campo__controle"
               accept="image/jpeg,image/png,image/webp" data-previa="previa-imagem"
               aria-describedby="ajuda-imagem<?= isset($erros['imagem']) ? ' erro-imagem' : '' ?>"
               <?= isset($erros['imagem']) ? 'aria-invalid="true"' : '' ?>>
        <p class="campo__ajuda" id="ajuda-imagem">
            JPG, PNG ou WEBP, até 2 MB.<?= $imagem_atual ? ' Envie um arquivo só se quiser trocar a foto atual.' : '' ?>
        </p>
        <?= $mensagem_erro('imagem') ?>
    </div>
</div>

<fieldset class="campo">
    <legend class="campo__rotulo">Disponível na vitrine</legend>
    <div class="opcoes">
        <label class="opcao">
            <input type="radio" name="ativo" value="true" required <?= $valores['ativo'] === 'true' ? 'checked' : '' ?>> Sim
        </label>
        <label class="opcao">
            <input type="radio" name="ativo" value="false" <?= $valores['ativo'] === 'false' ? 'checked' : '' ?>> Não
        </label>
    </div>
    <?= $mensagem_erro('ativo') ?>
</fieldset>
</div>
