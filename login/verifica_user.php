<?php 
// Colocar no topo das páginas que exigem login.
require_once __DIR__ . '/../includes/functions.php';

if(!isset($_SESSION['id'])) {
    header("Location: " . url('login/login.php'));
    exit; // sem o exit o PHP continuaria rodando o resto da página mesmo sem login
}
?>
