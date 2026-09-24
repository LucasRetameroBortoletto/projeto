<?php
// Só o administrador exclui lapiseiras (verifica_admin já carrega functions.php)
require_once __DIR__ . '/../login/verifica_admin.php';

// A exclusão só acontece por POST: um link (GET) poderia ser aberto sem querer,
// por um robô ou por um <img src="..."> em outro site.
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = ler_id($_POST['id'] ?? null);
    $voltar = caminho_retorno($_POST['voltar'] ?? '', 'app/delete.php');
    $lapiseira = $id ? consultar($conexao, $id) : false;

    if ($lapiseira && deletar($conexao, $id)) {
        apagar_imagem($lapiseira['imagem']);
        definir_aviso('sucesso', 'Lapiseira "' . $lapiseira['marca'] . ' ' . $lapiseira['modelo'] . '" excluída com sucesso.');
    } else {
        definir_aviso('erro', $id ? "Nenhuma lapiseira encontrada com o ID $id." : 'Informe um ID válido.');
    }

    redirecionar($voltar);
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

            <!-- data-confirmar: o site.js pede confirmação antes de enviar -->
            <form action="" method="post" class="formulario formulario--em-linha"
                  data-confirmar="Excluir a lapiseira com este ID? Essa ação não pode ser desfeita.">
                <div class="campo">
                    <label for="id">ID</label>
                    <input type="number" name="id" id="id" class="campo__controle" min="1" required>
                </div>
                <input type="reset" value="Limpar" class="botao botao--texto">
                <input type="submit" value="Apagar" class="botao botao--perigo">
            </form>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
