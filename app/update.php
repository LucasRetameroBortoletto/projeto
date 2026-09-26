<?php require_once __DIR__ .'/../login/verifica_admin.php'; // só o admin atualiza ?>
<?php
// O id chega pela URL (update.php?id=3, vindo do ícone de editar)
// ou pelo campo escondido do formulário quando ele é enviado.
$id = 0;
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];   // (int) transforma em número; "abc" vira 0
}
if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
}

$lapiseira = consultar($conexao, $id);   // false se o id não existir
$erros = [];
$sucesso = false;

if ($lapiseira && $_SERVER['REQUEST_METHOD'] == "POST") {
    $erros = validar_lapiseira($_POST);

    // Sem foto nova, continua com a foto que já estava cadastrada
    $imagem = $lapiseira['imagem'];

    if (count($erros) == 0) {
        list($nova_imagem, $erro_imagem) = salvar_imagem($_FILES['imagem']);
        if ($erro_imagem != '') {
            $erros[] = $erro_imagem;
        } elseif ($nova_imagem) {
            apagar_imagem($lapiseira['imagem']);   // a foto antiga não é mais usada
            $imagem = $nova_imagem;
        }
    }

    if (count($erros) == 0) {
        atualizar($conexao, $id, $_POST['modelo'], $_POST['marca'], $_POST['bitola'], $_POST['preco'], $imagem, $_POST['ativo']);
        $sucesso = true;
        $lapiseira = consultar($conexao, $id);   // busca de novo para mostrar os dados salvos
    }
}

// Valores dos campos: o que foi digitado (se deu erro) ou o que está no banco
if (count($erros) > 0) {
    $valores = $_POST;
} elseif ($lapiseira) {
    $valores = $lapiseira;
    $valores['ativo'] = $lapiseira['ativo'] ? 'true' : 'false';   // o banco devolve true/false
}

$titulo = 'Atualizar lapiseira · Lapisari';
$estilos = ['forms.css'];
$pagina = 'atualizar';
include __DIR__ . '/../includes/head.php';
?>
<body>
    <?php  include __DIR__ . '/../includes/header.php'; ?>

    <main class="pagina">
        <div class="container container--estreito">
            <div class="pagina__topo">
                <p class="sobretitulo">Administração</p>
                <h1 class="pagina__titulo">Atualizar lapiseira</h1>
            </div>

            <?php if (!$lapiseira): ?>
                <!-- Nenhuma lapiseira escolhida (ou id inexistente): pede o ID -->
                <form action="" method="get" class="formulario formulario--em-linha">
                    <div class="campo">
                        <label for="id">ID da lapiseira</label>
                        <input type="number" name="id" id="id" class="campo__controle" min="1" required>
                    </div>
                    <input type="submit" value="Buscar" class="botao botao--primario">
                </form>
                <?php if ($id > 0): ?>
                    <p class="resultado-vazio">Nenhuma lapiseira encontrada com o ID <?= $id ?>.</p>
                <?php endif; ?>

            <?php else: ?>
                <form action="" method="post" enctype="multipart/form-data" class="formulario">
                    <input type="hidden" name="id" value="<?= $lapiseira['id'] ?>">

                    <?php if ($sucesso): ?>
                        <p class="alerta alerta--sucesso">Alteração realizada com sucesso!!</p>
                    <?php endif; ?>

                    <?php foreach ($erros as $erro): ?>
                        <p class="alerta alerta--erro"><?= e($erro) ?></p>
                    <?php endforeach; ?>

                    <?php
                        $imagem_atual = $lapiseira['imagem'];
                        include __DIR__ . '/../includes/form_lapiseira.php';
                    ?>

                    <div class="formulario__acoes">
                        <a href="<?= url('index.php') ?>#vitrine" class="botao botao--texto">Voltar à vitrine</a>
                        <input type="reset" value="Desfazer alterações" class="botao botao--texto">
                        <input type="submit" value="Atualizar" class="botao botao--primario">
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
