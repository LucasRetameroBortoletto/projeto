<?php require_once __DIR__ . '/../login/verifica_admin.php'; // só o admin cadastra ?>
<?php
// Valores iniciais dos campos (formulário vazio, "Sim" marcado na vitrine)
$valores = ['modelo' => '', 'marca' => '', 'bitola' => '', 'preco' => '', 'ativo' => 'true'];
$erros = [];
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $valores = $_POST;                       // mantém o que foi digitado, caso dê erro
    $erros = validar_lapiseira($_POST);

    // A foto só é salva se os outros campos estiverem certos
    $imagem = null;
    if (count($erros) == 0) {
        list($imagem, $erro_imagem) = salvar_imagem($_FILES['imagem']);
        if ($erro_imagem != '') {
            $erros[] = $erro_imagem;
        }
    }

    if (count($erros) == 0) {
        cadastrar($conexao, $_POST['modelo'], $_POST['marca'], $_POST['bitola'], $_POST['preco'], $imagem, $_POST['ativo']);
        $sucesso = true;

        // Cadastrou: limpa o formulário para o próximo cadastro
        $valores = ['modelo' => '', 'marca' => '', 'bitola' => '', 'preco' => '', 'ativo' => 'true'];
    }
}

$titulo = 'Cadastrar lapiseira · Lapisari';
$estilos = ['forms.css'];
$pagina = 'cadastrar';
include __DIR__ . '/../includes/head.php';
?>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="pagina">
        <div class="container container--estreito">
            <div class="pagina__topo">
                <p class="sobretitulo">Administração</p>
                <h1 class="pagina__titulo">Cadastrar lapiseira</h1>
                <p class="pagina__texto">Preencha os dados do modelo. Se ele estiver disponível, aparece na vitrine assim que for salvo.</p>
            </div>

            <!-- enctype multipart: obrigatório para o formulário conseguir enviar arquivos (a foto) -->
            <form action="" method="post" enctype="multipart/form-data" class="formulario">

                <?php if ($sucesso): ?>
                    <p class="alerta alerta--sucesso">
                        Lapiseira cadastrada com sucesso! <a href="<?= url('index.php') ?>#vitrine">Ver na vitrine</a>
                    </p>
                <?php endif; ?>

                <?php foreach ($erros as $erro): ?>
                    <p class="alerta alerta--erro"><?= e($erro) ?></p>
                <?php endforeach; ?>

                <?php include __DIR__ . '/../includes/form_lapiseira.php'; ?>

                <div class="formulario__acoes">
                    <input type="reset" value="Limpar" class="botao botao--texto">
                    <input type="submit" value="Cadastrar" class="botao botao--primario">
                </div>
            </form>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
