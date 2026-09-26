<?php
// Barra do administrador, logo abaixo do header.
// Incluída pelo header.php só quando o usuário é admin.
// A página pode definir $pagina ('relatorio', 'usuarios'...) para o link dela ficar destacado.
if (!isset($pagina)) {
    $pagina = '';
}

// Links da barra: chave => [endereço, texto]
$links_admin = [
    'relatorio' => ['app/select.php', 'Relatório'],
    'consultar' => ['app/select_where.php', 'Consultar'],
    'atualizar' => ['app/update.php', 'Atualizar'],
    'excluir'   => ['app/delete.php', 'Excluir'],
    'usuarios'  => ['app/usuarios.php', 'Usuários'],
];
?>
<div class="barra-admin">
    <div class="container barra-admin__conteudo">
        <!-- Volta para a vitrine da página inicial (#vitrine pula a animação de abertura) -->
        <a href="<?= url('index.php') ?>#vitrine" class="barra-admin__voltar">
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                <path d="M10 3 L5 8 L10 13"/>
            </svg>
            Vitrine
        </a>

        <span class="barra-admin__rotulo">Administração</span>

        <nav class="barra-admin__links" aria-label="Administração">
            <?php foreach ($links_admin as $chave => $link): ?>
                <a href="<?= url($link[0]) ?>" <?= $pagina == $chave ? 'aria-current="page"' : '' ?>><?= $link[1] ?></a>
            <?php endforeach; ?>
        </nav>

        <a href="<?= url('app/create.php') ?>" class="botao botao--primario botao--pequeno"
           <?= $pagina == 'cadastrar' ? 'aria-current="page"' : '' ?>>+ Cadastrar Nova Lapiseira</a>
    </div>
</div>
