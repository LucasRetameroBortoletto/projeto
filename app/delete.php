<?php require_once __DIR__ . '/../login/verifica_admin.php'; // só o admin exclui ?>
<?php
$mensagem = '';

// A exclusão só acontece por POST (formulário), nunca por um link:
// um link poderia ser aberto sem querer e apagar algo.
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = (int) ($_POST['id'] ?? 0);   // (int) transforma em número; "abc" vira 0
    $lapiseira = consultar($conexao, $id);

    if ($lapiseira) {
        deletar($conexao, $id);
        apagar_imagem($lapiseira['imagem']);   // apaga também a foto do disco
        $mensagem = "Registro apagado com sucesso";
    } else {
        $mensagem = "Nenhuma lapiseira encontrada com o ID $id.";
    }
}

$titulo = 'Excluir lapiseira · Lapisari';
$estilos = ['forms.css'];
$pagina = 'excluir';
include __DIR__ . '/../includes/head.php';
?>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="pagina">
        <div class="container container--estreito">
            <div class="pagina__topo">
                <p class="sobretitulo">Administração</p>
                <h1 class="pagina__titulo">Excluir lapiseira</h1>
                <p class="pagina__texto">Informe o ID da lapiseira. A exclusão remove o modelo e a foto e não pode ser desfeita.</p>
            </div>

            <!-- data-confirmar: o site.js pergunta "tem certeza?" antes de enviar -->
            <form action="" method="post" class="formulario formulario--em-linha"
                  data-confirmar="Excluir a lapiseira com este ID? Essa ação não pode ser desfeita.">
                <div class="campo">
                    <label for="id">ID</label>
                    <input type="number" name="id" id="id" class="campo__controle" min="1" required>
                </div>
                <input type="reset" value="Limpar" class="botao botao--texto">
                <input type="submit" value="Apagar" class="botao botao--perigo">
            </form>

            <?php if ($mensagem != ''): ?>
                <p class="resultado-vazio"><?= e($mensagem) ?> · <a href="<?= url('index.php') ?>#vitrine">Voltar à vitrine</a></p>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
