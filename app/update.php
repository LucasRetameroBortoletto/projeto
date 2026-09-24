<?php
// Só o administrador edita lapiseiras (verifica_admin já carrega functions.php)
require_once __DIR__ .'/../login/verifica_admin.php';

// O id chega pela URL (update.php?id=3, vindo do card ou do relatório)
// ou pelo campo escondido do formulário, quando ele é enviado.
$id = ler_id($_POST['id'] ?? $_GET['id'] ?? null);
$lapiseira = $id ? consultar($conexao, $id) : false;

// Para onde voltar depois de salvar ou cancelar (vitrine ou relatório)
$voltar = caminho_retorno($_POST['voltar'] ?? $_GET['voltar'] ?? '', 'index.php#vitrine');
$erros = [];

if ($lapiseira && $_SERVER['REQUEST_METHOD'] == "POST") {
    [$valores, $erros] = validar_lapiseira($_POST);

    $nova_imagem = null;
    if (!$erros) {
        try {
            $nova_imagem = salvar_imagem($_FILES['imagem'] ?? null);
        } catch (RuntimeException $erro) {
            $erros['imagem'] = $erro->getMessage();
        }
    }

    if (!$erros) {
        try {
            // Sem arquivo novo, mantém a foto que já estava cadastrada
            atualizar($conexao, $id, $valores['modelo'], $valores['marca'], $valores['bitola'],
                      $valores['preco'], $nova_imagem ?? $lapiseira['imagem'], $valores['ativo'] === 'true');

            if ($nova_imagem) {
                apagar_imagem($lapiseira['imagem']); // a foto antiga não é mais usada
            }

            definir_aviso('sucesso', 'Alteração realizada com sucesso!');
            redirecionar($voltar);
        } catch (PDOException $erro) {
            apagar_imagem($nova_imagem);
            $erros['geral'] = 'Não foi possível salvar no banco de dados. Tente novamente.';
        }
    }
} elseif ($lapiseira) {
    // Primeira abertura: preenche o formulário com o que está no banco
    $valores = [
        'modelo' => $lapiseira['modelo'],
        'marca'  => $lapiseira['marca'],
        'bitola' => $lapiseira['bitola'],
        'preco'  => $lapiseira['preco'],
        'ativo'  => $lapiseira['ativo'] ? 'true' : 'false',
    ];
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
                <?php if ($lapiseira): ?>
                    <p class="pagina__texto">Editando <strong><?= e($lapiseira['marca'] . ' ' . $lapiseira['modelo']) ?></strong> (ID <?= $lapiseira['id'] ?>).</p>
                <?php else: ?>
                    <p class="pagina__texto">Informe o ID da lapiseira que você quer atualizar.</p>
                <?php endif; ?>
            </div>

            <?php if (!$lapiseira): ?>
                <!-- Sem lapiseira escolhida: busca pelo ID (GET, para cair no mesmo fluxo do link do card) -->
                <form action="" method="get" class="formulario formulario--em-linha">
                    <div class="campo">
                        <label for="id">ID</label>
                        <input type="number" name="id" id="id" class="campo__controle" min="1" required
                               value="<?= e($_GET['id'] ?? '') ?>"
                               <?= isset($_GET['id']) ? 'aria-invalid="true" aria-describedby="erro-id"' : '' ?>>
                        <?php if (isset($_GET['id'])): ?>
                            <p class="campo__erro" id="erro-id">Nenhuma lapiseira encontrada com este ID.</p>
                        <?php endif; ?>
                    </div>
                    <input type="submit" value="Buscar" class="botao botao--primario">
                </form>
            <?php else: ?>
                <form action="" method="post" enctype="multipart/form-data" class="formulario">
                    <input type="hidden" name="id" value="<?= $lapiseira['id'] ?>">
                    <input type="hidden" name="voltar" value="<?= e($voltar) ?>">

                    <?php if (isset($erros['geral'])): ?>
                        <p class="alerta alerta--erro" role="alert"><?= e($erros['geral']) ?></p>
                    <?php endif; ?>

                    <?php
                        $imagem_atual = $lapiseira['imagem'];
                        include __DIR__ . '/../includes/form_lapiseira.php';
                    ?>

                    <div class="formulario__acoes">
                        <a href="<?= url($voltar) ?>" class="botao botao--texto">Cancelar</a>
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
