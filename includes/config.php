<?php
// Configurações gerais do site. É carregado pelo functions.php,
// então toda página que inclui o functions.php já tem isto.

// Pasta do projeto dentro do servidor (a parte da URL depois do localhost).
// Ex.: http://localhost/MINI SISTEMA/index.php  ->  '/MINI SISTEMA'
// Se mudar o nome da pasta, troque só aqui.
define('BASE_URL', '/MINI SISTEMA');

// A sessão guarda quem está logado e o carrinho.
// Ela precisa ser iniciada antes de qualquer HTML ser enviado.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
