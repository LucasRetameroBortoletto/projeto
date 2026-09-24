<?php
// Remove um modelo do carrinho (POST vindo da página do carrinho).
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === "POST" && ($id = ler_id($_POST['id'] ?? null))) {
    carrinho_remover($id);
    definir_aviso('sucesso', 'Item removido do carrinho.');
}

redirecionar('carrinho/index.php');
