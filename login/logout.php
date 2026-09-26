<?php
require_once __DIR__ . '/../includes/functions.php';

// Tira só os dados do login da sessão; o carrinho continua
unset($_SESSION['id']);
unset($_SESSION['papel']);

header("Location: " . url('index.php'));
exit;
?>
