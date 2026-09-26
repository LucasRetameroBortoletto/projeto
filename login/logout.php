<?php
require_once __DIR__ . '/../includes/functions.php';

// Sair apaga a sessão inteira: o login e também o carrinho
$_SESSION = [];
session_destroy();

header("Location: " . url('index.php'));
exit;
?>
