<?php
// Cadastro de clientes: página aberta (o cliente cria a própria conta).
// Toda conta nasce como 'cliente'; para virar admin, altere o papel direto no banco.
require_once __DIR__ . '/../includes/functions.php';

$email = '';
$erros = [];
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Informe um e-mail válido.';
    }
    if (strlen($_POST['password']) < 6) {
        $erros[] = 'A senha precisa ter pelo menos 6 caracteres.';
    }
    if (count($erros) == 0 && consultar_user($conexao, $email)) {
        $erros[] = 'Já existe uma conta com este e-mail.';
    }

    if (count($erros) == 0) {
        cadastrar_user($conexao, $email, $_POST['password']);
        $sucesso = true;
        $email = '';
    }
}

$titulo = 'Criar conta · Lapisari';
$estilos = ['forms.css'];
$pagina = 'registrar';
include __DIR__ . '/../includes/head.php';
?>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="pagina">
        <div class="container container--estreito">
            <div class="pagina__topo">
                <p class="sobretitulo">Sua conta</p>
                <h1 class="pagina__titulo">Criar conta</h1>
                <p class="pagina__texto">Cadastre-se para acompanhar seus pedidos e sua coleção.</p>
            </div>

            <form action="" method="post" class="formulario">
                <?php if ($sucesso): ?>
                    <p class="alerta alerta--sucesso">
                        Usuário cadastrado com sucesso! <a href="<?= url('login/login.php') ?>">Entrar</a>
                    </p>
                <?php endif; ?>

                <?php foreach ($erros as $erro): ?>
                    <p class="alerta alerta--erro"><?= e($erro) ?></p>
                <?php endforeach; ?>

                <div class="formulario__linha">
                    <div class="campo">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" class="campo__controle" required maxlength="120"
                               value="<?= e($email) ?>">
                    </div>

                    <div class="campo">
                        <label for="password">Senha</label>
                        <input type="password" name="password" id="password" class="campo__controle" required minlength="6">
                        <p class="campo__ajuda">Mínimo de 6 caracteres.</p>
                    </div>
                </div>

                <div class="formulario__acoes">
                    <p class="formulario__rodape">
                        Já tem conta? <a href="<?= url('login/login.php') ?>">Entrar</a>
                    </p>
                    <input type="reset" value="Limpar" class="botao botao--texto">
                    <input type="submit" value="Cadastrar" class="botao botao--primario">
                </div>
            </form>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
