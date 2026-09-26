<?php
// Recebe o clique em "Remover" na página do carrinho
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    carrinho_remover((int) $_POST['id']);
}

// Volta para a página do carrinho
header("Location: " . url('carrinho/index.php'));
exit;
?>
