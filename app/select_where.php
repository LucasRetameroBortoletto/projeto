<?php
// Consulta de uma lapiseira pelo ID (área do administrador)
require_once __DIR__ . '/../login/verifica_admin.php';

$buscou = $_SERVER['REQUEST_METHOD'] == "POST";
$lapiseira = false;
if ($buscou) {
    $id = ler_id($_POST['id'] ?? null);
    $lapiseira = $id ? consultar($conexao, $id) : false;
}

$titulo = 'Consultar lapiseira · Lapisari';
$estilos = ['forms.css'];
$pagina = 'consultar';
include __DIR__ . '/../includes/head.php';
?>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="pagina">
        <div class="container container--estreito">
            <div class="pagina__topo">
                <p class="sobretitulo">Administração</p>
                <h1 class="pagina__titulo">Consultar lapiseira</h1>
                <p class="pagina__texto">Busque um modelo pelo ID para ver todos os dados cadastrados.</p>
            </div>

            <form action="" method="post" class="formulario formulario--em-linha">
                <div class="campo">
                    <label for="id">ID</label>
                    <input type="number" name="id" id="id" class="campo__controle" min="1" required
                           value="<?= e($_POST['id'] ?? '') ?>">
                </div>
                <input type="reset" value="Limpar" class="botao botao--texto">
                <input type="submit" value="Consultar" class="botao botao--primario">
            </form>

            <?php if ($buscou && !$lapiseira): ?>
                <p class="resultado-vazio" role="status">Nenhuma lapiseira encontrada com este ID.</p>
            <?php elseif ($lapiseira): ?>
                <article class="ficha" aria-label="Resultado da busca">
                    <img src="<?= imagem_lapiseira($lapiseira) ?>" alt="<?= e($lapiseira['marca'] . ' ' . $lapiseira['modelo']) ?>" class="ficha__foto">
                    <div>
                        <p class="sobretitulo"><?= e($lapiseira['marca']) ?></p>
                        <h2 class="ficha__modelo"><?= e($lapiseira['modelo']) ?></h2>
                        <dl class="ficha__dados">
                            <dt>ID</dt>        <dd><?= $lapiseira['id'] ?></dd>
                            <dt>Bitola</dt>    <dd><?= formatar_bitola($lapiseira['bitola']) ?></dd>
                            <dt>Preço</dt>     <dd><?= formatar_preco($lapiseira['preco']) ?></dd>
                            <dt>Vitrine</dt>   <dd><?= $lapiseira['ativo'] ? 'Disponível' : 'Fora da vitrine' ?></dd>
                            <dt>Cadastro</dt>  <dd><?= date('d/m/Y H:i', strtotime($lapiseira['criado_em'])) ?></dd>
                        </dl>
                        <div class="ficha__acoes">
                            <a href="<?= url('app/update.php?id=' . $lapiseira['id']) ?>" class="botao botao--contorno botao--pequeno">Editar</a>
                            <form method="post" action="<?= url('app/delete.php') ?>"
                                  data-confirmar="Excluir a lapiseira &quot;<?= e($lapiseira['marca'] . ' ' . $lapiseira['modelo']) ?>&quot;? Essa ação não pode ser desfeita.">
                                <input type="hidden" name="id" value="<?= $lapiseira['id'] ?>">
                                <button type="submit" class="botao botao--perigo botao--pequeno">Excluir</button>
                            </form>
                        </div>
                    </div>
                </article>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
