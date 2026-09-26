<?php
// Campos do formulário de lapiseira, usados no cadastro (app/create.php)
// e na edição (app/update.php), para não repetir o mesmo HTML nos dois.
// A página precisa ter definido antes do include:
//   $valores      -> valores para preencher os campos (modelo, marca, bitola, preco, ativo)
//   $imagem_atual -> (só na edição) caminho da foto já cadastrada
if (!isset($imagem_atual)) {
    $imagem_atual = null;
}
// Garante que todos os campos existem (evita aviso do PHP se algum não veio no envio)
foreach (['modelo', 'marca', 'bitola', 'preco', 'ativo'] as $campo) {
    if (!isset($valores[$campo])) {
        $valores[$campo] = '';
    }
}
?>
<div class="formulario__linha">
    <div class="campo">
        <label for="modelo">Modelo</label>
        <input type="text" name="modelo" id="modelo" class="campo__controle" maxlength="120" required
               value="<?= e($valores['modelo']) ?>">
    </div>

    <div class="campo">
        <label for="marca">Marca</label>
        <input type="text" name="marca" id="marca" class="campo__controle" maxlength="60" required
               value="<?= e($valores['marca']) ?>">
    </div>
</div>

<div class="formulario__linha">
    <div class="campo">
        <label for="bitola">Bitola</label>
        <select name="bitola" id="bitola" class="campo__controle" required>
            <option value="">Selecione</option>
            <?php foreach ($bitolas as $opcao): ?>
                <!-- "selected" deixa marcada a bitola que já estava escolhida -->
                <option value="<?= $opcao ?>" <?= $opcao == $valores['bitola'] ? 'selected' : '' ?>><?= formatar_bitola($opcao) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="campo">
        <label for="preco">Preço (R$)</label>
        <input type="number" name="preco" id="preco" class="campo__controle" min="0" step="0.01" required
               value="<?= e($valores['preco']) ?>">
    </div>
</div>

<div class="formulario__linha">
    <div class="campo-foto">
        <!-- Prévia da foto: o site.js troca esta imagem assim que um arquivo é escolhido -->
        <?php if ($imagem_atual): ?>
            <img src="<?= url($imagem_atual) ?>" alt="" class="campo-foto__previa" id="previa-imagem">
        <?php else: ?>
            <img src="<?= url('assets/img/produto-sem-foto.svg') ?>" alt="" class="campo-foto__previa" id="previa-imagem">
        <?php endif; ?>

        <div class="campo">
            <label for="imagem">Foto</label>
            <input type="file" name="imagem" id="imagem" class="campo__controle"
                   accept="image/jpeg,image/png,image/webp" data-previa="previa-imagem">
            <p class="campo__ajuda">
                JPG, PNG ou WEBP, até 2 MB.
                <?php if ($imagem_atual): ?>Deixe vazio para manter a foto atual.<?php endif; ?>
            </p>
        </div>
    </div>

    <fieldset class="campo">
        <legend class="campo__rotulo">Disponível na vitrine</legend>
        <div class="opcoes">
            <label class="opcao">
                <input type="radio" name="ativo" value="true" required <?= $valores['ativo'] == 'true' ? 'checked' : '' ?>> Sim
            </label>
            <label class="opcao">
                <input type="radio" name="ativo" value="false" <?= $valores['ativo'] == 'false' ? 'checked' : '' ?>> Não
            </label>
        </div>
    </fieldset>
</div>
