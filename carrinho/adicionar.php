<?php
// Recebe o clique em "Adicionar ao Carrinho" (formulário POST do card na vitrine)
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = (int) $_POST['id'];
    $lapiseira = consultar($conexao, $id);

    // Só entra no carrinho uma lapiseira que existe e está na vitrine
    if ($lapiseira && $lapiseira['ativo']) {
        carrinho_adicionar($id);
    }
}

// Volta para a vitrine. O contador do carrinho no header já mostra o item novo.
header("Location: " . url('index.php') . '#vitrine');
exit;
?>
