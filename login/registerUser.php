<?php
// Cadastro de clientes: página pública (numa loja, o cliente cria a própria conta).
// Toda conta nasce como 'cliente'; o admin é definido direto no banco.
require_once __DIR__ . '/../includes/functions.php';

$email = '';
$erros = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 120) {
        $erros['email'] = 'Informe um e-mail válido.';
    }
    if (mb_strlen($senha) < 6) {
        $erros['password'] = 'A senha precisa ter pelo menos 6 caracteres.';
    }
    if (!$erros && consultar_user($conexao, $email)) {
        $erros['email'] = 'Já existe uma conta com este e-mail.';
    }

    if (!$erros) {
        try {
            cadastrar_user($conexao, $email, $senha);
            definir_aviso('sucesso', 'Conta criada! Agora é só entrar.');
            redirecionar('login/login.php');
        } catch (PDOException $erro) {
            $erros['geral'] = 'Não foi possível criar a conta. Tente novamente.';
        }
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
                <?php if (isset($erros['geral'])): ?>
                    <p class="alerta alerta--erro" role="alert"><?= e($erros['geral']) ?></p>
                <?php endif; ?>

                <div class="campo">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email" class="campo__controle" required maxlength="120"
                           autocomplete="email" value="<?= e($email) ?>"
                           <?= isset($erros['email']) ? 'aria-invalid="true" aria-describedby="erro-email"' : '' ?>>
                    <?php if (isset($erros['email'])): ?>
                        <p class="campo__erro" id="erro-email"><?= e($erros['email']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="campo">
                    <label for="password">Senha</label>
                    <input type="password" name="password" id="password" class="campo__controle" required minlength="6"
                           autocomplete="new-password"
                           aria-describedby="ajuda-senha<?= isset($erros['password']) ? ' erro-senha' : '' ?>"
                           <?= isset($erros['password']) ? 'aria-invalid="true"' : '' ?>>
                    <p class="campo__ajuda" id="ajuda-senha">Mínimo de 6 caracteres.</p>
                    <?php if (isset($erros['password'])): ?>
                        <p class="campo__erro" id="erro-senha"><?= e($erros['password']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="formulario__acoes">
                    <input type="reset" value="Limpar" class="botao botao--texto">
                    <input type="submit" value="Cadastrar" class="botao botao--primario">
                </div>

                <p class="formulario__rodape">
                    Já tem conta? <a href="<?= url('login/login.php') ?>">Entrar</a>
                </p>
            </form>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
