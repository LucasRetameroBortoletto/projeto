<?php
require_once __DIR__ . '/../includes/functions.php';

// Quem já está logado não precisa ver o login
if (usuario_logado()) {
    redirecionar('index.php');
}

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $usuario = consultar_user($conexao, trim($_POST['email'] ?? ''));

    // password_verify compara a senha digitada com o hash salvo no banco.
    // A mensagem de erro é a mesma para "e-mail não existe" e "senha errada",
    // para não revelar quais e-mails têm conta.
    if($usuario && password_verify($_POST['password'] ?? '', $usuario['senha'])) {

        // Novo id de sessão ao logar: impede que alguém que conhecia o id
        // antigo (session fixation) aproveite a sessão já autenticada.
        session_regenerate_id(true);

        //cria uma sessão para manter o usuário logado
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['papel'] = $usuario['papel'];

        redirecionar('index.php'); //leva o usuário a index.php
    } else {
        $mensagem = "Usuário ou senha inválidos!!";
    }
}

$titulo = 'Entrar · Lapisari';
$estilos = ['login.css'];
$scripts = ['login.js'];
include __DIR__ . '/../includes/head.php';
?>
<body class="pagina-login">
    <?php include __DIR__ . '/../includes/aviso.php'; ?>

    <main class="login">
        <div class="login__cartao">
            <a href="<?= url('index.php') ?>" class="login__marca" aria-label="Lapisari, página inicial">Lapisari</a>

            <h1 class="login__titulo">Bem-vindo</h1>
            <p class="login__subtitulo">Faça login para continuar</p>

            <?php if (isset($mensagem)): ?>
                <p class="login__erro" role="alert"><?= e($mensagem) ?></p>
            <?php endif; ?>

            <!-- novalidate: a validação de campo vazio é feita pelo login.js, com o visual do site -->
            <form action="" method="POST" id="formulario" class="login__formulario" novalidate>
                <div class="login__campo">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email" autocomplete="email"
                           value="<?= e($_POST['email'] ?? '') ?>">
                </div>

                <div class="login__campo">
                    <label for="password">Senha</label>
                    <input type="password" name="password" id="password" autocomplete="current-password">
                </div>

                <button type="submit" id="enviar" class="login__enviar">Entrar</button>
            </form>

            <footer class="login__rodape">
                <a href="<?= url('login/registerUser.php') ?>">Criar conta</a>
                <span aria-hidden="true">·</span>
                <a href="<?= url('index.php') ?>">Voltar à loja</a>
            </footer>
        </div>
    </main>
</body>
</html>
