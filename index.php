<?php
require_once __DIR__ . '/includes/functions.php';

// Filtros da vitrine vêm da URL (?bitola=0.5&ordem=preco_asc).
// Valores fora das listas permitidas são ignorados.
$bitola = in_array($_GET['bitola'] ?? '', BITOLAS, true) ? $_GET['bitola'] : '';
$ordem = isset(ORDENACOES[$_GET['ordem'] ?? '']) ? $_GET['ordem'] : 'novidades';

// O admin também vê as lapiseiras inativas (marcadas no card), os clientes não.
$lapiseiras = relatorio($conexao, $bitola, $ordem, usuario_admin());

// Endereço desta vitrine com os filtros atuais: usado para voltar ao mesmo lugar
// depois de adicionar ao carrinho ou excluir uma lapiseira.
$filtros_atuais = array_filter(['bitola' => $bitola, 'ordem' => $ordem !== 'novidades' ? $ordem : '']);
$voltar = 'index.php' . ($filtros_atuais ? '?' . http_build_query($filtros_atuais) : '') . '#vitrine';

$titulo = 'Lapisari · Lapiseiras de coleção';
$estilos = ['vitrine.css'];
$pagina = 'inicio';
include __DIR__ . '/includes/head.php';
?>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <main>
        <!-- Seção conceitual: os três pilares da marca -->
        <section class="conceito" aria-labelledby="conceito-titulo">
            <div class="container">
                <div class="conceito__abertura">
                    <p class="sobretitulo">A casa da lapiseira</p>
                    <h1 id="conceito-titulo" class="conceito__titulo">Instrumentos de escrita escolhidos como peças de relojoaria.</h1>
                    <p class="conceito__texto">
                        Revendemos lapiseiras de marcas oficiais, selecionadas pelo mecanismo,
                        pelo equilíbrio na mão e pela história de cada modelo.
                    </p>
                </div>

                <div class="pilares">
                    <article class="pilar">
                        <svg class="pilar__icone" viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1"
                             stroke-linejoin="round" aria-hidden="true" focusable="false">
                            <path d="M8 15 L14 7 H26 L32 15 L20 33 Z"/>
                            <path d="M8 15 H32 M14 7 L17 15 L20 33 L23 15 L26 7"/>
                        </svg>
                        <p class="pilar__numero">01</p>
                        <h2 class="pilar__titulo">Curadoria Premium</h2>
                        <p class="pilar__texto">
                            Apenas marcas renomadas e modelos colecionáveis, dos clássicos do desenho
                            técnico às edições limitadas.
                        </p>
                    </article>

                    <article class="pilar">
                        <svg class="pilar__icone" viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1"
                             stroke-linecap="round" aria-hidden="true" focusable="false">
                            <circle cx="20" cy="20" r="12"/>
                            <circle cx="20" cy="20" r="4"/>
                            <path d="M20 4 V11 M20 29 V36 M4 20 H11 M29 20 H36"/>
                        </svg>
                        <p class="pilar__numero">02</p>
                        <h2 class="pilar__titulo">Mecanismo &amp; Precisão</h2>
                        <p class="pilar__texto">
                            Da 0.3 à 2.0 mm, cada bitola é escolhida pela engenharia do mecanismo:
                            avanço do grafite, firmeza da ponta e constância do traço.
                        </p>
                    </article>

                    <article class="pilar">
                        <svg class="pilar__icone" viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                            <path d="M20 4 L29 17 L24 31 H16 L11 17 Z"/>
                            <path d="M20 4 V21"/>
                            <circle cx="20" cy="23" r="2"/>
                            <path d="M15 36 H25"/>
                        </svg>
                        <p class="pilar__numero">03</p>
                        <h2 class="pilar__titulo">Atendimento Especializado</h2>
                        <p class="pilar__texto">
                            Uma experiência pensada para o entusiasta da escrita, com orientação para
                            escolher a bitola, o peso e o grafite certos.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <!-- Vitrine -->
        <section class="vitrine" id="vitrine" aria-labelledby="vitrine-titulo">
            <div class="container">
                <div class="vitrine__topo">
                    <div>
                        <p class="sobretitulo">Coleção</p>
                        <h2 id="vitrine-titulo" class="vitrine__titulo">Vitrine</h2>
                    </div>

                    <!-- GET: o filtro fica na URL (dá para recarregar ou compartilhar).
                         O #vitrine no action faz a página voltar direto para esta seção. -->
                    <form class="filtros" method="get" action="<?= url('index.php') ?>#vitrine" data-envio-automatico>
                        <div class="filtros__campo">
                            <label for="filtro-bitola">Bitola</label>
                            <select name="bitola" id="filtro-bitola" class="campo__controle">
                                <option value="">Todas</option>
                                <?php foreach (BITOLAS as $opcao): ?>
                                    <option value="<?= $opcao ?>" <?= $opcao === $bitola ? 'selected' : '' ?>><?= formatar_bitola($opcao) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filtros__campo">
                            <label for="filtro-ordem">Ordenar por</label>
                            <select name="ordem" id="filtro-ordem" class="campo__controle">
                                <?php foreach (ORDENACOES as $chave => $opcao): ?>
                                    <option value="<?= $chave ?>" <?= $chave === $ordem ? 'selected' : '' ?>><?= $opcao['rotulo'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Sem JavaScript, este botão aplica o filtro. Com JS, ele some e
                             o filtro é aplicado assim que um select muda (assets/js/site.js). -->
                        <button type="submit" class="botao botao--contorno botao--pequeno" data-botao-filtrar>Filtrar</button>
                    </form>
                </div>

                <p class="vitrine__contagem">
                    <?= count($lapiseiras) === 1 ? '1 modelo' : count($lapiseiras) . ' modelos' ?>
                    <?= $bitola !== '' ? 'em ' . formatar_bitola($bitola) : '' ?>
                </p>

                <?php if (!$lapiseiras): ?>
                    <div class="vitrine__vazia">
                        <p><?= $bitola !== '' ? 'Nenhuma lapiseira nesta bitola por enquanto.' : 'A vitrine ainda está vazia.' ?></p>
                        <?php if ($bitola !== ''): ?>
                            <a href="<?= url('index.php') ?>#vitrine" class="botao botao--contorno botao--pequeno">Ver todas</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="grade-produtos">
                        <?php foreach ($lapiseiras as $lapiseira): ?>
                            <article class="card-produto<?= $lapiseira['ativo'] ? '' : ' card-produto--inativo' ?>">
                                <div class="card-produto__foto">
                                    <img src="<?= imagem_lapiseira($lapiseira) ?>"
                                         alt="<?= e($lapiseira['marca'] . ' ' . $lapiseira['modelo']) ?>" loading="lazy">

                                    <?php if (!$lapiseira['ativo']): ?>
                                        <span class="selo">Fora da vitrine</span>
                                    <?php endif; ?>

                                    <?php if (usuario_admin()): ?>
                                        <div class="card-produto__admin">
                                            <a href="<?= url('app/update.php?' . http_build_query(['id' => $lapiseira['id'], 'voltar' => $voltar])) ?>" class="botao-icone"
                                               aria-label="Editar <?= e($lapiseira['modelo']) ?>" title="Editar">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"
                                                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                                    <path d="M4 20 L5 15.5 L16 4.5 L19.5 8 L8.5 19 Z"/>
                                                    <path d="M13.5 7 L17 10.5"/>
                                                </svg>
                                            </a>
                                            <!-- Exclusão sempre por POST. data-confirmar pede confirmação antes de enviar. -->
                                            <form method="post" action="<?= url('app/delete.php') ?>"
                                                  data-confirmar="Excluir a lapiseira &quot;<?= e($lapiseira['marca'] . ' ' . $lapiseira['modelo']) ?>&quot;? Essa ação não pode ser desfeita.">
                                                <input type="hidden" name="id" value="<?= $lapiseira['id'] ?>">
                                                <input type="hidden" name="voltar" value="<?= e($voltar) ?>">
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
                                    <?php endif; ?>
                                </div>

                                <div class="card-produto__info">
                                    <p class="card-produto__marca"><?= e($lapiseira['marca']) ?></p>
                                    <h3 class="card-produto__modelo"><?= e($lapiseira['modelo']) ?></h3>
                                    <p class="card-produto__bitola">Bitola <?= formatar_bitola($lapiseira['bitola']) ?></p>
                                    <p class="card-produto__preco"><?= formatar_preco($lapiseira['preco']) ?></p>
                                </div>

                                <form method="post" action="<?= url('carrinho/adicionar.php') ?>" class="card-produto__acao">
                                    <input type="hidden" name="id" value="<?= $lapiseira['id'] ?>">
                                    <input type="hidden" name="voltar" value="<?= e($voltar) ?>">
                                    <button type="submit" class="botao botao--contorno botao--largo"
                                            <?= $lapiseira['ativo'] ? '' : 'disabled' ?>>Adicionar ao Carrinho</button>
                                </form>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
