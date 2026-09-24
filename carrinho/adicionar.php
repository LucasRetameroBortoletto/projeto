<?php
// Recebe o POST do botão "Adicionar ao Carrinho" e volta para a vitrine.
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    redirecionar('index.php');
}

$id = ler_id($_POST['id'] ?? null);
$voltar = caminho_retorno($_POST['voltar'] ?? '', 'index.php#vitrine');
$lapiseira = $id ? consultar($conexao, $id) : false;

// Só entra no carrinho o que existe e está na vitrine
if ($lapiseira && $lapiseira['ativo']) {
    carrinho_adicionar($id);
    definir_aviso('sucesso', $lapiseira['marca'] . ' ' . $lapiseira['modelo'] . ' foi adicionada ao carrinho.');
} else {
    definir_aviso('erro', 'Esta lapiseira não está disponível.');
}

redirecionar($voltar);
