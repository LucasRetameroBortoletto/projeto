<?php
// Header fixo do site. A página precisa ter carregado includes/functions.php antes.
$quantidade_carrinho = carrinho_quantidade();
$texto_itens = $quantidade_carrinho === 1 ? '1 item' : $quantidade_carrinho . ' itens';
?>
<header class="cabecalho">
    <!-- Nome e lapiseira são elementos separados de propósito: nas próximas fases
         cada um é animado de forma independente (o nome na splash, a lapiseira no login). -->
    <a href="<?= url('index.php') ?>" class="cabecalho__marca" aria-label="Lapisari, página inicial">Lapisari</a>

    <!-- Lapiseira em SVG inline (e não <img>) para o CSS/JS conseguirem animar cada peça.
         Cada peça tem sua classe: botão traseiro, corpo, grip, ponta e grafite. -->
    <svg class="cabecalho__lapiseira lapiseira" viewBox="0 0 300 24" fill="none" stroke="currentColor"
         stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
        <g class="lapiseira__botao">
            <rect x="4" y="8.5" width="14" height="7" rx="1.5"/>
        </g>
        <g class="lapiseira__corpo">
            <rect x="18" y="7" width="158" height="10" rx="0.5"/>
            <path d="M23 7 V17"/>
            <path d="M30 7 V4 H116 C120 4 122 5.2 122 7"/>
        </g>
        <g class="lapiseira__grip">
            <path d="M176 7.5 H236 V16.5 H176"/>
            <path d="M181 7.5 V16.5 M186 7.5 V16.5 M191 7.5 V16.5 M196 7.5 V16.5 M201 7.5 V16.5 M206 7.5 V16.5
                     M211 7.5 V16.5 M216 7.5 V16.5 M221 7.5 V16.5 M226 7.5 V16.5 M231 7.5 V16.5" stroke-width="0.6"/>
        </g>
        <g class="lapiseira__ponta">
            <path d="M236 7.5 L258 10.5 V13.5 L236 16.5"/>
            <path d="M258 11.2 H276 V12.8 H258"/>
        </g>
        <path class="lapiseira__grafite" d="M276 12 H282" stroke-width="1.4"/>
    </svg>

    <nav class="cabecalho__acoes" aria-label="Conta e carrinho">
        <?php if (usuario_logado()): ?>
            <a href="<?= url('login/logout.php') ?>" class="botao-cabecalho">Sair</a>
        <?php else: ?>
            <a href="<?= url('login/login.php') ?>" class="botao-cabecalho botao-cabecalho--contorno" id="botao-entrar">Entrar</a>
        <?php endif; ?>

        <a href="<?= url('carrinho/index.php') ?>" class="botao-cabecalho botao-cabecalho--carrinho"
           aria-label="Carrinho, <?= $texto_itens ?>">
            <svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                <path d="M5 8 H19 L18 21 H6 Z"/>
                <path d="M9 8 V6.5 A3 3 0 0 1 15 6.5 V8"/>
            </svg>
            <span>Carrinho</span>
            <span class="contador<?= $quantidade_carrinho === 0 ? ' contador--vazio' : '' ?>" aria-hidden="true"><?= $quantidade_carrinho ?></span>
        </a>
    </nav>
</header>

<?php if (usuario_admin()) include __DIR__ . '/barra_admin.php'; ?>

<?php include __DIR__ . '/aviso.php'; ?>
