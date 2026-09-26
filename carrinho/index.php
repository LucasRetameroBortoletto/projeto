<?php
require_once __DIR__ . '/../includes/functions.php';

// Lista do carrinho (dados de cada lapiseira + quantidade + subtotal)
$itens = carrinho_itens($conexao);

// Soma os subtotais para ter o total do pedido
$total = 0;
foreach ($itens as $item) {
    $total = $total + $item['subtotal'];
}

$titulo = 'Carrinho · Lapisari';
$estilos = ['carrinho.css'];
$pagina = 'carrinho';
include __DIR__ . '/../includes/head.php';
?>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="pagina">
        <div class="container">
            <div class="pagina__topo">
                <p class="sobretitulo">Sua seleção</p>
                <h1 class="pagina__titulo">Carrinho</h1>
            </div>

            <?php if (!$itens): ?>
                <div class="carrinho-vazio">
                    <p>Seu carrinho está vazio.</p>
                    <a href="<?= url('index.php') ?>#vitrine" class="botao botao--contorno">Ver a vitrine</a>
                </div>
            <?php else: ?>
                <div class="carrinho">
                    <ul class="carrinho__lista">
                        <?php foreach ($itens as $item): ?>
                            <li class="item-carrinho">
                                <img src="<?= imagem_lapiseira($item) ?>" alt="" class="item-carrinho__foto">
                                <div class="item-carrinho__info">
                                    <p class="item-carrinho__marca"><?= e($item['marca']) ?></p>
                                    <p class="item-carrinho__modelo"><?= e($item['modelo']) ?></p>
                                    <p class="item-carrinho__detalhe">
                                        Bitola <?= formatar_bitola($item['bitola']) ?> ·
                                        <?= $item['quantidade'] ?> &times; <?= formatar_preco($item['preco']) ?>
                                    </p>
                                </div>
                                <p class="item-carrinho__subtotal"><?= formatar_preco($item['subtotal']) ?></p>
                                <form method="post" action="<?= url('carrinho/remover.php') ?>">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="botao botao--texto botao--pequeno"
                                            aria-label="Remover <?= e($item['modelo']) ?> do carrinho">Remover</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <aside class="resumo" aria-label="Resumo do pedido">
                        <p class="sobretitulo">Resumo</p>
                        <dl class="resumo__linhas">
                            <dt>Itens</dt>
                            <dd><?= carrinho_quantidade() ?></dd>
                            <dt class="resumo__total">Total</dt>
                            <dd class="resumo__total"><?= formatar_preco($total) ?></dd>
                        </dl>
                        <a href="<?= url('index.php') ?>#vitrine" class="botao botao--contorno botao--largo">Continuar comprando</a>
                    </aside>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
