<?php
require_once __DIR__ . '/../includes/functions.php';

// Quem já está logado não precisa ver o login
if (usuario_logado()) {
    header("Location: " . url('index.php'));
    exit;
}

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $usuario = consultar_user($conexao, $_POST['email']);

    // password_verify compara a senha digitada com o hash salvo no banco
    if($usuario && password_verify($_POST['password'], $usuario['senha'])) {

        // Gera um novo id de sessão ao logar (proteção contra roubo de sessão)
        session_regenerate_id(true);

        //cria uma sessão para manter o usuário logado
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['papel'] = $usuario['papel'];

        // #vitrine: vai direto para a vitrine, sem repetir a splash de abertura
        header("Location: " . url('index.php') . '#vitrine'); //leva o usuário a index.php
        exit;
    } else {
        $mensagem = "Usuário ou senha inválidos!!";

        // O login veio do cartão do header (campo escondido "origem"):
        // volta para a página inicial com o cartão já aberto e a mensagem.
        // A mensagem fica guardada na sessão até o includes/metamorfose.php mostrá-la.
        if (isset($_POST['origem']) && $_POST['origem'] == 'cartao') {
            $_SESSION['login_erro'] = ['mensagem' => $mensagem, 'email' => $_POST['email']];
            header("Location: " . url('index.php'));
            exit;
        }
    }
}

$titulo = 'Entrar · Lapisari';
$estilos = ['login.css'];
$scripts = ['login.js'];
include __DIR__ . '/../includes/head.php';
?>
<body class="pagina-login">

    <main class="login">
        <!-- Cartão horizontal: marca e boas-vindas à esquerda, formulário à direita,
             para caber inteiro na tela sem rolar -->
        <div class="login__cartao">
            <div class="login__apresentacao">
                <a href="<?= url('index.php') ?>" class="login__marca" aria-label="Lapisari, página inicial">
                    <?php
                        $classe_assinatura = 'login__assinatura';
                        include __DIR__ . '/../includes/assinatura.php';
                    ?>
                </a>
                <h1 class="login__titulo">Bem-vindo</h1>
                <p class="login__subtitulo">Faça login para continuar</p>

                <!-- Ações secundárias como botões de verdade: fáceis de achar e de clicar -->
                <footer class="login__rodape">
                    <p class="login__convite">Ainda não tem conta?</p>
                    <a href="<?= url('login/registerUser.php') ?>" class="login__botao-secundario">Criar conta</a>
                    <a href="<?= url('index.php') ?>" class="login__voltar">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                            <path d="M10 3 L5 8 L10 13"/>
                        </svg>
                        Voltar à loja
                    </a>
                </footer>
            </div>

            <div class="login__area-formulario">
                <?php if (isset($mensagem)): ?>
                    <p class="login__erro" role="alert"><?= e($mensagem) ?></p>
                <?php endif; ?>

                <!-- novalidate: a validação de campo vazio é feita pelo login.js, com o visual do site -->
                <form action="" method="POST" id="formulario" class="login__formulario" novalidate data-form-login>
                    <div class="login__campo">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" autocomplete="email"
                               value="<?= e($_POST['email'] ?? '') ?>">
                    </div>

                    <div class="login__campo">
                        <label for="password">Senha</label>
                        <input type="password" name="password" id="password" autocomplete="current-password">
                    </div>

                    <!-- A lapiseira é o botão de envio. O rótulo "Entrar" fica visível
                         para ninguém depender só do desenho para entender que é clicável.
                         login__lapiseira--erro (login recusado) faz o login.js quebrar o grafite. -->
                    <button type="submit" id="enviar" class="login__lapiseira<?= isset($mensagem) ? ' login__lapiseira--erro' : '' ?>" aria-label="Entrar na conta">
                        <?php
                            $classe_lapiseira = 'login__lapiseira-desenho';
                            include __DIR__ . '/../includes/lapiseira.php';
                        ?>
                        <span class="login__lapiseira-rotulo" aria-hidden="true">Entrar</span>
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
