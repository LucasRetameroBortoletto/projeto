<?php
// Só o administrador cadastra lapiseiras (verifica_admin já carrega functions.php)
require_once __DIR__ . '/../login/verifica_admin.php';

$valores = ['modelo' => '', 'marca' => '', 'bitola' => '', 'preco' => '', 'ativo' => 'true'];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    [$valores, $erros] = validar_lapiseira($_POST);

    // A foto só é salva depois que os outros campos passam na validação,
    // para não sobrar arquivo na pasta de um cadastro que não aconteceu.
    $imagem = null;
    if (!$erros) {
        try {
            $imagem = salvar_imagem($_FILES['imagem'] ?? null);
        } catch (RuntimeException $erro) {
            $erros['imagem'] = $erro->getMessage();
        }
    }

    if (!$erros) {
        try {
            cadastrar($conexao, $valores['modelo'], $valores['marca'], $valores['bitola'],
                      $valores['preco'], $imagem, $valores['ativo'] === 'true');

            definir_aviso('sucesso', 'Lapiseira "' . $valores['marca'] . ' ' . $valores['modelo'] . '" cadastrada com sucesso!');
            // Redireciona depois de salvar: assim um F5 não cadastra a mesma lapiseira de novo
            redirecionar('index.php#vitrine');
        } catch (PDOException $erro) {
            apagar_imagem($imagem);
            $erros['geral'] = 'Não foi possível salvar no banco de dados. Tente novamente.';
        }
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

            <!-- enctype multipart: obrigatório para o formulário conseguir enviar arquivos -->
            <form action="" method="post" enctype="multipart/form-data" class="formulario">
                <?php if (isset($erros['geral'])): ?>
                    <p class="alerta alerta--erro" role="alert"><?= e($erros['geral']) ?></p>
                <?php endif; ?>

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
