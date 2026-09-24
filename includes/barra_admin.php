<?php
// Barra do administrador, logo abaixo do header. Incluída pelo header.php só quando usuario_admin().
// A página pode definir $pagina ('relatorio', 'consultar'...) para destacar o link atual.
$links_admin = [
    'relatorio' => ['app/select.php', 'Relatório'],
    'consultar' => ['app/select_where.php', 'Consultar'],
    'atualizar' => ['app/update.php', 'Atualizar'],
    'excluir'   => ['app/delete.php', 'Excluir'],
];
$pagina = $pagina ?? '';
?>
<div class="barra-admin">
    <div class="container barra-admin__conteudo">
        <span class="barra-admin__rotulo">Administração</span>

        <nav class="barra-admin__links" aria-label="Administração">
            <?php foreach ($links_admin as $chave => [$caminho, $rotulo]): ?>
                <a href="<?= url($caminho) ?>" <?= $pagina === $chave ? 'aria-current="page"' : '' ?>><?= $rotulo ?></a>
            <?php endforeach; ?>
        </nav>

        <a href="<?= url('app/create.php') ?>" class="botao botao--primario botao--pequeno"
           <?= $pagina === 'cadastrar' ? 'aria-current="page"' : '' ?>>+ Cadastrar Nova Lapiseira</a>
    </div>
</div>
