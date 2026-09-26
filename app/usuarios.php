<?php
// Tela de usuários: o admin vê todas as contas e troca o papel (cliente/admin)
require_once __DIR__ . '/../login/verifica_admin.php'; // só o admin acessa

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = (int) $_POST['id'];
    $papel = $_POST['papel'];

    if ($papel != 'cliente' && $papel != 'admin') {
        // Só aceita os dois papéis que existem (o banco também recusaria outro valor)
        $erro = 'Papel inválido.';
    } elseif ($id == $_SESSION['id'] && $papel != 'admin') {
        // O admin não pode tirar o próprio acesso: poderia ficar sem nenhum admin no sistema
        $erro = 'Você não pode remover o seu próprio acesso de administrador.';
    } else {
        atualizar_papel($conexao, $id, $papel);
        $mensagem = 'Papel atualizado com sucesso!';
    }
}

$usuarios = listar_usuarios($conexao);

$titulo = 'Usuários · Lapisari';
$estilos = ['forms.css'];
$pagina = 'usuarios';
include __DIR__ . '/../includes/head.php';
?>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="pagina">
        <div class="container container--estreito">
            <div class="pagina__topo">
                <p class="sobretitulo">Administração</p>
                <h1 class="pagina__titulo">Usuários</h1>
                <p class="pagina__texto">Escolha o papel de cada conta. Administradores veem a barra de administração e podem cadastrar, editar e excluir lapiseiras.</p>
            </div>

            <?php if ($mensagem != ''): ?>
                <p class="alerta alerta--sucesso"><?= e($mensagem) ?></p>
            <?php endif; ?>
            <?php if ($erro != ''): ?>
                <p class="alerta alerta--erro"><?= e($erro) ?></p>
            <?php endif; ?>

            <div class="tabela-container">
                <table class="tabela">
                    <thead>
                        <tr>
                            <th scope="col">E-mail</th>
                            <th scope="col">Papel</th>
                            <th scope="col"><span class="visualmente-oculto">Ação</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td>
                                    <?= e($usuario['email']) ?>
                                    <?php if ($usuario['id'] == $_SESSION['id']): ?>
                                        <span class="selo">Você</span>
                                    <?php endif; ?>
                                </td>
                                <!-- Um formulário por linha: envia o id do usuário e o papel escolhido -->
                                <td colspan="2">
                                    <form action="" method="post" class="usuarios__form">
                                        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                                        <label class="visualmente-oculto" for="papel-<?= $usuario['id'] ?>">Papel de <?= e($usuario['email']) ?></label>
                                        <select name="papel" id="papel-<?= $usuario['id'] ?>" class="campo__controle">
                                            <option value="cliente" <?= trim($usuario['papel']) == 'cliente' ? 'selected' : '' ?>>Cliente</option>
                                            <option value="admin" <?= trim($usuario['papel']) == 'admin' ? 'selected' : '' ?>>Administrador</option>
                                        </select>
                                        <input type="submit" value="Salvar" class="botao botao--contorno botao--pequeno">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
