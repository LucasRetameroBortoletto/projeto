<?php
// Relatório com todas as lapiseiras, inclusive as que estão fora da vitrine (área do administrador)
require_once __DIR__ . '/../login/verifica_admin.php';

$lapiseiras = relatorio($conexao, '', 'novidades', true);
$total_ativas = count(array_filter($lapiseiras, fn($l) => $l['ativo']));

$titulo = 'Relatório · Lapisari';
$pagina = 'relatorio';
include __DIR__ . '/../includes/head.php';
?>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="pagina">
        <div class="container">
            <div class="pagina__topo">
                <p class="sobretitulo">Administração</p>
                <h1 class="pagina__titulo">Relatório</h1>
                <p class="pagina__texto">
                    <?= count($lapiseiras) ?> <?= count($lapiseiras) === 1 ? 'lapiseira cadastrada' : 'lapiseiras cadastradas' ?>,
                    <?= $total_ativas ?> na vitrine.
                </p>
            </div>

            <?php if (!$lapiseiras): ?>
                <p class="pagina__texto">Nenhuma lapiseira cadastrada ainda.</p>
            <?php else: ?>
                <div class="tabela-container">
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th scope="col"><span class="visualmente-oculto">Foto</span></th>
                                <th scope="col">ID</th>
                                <th scope="col">Modelo</th>
                                <th scope="col">Marca</th>
                                <th scope="col">Bitola</th>
                                <th scope="col" class="tabela__numero">Preço</th>
                                <th scope="col">Vitrine</th>
                                <th scope="col">Cadastro</th>
                                <th scope="col"><span class="visualmente-oculto">Ações</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lapiseiras as $lapiseira): ?>
                                <tr>
                                    <td><img src="<?= imagem_lapiseira($lapiseira) ?>" alt="" class="tabela__miniatura" loading="lazy"></td>
                                    <td><?= $lapiseira['id'] ?></td>
                                    <td><?= e($lapiseira['modelo']) ?></td>
                                    <td><?= e($lapiseira['marca']) ?></td>
                                    <td><?= formatar_bitola($lapiseira['bitola']) ?></td>
                                    <td class="tabela__numero"><?= formatar_preco($lapiseira['preco']) ?></td>
                                    <td>
                                        <span class="selo<?= $lapiseira['ativo'] ? ' selo--ativo' : '' ?>">
                                            <?= $lapiseira['ativo'] ? 'Na vitrine' : 'Fora' ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($lapiseira['criado_em'])) ?></td>
                                    <td>
                                        <div class="tabela__acoes">
                                            <a href="<?= url('app/update.php?' . http_build_query(['id' => $lapiseira['id'], 'voltar' => 'app/select.php'])) ?>"
                                               class="botao-icone" aria-label="Editar <?= e($lapiseira['modelo']) ?>" title="Editar">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"
                                                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                                    <path d="M4 20 L5 15.5 L16 4.5 L19.5 8 L8.5 19 Z"/>
                                                    <path d="M13.5 7 L17 10.5"/>
                                                </svg>
                                            </a>
                                            <form method="post" action="<?= url('app/delete.php') ?>"
                                                  data-confirmar="Excluir a lapiseira &quot;<?= e($lapiseira['marca'] . ' ' . $lapiseira['modelo']) ?>&quot;? Essa ação não pode ser desfeita.">
                                                <input type="hidden" name="id" value="<?= $lapiseira['id'] ?>">
                                                <input type="hidden" name="voltar" value="app/select.php">
                                                <button type="submit" class="botao-icone botao-icone--perigo"
                                                        aria-label="Excluir <?= e($lapiseira['modelo']) ?>" title="Excluir">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"
                                                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                                        <path d="M4 7 H20 M9 7 V4.5 H15 V7 M6.5 7 L7.5 20 H16.5 L17.5 7"/>
                                                        <path d="M10.5 11 V16 M13.5 11 V16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
