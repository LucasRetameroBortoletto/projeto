<?php 
// Incluir no topo das páginas que exigem login.
require_once __DIR__ . '/../includes/functions.php';

if(!isset($_SESSION['id'])) {
    header("Location: " . url('login/login.php'));
    // O header() só pede ao navegador para mudar de página; sem o exit o PHP
    // continuaria executando o resto do arquivo (e um formulário enviado seria processado).
    exit;
}
?>
