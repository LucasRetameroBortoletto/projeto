<?php
// Header fixo do site. A página precisa ter carregado includes/functions.php antes.
$quantidade_carrinho = carrinho_quantidade();
$texto_itens = $quantidade_carrinho === 1 ? '1 item' : $quantidade_carrinho . ' itens';
?>
<?php
// Login recusado vindo do cartão do header: o header já começa "transformado"
// (escondido), porque o cartão é desenhado aberto (includes/metamorfose.php)
$metamorfose_aberta = !usuario_logado() && isset($_SESSION['login_erro']);
?>
<header class="cabecalho<?= $metamorfose_aberta ? ' cabecalho--metamorfose' : '' ?>">
    <!-- Nome e lapiseira são elementos separados de propósito: nas próximas fases
         cada um é animado de forma independente (o nome na splash, a lapiseira no login). -->
    <a href="<?= url('index.php') ?>" class="cabecalho__marca" aria-label="Lapisari, página inicial">
        <?php
            // Logotipo em SVG (includes/assinatura.php): o mesmo traço escrito na splash
            $classe_assinatura = 'cabecalho__assinatura';
            include __DIR__ . '/assinatura.php';
        ?>
    </a>

    <?php
        // Lapiseira em SVG (includes/lapiseira.php): o mesmo desenho é usado no login
        $classe_lapiseira = 'cabecalho__lapiseira';
        include __DIR__ . '/lapiseira.php';
    ?>

    <nav class="cabecalho__acoes" aria-label="Conta e carrinho">
        <?php if (usuario_logado()): ?>
            <a href="<?= url('login/logout.php') ?>" class="botao-cabecalho">Sair</a>
        <?php else: ?>
            <!-- Sem JS é um link para a página de login; com JS abre a metamorfose -->
            <a href="<?= url('login/login.php') ?>" class="botao-cabecalho botao-cabecalho--contorno" id="botao-entrar"
               aria-haspopup="dialog" data-abrir-metamorfose>Entrar</a>
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

<?php if (!usuario_logado()) include __DIR__ . '/metamorfose.php'; ?>

<?php if (usuario_admin()) include __DIR__ . '/barra_admin.php'; ?>
